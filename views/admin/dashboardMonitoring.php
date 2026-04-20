<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Transaksi - Depot Purnomo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0077b6;
            --dark: #03045e;
            --light-bg: #f1f5f9;
            --sidebar-width: 260px;
            --success: #10b981;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--light-bg);
            color: var(--dark);
            display: flex;
        }

        /* Sidebar Style */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--dark);
            height: 100vh;
            position: fixed;
            color: white;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 0 25px 30px;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
        }

        .sidebar-menu li a {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background: var(--primary);
            color: white;
        }

        /* Main Content */
        .main-container {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
        }

        header {
            background: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .content-padding {
            padding: 30px 40px;
        }

        /* Stats Card (Omzet Highlight) */
        .omzet-card {
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 20px rgba(3, 4, 94, 0.2);
        }

        /* Data Card & Table */
        .data-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th {
            text-align: left;
            background: #f8fafc;
            padding: 15px;
            color: #64748b;
            font-weight: 600;
            border-bottom: 2px solid #edf2f7;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #edf2f7;
        }

        .badge-audit {
            background: #dcfce7;
            color: var(--success);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .btn-back {
            text-decoration: none;
            color: var(--primary);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-faucet-drip"></i> DEPOT PURNOMO
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboardAdmin.php"><i class="fa-solid fa-gauge"></i> Dashboard Utama</a></li>
            <li><a href="stockHistory.php"><i class="fa-solid fa-history"></i> Histori Stok</a></li>
            <li><a href="reportSales.php"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Penjualan</a></li>
            <li><a href="dashboardMonitoring.php" class="active"><i class="fa-solid fa-desktop"></i> Monitoring Transaksi</a></li>
        </ul>
        <div style="padding: 20px;">
            <a href="../../logout.php" style="color: #ff9999; text-decoration: none; font-size: 14px; font-weight: 600;">
                <i class="fa-solid fa-door-open"></i> Keluar Panel
            </a>
        </div>
    </div>

    <div class="main-container">
        <header>
            <div class="page-title">
                <h2 style="margin:0; font-size: 20px; font-weight: 800;">Monitoring Real-Time</h2>
            </div>
            <div class="user-info">
                <span>Admin: <strong><?= $username ?></strong></span>
            </div>
        </header>

        <div class="content-padding">
            <a href="dashboardAdmin.php" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>

            <?php
            // Ambil data dari tabel log transaksi
            $query = "SELECT * FROM transaction_logs ORDER BY created_at DESC";
            $logs = mysqli_query($conn, $query);
            $total_omzet = 0;
            
            // Hitung total dulu untuk ditampilkan di atas
            $temp_logs = mysqli_query($conn, $query);
            while($t = mysqli_fetch_assoc($temp_logs)) { $total_omzet += $t['amount']; }
            ?>

            <div class="omzet-card">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; font-weight: 300;">TOTAL OMZET KESELURUHAN</div>
                    <div style="font-size: 32px; font-weight: 800; margin-top: 5px;">Rp <?= number_format($total_omzet, 0, ',', '.'); ?></div>
                </div>
                <div style="font-size: 40px; opacity: 0.3;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>

            <div class="data-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin:0;"><i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Log Audit Transaksi</h3>
                    <small style="color: #94a3b8;"><i class="fa-solid fa-circle-dot" style="color: var(--success); font-size: 8px;"></i> Live Updates</small>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Waktu Transaksi</th>
                            <th>Nama Pelanggan</th>
                            <th>Tipe Pesanan</th>
                            <th style="text-align:center;">Status Audit</th>
                            <th style="text-align:right;">Pemasukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($logs) > 0) {
                            while ($l = mysqli_fetch_assoc($logs)):
                                $display_type = $l['order_type'];
                                if ($display_type == 'Mineral3L') $display_type = "Air Mineral 3L";
                                if ($display_type == 'Tukar') $display_type = "Isi Ulang Galon";
                                if ($display_type == 'Baru') $display_type = "Galon Baru + Isi";
                        ?>
                                <tr>
                                    <td style="color: #64748b;"><?= date('d M Y, H:i', strtotime($l['created_at'])); ?></td>
                                    <td><strong><?= $l['customer_name']; ?></strong></td>
                                    <td><?= $display_type; ?></td>
                                    <td align="center"><span class="badge-audit">RECORDED</span></td>
                                    <td align="right"><b style="color: var(--dark);">Rp <?= number_format($l['amount'], 0, ',', '.'); ?></b></td>
                                </tr>
                        <?php
                            endwhile;
                        } else {
                            echo "<tr><td colspan='5' align='center' style='padding: 50px; color: #94a3b8;'>Belum ada transaksi tercatat hari ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #e2e8f0; font-size: 12px; color: #94a3b8; font-style: italic;">
                    * Data di atas mencatat setiap transaksi yang berhasil diselesaikan oleh kurir secara otomatis.
                </div>
            </div>
        </div>
    </div>

</body>
</html>