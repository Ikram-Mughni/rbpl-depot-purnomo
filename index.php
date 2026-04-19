<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Depot Purnomo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-bg: linear-gradient(135deg, #caf0f8 0%, #ade8f4 100%);
            --accent-blue: #0077b6;
            --deep-blue: #03045e;
            --glass-white: rgba(255, 255, 255, 0.9);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--main-bg);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--deep-blue);
        }

        .mobile-container {
            width: 100%;
            max-width: 400px;
            background: var(--glass-white);
            min-height: 90vh;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin: 20px;
        }

        .content {
            padding: 50px 30px;
            display: flex;
            flex-direction: column;
        }

        /* Header Style Modern */
        .header-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-section h1 {
            font-weight: 800;
            font-size: 42px;
            margin: 0;
            background: linear-gradient(to bottom, var(--accent-blue), var(--deep-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }

        .header-section p {
            font-size: 14px;
            color: #558b9f;
            margin-top: 10px;
            font-weight: 400;
        }

        /* Alert Box */
        .alert {
            padding: 12px;
            border-radius: 15px;
            margin-bottom: 25px;
            font-size: 13px;
            text-align: center;
            animation: slideIn 0.5s ease;
        }
        @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .error { background: #fee2e2; color: #b91c1c; }
        .success { background: #dcfce7; color: #15803d; }

        /* Form Styling */
        form { display: flex; flex-direction: column; }

        label {
            font-size: 13px;
            font-weight: 600;
            margin-left: 5px;
            margin-bottom: 8px;
            color: var(--accent-blue);
        }

        input {
            background: #f1faff;
            border: 2px solid transparent;
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: var(--accent-blue);
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 119, 182, 0.1);
        }

        .register-link {
            text-align: right;
            margin-bottom: 35px;
            font-size: 12px;
        }

        .register-link a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
        }

        /* Button Modern */
        .btn-container { text-align: center; }

        button[name="login"] {
            background: var(--accent-blue);
            color: white;
            border: none;
            padding: 15px 80px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0, 119, 182, 0.3);
            transition: all 0.3s ease;
        }

        button[name="login"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(0, 119, 182, 0.4);
            background: var(--deep-blue);
        }

        button[name="login"]:active {
            transform: translateY(0);
        }

    </style>
</head>
<body>

<div class="mobile-container">
    <div class="content">
        <div class="header-section">
            <h1>Galon</h1>
            <p>Lanjutkan Yukk, Habis<br>Ini Kalian Galgah</p>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <div class="alert <?= strpos($_GET['status'], 'Success') !== false ? 'success' : 'error' ?>">
                <?php 
                    if ($_GET['status'] == 'user_not_found') echo "Username tidak ditemukan!";
                    if ($_GET['status'] == 'wrong_password') echo "Password Anda keliru!";
                    if ($_GET['status'] == 'registerSuccess') echo "Akun siap! Silakan masuk.";
                ?>
            </div>
        <?php endif; ?>

        <form action="logic/auth/loginProcess.php" method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Ketik username..." required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Ketik password..." required>

            <div class="register-link">
                Belum punya akun? <a href="register.php">Daftar Sekarang</a>
            </div>

            <div class="btn-container">
                <button type="submit" name="login">Masuk</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>