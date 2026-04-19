<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi Khusus Owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Owner') {
    header("Location: ../../index.php");
    exit();
}

// Filter Tanggal (PBI-034)
$tgl_mulai = isset($_POST['tgl_mulai']) ? $_POST['tgl_mulai'] : date('Y-m-d', strtotime('-30 days'));
$tgl_selesai = isset($_POST['tgl_selesai']) ? $_POST['tgl_selesai'] : date('Y-m-d');

// Query Data Laporan dari Log Transaksi
$query = "SELECT * FROM transaction_logs 
          WHERE DATE(created_at) BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
          ORDER BY created_at ASC";
$result = mysqli_query($conn, $query);

$label_grafik = [];
$data_grafik = [];
$total_omzet = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $label_grafik[] = date('d M', strtotime($row['created_at']));
    $data_grafik[] = $row['amount'];
    $total_omzet += $row['amount'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Laporan Performa Bisnis - Owner Depot Purnomo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 30px;
            background: #f4f7f6;
        }

        .card-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            flex: 1;
            padding: 25px;
            border-radius: 12px;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .bg-blue {
            background: #007bff;
        }

        .bg-green {
            background: #28a745;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>📈 Ringkasan Performa Bisnis (Owner)</h1>
        <div class="no-print">
            <a href="dashboardOwner.php" style="text-decoration: none; color: #666;">⬅ Kembali</a> |
            <a href="../../logout.php" style="color: red;">Keluar</a>
        </div>
    </div>

    <div class="filter-section no-print">
        <form method="POST">
            <strong>Filter Periode:</strong>
            <input type="date" name="tgl_mulai" value="<?= $tgl_mulai ?>">
            s/d
            <input type="date" name="tgl_selesai" value="<?= $tgl_selesai ?>">
            <button type="submit" style="padding: 5px 15px; cursor: pointer;">Tampilkan</button>
            <button type="button" onclick="window.print()" style="padding: 5px 15px; cursor: pointer; background: #333; color: white;">🖨 Cetak Laporan (PBI-036)</button>
        </form>
    </div>

    <div class="card-container">
        <div class="card bg-blue">
            <small>Total Omzet</small>
            <h2>Rp <?= number_format($total_omzet, 0, ',', '.') ?></h2>
        </div>
        <div class="card bg-green">
            <small>Estimasi Profit Bersih (20%)</small>
            <h2>Rp <?= number_format($total_omzet * 0.2, 0, ',', '.') ?></h2>
        </div>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #ddd;">
        <h3>Tren Penjualan</h3>
        <canvas id="salesChart" height="100"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($label_grafik) ?>,
                datasets: [{
                    label: 'Pendapatan Harian (Rp)',
                    data: <?= json_encode($data_grafik) ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3
                }]
            }
        });
    </script>

</body>

</html>