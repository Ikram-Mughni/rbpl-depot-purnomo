<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    header("Location: ../../index.php");
    exit();
}

$id_order = $_GET['id_order'];
$query = "SELECT * FROM orders WHERE id_order = '$id_order' AND id_user = '" . $_SESSION['id_user'] . "'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Pesanan tidak ditemukan";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS - Depot Purnomo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0077b6;
            --dark: #03045e;
            --light-bg: #f8fafc;
            --success: #10b981;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #e2e8f0;
            color: var(--dark);
        }

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

        /* Header senada dengan dashboard */
        .header-app {
            background: var(--dark);
            color: white;
            padding: 25px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-back {
            color: white;
            text-decoration: none;
            font-size: 20px;
        }

        .main-content {
            padding: 20px;
            flex: 1;
        }

        /* Card Informasi Harga */
        .price-card {
            background: white;
            padding: 20px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            margin-bottom: 20px;
        }

        .total-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 5px;
        }

        .total-amount {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }

        /* Area QRIS */
        .qris-container {
            background: white;
            padding: 30px 20px;
            border-radius: 20px;
            text-align: center;
            border: 2px dashed #cbd5e1;
            position: relative;
        }

        .qris-logo {
            width: 120px;
            margin-bottom: 20px;
        }

        .qr-placeholder {
            width: 220px;
            height: 220px;
            background: #f1f5f9;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .qr-placeholder i {
            font-size: 150px;
            color: #cbd5e1;
        }

        .instruction {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }

        /* Button Group */
        .footer-actions {
            padding: 20px;
            background: white;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-confirm {
            background: var(--success);
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            transition: 0.3s;
            font-family: inherit;
        }

        .btn-confirm:active {
            transform: scale(0.98);
        }

        .btn-later {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #ef4444;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="app-shell">
        <header class="header-app">
            <a href="dashboardCustomer.php" class="btn-back"><i class="fa-solid fa-chevron-left"></i></a>
            <h2 style="margin:0; font-size: 18px; font-weight: 800;">Pembayaran</h2>
        </header>

        <div class="main-content">
            <div class="price-card">
                <div class="total-label">Total yang harus dibayar</div>
                <h1 class="total-amount">Rp <?= number_format($data['total_price'], 0, ',', '.'); ?></h1>
                <small style="color: #94a3b8;">Order ID: #<?= $id_order ?></small>
            </div>

            <div class="qris-container">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Logo_QRIS.svg/1200px-Logo_QRIS.svg.png" alt="Logo QRIS" class="qris-logo">
                
                <div class="qr-placeholder">
                    <i class="fa-solid fa-qrcode"></i>
                </div>

                <p class="instruction">
                    Scan kode QR di atas menggunakan aplikasi m-Banking atau E-Wallet (Gopay, OVO, Dana, ShopeePay) favorit Anda.
                </p>
            </div>
        </div>

        <div class="footer-actions">
            <form action="../../logic/orders/confirmPayment.php" method="POST">
                <input type="hidden" name="id_order" value="<?= $id_order ?>">
                <button type="submit" name="confirm" class="btn-confirm">
                    <i class="fa-solid fa-circle-check"></i> SAYA SUDAH BAYAR
                </button>
            </form>
            
            <a href="dashboardCustomer.php" class="btn-later">Bayar Nanti (Kembali ke Beranda)</a>
        </div>
    </div>

</body>

</html>