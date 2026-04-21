<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi Kurir
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Kurir') {
    header("Location: ../../index.php");
    exit();
}
$id_kurir = $_SESSION['id_user'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Panel Kurir - Depot Purnomo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0077b6;
            --dark: #03045e;
            --light-bg: #f8fafc;
            --success: #10b981;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #e2e8f0; /* Background luar shell */
            color: var(--dark);
        }

        /* App Shell - Container Utama Mobile */
        .app-shell {
            max-width: 480px;
            margin: 0 auto;
            background: var(--light-bg);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        /* Header Apps */
        .header-app {
            background: var(--dark);
            color: white;
            padding: 25px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-info h2 { margin: 0; font-size: 18px; font-weight: 800; }
        .header-info p { margin: 0; font-size: 12px; opacity: 0.7; }

        .btn-logout {
            color: #ff9999;
            text-decoration: none;
            font-size: 18px;
            background: rgba(255,255,255,0.1);
            padding: 8px;
            border-radius: 10px;
        }

        .main-content {
            padding: 20px;
            flex: 1;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Order Card */
        .card-order {
            background: white;
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .order-id { font-weight: 800; color: var(--primary); font-size: 14px; }
        
        .badge-status {
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 700;
        }

        .info-row {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 13px;
            line-height: 1.4;
        }

        .info-row i { color: var(--primary); width: 16px; margin-top: 3px; }

        /* Button Group */
        .btn-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-wa { background: #dcfce7; color: #155724; }
        .btn-maps { background: #e0f2fe; color: #0369a1; }
        
        .btn-take {
            background: var(--warning);
            color: white;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }

        .btn-confirm {
            background: var(--success);
            color: white;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        /* Upload Area */
        .upload-area {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            padding: 15px;
            border-radius: 12px;
            margin-top: 10px;
            text-align: center;
        }

        .upload-area label { font-size: 11px; font-weight: 600; color: #64748b; }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <div class="app-shell">
        <header class="header-app">
            <div class="header-info">
                <p>Selamat Bekerja,</p>
                <h2><?= $username ?></h2>
            </div>
            <a href="../../logout.php" class="btn-logout" onclick="return confirm('Logout sekarang?')">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </header>

        <div class="main-content">
            <div class="section-title">
                <i class="fa-solid fa-truck-fast"></i> DAFTAR PENGIRIMAN
            </div>

            <?php
            $query = "SELECT orders.*, users.username, users.address, users.phone_number, users.latitude, users.longitude 
                      FROM orders 
                      JOIN users ON orders.id_user = users.id_user 
                      WHERE orders.status = 'Diproses' OR (orders.status = 'Dikirim' AND orders.id_kurir = '$id_kurir')
                      ORDER BY orders.status DESC";

            $res = mysqli_query($conn, $query);

            if (mysqli_num_rows($res) > 0):
                while ($row = mysqli_fetch_assoc($res)):
                    $is_sending = ($row['status'] == 'Dikirim');
            ?>
                    <div class="card-order">
                        <div class="card-header">
                            <span class="order-id">#ORD-<?= $row['id_order'] ?></span>
                            <span class="badge-status" style="background: <?= $is_sending ? '#fef3c7' : '#dcfce7' ?>; color: <?= $is_sending ? '#92400e' : '#155724' ?>;">
                                <?= strtoupper($row['status']) ?>
                            </span>
                        </div>

                        <div class="info-row">
                            <i class="fa-solid fa-user"></i>
                            <span><strong><?= $row['username'] ?></strong></span>
                        </div>

                        <div class="info-row">
                            <i class="fa-solid fa-location-dot"></i>
                            <span><?= $row['address'] ?></span>
                        </div>

                        <div class="info-row">
                            <i class="fa-solid fa-box-open"></i>
                            <span><?= ($row['order_type'] == 'Mineral3L') ? "Air Mineral 3L" : $row['order_type'] ?> (x<?= $row['quantity'] ?>)</span>
                        </div>

                        <div class="btn-grid">
                            <a href="https://wa.me/<?= $row['phone_number'] ?>" class="btn-action btn-wa">
                                <i class="fa-brands fa-whatsapp"></i> WhatsApp
                            </a>
                            <?php if (!empty($row['latitude'])): ?>
                                <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-action btn-maps">
                                    <i class="fa-solid fa-map-location-dot"></i> Lokasi
                                </a>
                            <?php else: ?>
                                <button class="btn-action" style="background:#f1f5f9; color:#94a3b8;" disabled>No GPS</button>
                            <?php endif; ?>
                        </div>

                        <?php if ($row['status'] == 'Diproses'): ?>
                            <a href="../../logic/kurir/updateStatus.php?id=<?= $row['id_order'] ?>&action=kirim" class="btn-action btn-take">
                                <i class="fa-solid fa-truck-pickup"></i> AMBIL PESANAN
                            </a>
                        <?php elseif ($row['status'] == 'Dikirim'): ?>
                            <form action="../../logic/kurir/updateStatus.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id_order" value="<?= $row['id_order'] ?>">
                                <div class="upload-area">
                                    <label>FOTO BUKTI PENGIRIMAN</label><br>
                                    <input type="file" name="foto_bukti" accept="image/*" capture="camera" required style="margin-top:8px">
                                </div>
                                <button type="submit" name="action" value="konfirmasi_foto" class="btn-action btn-confirm">
                                    <i class="fa-solid fa-circle-check"></i> SELESAIKAN PESANAN
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
            <?php 
                endwhile;
            else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-box-open" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>Tidak ada pesanan untuk dikirim.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="padding: 20px; text-align: center; font-size: 10px; color: #cbd5e1;">
            Depot Purnomo Delivery System v1.0
        </div>
    </div>

</body>
</html>