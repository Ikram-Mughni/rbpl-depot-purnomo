<?php
session_start(); // Memulai session agar data login tersimpan
include '../../config/dbConfig.php';

if (isset($_POST['login'])) {
    // Mengamankan input dari SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Ambil data user dan nama role-nya menggunakan JOIN
    $query = "SELECT users.*, roles.role_name FROM users 
              JOIN roles ON users.id_role = roles.id_role 
              WHERE username = '$username'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password hash
        if (password_verify($password, $user['password'])) {
            // Simpan data ke session
            $_SESSION['id_user']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role_name'];

            // Arahkan ke dashboard sesuai role (Jangan dihilangkan)
            if ($user['role_name'] == 'Admin') {
                header("Location: ../../views/admin/dashboardAdmin.php");
            } else if ($user['role_name'] == 'Pelanggan') {
                header("Location: ../../views/customer/dashboardCustomer.php");
            } else if ($user['role_name'] == 'Kurir') {
                header("Location: ../../views/courier/dashboardCourier.php");
            } else if ($user['role_name'] == 'Owner') {
                header("Location: ../../views/owner/dashboardOwner.php");
            }
            exit(); // Hentikan skrip setelah redirect sukses
        } else {
            // ERROR: Password salah (status dikirim ke index.php)
            header("Location: ../../index.php?status=wrong_password");
            exit();
        }
    } else {
        // ERROR: Username tidak ditemukan (status dikirim ke index.php)
        header("Location: ../../index.php?status=user_not_found");
        exit();
    }
} else {
    // Jika mencoba akses file ini secara langsung tanpa POST login
    header("Location: ../../index.php");
    exit();
}
