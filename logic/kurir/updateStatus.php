<?php
session_start();
include '../../config/dbConfig.php';

// Pastikan hanya Kurir yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Kurir') {
    header("Location: ../../index.php");
    exit();
}

$id_kurir = $_SESSION['id_user'];

// 1. LOGIKA UNTUK MENGAMBIL PESANAN (ACTION DARI LINK GET)
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'kirim') {
    $id_order = $_GET['id'];

    // Status jadi 'Dikirim' dan catat siapa kurirnya
    $query = "UPDATE orders SET status = 'Dikirim', id_kurir = '$id_kurir' WHERE id_order = '$id_order'";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../views/courier/dashboardCourier.php?msg=otw");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// 2. LOGIKA UNTUK KONFIRMASI SELESAI DENGAN FOTO (ACTION DARI FORM POST)
if (isset($_POST['action']) && $_POST['action'] == 'konfirmasi_foto') {
    $id_order = $_POST['id_order'];

    // Konfigurasi Upload Foto
    $namaFile = $_FILES['foto_bukti']['name'];
    $tmpName  = $_FILES['foto_bukti']['tmp_name'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    // Validasi Ekstensi (Hanya gambar)
    $ext_boleh = ['jpg', 'jpeg', 'png'];

    if (in_array($ekstensi, $ext_boleh)) {
        // Buat nama file unik: bukti_IDORDER_WAKTU.jpg
        $namaBaru = "bukti_" . $id_order . "_" . time() . "." . $ekstensi;
        $tujuan   = "../../uploads/" . $namaBaru;

        if (move_uploaded_file($tmpName, $tujuan)) {
            // Jika upload sukses, update status dan simpan nama file fotonya
            $query = "UPDATE orders SET status = 'Selesai', delivery_proof = '$namaBaru' WHERE id_order = '$id_order'";

            if (mysqli_query($conn, $query)) {
                header("Location: ../../views/courier/dashboardCourier.php?msg=delivered");
            } else {
                echo "Gagal Update Database: " . mysqli_error($conn);
            }
        } else {
            echo "Gagal mengunggah file. Pastikan folder 'uploads' tersedia di root proyek.";
        }
    } else {
        echo "Format file tidak didukung! Gunakan JPG, JPEG, atau PNG.";
    }
}
