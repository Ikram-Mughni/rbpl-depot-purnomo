<?php
session_start();
// Memanggil koneksi agar $conn dikenali (PBI-031)
include '../../config/dbConfig.php';

// Proteksi halaman: Hanya role 'Admin' yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Monitoring Transaksi - Depot Purnomo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #007bff;
            color: white;
            padding: 12px;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .summary-row {
            background: #eee;
            font-weight: bold;
            font-size: 1.1em;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
            padding: 5px;
            border-radius: 3px;
            font-size: 0.8em;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>📊 Monitoring Transaksi Real-Time</h1>
        <div>
            <strong>Admin: <?php echo $_SESSION['username']; ?></strong> |
            <a href="dashboardAdmin.php">Kembali ke Dashboard</a> |
            <a href="../../logout.php" style="color: red;">Logout</a>
        </div>
    </div>

    <hr>

    <p>Data di bawah ini mencatat setiap transaksi yang berhasil diselesaikan oleh kurir secara otomatis (Audit Log).</p>

    <table>
        <thead>
            <tr>
                <th>Waktu Transaksi</th>
                <th>Nama Pelanggan</th>
                <th>Tipe Pesanan</th>
                <th>Status Audit</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // PBI-030: Ambil data dari tabel log transaksi
            $query = "SELECT * FROM transaction_logs ORDER BY created_at DESC";
            $logs = mysqli_query($conn, $query);

            $total_omzet = 0;

            // Cek jika ada data
            if (mysqli_num_rows($logs) > 0) {
                while ($l = mysqli_fetch_assoc($logs)):
                    $total_omzet += $l['amount'];

                    // Format tampilan tipe barang agar lebih rapi
                    $display_type = ($l['order_type'] == 'Mineral3L') ? "Air Mineral 3L" : $l['order_type'];
            ?>
                    <tr>
                        <td><?php echo date('d M Y, H:i', strtotime($l['created_at'])); ?></td>
                        <td><?php echo $l['customer_name']; ?></td>
                        <td><?php echo $display_type; ?></td>
                        <td><span class="badge-success">RECORDED</span></td>
                        <td>Rp <?php echo number_format($l['amount'], 0, ',', '.'); ?></td>
                    </tr>
            <?php
                endwhile;
            } else {
                echo "<tr><td colspan='5'>Belum ada transaksi tercatat hari ini.</td></tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="summary-row">
                <td colspan="4" style="text-align: right;">TOTAL OMZET KESELURUHAN</td>
                <td style="color: #28a745;">Rp <?php echo number_format($total_omzet, 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 0.9em; color: gray;">
        <em>* Halaman ini diperbarui secara otomatis setiap kali data transaksi baru masuk ke log audit.</em>
    </div>

</body>

</html>