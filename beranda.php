<?php
// 1. Koneksi Database
$host = "127.0.0.1";
$user = "root"; 
$pass = "";     
$db   = "trash2power_db";

$conn = @new mysqli($host, $user, $pass, $db);

// Inisialisasi Data (Default)
$nama_user = "Ujang"; 
$saldo = 0;
$total_botol = 0;
$total_kaleng = 0;

if (!$conn->connect_error) {
    $id_user = 1; // Simulasi ID user yang login

    // Ambil Nama & Saldo dari tabel 'users'
    $q_user = $conn->query("SELECT nama, saldo FROM users WHERE id_user = $id_user");
    if ($row = $q_user->fetch_assoc()) {
        $nama_user = $row['nama'];
        $saldo = $row['saldo'];
    }

    // Hitung Statistik dari tabel 'setoran' & 'kategori_sampah'
    // Menghitung jumlah berdasarkan kategori (ID 1: Botol Plastik, ID 2: Kaleng - Menyesuaikan data umum)
    $q_botol = $conn->query("SELECT SUM(jumlah_sampah) as total FROM setoran WHERE id_warga = $id_user AND id_kategori = 1");
    $total_botol = $q_botol->fetch_assoc()['total'] ?? 0;

    $q_kaleng = $conn->query("SELECT SUM(jumlah_sampah) as total FROM setoran WHERE id_warga = $id_user AND id_kategori = 2");
    $total_kaleng = $q_kaleng->fetch_assoc()['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Sampah Jadi Uang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .nav-active { border-bottom: 3px solid #34A853; color: #34A853; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="min-h-screen">

    <nav class="w-full bg-white border-b sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-8 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 text-[#34A853]">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
                        <path d="M7 21L3 17M3 17L7 13M3 17H13.5C18.4497 14.9497 20.5 11.8565 20.5 10C20.5 8.1435 18.4497 5.0503 13.5 3H11" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 3L21 7M21 7L17 11M21 7H10.5C5.5503 9.0503 3.5 12.1435 3.5 14" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-xl leading-none">Sampah</span>
                    <span class="text-[#1A5319] font-extrabold text-xl leading-none">Jadi Uang</span>
                </div>
            </div>

            <div class="flex gap-14 font-bold text-gray-500">
                <a href="beranda.php" class="nav-active pb-1">Beranda</a>
                <a href="scan.php" class="hover:text-green-600 transition">Scan</a>
                <a href="reward.php" class="hover:text-green-600 transition">Reward</a>
            </div>
            <div class="w-20"></div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-10">
        <h2 class="text-2xl font-extrabold text-[#34A853] mb-8">Beranda</h2>

        <div class="flex justify-between items-center mb-10">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 bg-gray-200 rounded-full overflow-hidden border-4 border-white shadow-md">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=random" alt="Avatar">
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800 italic">Halo, <?php echo $nama_user; ?></h1>
                    <p class="text-gray-500 font-medium">Yuk terus kumpulkan sampah dan raih reward</p>
                </div>
            </div>
            <button class="bg-[#34A853] text-white px-8 py-2.5 rounded-full font-bold shadow-lg hover:bg-[#2d9147] transition">
                Edit Profile
            </button>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-12">
            <div class="bg-[#D9F2E1] p-8 rounded-[25px] flex items-center gap-5 border border-white shadow-sm">
                <div class="text-4xl">👥</div>
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Total uang</p>
                    <h3 class="text-2xl font-black text-gray-900">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h3>
                </div>
            </div>

            <div class="bg-[#E2F0F9] p-8 rounded-[25px] flex items-center gap-5 border border-white shadow-sm">
                <div class="text-4xl text-green-500">♻️</div>
                <div>
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Total Disetor</p>
                    <h3 class="text-xl font-black text-gray-900"><?php echo $total_botol; ?> botol & <?php echo $total_kaleng; ?> Kaleng</h3>
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
                Trash2Power adalah platform inovatif yang memungkinkan pengguna mengonversi sampah terpilah menjadi nilai guna, 
                seperti pulsa dan token listrik. Kami berkomitmen untuk mendorong kebiasaan daur ulang yang berkelanjutan 
                melalui teknologi yang mudah diakses.
            </p>

            <h4 class="text-lg font-bold text-gray-900 mb-4 tracking-tight">Kriteria Sampah yang Dapat Diproses</h4>
            <div class="grid grid-cols-2 gap-10">
                <div>
                    <h5 class="font-bold text-gray-900 mb-3">Botol Plastik:</h5>
                    <ul class="space-y-2 text-sm text-gray-700 font-medium">
                        <li class="flex items-start gap-2"><span>•</span> Dalam kondisi bersih dan bebas dari sisa cairan</li>
                        <li class="flex items-start gap-2"><span>•</span> Tidak berbau menyengat atau terkontaminasi</li>
                        <li class="flex items-start gap-2"><span>•</span> Barcode atau label masih utuh dan dapat dipindai</li>
                        <li class="flex items-start gap-2"><span>•</span> Tidak rusak, sobek, atau terdeformasi berat</li>
                        <li class="flex items-start gap-2"><span>•</span> Belum pernah digunakan untuk proses scan sebelumnya</li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 mb-3">Kaleng Aluminium:</h5>
                    <ul class="space-y-2 text-sm text-gray-700 font-medium">
                        <li class="flex items-start gap-2"><span>•</span> Dalam kondisi bersih dan kering</li>
                        <li class="flex items-start gap-2"><span>•</span> Tidak berkarat atau terkontaminasi</li>
                        <li class="flex items-start gap-2"><span>•</span> Barcode/label masih tersedia dan dapat dipindai</li>
                        <li class="flex items-start gap-2"><span>•</span> Tidak penyok berat atau rusak</li>
                        <li class="flex items-start gap-2"><span>•</span> Belum pernah dipindai sebelumnya</li>
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
            <div class="absolute right-10 bottom-0 opacity-20">
                <svg width="200" height="200" viewBox="0 0 24 24" fill="currentColor" class="text-[#34A853]">
                    <path d="M4 4h3V2H4c-1.1 0-2 .9-2 2v3h2V4zm13-2h3c1.1 0 2 .9 2 2v3h-2V4h-3V2zM4 17v3h3v2H4c-1.1 0-2-.9-2-2v-3h2zm17 3v-3h2v3c0 1.1-.9 2-2 2h-3v-2h3zM12 6c-3.3 0-6 2.7-6 6s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zm0 10c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"/>
                </svg>
            </div>
        </div>
    </main>

    <footer class="py-10 text-center text-gray-400 text-sm">
        &copy; 2026 Trash2Power - Sampah Jadi Uang
    </footer>
</body>
</html>