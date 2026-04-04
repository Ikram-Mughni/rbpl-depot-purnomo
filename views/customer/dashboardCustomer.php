<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Pelanggan - Depot Purnomo</title>
</head>

<body>
    <h1>Halo Pelanggan, <?php echo $_SESSION['username']; ?>!</h1>
    <p>Mau pesan galon hari ini? Silakan pilih menu pemesanan.</p>
    <a href="../../logout.php">Logout</a>
    <hr>

    <h3>Form Pemesanan Galon</h3>

    <?php if (isset($_GET['order']) && $_GET['order'] == 'success') echo "<p style='color:green;'><b>Sukses:</b> Pesanan berhasil dibuat! Silakan lakukan pembayaran.</p>"; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'canceled') echo "<p style='color:blue;'><b>Info:</b> Pesanan telah dibatalkan.</p>"; ?>
    <?php if (isset($_GET['payment']) && $_GET['payment'] == 'success') echo "<p style='color:orange;'><b>Sukses:</b> Pembayaran diterima! Pesanan Anda sedang menunggu verifikasi Admin.</p>"; ?>

    <form action="../../logic/orders/placeOrderProcess.php" method="POST">
        <label>Tipe Pesanan:</label><br>
        <select name="order_type" required>
            <option value="Tukar">Tukar Galon (Isi Ulang - Rp 5.000)</option>
            <option value="Baru">Beli Galon Baru (Kemasan - Rp 50.000)</option>
            <option value="Mineral3L">Air Mineral 3L (Botol - Rp 15.000)</option>
        </select><br><br>

        <label>Jumlah Pesanan:</label><br>
        <input type="number" name="quantity" min="1" value="1" required><br><br>

        <button type="submit" name="submit_order">Pesan Sekarang</button>
    </form>

    <hr>

    <h3>Riwayat & Status Pesanan Anda</h3>
    <table border="1" cellpadding="10" style="width: 100%; text-align: center; border-collapse: collapse;">
        <tr style="background-color: #f2f2f2;">
            <th>ID Order</th>
            <th>Tipe</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi / Keterangan</th>
        </tr>
        <?php
        $id_user = $_SESSION['id_user'];

        // Query mengambil data pesanan terbaru
        $res = mysqli_query($conn, "SELECT * FROM orders WHERE id_user = '$id_user' ORDER BY order_date DESC");

        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)):
                // 1. Logika Warna Status (Update Sprint 4)
                $status = $row['status'];
                $color = "black";
                if ($status == 'Pending') $color = "orange";
                if ($status == 'Dibayar') $color = "blue";
                if ($status == 'Diproses') $color = "purple";
                if ($status == 'Dikirim') $color = "darkgoldenrod"; // Warna baru untuk kurir jalan
                if ($status == 'Dibatalkan') $color = "red";
                if ($status == 'Selesai') $color = "green";

                // 2. Logika Nama Tampilan Tipe
                $display_type = $row['order_type'];
                if ($display_type == 'Mineral3L') $display_type = "Air Mineral 3L";
        ?>
                <tr>
                    <td>#<?php echo $row['id_order']; ?></td>
                    <td><?php echo $display_type; ?></td>
                    <td>Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?></td>
                    <td>
                        <strong style="color: <?php echo $color; ?>;"><?php echo $status; ?></strong>
                    </td>
                    <td>
                        <?php if ($status == 'Pending'): ?>
                            <form action="../../logic/orders/cancelOrder.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id_order" value="<?php echo $row['id_order']; ?>">
                                <button type="submit" name="cancel_order" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">Batalkan</button>
                            </form>
                            |
                            <a href="paymentPage.php?id_order=<?php echo $row['id_order']; ?>">Bayar</a>

                        <?php elseif ($status == 'Dibayar'): ?>
                            <small>Menunggu Verifikasi Admin</small>

                        <?php elseif ($status == 'Diproses'): ?>
                            <small style="color: purple;">Pesanan sedang disiapkan</small>

                        <?php elseif ($status == 'Dikirim'): ?>
                            <small style="color: darkgoldenrod;">🚚 Kurir sedang menuju lokasi Anda</small>

                        <?php elseif ($status == 'Selesai'): ?>
                            <span style="color: green;">✔ Sampai Tujuan</span>
                            <?php if (!empty($row['delivery_proof'])): ?>
                                <br>
                                <a href="../../uploads/<?php echo $row['delivery_proof']; ?>" target="_blank" style="font-size: 0.85em; color: blue;">
                                    [Lihat Bukti Foto Sampai]
                                </a>
                            <?php endif; ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
        <?php
            endwhile;
        } else {
            echo "<tr><td colspan='5'>Belum ada riwayat pesanan.</td></tr>";
        }
        ?>
    </table>
</body>

</html>