<?php
session_start();
if ($_SESSION['role'] !== 'Owner') {
    header("Location: ../../index.php");
    exit();
}
?>
<h1>Laporan Bisnis Owner, <?php echo $_SESSION['username']; ?></h1>
<p>Pantau grafik penjualan dan performa Depot Purnomo di sini.</p>
<a href="../../logout.php">Logout</a>