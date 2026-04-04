<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi Halaman Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Admin - Depot Purnomo</title>
</head>

<body>
    <h1>Panel Kendali Admin</h1>
    <p>Selamat bekerja, <strong><?php echo $_SESSION['username']; ?></strong> | <a href="../../logout.php">Logout</a></p>
    <hr>

    <nav style="background: #333; padding: 10px; margin-bottom: 20px;">
        <a href="dashboardAdmin.php" style="color: white; text-decoration: none; margin-right: 20px;">🏠 Dashboard Utama</a>
        <a href="stockHistory.php" style="color: white; text-decoration: none; margin-right: 20px;">📜 Histori Stok (PBI-017)</a>
        <a href="reportSales.php" style="color: white; text-decoration: none;">📊 Laporan Penjualan</a>
    </nav>

    <h3>📦 Inventaris Stok Barang</h3>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'stock_updated') echo "<p style='color:blue;'><b>Sukses:</b> Stok berhasil diperbarui secara manual.</p>"; ?>

    <table border="1" cellpadding="8" style="width: 80%; border-collapse: collapse;">
        <tr style="background: #eee;">
            <th>Nama Barang</th>
            <th>Stok Saat Ini</th>
            <th>Update Terakhir</th>
            <th>Aksi Manual (Offline/Restok)</th>
        </tr>
        <?php
        $stocks = mysqli_query($conn, "SELECT * FROM stocks");
        while ($s = mysqli_fetch_assoc($stocks)):
        ?>
            <tr>
                <td><strong><?php echo $s['item_name']; ?></strong></td>
                <td align="center"><span style="font-size: 1.2em;"><?php echo $s['quantity']; ?></span></td>
                <td><small><?php echo $s['last_update']; ?></small></td>
                <td>
                    <form action="../../logic/admin/updateStockManual.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id_stock" value="<?php echo $s['id_stock']; ?>">
                        <input type="hidden" name="item_name" value="<?php echo $s['item_name']; ?>">

                        <input type="number" name="amount" min="1" style="width: 60px;" placeholder="Qty" required>

                        <button type="submit" name="action" value="add" style="background: #4CAF50; color: white; cursor: pointer;">+ Restok</button>
                        <button type="submit" name="action" value="subtract" style="background: #f44336; color: white; cursor: pointer;">- Jual Offline</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <hr>

    <h3>🔔 Pesanan Masuk (Butuh Verifikasi)</h3>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'verified') echo "<p style='color:green;'><b>Sukses:</b> Pesanan berhasil diverifikasi dan stok dipotong otomatis.</p>"; ?>

    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
        <tr style="background: #f9f9f9;">
            <th>ID Order</th>
            <th>Pelanggan</th>
            <th>Tipe Pesanan</th>
            <th>Qty</th>
            <th>Aksi</th>
        </tr>
        <?php
        // Mengambil pesanan yang statusnya 'Dibayar' (sudah upload bukti/bayar)
        $orders = mysqli_query($conn, "SELECT orders.*, users.username FROM orders 
                                       JOIN users ON orders.id_user = users.id_user 
                                       WHERE status = 'Dibayar' ORDER BY order_date ASC");

        if (mysqli_num_rows($orders) > 0):
            while ($o = mysqli_fetch_assoc($orders)):
                // Merapikan tampilan tipe pesanan
                $display_type = $o['order_type'];
                if ($display_type == 'Mineral3L') $display_type = "Air Mineral 3L";
        ?>
                <tr>
                    <td>#<?php echo $o['id_order']; ?></td>
                    <td><?php echo $o['username']; ?></td>
                    <td><strong><?php echo $display_type; ?></strong></td>
                    <td><?php echo $o['quantity']; ?> Unit</td>
                    <td>
                        <form action="../../logic/admin/verifyOrderProcess.php" method="POST">
                            <input type="hidden" name="id_order" value="<?php echo $o['id_order']; ?>">
                            <input type="hidden" name="qty_order" value="<?php echo $o['quantity']; ?>">
                            <input type="hidden" name="order_type" value="<?php echo $o['order_type']; ?>">
                            <button type="submit" name="verify_btn" style="background: green; color: white; padding: 5px 10px; cursor: pointer;">Verifikasi & Proses</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile;
        else: ?>
            <tr>
                <td colspan="5" align="center">Tidak ada pesanan menunggu verifikasi.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>

</html>+