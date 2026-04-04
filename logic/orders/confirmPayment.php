<?php
session_start();
include '../../config/dbConfig.php';

if (isset($_POST['confirm'])) {
    $id_order = $_POST['id_order'];
    $id_user = $_SESSION['id_user'];

    // 1. Update status di tabel orders menjadi 'Dibayar' (PBI-016)
    $updateOrder = "UPDATE orders SET status = 'Dibayar' 
                    WHERE id_order = '$id_order' AND id_user = '$id_user'";

    if (mysqli_query($conn, $updateOrder)) {

        // 2. Update status di tabel payments menjadi 'Success' (PBI-010)
        $updatePayment = "UPDATE payments SET payment_status = 'Success' 
                          WHERE id_order = '$id_order'";
        mysqli_query($conn, $updatePayment);

        // Kembali ke dashboard dengan pesan sukses
        header("Location: ../../views/customer/dashboardCustomer.php?payment=success");
        exit();
    } else {
        echo "Gagal mengonfirmasi pembayaran: " . mysqli_error($conn);
    }
} else {
    header("Location: ../../views/customer/dashboardCustomer.php");
    exit();
}
