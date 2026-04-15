<?php
// BAGIAN 1: KONEKSI DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "trash2power_db";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// BAGIAN 2: LOGIKA PENDAFTARAN
if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Email sudah terdaftar, gunakan email lain!');</script>";
    } else {
        $query = "INSERT INTO users (email, password, nama, role, saldo) 
                  VALUES ('$email', '$password', '$nama', 'warga', 0)";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan masuk ke akunmu.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Gagal mendaftar: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Trash2Power</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
            background-color: #ffffff;
        }

        /* Sisi Kiri */
        .sisi-kiri {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
        }

        .logo-box {
            position: relative;
            width: 180px;
            height: 180px;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tote-bag {
            width: 100px;
            height: 120px;
            background-color: #e6ccb2;
            border: 3px solid #7f5539;
            border-radius: 8px;
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
        }

        .trash-item {
            position: absolute;
            font-size: 24px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }

        .judul-utama {
            font-size: 48px;
            font-weight: 800;
            color: #1b4332;
            margin: 10px 0;
        }

        .deskripsi {
            color: #2d6a4f;
            font-size: 16px;
            max-width: 400px;
            line-height: 1.5;
        }

        /* Sisi Kanan */
        .sisi-kanan {
            flex: 1;
            background-color: #2d6a4f;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-card {
            background-color: #f0fff4;
            padding: 40px;
            border-radius: 30px;
            width: 85%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .form-card h2 {
            color: #1b4332;
            margin-bottom: 30px;
            font-size: 26px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1b4332;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        /* Ikon Sisi Kiri */
        .input-wrapper i.fa-user,
        .input-wrapper i.fa-envelope,
        .input-wrapper i.fa-lock {
            position: absolute;
            left: 15px;
            color: #52b788;
            z-index: 5;
        }

        .input-field {
            width: 100%;
            padding: 12px 45px 12px 45px;
            /* Padding kanan ditambah untuk ruang mata */
            border-radius: 12px;
            border: 1.5px solid #b7e4c7;
            background-color: #ffffff;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .input-field:focus {
            border-color: #409167;
            box-shadow: 0 0 8px rgba(64, 145, 103, 0.2);
        }

        /* Ikon Mata Sisi Kanan */
        .toggle-password {
            position: absolute;
            right: 15px;
            cursor: pointer;
            color: #52b788;
            transition: 0.3s;
            z-index: 5;
        }

        .toggle-password:hover {
            color: #1b4332;
        }

        .btn-daftar {
            width: 100%;
            padding: 15px;
            background-color: #2d6a4f;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-daftar:hover {
            background-color: #1b4332;
            transform: translateY(-2px);
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #2d6a4f;
        }

        .footer-text a {
            font-weight: bold;
            color: #1b4332;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="sisi-kiri">
        <div class="logo-box">
            <div class="tote-bag">♻️</div>
            <div class="trash-item" style="top: -10px; left: -20px;">🧴</div>
            <div class="trash-item" style="top: 20px; right: -30px; animation-delay: 0.5s;">🥫</div>
            <div class="trash-item" style="bottom: 0px; left: -30px; animation-delay: 1s;">🍼</div>
        </div>
        <p style="color: #409167; font-weight: bold; margin: 0;">Trash2Power</p>
        <h1 class="judul-utama">Selamat Datang!</h1>
        <p class="deskripsi">Daftar sekarang untuk mulai mengumpulkan saldo langsung dari hasil penukaran sampah plastik dan alumunium!</p>
    </div>

    <div class="sisi-kanan">
        <div class="form-card">
            <h2>Daftar Sekarang</h2>
            <form method="POST">
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nama" class="input-field" placeholder="Nama lengkap kamu" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="input-field" placeholder="contoh123@gmail.com" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Kata Sandi</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="input-field" placeholder="Masukkan kata sandi" required>
                        <i class="fas fa-eye toggle-password" id="eyeIcon" onclick="togglePass()"></i>
                    </div>
                </div>

                <button type="submit" name="daftar" class="btn-daftar">Daftar Sekarang</button>
            </form>
            <div class="footer-text">
                Sudah punya akun? <a href="login.php">Masuk di sini</a>
            </div>
        </div>
    </div>

    <script>
        function togglePass() {
            const passwordField = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }
    </script>

</body>

</html>