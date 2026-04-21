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
    <title>Histori Stok - Depot Purnomo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0077b6;
            --dark: #03045e;
            --light-bg: #f1f5f9;
            --sidebar-width: 260px;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--light-bg);
            color: var(--dark);
            display: flex;
        }

        /* Sidebar Style (Sama dengan Dashboard) */
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

        /* Card & Table Style */
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

        /* Badges */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }

        .badge-in { background: #dcfce7; color: var(--success); }
        .badge-out { background: #fee2e2; color: var(--danger); }

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
            <li><a href="stockHistory.php" class="active"><i class="fa-solid fa-history"></i> Histori Stok</a></li>
            <li><a href="dashboardMonitoring.php"><i class="fa-solid fa-desktop"></i> Monitoring Transaksi</a></li>
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
                <h2 style="margin:0; font-size: 20px; font-weight: 800;">Log Histori Stok</h2>
            </div>
            <div class="user-info">
                <span>Admin: <strong><?= $username ?></strong></span>
            </div>
        </header>

        <div class="content-padding">
            <a href="dashboardAdmin.php" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>

            <div class="data-card">
                <h3 style="margin-top:0; margin-bottom: 25px;"><i class="fa-solid fa-list-ul" style="color: var(--primary);"></i> Riwayat Pergerakan Barang</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Nama Barang</th>
                            <th>Aksi</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT h.*, s.item_name 
                                  FROM stock_history h 
                                  JOIN stocks s ON h.id_stock = s.id_stock 
                                  ORDER BY h.created_at DESC";

                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)):
                                $is_in = ($row['type'] == 'In');
                                $badge_class = $is_in ? 'badge-in' : 'badge-out';
                                $type_label = $is_in ? 'MASUK (+)' : 'KELUAR (-)';
                                $icon = $is_in ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
                        ?>
                                <tr>
                                    <td style="color: #64748b;"><?= date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                                    <td><strong style="color: var(--dark);"><?= $row['item_name']; ?></strong></td>
                                    <td>
                                        <span class="badge <?= $badge_class ?>">
                                            <i class="fa-solid <?= $icon ?>"></i> <?= $type_label; ?>
                                        </span>
                                    </td>
                                    <td><b style="font-size: 15px;"><?= $row['amount']; ?> Unit</b></td>
                                    <td style="font-style: italic; color: #64748b;"><?= $row['description']; ?></td>
                                </tr>
                        <?php
                            endwhile;
                        } else {
                            echo "<tr><td colspan='5' align='center' style='padding: 50px; color: #94a3b8;'>Belum ada riwayat perubahan stok.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>