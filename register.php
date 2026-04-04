<!DOCTYPE html>
<html>

<head>
    <title>Registrasi - Depot Purnomo</title>
</head>

<body>
    <h2>Daftar Akun Baru</h2>

    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'username_taken') {
            echo "<p style='color: red;'>Gagal: Username sudah terdaftar!</p>";
        }
    }
    ?>

    <form action="/rbpl-depot-purnomo/logic/auth/registerProcess.php" method="POST">

        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Alamat Lengkap:</label><br>
        <textarea name="address" rows="3" placeholder="Jl. Contoh No. 123" required></textarea><br><br>

        <label>Nomor Telepon/WA:</label><br>
        <input type="text" name="phone_number" placeholder="0812xxxx" required><br><br>

        <button type="submit" name="register">Daftar Sekarang</button>
    </form>

    <p>Sudah punya akun? <a href="index.php">Login di sini</a></p>
</body>

</html>