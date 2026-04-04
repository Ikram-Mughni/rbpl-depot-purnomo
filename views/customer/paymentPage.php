<?php
session_start();
include '../../config/dbConfig.php';

$id_order = $_GET['id_order'];
$query = "SELECT * FROM orders WHERE id_order = '$id_order' AND id_user = '" . $_SESSION['id_user'] . "'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Pesanan tidak ditemukan";
    exit();
}
?>

<h2>Halaman Pembayaran QRIS</h2>
<p>Total yang harus dibayar: <strong>Rp <?php echo number_format($data['total_price'], 0, ',', '.'); ?></strong></p>

<div style="border: 1px solid #000; display: inline-block; padding: 20px;">

    <p>Silakan Scan untuk Membayar</p>
</div>

<form action="../../logic/orders/confirmPayment.php" method="POST">
    <input type="hidden" name="id_order" value="<?php echo $id_order ?>">
    <button type="submit" name="confirm">Saya Sudah Bayar</button>
</form>
<br>
<a href="dashboardCustomer.php" style="color: red;">Bayar Nanti (Kembali ke Dashboard)</a>