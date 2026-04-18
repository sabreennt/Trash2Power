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

$error_msg = "";
$success_msg = "";

// Mengambil email dari URL
$email_user = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';

// --- 2. LOGIKA UPDATE PASSWORD ---
if (isset($_POST['update_password'])) {
    $pass_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    if (!empty($pass_baru) && !empty($konfirmasi)) {
        if ($pass_baru === $konfirmasi) {
            $query = "UPDATE users SET password = '$pass_baru' WHERE email = '$email_user'";
            if ($conn->query($query)) {
                $success_msg = "Kata sandi berhasil diperbarui! Mengalihkan ke halaman login...";
                // Otomatis pindah ke login.php dalam 3 detik
                header("refresh:3;url=login.php");
            } else {
                $error_msg = "Gagal memperbarui kata sandi: " . $conn->error;
            }
        } else {
            $error_msg = "Konfirmasi kata sandi tidak cocok!";
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
    <title>Reset Sandi - Trash2Power</title>
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

        /* --- Branding Sisi Kiri --- */
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
            font-size: 22px;
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

        /* --- Form Sisi Kanan --- */
        .form-section {
            flex: 1;
            background-color: #2d6a4f;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .reset-card {
            background-color: #e9f5f2;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .reset-card h2 {
            color: #1b4332;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .reset-card .info-text {
            color: #444;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.4;
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

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
            border: 1px solid #c3e6cb;
        }

        /* --- Tampilan HP --- */
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

            .reset-card {
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

            .reset-card h2 {
                display: block !important;
                text-align: left;
                font-size: 20px;
                margin-bottom: 10px;
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
        <img src="logoxd.png" alt="Logo">
        <h2>Trash2Power</h2>
        <h1>Satu Langkah Lagi!</h1>
        <p>Jangan lupa passwordnya dicatat dan disimpan supaya kamu tidak lupa lagi ya!</p>
    </div>

    <div class="form-section">
        <div class="reset-card">
            <h2>Buat Sandi Baru Ya!</h2>
            <p class="info-text">Halo <strong><?php echo htmlspecialchars($email_user); ?></strong>, buat kata sandi baru yang kuat untuk akun Trash2Power kamu.</p>

            <?php if ($error_msg != ""): ?>
                <div class="alert-error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <?php if ($success_msg != ""): ?>
                <div class="alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>

            <?php if ($success_msg == ""): ?>
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Kata Sandi Baru</label>
                        <div class="input-box">
                            <img src="password.png" alt="Password Icon">
                            <input type="password" name="password_baru" id="pass1" placeholder="Isi kata sandi baru kamu" required>
                            <img src="close-eye.png" id="eye1" class="eye-icon" onclick="toggleVisibility('pass1', 'eye1')">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Kata Sandi</label>
                        <div class="input-box">
                            <img src="password.png" alt="Password Icon">
                            <input type="password" name="konfirmasi_password" id="pass2" placeholder="Konfirmasi kata sandi baru" required>
                            <img src="close-eye.png" id="eye2" class="eye-icon" onclick="toggleVisibility('pass2', 'eye2')">
                        </div>
                    </div>

                    <button type="submit" name="update_password" class="btn-submit">Simpan Perubahan</button>
                </form>
            <?php endif; ?>

            <div class="footer-text">
                <a href="login.php">Kembali ke Masuk</a>
            </div>
        </div>
    </div>

    <script>
        function toggleVisibility(passId, eyeId) {
            var x = document.getElementById(passId);
            var icon = document.getElementById(eyeId);

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