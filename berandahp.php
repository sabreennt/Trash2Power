<?php
session_start();

// --- KONEKSI DATABASE ---
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "trash2power_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Simulasi user login (Pastikan id_user tersedia di database)
$id_user = 1;

// 1. Ambil Data User (Nama & Saldo)
$sql_user = "SELECT nama, saldo FROM users WHERE id_user = $id_user";
$result_user = $conn->query($sql_user);
$user_data = $result_user->fetch_assoc();

$nama_user = $user_data['nama'] ?? 'Pengguna';
$saldo = $user_data['saldo'] ?? 0;

// 2. Ambil Data Total Setoran (Botol & Kaleng secara dinamis)
$sql_setoran = "SELECT ks.jenis_sampah, SUM(s.jumlah_sampah) as total 
                FROM setoran s 
                JOIN kategori_sampah ks ON s.id_kategori = ks.id_sampah 
                WHERE s.id_warga = $id_user 
                GROUP BY ks.id_sampah";
$result_setoran = $conn->query($sql_setoran);

$string_setoran = "";
$setoran_arr = [];
if ($result_setoran && $result_setoran->num_rows > 0) {
    while ($row = $result_setoran->fetch_assoc()) {
        $setoran_arr[] = $row['total'] . " " . $row['jenis_sampah'];
    }
    $string_setoran = implode(" & ", $setoran_arr);
} else {
    $string_setoran = "Belum ada setoran";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Trash2Power</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #28a745;
            --primary-dark: #1e7e34;
            --bg-light: #f8fafc;
            --card-green: #e8f5e9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f0f2f5;
            color: #333;
            display: flex;
            justify-content: center;
            padding: 20px 0;
        }

        /* Container utama untuk Desktop */
        .container {
            width: 90%;
            max-width: 1100px;
            /* Lebar optimal desktop */
            background-color: #fff;
            min-height: 90vh;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            padding-bottom: 40px;
        }

        /* Navbar */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            border-bottom: 1px solid #eee;
        }

        .logo {
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        nav {
            display: flex;
            gap: 40px;
        }

        nav a {
            text-decoration: none;
            color: #666;
            font-weight: 600;
            transition: 0.3s;
            position: relative;
        }

        nav a.active {
            color: var(--primary);
        }

        nav a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary);
            border-radius: 2px;
        }

        /* Layout Utama */
        main {
            padding: 30px 60px;
        }

        .section-title {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 25px;
        }

        /* Profile Row */
        .profile-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .user-meta {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #00a8ff;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .greeting h2 {
            font-size: 1.6rem;
            color: #2c3e50;
        }

        .greeting p {
            color: #7f8c8d;
            font-size: 0.95rem;
        }

        .btn-edit {
            background-color: var(--primary);
            color: white;
            padding: 10px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .btn-edit:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .card {
            padding: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card-uang {
            background-color: #dcfce7;
        }

        .card-disetor {
            background-color: #f1f5f9;
        }

        .card-riwayat {
            background-color: #e2e8f0;
            cursor: pointer;
        }

        .card-icon {
            font-size: 1.8rem;
        }

        .card-info span {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 600;
        }

        .card-info h3 {
            font-size: 1.2rem;
            color: #1e293b;
        }

        /* Welcome & Info Box */
        .info-box {
            background-color: var(--card-green);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .info-box h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
            color: #1e293b;
        }

        .info-box p {
            font-size: 0.95rem;
            color: #475569;
            margin-bottom: 20px;
        }

        .rules {
            margin-top: 15px;
        }

        .rules strong {
            display: block;
            margin-bottom: 5px;
        }

        .rules ul {
            margin-left: 20px;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }

        /* CTA Banner */
        .cta-banner {
            background-color: var(--card-green);
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 50px;
            position: relative;
            overflow: hidden;
        }

        .cta-text h3 {
            font-size: 1.4rem;
            margin-bottom: 8px;
        }

        .cta-text p {
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .btn-scan {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
            padding: 12px 35px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-scan:hover {
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            transform: scale(1.02);
        }

        .cta-img {
            width: 180px;
            filter: drop-shadow(0 10px 10px rgba(0, 0, 0, 0.1));
        }

        /* Responsive Desktop */
        @media (max-width: 900px) {

            header,
            main {
                padding: 20px 30px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <header>
            <div class="logo">
                <span style="font-size: 2rem;">♻️</span> Sampah Jadi Uang
            </div>
            <nav>
                <a href="index.php" class="active">Beranda</a>
                <a href="scan.php">Scan</a>
                <a href="reward.php">Reward</a>
            </nav>
        </header>

        <main>
            <h2 class="section-title">Beranda</h2>

            <div class="profile-row">
                <div class="user-meta">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=00a8ff&color=fff" class="avatar" alt="User">
                    <div class="greeting">
                        <h2>Halo, <?php echo htmlspecialchars($nama_user); ?></h2>
                        <p>Yuk terus kumpulkan sampah dan raih reward</p>
                    </div>
                </div>
                <a href="edit_profile.php" class="btn-edit">Edit Profile</a>
            </div>

            <div class="stats-grid">
                <div class="card card-uang">
                    <div class="card-icon">💰</div>
                    <div class="card-info">
                        <span>Total uang</span>
                        <h3>Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h3>
                    </div>
                </div>
                <div class="card card-disetor">
                    <div class="card-icon">♻️</div>
                    <div class="card-info">
                        <span>Total Disetor</span>
                        <h3><?php echo htmlspecialchars($string_setoran); ?></h3>
                    </div>
                </div>
                <div class="card card-riwayat" onclick="window.location.href='riwayat.php'">
                    <div class="card-icon">📅</div>
                    <div class="card-info">
                        <span>Riwayat</span>
                        <h3 style="color: #666;">Lihat Semua</h3>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h3>Selamat datang di website Trash2power</h3>
                <p>Ini adalah Website penyedia konversi sampah menjadi Pulsa atau Token Listrik</p>

                <div class="rules">
                    <h3>Syarat penukaran sampah</h3>
                    <strong>Botol Plastik</strong>
                    <ul>
                        <li>Bersih</li>
                        <li>Masih Ada Barcode</li>
                        <li>Utuh</li>
                    </ul>
                    <strong>Kaleng Aluminium</strong>
                    <ul>
                        <li>Bersih, Masih Ada Barcode, Masih Utuh</li>
                    </ul>
                </div>
            </div>

            <div class="cta-banner">
                <div class="cta-text">
                    <h3>Ayo Scan Sampah!</h3>
                    <p>Scan botol atau kaleng<br>untuk tambah poin kamu.</p>
                    <button class="btn-scan" onclick="window.location.href='scan.php'">Mulai Scan</button>
                </div>
                <img src="https://cdn-icons-png.flaticon.com/512/3299/3299935.png" alt="Trash Bin" class="cta-img">
            </div>
        </main>
    </div>

</body>

</html>