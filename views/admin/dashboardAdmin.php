<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi Halaman Admin
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
    <title>Dashboard Admin - Depot Purnomo</title>
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
            --warning: #f59e0b;
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

        /* Stats Card (Tambahan Visual Desktop) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        /* Table Style */
        .data-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .data-card h3 {
            margin-top: 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        /* Form & Buttons */
        .input-qty {
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 60px;
            outline: none;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-family: inherit;
            font-size: 13px;
            transition: 0.2s;
        }

        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-primary { background: var(--primary); color: white; }
        
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-info { background: #e0f2fe; color: #0369a1; border-left: 4px solid #0369a1; }
        .alert-success { background: #dcfce7; color: #15803d; border-left: 4px solid #15803d; }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-faucet-drip"></i> DEPOT PURNOMO
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboardAdmin.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard Utama</a></li>
            <li><a href="stockHistory.php"><i class="fa-solid fa-history"></i> Histori Stok</a></li>
            <li><a href="reportSales.php"><i class="fa-solid fa-file-invoice-dollar"></i> Laporan Penjualan</a></li>
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
                <h2 style="margin:0; font-size: 20px; font-weight: 800;">Panel Kendali Admin</h2>
            </div>
            <div class="user-info">
                <span>Selamat bekerja, <strong><?= $username ?></strong> 👋</span>
            </div>
        </header>

        <div class="content-padding">

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--primary);"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <div style="font-size: 12px; color: #64748b;">Inventaris</div>
                        <div style="font-weight: 800; font-size: 18px;">Stok Barang</div>
                    </div>
                </div>
                </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'stock_updated'): ?>
                <div class="alert alert-info"><b>Sukses!</b> Stok berhasil diperbarui secara manual.</div>
            <?php endif; ?>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'verified'): ?>
                <div class="alert alert-success"><b>Verifikasi Berhasil!</b> Pesanan diproses dan stok dipotong otomatis.</div>
            <?php endif; ?>

            <div class="data-card">
                <h3><i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i> Inventaris Stok Barang</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th style="text-align:center;">Stok Saat Ini</th>
                            <th>Update Terakhir</th>
                            <th>Aksi Manual (Offline/Restok)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stocks = mysqli_query($conn, "SELECT * FROM stocks");
                        while ($s = mysqli_fetch_assoc($stocks)):
                        ?>
                            <tr>
                                <td><strong style="color: var(--dark);"><?= $s['item_name']; ?></strong></td>
                                <td align="center"><span style="font-size: 1.1em; font-weight: 800; color: var(--primary);"><?= $s['quantity']; ?></span></td>
                                <td><small style="color: #94a3b8;"><?= $s['last_update']; ?></small></td>
                                <td>
                                    <form action="../../logic/admin/updateStockManual.php" method="POST" style="display:flex; gap:10px;">
                                        <input type="hidden" name="id_stock" value="<?= $s['id_stock']; ?>">
                                        <input type="hidden" name="item_name" value="<?= $s['item_name']; ?>">
                                        <input type="number" name="amount" min="1" class="input-qty" placeholder="Qty" required>
                                        
                                        <button type="submit" name="action" value="add" class="btn btn-success"><i class="fa-solid fa-plus"></i> Restok</button>
                                        <button type="submit" name="action" value="subtract" class="btn btn-danger"><i class="fa-solid fa-minus"></i> Jual Offline</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="data-card">
                <h3><i class="fa-solid fa-bell" style="color: var(--warning);"></i> Pesanan Masuk (Butuh Verifikasi)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID Order</th>
                            <th>Pelanggan</th>
                            <th>Tipe Pesanan</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = mysqli_query($conn, "SELECT orders.*, users.username FROM orders 
                                                       JOIN users ON orders.id_user = users.id_user 
                                                       WHERE status = 'Dibayar' ORDER BY order_date ASC");

                        if (mysqli_num_rows($orders) > 0):
                            while ($o = mysqli_fetch_assoc($orders)):
                                $display_type = $o['order_type'];
                                // Penamaan barang agar rapi di dashboard
                                if ($display_type == 'Mineral3L') $display_type = "Air Mineral 3L";
                                if ($display_type == 'Tukar') $display_type = "Isi Ulang Galon";
                                if ($display_type == 'Baru') $display_type = "Galon Baru + Isi";
                        ?>
                                <tr>
                                    <td><span style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-weight: 600;">#<?= $o['id_order']; ?></span></td>
                                    <td><b><?= $o['username']; ?></b></td>
                                    <td><?= $display_type; ?></td>
                                    <td><?= $o['quantity']; ?> Unit</td>
                                    <td>
                                        <form action="../../logic/admin/verifyOrderProcess.php" method="POST">
                                            <input type="hidden" name="id_order" value="<?= $o['id_order']; ?>">
                                            <input type="hidden" name="qty_order" value="<?= $o['quantity']; ?>">
                                            <input type="hidden" name="order_type" value="<?= $o['order_type']; ?>">
                                            <button type="submit" name="verify_btn" class="btn btn-primary">
                                                <i class="fa-solid fa-check-double"></i> Verifikasi & Proses
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="5" align="center" style="padding: 40px; color: #94a3b8;">
                                    <i class="fa-solid fa-clipboard-check" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                                    Tidak ada pesanan menunggu verifikasi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>