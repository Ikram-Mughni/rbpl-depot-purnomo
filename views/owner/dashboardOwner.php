<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi Khusus Owner
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Owner') {
    header("Location: ../../index.php");
    exit();
}

$username = $_SESSION['username'];

// Filter Tanggal (PBI-034)
$tgl_mulai = isset($_POST['tgl_mulai']) ? $_POST['tgl_mulai'] : date('Y-m-d', strtotime('-30 days'));
$tgl_selesai = isset($_POST['tgl_selesai']) ? $_POST['tgl_selesai'] : date('Y-m-d');

// Query Data Laporan dari Log Transaksi
$query = "SELECT DATE(created_at) as tanggal, SUM(amount) as total_harian 
          FROM transaction_logs 
          WHERE DATE(created_at) BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
          GROUP BY DATE(created_at)
          ORDER BY created_at ASC";
$result = mysqli_query($conn, $query);

$label_grafik = [];
$data_grafik = [];
$total_omzet = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $label_grafik[] = date('d M', strtotime($row['tanggal']));
    $data_grafik[] = $row['total_harian'];
    $total_omzet += $row['total_harian'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Dashboard - Owner Depot Purnomo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --success: #10b981;
            --bg-light: #f8fafc;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--bg-light);
            display: flex;
        }

        /* Sidebar Style - Menggunakan Flexbox */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary);
            height: 100vh;
            position: fixed;
            color: white;
            display: flex;
            flex-direction: column; /* Membuat isi sidebar tersusun vertikal */
            z-index: 100;
        }

        .sidebar-brand {
            padding: 25px;
            font-size: 18px;
            font-weight: 800;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
            flex: 1; /* Mengisi ruang kosong agar mendorong footer ke bawah */
        }

        .sidebar-menu li a {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar-menu li a.active, .sidebar-menu li a:hover {
            background: rgba(255,255,255,0.05);
            color: white;
            border-left: 4px solid var(--accent);
        }

        /* Sidebar Footer (Logout) */
        .sidebar-footer {
            padding: 20px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer a {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #f87171;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 600;
        }

        .sidebar-footer a:hover {
            background: rgba(248, 113, 113, 0.1);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 30px 40px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.02);
            border: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-card h3 { margin: 0; color: #64748b; font-size: 14px; font-weight: 400; }
        .stat-card p { margin: 10px 0 0; font-size: 28px; font-weight: 800; color: var(--primary); }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* Filter Section */
        .filter-box {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid #e2e8f0;
        }

        input[type="date"] {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-family: inherit;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-primary { background: var(--accent); color: white; }
        .btn-dark { background: var(--primary); color: white; }

        /* Chart Area */
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
        }

        @media print {
            .sidebar, .filter-box, header { display: none; }
            .main-content { margin: 0; width: 100%; }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-droplet"></i> OWNER PANEL
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="#" class="active"><i class="fa-solid fa-chart-pie"></i> Laporan Bisnis</a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="../../logout.php" onclick="return confirm('Yakin ingin keluar?')">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div>
                <h1 style="margin:0; font-size: 24px;">Selamat Datang, Owner.</h1>
                <p style="color: #64748b; margin: 5px 0 0;">Pantau performa bisnis Depot Purnomo Anda.</p>
            </div>
            <div style="text-align: right;">
                <strong><?= $username ?></strong><br>
                <small style="color: var(--success);">Online Status</small>
            </div>
        </header>

        <div class="filter-box">
            <form method="POST" style="display: flex; align-items: center; gap: 15px; flex: 1;">
                <span style="font-weight: 600; font-size: 14px;">Periode:</span>
                <input type="date" name="tgl_mulai" value="<?= $tgl_mulai ?>">
                <span style="color: #cbd5e1;">s/d</span>
                <input type="date" name="tgl_selesai" value="<?= $tgl_selesai ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-sync"></i> Update Data
                </button>
            </form>
            <button onclick="window.print()" class="btn btn-dark">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </button>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <h3>Total Omzet Periode Ini</h3>
                    <p>Rp <?= number_format($total_omzet, 0, ',', '.') ?></p>
                </div>
                <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>

            <div class="stat-card">
                <div>
                    <h3>Estimasi Profit Bersih (20%)</h3>
                    <p style="color: var(--success);">Rp <?= number_format($total_omzet * 0.2, 0, ',', '.') ?></p>
                </div>
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h3 style="margin:0;">Tren Pendapatan Harian</h3>
                <small style="color: #94a3b8;">Data berdasarkan log transaksi terverifikasi</small>
            </div>
            <canvas id="salesChart" height="110"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($label_grafik) ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($data_grafik) ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

</body>
</html>