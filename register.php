<!DOCTYPE html>
<html>
<head>
    <title>Registrasi - Depot Purnomo</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 300px; width: 100%; border: 2px solid #333; margin-top: 10px; border-radius: 8px; }
        .error-msg { color: red; font-weight: bold; display: none; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Daftar Akun Baru</h2>

    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'username_taken') {
            echo "<p style='color: red;'>Gagal: Username sudah terdaftar!</p>";
        } else if ($_GET['error'] == 'out_of_range') {
            echo "<p style='color: red;'>Gagal: Lokasi Anda di luar jangkauan pengiriman!</p>";
        }
    }
    ?>

    <form action="/rbpl-depot-purnomo/logic/auth/registerProcess.php" method="POST" id="regForm">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Nomor Telepon/WA:</label><br>
        <input type="text" name="phone_number" placeholder="0812xxxx" required><br><br>

        <label>Pilih Lokasi Rumah (Maks 5KM dari Depot):</label>
        <div id="map"></div>
        <p id="distanceMsg" class="error-msg">⚠️ Lokasi di luar jangkauan! Kami hanya melayani radius 5KM.</p>
        
        <input type="hidden" name="latitude" id="lat">
        <input type="hidden" name="longitude" id="lng">

        <br>
        <label>Alamat Lengkap (Detail):</label><br>
        <textarea name="address" id="address" rows="3" placeholder="Pilih lokasi di peta terlebih dahulu..." required readonly></textarea><br><br>

        <button type="submit" name="register" id="btnSubmit">Daftar Sekarang</button>
    </form>

    <p>Sudah punya akun? <a href="index.php">Login di sini</a></p>

    <script>
        // Set Titik Depot Purnomo (Contoh: Dekat Kampus UPN Veteran)
        const depotLat = -7.7744; 
        const depotLng = 110.4135;
        const maxRadius = 5000; // 5 KM dalam meter

        const map = L.map('map').setView([depotLat, depotLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Marker Depot
        L.marker([depotLat, depotLng]).addTo(map).bindPopup("<b>Depot Purnomo</b>").openPopup();
        
        // Visualisasi Radius Jangkauan
        L.circle([depotLat, depotLng], { radius: maxRadius, color: 'green', fillOpacity: 0.1 }).addTo(map);

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
                btn.style.opacity = "0.5";
                msg.style.display = "block";
                addr.readOnly = true;
                addr.placeholder = "Lokasi tidak terjangkau...";
            } else {
                btn.disabled = false;
                btn.style.opacity = "1";
                msg.style.display = "none";
                addr.readOnly = false;
                addr.placeholder = "Masukkan detail jalan/no rumah...";
            }
        });
    </script>
</body>
</html>