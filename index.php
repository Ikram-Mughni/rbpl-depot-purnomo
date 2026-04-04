<!DOCTYPE html>
<html>

<head>
    <title>Login - Depot Purnomo</title>
</head>

<body>
    <h2>Login Sistem Depot Purnomo</h2>

    <?php
    // CEK APAKAH ADA STATUS DI URL
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'user_not_found') {
            echo "<p style='color: red;'>Username tidak terdaftar!</p>";
        } else if ($_GET['status'] == 'wrong_password') {
            echo "<p style='color: red;'>Password salah, silakan coba lagi!</p>";
        } else if ($_GET['status'] == 'registerSuccess') {
            echo "<p style='color: green;'>Registrasi Berhasil! Silakan Login.</p>";
        }
    }
    ?>

    <form action="logic/auth/loginProcess.php" method="POST">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar Pelanggan</a></p>
</body>

</html>