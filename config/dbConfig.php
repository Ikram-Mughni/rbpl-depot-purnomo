<?php
// Konfigurasi koneksi ke database MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db   = "depot_purnomo";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
