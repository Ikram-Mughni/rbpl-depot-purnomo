<?php
session_start();
include '../../config/dbConfig.php';

// Proteksi halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Pelanggan') {
    header("Location: ../../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];

// Ambil Nama Lengkap untuk Header (Sesuai Wireframe 3)
$user_query = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE id_user = '$id_user'");
$user_info = mysqli_fetch_assoc($user_query);
$nama_tampil = $user_info['nama_lengkap'] ?? $username;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - Depot Purnomo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0077b6;
            --dark: #03045e;
            --light-bg: #f8fafc;
            --glass: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #e2e8f0;
            color: var(--dark);
        }

        /* Container agar terlihat seperti App Mobile di Desktop */
        .app-shell {
            max-width: 480px;
            margin: 0 auto;
            background: var(--light-bg);
            min-height: 100vh;
            position: relative;
            padding-bottom: 90px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }

        /* Header Profil - Wireframe 3 */
        .header-app {
            background: var(--dark);
            color: white;
            padding: 40px 20px 30px;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            display: flex;
            align-items: center;
        }

        .avatar-box {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            border: 3px solid var(--primary);
        }

        /* Navigasi Bawah - Wireframe 3, 4, 7 */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            max-width: 480px;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
            z-index: 1000;
        }

        .nav-link {
            text-align: center;
            color: #94a3b8;
            text-decoration: none;
            font-size: 11px;
            flex: 1;
            cursor: pointer;
            transition: 0.3s;
        }

        .nav-link i {
            font-size: 22px;
            margin-bottom: 4px;
            display: block;
        }

        .nav-link.active {
            color: var(--primary);
        }

        /* Content Wrapper */
        .main-content {
            padding: 20px;
        }

        .section {
            display: none;
            animation: slideUp 0.3s ease-out;
        }

        .section.active {
            display: block;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Notifikasi Box */
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
        }

        /* UI Cards */
        .card {
            background: white;
            padding: 20px;
            border-radius: 18px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        h3 {
            font-size: 16px;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Elements */
        label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        select,
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            font-family: inherit;
            box-sizing: border-box;
        }

        .btn-order {
            background: var(--primary);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 119, 182, 0.2);
        }

        /* Status & History Card - Wireframe 7 & 8 */
        .item-status {
            padding: 15px;
            background: #fff;
            border-radius: 15px;
            margin-bottom: 12px;
            border: 1px solid #edf2f7;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-cancel {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-pay {
            color: var(--primary);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--primary);
            padding: 4px 10px;
            border-radius: 6px;
        }
    </style>
</head>

