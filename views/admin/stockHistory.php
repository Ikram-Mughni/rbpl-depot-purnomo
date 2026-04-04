<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Histori Stok - Depot Purnomo</title>
</head>

<body>
    <h1>📜 Log Histori Stok Barang</h1>
    <a href="dashboardAdmin.php">← Kembali ke Dashboard</a>
    <hr>

    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
        <tr style="background-color: #f2f2f2;">
            <th>Waktu</th>
            <th>Nama Barang</th>
            <th>Aksi</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>

        <?php
        // Query Join untuk mengambil nama barang dari tabel stocks
        $query = "SELECT h.*, s.item_name 
                  FROM stock_history h 
                  JOIN stocks s ON h.id_stock = s.id_stock 
                  ORDER BY h.created_at DESC";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)):
                $type_color = ($row['type'] == 'In') ? 'green' : 'red';
                $type_label = ($row['type'] == 'In') ? 'MASUK (+)' : 'KELUAR (-)';
        ?>
                <tr>
                    <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                    <td><strong><?php echo $row['item_name']; ?></strong></td>
                    <td style="color: <?php echo $type_color; ?>; font-weight: bold;">
                        <?php echo $type_label; ?>
                    </td>
                    <td><?php echo $row['amount']; ?> Unit</td>
                    <td><?php echo $row['description']; ?></td>
                </tr>
        <?php
            endwhile;
        } else {
            echo "<tr><td colspan='5' align='center'>Belum ada riwayat perubahan stok.</td></tr>";
        }
        ?>
    </table>
</body>

</html>