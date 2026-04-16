<?php

/**
 * BAGIAN 1: LOGIKA PHP (SUDAH DIPERBAIKI)
 */
session_start();

// Koneksi Database
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

// Logika cek email
if (isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if (!empty($email)) {
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // Jika email ada, langsung arahkan ke reset-sandi.php
            header("Location: reset-sandi.php?email=" . urlencode($email));
            exit();
        } else {
            $error_msg = "Email tidak terdaftar di sistem kami.";
        }
    } else {
        $error_msg = "Harap masukkan alamat email!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Trash2Power</title>
    <style>
        /**
         * BAGIAN 2: CSS (STYLING) - SESUAI DENGAN REGISTRASI.PHP & LOGIN.PHP
         */
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
            color: #2d6a4f;
            font-size: 2px;
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
            margin-bottom: 30px;
            font-weight: 700;
        }

        .reset-card p.info-text {
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

        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }

            .branding-section,
            .form-section {
                padding: 40px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="branding-section">
        <img src="https://cdn-icons-png.flaticon.com/512/3299/3299935.png" alt="Logo">
        <h2>Trash2Power</h2>
        <h1>Selamat Datang!</h1>
        <p>Jangan buang sampah sembarangan terus! Mending tukar sampah botol plastik dan kaleng alumunium jadi saldo yuk!</p>
    </div>

    <div class="form-section">
        <div class="reset-card">
            <h2>Lupa Sandi?</h2>
            <p class="info-text">Masukkan email kamu di bawah ini untuk lanjut ke pengaturan kata sandi baru.</p>

            <?php if ($error_msg != ""): ?>
                <div class="alert-error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Email Terdaftar</label>
                    <div class="input-box">
                        <img src="email.png" alt="Email Icon">
                        <input type="email" name="email" placeholder="Contoh123@gmail.com" required>
                    </div>
                </div>

                <button type="submit" name="reset_password" class="btn-submit">Verifikasi Email</button>

                <div class="footer-text">
                    Ingat kata sandi? <a href="login.php">Masuk di sini</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>