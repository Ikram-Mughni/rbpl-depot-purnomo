<?php
session_start();
include '../../config/dbConfig.php';

if (isset($_POST['submit_order'])) {
    $id_user = $_SESSION['id_user'];
    $order_type = $_POST['order_type'];
    $quantity = (int)$_POST['quantity']; // Pastikan jadi integer untuk keamanan

    // 1. Kalkulasi Harga Berdasarkan Tipe (PBI-014)
    if ($order_type == 'Tukar') {
        $price = 5000;
    } elseif ($order_type == 'Baru') {
        $price = 50000;
    } elseif ($order_type == 'Mineral3L') {
        $price = 15000;
    } else {
        $price = 0;
    }

    // Hitung total (Gunakan variabel yang konsisten)
    $total_price = $price * $quantity;

    // 2. Simpan ke Tabel Orders (Gunakan $total_price yang benar)
    $queryOrder = "INSERT INTO orders (id_user, order_type, quantity, total_price, status) 
               VALUES ('$id_user', '$order_type', '$quantity', '$total_price', 'Pending')";

    if (mysqli_query($conn, $queryOrder)) {
        $id_order = mysqli_insert_id($conn); // Ambil ID order yang barusan masuk

        // 3. Simpan ke Tabel Payments sebagai inisialisasi (PBI-010)
        // Pastikan tabel payments kamu punya id_order
        mysqli_query($conn, "INSERT INTO payments (id_order, payment_status) VALUES ('$id_order', 'Pending')");

        // 4. Alihkan ke halaman scan QRIS
        header("Location: ../../views/customer/paymentPage.php?id_order=$id_order");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
