<?php
include '../../config/dbConfig.php';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $lat = mysqli_real_escape_string($conn, $_POST['latitude']);
    $lng = mysqli_real_escape_string($conn, $_POST['longitude']);

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $id_role = 2; // Role Pelanggan 

    // 1. Cek Koordinat (Harus dipilih di peta)
    if (empty($lat) || empty($lng)) {
        header("Location: ../../register.php?error=no_location");
        exit();
    }

    // 2. Cek Duplikasi Username
    $checkUser = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        header("Location: ../../register.php?error=username_taken");
        exit();
    }

    // 3. Update Query: Sertakan Alamat, Latitude, dan Longitude
    $query = "INSERT INTO users (username, password, phone_number, address, id_role, latitude, longitude) 
              VALUES ('$username', '$password', '$phone', '$address', '$id_role', '$lat', '$lng')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../index.php?status=registerSuccess");
    } else {
        echo "Registrasi Gagal: " . mysqli_error($conn);
    }
}