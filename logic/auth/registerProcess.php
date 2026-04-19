<?php
include '../../config/dbConfig.php';

if (isset($_POST['register'])) {
    // --- TAMBAHAN: TANGKAP NAMA LENGKAP ---
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $lat = mysqli_real_escape_string($conn, $_POST['latitude']);
    $lng = mysqli_real_escape_string($conn, $_POST['longitude']);

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $id_role = 2; // Role Pelanggan 

    // 1. Cek Duplikasi Username
    $checkUser = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        header("Location: ../../register.php?error=username_taken");
        exit();
    }

    // 2. UPDATE QUERY: Tambahkan kolom nama_lengkap dan value-nya
    $query = "INSERT INTO users (nama_lengkap, username, password, phone_number, address, id_role, latitude, longitude) 
              VALUES ('$nama_lengkap', '$username', '$password', '$phone', '$address', '$id_role', '$lat', '$lng')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../index.php?status=registerSuccess");
    } else {
        echo "Registrasi Gagal: " . mysqli_error($conn);
    }
}