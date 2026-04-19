<?php
session_start();
include '../../config/dbConfig.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Kurir') {
    header("Location: ../../index.php");
    exit();
}
$id_kurir = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Kurir - Depot Purnomo</title>
</head>

<body>
    <h1>Panel Pengiriman (Kurir)</h1>
    <p>Halo, <strong><?php echo $_SESSION['username']; ?></strong> | <a href="../../logout.php">Logout</a></p>
    <hr>

    <h3>🚚 Daftar Pesanan Siap Kirim</h3>
    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
        <tr style="background: #eee;">
            <th>ID Order</th>
            <th>Pelanggan</th>
            <th>No. Telepon</th>
            <th>Alamat & Titik Lokasi</th> <th>Tipe Barang</th>
            <th>Qty</th>
            <th>Aksi</th>
        </tr>
        <?php
        // Update Query untuk mengambil latitude dan longitude
        $query = "SELECT orders.*, users.username, users.address, users.phone_number, users.latitude, users.longitude 
          FROM orders 
          JOIN users ON orders.id_user = users.id_user 
          WHERE orders.status = 'Diproses' OR (orders.status = 'Dikirim' AND orders.id_kurir = '$id_kurir')
          ORDER BY orders.status DESC";

        $res = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($res)):
        ?>
            <tr>
                <td>#<?php echo $row['id_order']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td>
                    <a href="https://wa.me/<?php echo $row['phone_number']; ?>" target="_blank" style="color: green; text-decoration: none;">
                        📞 <?php echo $row['phone_number']; ?>
                    </a>
                </td>
                <td>
                    <strong>Detail:</strong> <?php echo $row['address']; ?><br><br>
                    
                    <?php if (!empty($row['latitude']) && !empty($row['longitude'])): ?>
                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>" 
                           target="_blank" 
                           style="background: #4285F4; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.8em;">
                           📍 Lihat di Maps
                        </a>
                    <?php else: ?>
                        <span style="color: gray; font-size: 0.8em;">(Titik GPS tidak tersedia)</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $row['order_type']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>
                    <?php if ($row['status'] == 'Diproses'): ?>
                        <a href="../../logic/kurir/updateStatus.php?id=<?php echo $row['id_order']; ?>&action=kirim"
                            style="background: orange; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">Ambil & Kirim</a>

                    <?php elseif ($row['status'] == 'Dikirim'): ?>
                        <div style="border: 1px dashed gray; padding: 10px; background: #f9f9f9;">
                            <form action="../../logic/kurir/updateStatus.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id_order" value="<?php echo $row['id_order']; ?>">

                                <label style="font-size: 0.8em;">Upload Bukti Foto:</label><br>
                                <input type="file" name="foto_bukti" accept="image/*" required style="margin-bottom: 5px;"><br>

                                <button type="submit" name="action" value="konfirmasi_foto"
                                    style="background: green; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">
                                    Konfirmasi Sampai
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>