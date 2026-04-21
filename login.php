<?php
session_start();

// --- 1. KONEKSI DATABASE ---
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'trash2power_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

$success_msg = "";
$error_msg = "";

// --- 2. LOGIKA LOGIN ---
if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Password yang diinput user (plain text)

    if (!empty($email) && !empty($password)) {
        // Cari user berdasarkan email saja
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();

            // --- PERUBAHAN DI SINI: Verifikasi password menggunakan password_verify() ---
            if (password_verify($password, $user_data['password'])) {
                // Set Session
                $_SESSION['id_user'] = $user_data['id_user'];
                $_SESSION['nama']    = $user_data['nama'];
                $_SESSION['role']    = $user_data['role'];
                $_SESSION['saldo']   = $user_data['saldo'];

                // Redirect berdasarkan role (admin/warga)
                if ($user_data['role'] == 'admin') {
                     header("Location: admin_dashboard.php"); // Ganti dengan halaman admin yang sebenarnya
                } else {
                     header("Location: beranda.php");
                }
                exit();
            } else {
                $error_msg = "Kata sandi salah!";
            }
        } else {
            $error_msg = "Email tidak terdaftar!";
        }
    } else {
        $error_msg = "Harap isi semua kolom!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Trash2Power</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f9f9f9;
        }

        /* Sisi Kiri - Branding */
        .branding-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px;
            background-color: #ffffff;
            text-align: center;
        }

        .branding-section img {
            width: 150px;
            margin-bottom: 20px;
        }

        .branding-section h2 {
            color: #52b788;
            font-size: 30px;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .branding-section h1 {
            color: #1b4332;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .branding-section p {
            color: #52b788;
            font-size: 16px;
            max-width: 400px;
            line-height: 1.6;
        }

        /* Sisi Kanan - Form */
        .form-section {
            flex: 1;
            background-color: #2d6a4f;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .login-card {
            background-color: #e9f5f2;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .login-card h2 {
            color: #1b4332;
            font-size: 28px;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1b4332;
            font-weight: 600;
            font-size: 14px;
        }

        .input-box {
            display: flex;
            align-items: center;
            background-color: #d8ede6;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid transparent;
        }

        .input-box img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            filter: invert(18%) sepia(19%) saturate(1915%) hue-rotate(102deg) brightness(91%) contrast(91%);
        }

        /* --- eye toggle visibility --- */
        .eye-icon {
            cursor: pointer;
            width: 22px !important;
            height: auto !important;
            margin-left: 10px;
            margin-right: 0 !important;
            filter: none !important;
        }

        .input-box:focus-within {
            border: 1px solid #52b788;
        }

        .input-box input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            font-size: 14px;
            color: #333;
        }

        .forgot-pass {
            display: block;
            font-size: 12px;
            color: #2d6a4f;
            text-decoration: none;
            margin-top: 5px;
            font-weight: 600;
        }

        .btn-submit {
            width: 100%;
            background-color: #2d6a4f;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background-color: #1b4332;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #444;
        }

        .footer-text a {
            color: #1b4332;
            text-decoration: none;
            font-weight: 700;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }

        /* --- KHUSUS TAMPILAN HP --- */
        @media (max-width: 820px) {
            body {
                flex-direction: column;
                background-color: #f9f9f9;
                padding: 15px;
                justify-content: flex-start;
                align-items: center;
            }

            /* --- Area Branding (Logo & Teks) --- */
            .branding-section {
                background-color: transparent;
                padding: 30px 10px 20px 10px;
                flex: none;
                width: 100%;
                text-align: center;
            }

            .branding-section img {
                width: 100px;
                margin-bottom: 15px;
            }

            .branding-section h2 {
                font-size: 22px;
                margin-bottom: 5px;
            }

            .branding-section h1 {
                font-size: 28px;
                margin-bottom: 12px;
            }

            .branding-section p {
                font-size: 12px;
                line-height: 1.5;
                max-width: 300px;
                margin: 0 auto;
            }

            /* --- Area Form --- */
            .form-section {
                background-color: transparent;
                padding: 0;
                flex: none;
                width: 100%;
                display: flex;
                justify-content: center;
            }

            .login-card,
            .register-card {
                background-color: #ffffff;
                border-radius: 40px;
                margin-left: 15px;
                margin-right: 15px;
                padding: 30px 30px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.10);
                width: 100%;
                max-width: 450px;
                border: 1px solid #f0f0f0;
            }

            .login-card h2,
            .register-card h2 {
                display: block !important;
                text-align: left;
                font-size: 20px;
                margin-bottom: 25px;
                color: #1b4332;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .form-group label {
                margin-left: 10px;
                font-size: 13px;
            }

            .input-box {
                border-radius: 12px;
                padding: 12px 15px;
            }

            .btn-submit {
                background-color: #2d6a4f;
                border-radius: 18px;
                font-size: 18px;
                padding: 16px;
                font-weight: 700;
                margin-top: 15px;
                box-shadow: 0 4px 15px rgba(42, 145, 52, 0.3);
            }

            .footer-text {
                margin-top: 25px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <div class="branding-section">
        <img src="logosmirk.png" alt="Logo">
        <h2>Trash2Power</h2>
        <h1>Selamat Datang Kembali!</h1>
        <p>Jangan lupa untuk mengumpulkan sampah botol plastik dan kaleng alumunium hari ini untuk jadi saldo ya!</p>
    </div>

    <div class="form-section">
        <div class="login-card">
            <h2>Masuk Sekarang Yuk!</h2>

            <?php if ($error_msg != ""): ?>
                <div class="alert-error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-box">
                        <img src="email.png" alt="Email Icon">
                        <input type="email" name="email" placeholder="Contoh123@gmail.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Kata Sandi</label>
                    <div class="input-box">
                        <img src="password.png" alt="Password Icon">
                        <input type="password" name="password" id="password" placeholder="Isi kata sandi kamu" required>
                        <img src="close-eye.png" id="eye-icon" class="eye-icon" onclick="toggleVisibility()">
                    </div>
                    <a href="lupa-password.php" class="forgot-pass">Lupa Kata Sandi?</a>
                </div>

                <button type="submit" name="login" class="btn-submit">Masuk Sekarang</button>

                <div class="footer-text">
                    Belum punya akun? <a href="registrasi.php">Daftar di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleVisibility() {
            var x = document.getElementById("password");
            var icon = document.getElementById("eye-icon");

            if (x.type === "password") {
                x.type = "text";
                icon.src = "view.png";
            } else {
                x.type = "password";
                icon.src = "close-eye.png";
            }
        }
    </script>
</body>

</html>