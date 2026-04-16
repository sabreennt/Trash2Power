<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$database = "trash2power_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil ID user dari session, default ke 1 jika belum login
$id_user = $_SESSION['id_user'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $gender = $_POST['gender'];
    $password_baru = $_POST['password_baru'];

    // Logika Foto Profil
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = $_FILES['foto']['name'];
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_ext = pathinfo($foto_name, PATHINFO_EXTENSION);
        $foto_new_name = "profile_" . $id_user . "_" . time() . "." . $foto_ext;

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        move_uploaded_file($foto_temp, "uploads/" . $foto_new_name);
    } else {
        // DISESUAIKAN: Menggunakan nama file sesuai upload-an kamu
        $foto_new_name = ($gender == 'Pria') ? 'pria.png' : 'wanita.png';
    }

    // Query Update
    if (!empty($password_baru)) {
        $sql = "UPDATE users SET nama='$nama', email='$email', no_hp='$no_hp', gender='$gender', foto_profil='$foto_new_name', password='$password_baru' WHERE id_user='$id_user'";
    } else {
        $sql = "UPDATE users SET nama='$nama', email='$email', no_hp='$no_hp', gender='$gender', foto_profil='$foto_new_name' WHERE id_user='$id_user'";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: beranda.php");
        exit();
    }
}

$result = $conn->query("SELECT * FROM users WHERE id_user='$id_user'");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil - Trash2Power</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f9f4;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: white;
            padding: 30px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        }

        .sidebar h2 {
            color: #1b4332;
            margin-bottom: 40px;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            color: #555;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .sidebar a.active {
            background-color: #d1e7dd;
            color: #1b4332;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .card h1 {
            color: #1b4332;
            margin-top: 0;
        }

        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-section img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #1b4332;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #1b4332;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }

        .btn-save {
            background: #1b4332;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Trash2Power</h2>
        <a href="beranda.php">Beranda</a>
        <a href="#" class="active">Edit Profil</a>
        <a href="logout.php">Log out</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h1>Satu Langkah Lagi!</h1>
            <p>Lengkapi profil Anda untuk mulai menggunakan Trash2Power.</p>

            <form method="POST" enctype="multipart/form-data">
                <div class="profile-section">
                    <?php
                    // Logika penentuan foto yang muncul di halaman
                    $foto_db = $user['foto_profil'] ?? '';
                    if (!empty($foto_db) && file_exists("uploads/" . $foto_db)) {
                        $path = "uploads/" . $foto_db;
                    } elseif (file_exists($foto_db) && !empty($foto_db)) {
                        $path = $foto_db;
                    } else {
                        // Default jika data di database kosong
                        $path = ($user['gender'] == 'Pria') ? 'pria.png' : 'wanita.png';
                    }
                    ?>
                    <img id="preview" src="<?= $path ?>" alt="Foto Profil">
                    <p style="font-size: 12px; color: #666;">Avatar otomatis mendeteksi gender Anda.</p>
                    <input type="file" name="foto" accept="image/*" style="margin-top: 10px;">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" id="gender" onchange="changeAvatar(this.value)" required>
                            <option value="Wanita" <?= ($user['gender'] == 'Wanita') ? 'selected' : '' ?>>Wanita</option>
                            <option value="Pria" <?= ($user['gender'] == 'Pria') ? 'selected' : '' ?>>Pria</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= $user['nama'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= $user['email'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nomor HP</label>
                        <input type="text" name="no_hp" value="<?= $user['no_hp'] ?>" placeholder="08xxxxxxxxx" required>
                    </div>

                    <div class="form-group">
                        <label>Password Saat Ini</label>
                        <input type="text" value="<?= $user['password'] ?>" readonly style="background: #f0f0f0; color: #888;">
                    </div>

                    <div class="form-group">
                        <label>Ganti Password Baru (Opsional)</label>
                        <input type="password" name="password_baru" placeholder="Isi jika ingin ganti">
                    </div>
                </div>

                <button type="submit" class="btn-save">Simpan Profil</button>
            </form>
        </div>
    </div>

    <script>
        function changeAvatar(val) {
            var img = document.getElementById('preview');
            // DISESUAIKAN: Nama file sesuai screenshot folder kamu
            if (val == 'Pria') {
                img.src = 'pria.png';
            } else {
                img.src = 'wanita.png';
            }
        }
    </script>
</body>

</html>