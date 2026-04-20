<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Depot Purnomo</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --main-bg: linear-gradient(135deg, #caf0f8 0%, #ade8f4 100%);
            --accent-blue: #0077b6;
            --deep-blue: #03045e;
            --glass-white: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--main-bg);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            color: var(--deep-blue);
        }

        .mobile-container {
            width: 100%;
            max-width: 450px;
            background: var(--glass-white);
            min-height: 100vh;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .content {
            padding: 40px 25px;
        }

        /* Header Sesuai Wireframe */
        .header-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .header-section h1 {
            font-weight: 800;
            font-size: 36px;
            margin: 0;
            background: linear-gradient(to bottom, var(--accent-blue), var(--deep-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-section p {
            font-size: 15px;
            color: #555;
            margin-top: 8px;
            line-height: 1.4;
            font-weight: 500;
        }

        /* Alert Box */
        .alert {
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecdd3;
        }

        /* Form Layout Modern Underline Style */
        form { display: flex; flex-direction: column; }

        label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--accent-blue);
        }

        input, textarea {
            background: transparent;
            border: none;
            border-bottom: 2px solid #ccc;
            padding: 10px 5px;
            margin-bottom: 25px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus {
            border-bottom-color: var(--accent-blue);
        }

        /* Map Container */
        #map { 
            height: 220px; 
            width: 100%; 
            border-radius: 15px; 
            margin-bottom: 10px;
            border: 1px solid #ddd;
            z-index: 1;
        }

        .error-msg { 
            background: #fff1f2;
            color: #e11d48;
            padding: 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            display: none; 
            margin-bottom: 15px;
            text-align: center;
        }

        /* Button Modern Pill Style */
        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        button[name="register"] {
            background: linear-gradient(135deg, var(--accent-blue) 0%, var(--deep-blue) 100%);
            color: white;
            border: none;
            padding: 16px 80px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0, 119, 182, 0.3);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            width: 85%;
        }

        button[name="register"]:hover:not(:disabled) {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(0, 119, 182, 0.4);
            filter: brightness(1.1);
        }

        button[name="register"]:disabled {
            background: #cbd5e1;
            box-shadow: none;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            padding-bottom: 40px;
        }

        .login-link a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="mobile-container">
    <div class="content">
        <div class="header-section">
            <h1>Galon</h1>
            <p>Hauss, Yuk Pesen!<br>Daftar Duluuu Yaa</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert">
                <?php
                if ($_GET['error'] == 'username_taken') echo "Username sudah terpakai!";
                else if ($_GET['error'] == 'out_of_range') echo "Lokasi di luar jangkauan 5KM!";
                ?>
            </div>
        <?php endif; ?>

        <form action="/rbpl-depot-purnomo/logic/auth/registerProcess.php" method="POST" id="regForm">
            
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" placeholder="Masukkan nama anda..." required>

            <label>No Telepon</label>
            <input type="text" name="phone_number" placeholder="Masukkan nomor telp anda..." required>

            <label>Pilih Lokasi Alamat</label>
            <div id="map"></div>
            <p id="distanceMsg" class="error-msg">⚠️ Maaf, lokasi Anda di luar radius 5KM Depot.</p>
            <textarea name="address" id="address" rows="3" placeholder="Klik titik lokasi pada peta..." required readonly></textarea>
            
            <input type="hidden" name="latitude" id="lat">
            <input type="hidden" name="longitude" id="lng">

            <label>Username</label>
            <input type="text" name="username" placeholder="Buat username..." required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Buat password..." required>

            <div class="btn-container">
                <button type="submit" name="register" id="btnSubmit">Daftar</button>
            </div>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="index.php">Login</a>
        </div>
    </div>
</div>

<script>
    // Koordinat Pusat Depot
    const depotLat = -7.7744; 
    const depotLng = 110.4135;
    const maxRadius = 5000; 

    const map = L.map('map', { zoomControl: false }).setView([depotLat, depotLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Marker Depot Purnomo
    L.marker([depotLat, depotLng]).addTo(map).bindPopup("<b>Depot Purnomo</b>");
    
    // Lingkaran Jangkauan Biru
    L.circle([depotLat, depotLng], { 
        radius: maxRadius, 
        color: '#0077b6', 
        fillColor: '#0077b6', 
        fillOpacity: 0.1 
    }).addTo(map);

    let userMarker;

    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        const distance = map.distance([depotLat, depotLng], e.latlng);

        if (userMarker) map.removeLayer(userMarker);
        userMarker = L.marker([lat, lng]).addTo(map);

        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;

        const btn = document.getElementById('btnSubmit');
        const msg = document.getElementById('distanceMsg');
        const addr = document.getElementById('address');

        if (distance > maxRadius) {
            btn.disabled = true;
            msg.style.display = "block";
            addr.readOnly = true;
            addr.placeholder = "Lokasi tidak terjangkau...";
        } else {
            btn.disabled = false;
            msg.style.display = "none";
            addr.readOnly = false;
            addr.placeholder = "Masukkan alamat detail (No. Rumah / Nama Gang)...";
        }
    });
</script>

</body>
</html>