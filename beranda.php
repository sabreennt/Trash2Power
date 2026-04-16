<?php
session_start();
// 1. Koneksi Database
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "trash2power_db";

$conn = @new mysqli($host, $user, $pass, $db);

// Inisialisasi Data (Default)
$nama_user = "Pengguna";
$saldo = 0;
$total_botol = 0;
$total_kaleng = 0;
$foto_profil = "";
$gender = "";

if (!$conn->connect_error) {
    // Mengambil ID user dari session, default ke 1 untuk testing
    $id_user = $_SESSION['id_user'] ?? 1;

    // Ambil Nama, Saldo, Gender, & Foto dari tabel 'users'
    $q_user = $conn->query("SELECT nama, saldo, gender, foto_profil FROM users WHERE id_user = $id_user");
    if ($row = $q_user->fetch_assoc()) {
        $nama_user = $row['nama'];
        $saldo = $row['saldo'];
        $gender = $row['gender'];
        $foto_profil = $row['foto_profil'];
    }

    // Hitung Statistik
    $q_botol = $conn->query("SELECT SUM(jumlah_sampah) as total FROM setoran WHERE id_warga = $id_user AND id_kategori = 1");
    $total_botol = $q_botol->fetch_assoc()['total'] ?? 0;

    $q_kaleng = $conn->query("SELECT SUM(jumlah_sampah) as total FROM setoran WHERE id_warga = $id_user AND id_kategori = 2");
    $total_kaleng = $q_kaleng->fetch_assoc()['total'] ?? 0;
}

// Logika Penentuan Foto Profil untuk Tampilan
if (!empty($foto_profil) && file_exists("uploads/" . $foto_profil)) {
    $tampilan_foto = "uploads/" . $foto_profil;
} elseif (!empty($foto_profil) && file_exists($foto_profil)) {
    $tampilan_foto = $foto_profil;
} else {
    // Jika tidak ada foto di database, gunakan default berdasarkan gender
    $tampilan_foto = ($gender == 'Pria') ? 'pria.png' : 'wanita.png';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Trash2Power</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
        }

        .nav-active {
            border-bottom: 3px solid #34A853;
            color: #34A853;
        }
    </style>
</head>

<body class="min-h-screen">

    <nav class="w-full bg-white border-b sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-8 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 text-[#34A853]">
                    <img src="https://cdn-icons-png.flaticon.com/512/3299/3299935.png" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-xl leading-none">Trash</span>
                    <span class="text-[#1A5319] font-extrabold text-xl leading-none">2Power</span>
                </div>
            </div>

            <div class="flex items-center gap-14 font-bold text-gray-500">
                <a href="beranda.php" class="nav-active pb-1">Beranda</a>
                <a href="scan.php" class="hover:text-green-600 transition">Scan</a>
                <a href="reward.php" class="hover:text-green-600 transition">Reward</a>
            </div>
            <a href="logout.php" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-green-50 transition">
                <img src="logout.png" alt="Logout" class="w-6 h-6">
            </a>
        </div>
    </nav>


    <main class=" max-w-6xl mx-auto px-6 py-10">
        <h2 class="text-2xl font-extrabold text-[#34A853] mb-8">Beranda</h2>

        <div class="flex justify-between items-center mb-10">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 bg-gray-200 rounded-full overflow-hidden border-4 border-white shadow-md">
                    <img src="<?php echo $tampilan_foto; ?>" alt="Avatar" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800 italic">Halo, <?php echo htmlspecialchars($nama_user); ?></h1>
                    <p class="text-gray-500 font-medium">Yuk terus kumpulkan sampah dan raih reward</p>
                </div>
            </div>
            <a href="edit-profile.php" class="bg-[#34A853] text-white px-8 py-2.5 rounded-full font-bold shadow-lg hover:bg-[#2d9147] transition">
                Edit Profile
            </a>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-12">
            <div class="bg-[#D9F2E1] p-8 rounded-[25px] flex items-center gap-5 border border-white shadow-sm">
                <div class="text-4xl">💰</div>
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Total uang</p>
                    <h3 class="text-2xl font-black text-gray-900">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h3>
                </div>
            </div>

            <div class="bg-[#E2F0F9] p-8 rounded-[25px] flex items-center gap-5 border border-white shadow-sm">
                <div class="text-4xl text-green-500">♻️</div>
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Total Disetor</p>
                    <h3 class="text-xl font-black text-gray-900"><?php echo (int)$total_botol; ?> botol & <?php echo (int)$total_kaleng; ?> Kaleng</h3>
                </div>
            </div>

            <div class="bg-[#E5E7EB] p-8 rounded-[25px] flex items-center gap-5 border border-white shadow-sm cursor-pointer hover:bg-gray-300 transition">
                <div class="text-4xl">📁</div>
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Riwayat</p>
                    <h3 class="text-xl font-black text-[#34A853]">Lihat Semua</h3>
                </div>
            </div>
        </div>

        <div class="bg-[#EBF7EE] rounded-[35px] p-10 border border-white shadow-sm mb-12">
            <h3 class="text-xl font-extrabold text-gray-900 mb-4">Selamat Datang di Trash2Power</h3>
            <p class="text-gray-700 leading-relaxed mb-8 font-medium">
                Trash2Power adalah platform inovatif yang memungkinkan pengguna mengonversi sampah terpilah menjadi nilai guna, seperti pulsa dan token listrik. Kami berkomitmen untuk mendorong kebiasaan daur ulang yang berkelanjutan melalui teknologi yang mudah diakses.
            </p>
        </div>

        <div class="bg-[#EBF7EE] rounded-[35px] p-10 border border-white shadow-sm mb-12">
            <h2 class="text-xl font-extrabold text-gray-900 mb-6">Sampah yang Dapat Diproses</h2>
            <p class="text-gray-700 leading-relaxed mb-8 font-medium">
                Untuk memastikan proses verifikasi berjalan optimal, sampah yang akan dipindai harus memenuhi ketentuan berikut:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Botol Plastik:</h3>
                    <ul class="text-gray-700 leading-relaxed font-medium space-y-2">
                        <li>• Dalam kondisi bersih dan bebas dari sisa cairan</li>
                        <li>• Tidak berbau menyengat atau terkontaminasi</li>
                        <li>• Barcode atau label masih utuh dan dapat dipindai</li>
                        <li>• Tidak rusak, sobek, atau terdeformasi berat</li>
                        <li>• Belum pernah digunakan untuk proses scan sebelumnya</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Kaleng Aluminium:</h3>
                    <ul class="text-gray-700 leading-relaxed font-medium space-y-2">
                        <li>• Dalam kondisi bersih dan kering</li>
                        <li>• Tidak berkarat atau terkontaminasi</li>
                        <li>• Barcode/label masih tersedia dan dapat dipindai</li>
                        <li>• Tidak penyok berat atau rusak</li>
                        <li>• Belum pernah dipindai sebelumnya</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-[#F1F9F3] rounded-[30px] p-10 flex justify-between items-center border border-white shadow-sm overflow-hidden relative">
            <div class="z-10">
                <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Ayo Scan Sampah!</h3>
                <p class="text-gray-500 font-medium mb-6">Scan botol atau kaleng untuk tambah point</p>
                <a href="scan.php" class="bg-[#34A853] text-white px-12 py-3 rounded-xl font-bold text-lg shadow-lg hover:bg-[#2d9147] transition">
                    Mulai Scan
                </a>
            </div>
        </div>
    </main>

    <footer class="py-10 text-center text-gray-400 text-sm">
        &copy; 2026 Trash2Power - Sampah Jadi Uang
    </footer>
</body>

</html>