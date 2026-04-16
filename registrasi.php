<?php

/**
 * BAGIAN 1: LOGIKA PHP & KONEKSI DATABASE
 */
session_start();

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

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role     = 'warga';
    $saldo    = 0.00;

    if (!empty($nama) && !empty($email) && !empty($password)) {
        $cek_email = "SELECT * FROM users WHERE email = '$email'";
        $hasil_cek = $conn->query($cek_email);

        if ($hasil_cek->num_rows > 0) {
            $error_msg = "Email sudah terdaftar!";
        } else {
            $query = "INSERT INTO users (email, password, nama, role, saldo) 
                      VALUES ('$email', '$password', '$nama', '$role', '$saldo')";

            if ($conn->query($query)) {
                $success_msg = "Akun berhasil dibuat! Silakan masuk.";
            } else {
                $error_msg = "Gagal mendaftar: " . $conn->error;
            }
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
    <title>Daftar - Trash2Power</title>
    <style>
        /**
         * BAGIAN 2: CSS (STYLING)
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
            font-size: 25px;
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

        .register-card {
            background-color: #e9f5f2;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .register-card h2 {
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

        /* --- BAGIAN UPDATE KHUSUS IKON MATA --- */
        .eye-icon {
            cursor: pointer;
            width: 22px !important;
            height: auto !important;
            margin-left: 10px;
            margin-right: 0 !important;
            filter: none !important;
            /* Agar ikon mata tidak kena filter hijau tua kalau kamu mau warna aslinya */
        }

        .input-box:focus-within {
            border: 1px solid #52b788;
        }

        .input-box span {
            margin-right: 10px;
            color: #2d6a4f;
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
        <div class="register-card">
            <h2>Daftar Sekarang Yuk!</h2>

            <?php if ($error_msg != ""): ?>
                <div class="alert-error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <?php if ($success_msg != ""): ?>
                <div class="alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <div class="input-box">
                        <img src="avatar.png" alt="User Icon">
                        <input type="text" name="nama" placeholder="Isi nama lengkap kamu" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <div class="input-box">
                        <img src="email.png" alt="User Icon">
                        <input type="email" name="email" placeholder="Contoh123@gmail.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Kata Sandi</label>
                    <div class="input-box">
                        <img src="password.png" alt="User Icon">
                        <input type="password" name="password" id="password" placeholder="Isi kata sandi kamu" required>
                        <img src="close-eye.png" id="eye-icon" class="eye-icon" onclick="toggleVisibility()">
                    </div>
                </div>

                <button type="submit" name="register" class="btn-submit">Daftar Sekarang</button>

                <div class="footer-text">
                    Sudah punya akun? <a href="login.php">Masuk di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // UPDATE: Logika toggle untuk mengganti sumber gambar (src)
        function toggleVisibility() {
            var x = document.getElementById("password");
            var icon = document.getElementById("eye-icon");

            if (x.type === "password") {
                x.type = "text";
                icon.src = "view.png"; // Gambar mata terbuka
            } else {
                x.type = "password";
                icon.src = "close-eye.png"; // Gambar mata tertutup
            }
        }
    </script>
</body>

</html>