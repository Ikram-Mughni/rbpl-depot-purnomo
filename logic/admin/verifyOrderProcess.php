<?php
session_start();
include '../../config/dbConfig.php';

if (isset($_POST['verify_btn'])) {
    $id_order = $_POST['id_order'];
    $qty_order = $_POST['qty_order'];
    $order_type = $_POST['order_type'];

    // --- PEMETAAN STOK DINAMIS (PBI-019 & PBI-022) ---
    // Mencocokkan tipe order dari customer dengan item_name di tabel stocks
    switch ($order_type) {
        case 'Tukar':
            $item_target = 'Air Isi Ulang (Refill)';
            break;
        case 'Baru':
            $item_target = 'Galon Kosong (Kemasan)';
            break;
        case 'Mineral3L':
            $item_target = 'Air Mineral 3L';
            break;
        default:
            $item_target = 'Air Isi Ulang (Refill)'; // Ganti ke yang ada di DB
            break;
    }

    // 1. Ambil data stok berdasarkan item_target (PBI-020)
    $res = mysqli_query($conn, "SELECT * FROM stocks WHERE item_name = '$item_target'");
    $stock = mysqli_fetch_assoc($res);

    // Cek apakah item ditemukan di database
    if (!$stock) {
        echo "<script>alert('Error: Data stok untuk $item_target tidak ditemukan di database!'); window.location='../../views/admin/dashboardAdmin.php';</script>";
        exit();
    }

    $id_stock = $stock['id_stock'];
    $stok_sekarang = $stock['quantity'];

    // 2. Validasi Kuota Stok (PBI-020)
    if ($stok_sekarang >= $qty_order) {

        // A. Update Status Order menjadi 'Diproses' (PBI-018)
        mysqli_query($conn, "UPDATE orders SET status = 'Diproses' WHERE id_order = '$id_order'");

        // B. Potong Stok Otomatis (PBI-019)
        $new_qty = $stok_sekarang - $qty_order;
        mysqli_query($conn, "UPDATE stocks SET quantity = '$new_qty' WHERE id_stock = '$id_stock'");

        // C. Catat ke Histori Stok (PBI-017)
        $desc = "Keluar: Pesanan #$id_order ($order_type)";
        mysqli_query($conn, "INSERT INTO stock_history (id_stock, id_order, type, amount, description) 
                             VALUES ('$id_stock', '$id_order', 'Out', '$qty_order', '$desc')");

        header("Location: ../../views/admin/dashboardAdmin.php?msg=verified");
    } else {
        // Jika stok tidak cukup
        echo "<script>alert('Gagal! Stok $item_target tidak cukup (Sisa: $stok_sekarang)'); window.location='../../views/admin/dashboardAdmin.php';</script>";
    }
} else {
    header("Location: ../../views/admin/dashboardAdmin.php");
}