<body>

    <div class="app-shell">

        <header class="header-app">
            <div class="avatar-box"><?= substr($nama_tampil, 0, 1) ?></div>
            <div class="welcome">
                <p style="margin:0; font-size: 13px; opacity: 0.8;">Selamat Datang,</p>
                <h2 style="margin:0; font-size: 18px; font-weight: 800;"><?= $nama_tampil ?></h2>
            </div>
        </header>

        <div class="main-content">

            <?php if (isset($_GET['order']) && $_GET['order'] == 'success'): ?>
                <div class="alert" style="background: #dcfce7; color: #15803d;">✅ Pesanan berhasil dibuat! Silakan bayar.</div>
            <?php endif; ?>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'canceled'): ?>
                <div class="alert" style="background: #fee2e2; color: #b91c1c;">ℹ Pesanan telah dibatalkan.</div>
            <?php endif; ?>
            <?php if (isset($_GET['payment']) && $_GET['payment'] == 'success'): ?>
                <div class="alert" style="background: #ffedd5; color: #9a3412;">✅ Pembayaran diterima! Menunggu verifikasi admin.</div>
            <?php endif; ?>

            <section id="tab-home" class="section active">
                <div class="card">
                    <h3><i class="fa-solid fa-house-chimney" style="color: var(--primary);"></i> Ringkasan Akun</h3>
                    <p style="font-size: 13px; color: #64748b; line-height: 1.6;">Halo <b><?= $username ?></b>, stok air di rumah menipis? Klik menu <b>Pesan</b> di bawah untuk order sekarang!</p>
                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 15px 0;">
                    <a href="../../logout.php" style="color: #ef4444; font-size: 13px; text-decoration: none; font-weight: 600;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar Aplikasi
                    </a>
                </div>
            </section>

            <section id="tab-pesan" class="section">
                <h3><i class="fa-solid fa-cart-plus" style="color: var(--primary);"></i> Form Pemesanan</h3>
                <div class="card">
                    <form action="../../logic/orders/placeOrderProcess.php" method="POST">
                        <label>Apa yang ingin anda pesan?</label>
                        <select name="order_type" required>
                            <option value="Tukar">Tukar Galon (Isi Ulang - Rp 5.000)</option>
                            <option value="Baru">Beli Galon Baru (Kemasan - Rp 50.000)</option>
                            <option value="Mineral3L">Air Mineral 3L (Botol - Rp 15.000)</option>
                        </select>

                        <label>Jumlah Pesanan</label>
                        <input type="number" name="quantity" min="1" value="1" required>

                        <button type="submit" name="submit_order" class="btn-order">Buat Pesanan Sekarang</button>
                    </form>
                </div>
            </section>

            <section id="tab-status" class="section">
                <h3><i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Riwayat & Status</h3>

                <?php
                $res = mysqli_query($conn, "SELECT * FROM orders WHERE id_user = '$id_user' ORDER BY order_date DESC");
                if (mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)):
                        // Logika Warna Status (Tetap sesuai kode awalmu)
                        $st = $row['status'];
                        $color = "black";
                        $bg = "#f1f5f9";
                        if ($st == 'Pending') {
                            $color = "orange";
                            $bg = "#fff7ed";
                        }
                        if ($st == 'Dibayar') {
                            $color = "blue";
                            $bg = "#eff6ff";
                        }
                        if ($st == 'Diproses') {
                            $color = "purple";
                            $bg = "#faf5ff";
                        }
                        if ($st == 'Dikirim') {
                            $color = "darkgoldenrod";
                            $bg = "#fefce8";
                        }
                        if ($st == 'Dibatalkan') {
                            $color = "red";
                            $bg = "#fef2f2";
                        }
                        if ($st == 'Selesai') {
                            $color = "green";
                            $bg = "#f0fdf4";
                        }
                ?>
                        <div class="item-status">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <small style="color: #94a3b8; font-size: 10px;">ID #<?= $row['id_order'] ?> • <?= date('d/m/y', strtotime($row['order_date'])) ?></small>
                                    <div style="font-weight: 700; margin-top: 4px;">
                                        <?php
                                        // Logika untuk mengubah penamaan dari database ke nama barang yang rapi
                                        if ($row['order_type'] == 'Tukar') {
                                            echo "Isi Ulang Galon";
                                        } elseif ($row['order_type'] == 'Baru') {
                                            echo "Galon Baru";
                                        } elseif ($row['order_type'] == 'Mineral3L') {
                                            echo "Air Mineral 3L";
                                        } else {
                                            echo $row['order_type']; // Cadangan jika ada nama lain
                                        }
                                        ?>
                                    </div>
                                </div>
                                <span class="badge" style="color: <?= $color ?>; background: <?= $bg ?>;"><?= $st ?></span>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                <span style="font-weight: 800; color: var(--dark);">Rp <?= number_format($row['total_price'], 0, ',', '.') ?></span>

                                <div class="actions">
                                    <?php if ($st == 'Pending'): ?>
                                        <form action="../../logic/orders/cancelOrder.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_order" value="<?= $row['id_order'] ?>">
                                            <button type="submit" name="cancel_order" class="btn-cancel" onclick="return confirm('Yakin batal?')">Batal</button>
                                        </form>
                                        <a href="paymentPage.php?id_order=<?= $row['id_order'] ?>" class="btn-pay">Bayar</a>
                                    <?php elseif ($st == 'Dikirim'): ?>
                                        <small style="color: darkgoldenrod;"><i class="fa-solid fa-truck-fast"></i> OTW Lokasi</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($st == 'Selesai' && !empty($row['delivery_proof'])): ?>
                                <div style="margin-top: 12px; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                                    <a href="../../uploads/<?= $row['delivery_proof'] ?>" target="_blank" style="text-decoration:none; font-size:11px; color: var(--primary); font-weight:600;">
                                        <i class="fa-solid fa-camera"></i> LIHAT BUKTI FOTO SAMPAI
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                <?php endwhile;
                } else {
                    echo "<p style='text-align:center; font-size:12px; color:#94a3b8;'>Belum ada pesanan.</p>";
                } ?>
            </section>

        </div>

        <nav class="bottom-nav">
            <a class="nav-link active" onclick="switchTab('tab-home', this)">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
            <a class="nav-link" onclick="switchTab('tab-pesan', this)">
                <i class="fa-solid fa-basket-shopping"></i>
                <span>Pesan</span>
            </a>
            <a class="nav-link" onclick="switchTab('tab-status', this)">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Status</span>
            </a>
            <a href="../../logout.php" class="nav-link" style="color: #ef4444;">
                <i class="fa-solid fa-power-off"></i>
                <span>Keluar</span>
            </a>
        </nav>

    </div>

    <script>
        function switchTab(tabId, el) {
            // Sembunyikan semua section
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            // Hilangkan class active di nav
            document.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));

            // Tampilkan yang dipilih
            document.getElementById(tabId).classList.add('active');
            el.classList.add('active');

            // Simpan posisi scroll ke atas saat pindah tab
            window.scrollTo(0, 0);
        }

        // Auto switch ke tab status jika ada notifikasi pesanan
        <?php if (isset($_GET['order']) || isset($_GET['payment']) || isset($_GET['msg'])): ?>
            switchTab('tab-status', document.querySelectorAll('.nav-link')[2]);
        <?php endif; ?>
    </script>

</body>

</html>