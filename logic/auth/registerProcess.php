<?php
include '../../config/dbConfig.php';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    // --- TAMBAHAN: TANGKAP DATA ALAMAT ---
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $id_role = 2; // Role Pelanggan 

    // --- LOGIKA BARU: CEK DUPLIKASI USERNAME ---
    $checkUser = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");

    if (mysqli_num_rows($checkUser) > 0) {
        header("Location: ../../register.php?error=username_taken");
        exit();
    }

    // --- UPDATE QUERY: TAMBAHKAN KOLOM ADDRESS ---
    $query = "INSERT INTO users (username, password, phone_number, address, id_role) 
              VALUES ('$username', '$password', '$phone', '$address', '$id_role')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../index.php?status=registerSuccess");
    } else {
        echo "Registrasi Gagal: " . mysqli_error($conn);
    }
}
