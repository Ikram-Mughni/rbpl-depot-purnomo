<?php
session_start();
include '../../config/dbConfig.php';

if (isset($_POST['action'])) {
    $id_stock = $_POST['id_stock'];
    $item_name = $_POST['item_name'];
    $amount = (int)$_POST['amount'];
    $action = $_POST['action'];

    if ($action == 'add') {
        // Logika TAMBAH (Restok)
        $query = "UPDATE stocks SET quantity = quantity + $amount WHERE id_stock = '$id_stock'";
        $type = 'In';
        $desc = "Restok Manual / Barang Masuk";
    } else {
        // Logika KURANG (Jual Offline / Rusak)
        // Cek dulu stoknya cukup tidak untuk dikurangi
        $check = mysqli_query($conn, "SELECT quantity FROM stocks WHERE id_stock = '$id_stock'");
        $data = mysqli_fetch_assoc($check);

        if ($data['quantity'] < $amount) {
            echo "<script>alert('Gagal! Stok tidak cukup untuk dikurangi manual.'); window.location='../../views/admin/dashboardAdmin.php';</script>";
            exit();
        }

        $query = "UPDATE stocks SET quantity = quantity - $amount WHERE id_stock = '$id_stock'";
        $type = 'Out';
        $desc = "Pengurangan Manual (Jual Offline/Rusak)";
    }

    // Eksekusi Update Stok
    if (mysqli_query($conn, $query)) {
        // CATAT KE HISTORI (PBI-017) agar sinkron
        mysqli_query($conn, "INSERT INTO stock_history (id_stock, type, amount, description) 
                             VALUES ('$id_stock', '$type', '$amount', '$desc')");

        header("Location: ../../views/admin/dashboardAdmin.php?msg=stock_updated");
    }
}
