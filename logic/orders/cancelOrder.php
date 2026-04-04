<?php
session_start();
include '../../config/dbConfig.php';

if (isset($_POST['cancel_order'])) {
    $id_order = $_POST['id_order'];
    $id_user = $_SESSION['id_user'];

    // Cek dulu apakah statusnya masih Pending
    $check = mysqli_query($conn, "SELECT status FROM orders WHERE id_order = '$id_order' AND id_user = '$id_user'");
    $order = mysqli_fetch_assoc($check);

    if ($order['status'] == 'Pending') {
        $update = "UPDATE orders SET status = 'Dibatalkan' WHERE id_order = '$id_order'";
        mysqli_query($conn, $update);
        header("Location: ../../views/customer/dashboardCustomer.php?msg=canceled");
    } else {
        header("Location: ../../views/customer/dashboardCustomer.php?msg=cannot_cancel");
    }
}
