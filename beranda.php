<?php
session_start();

// 1. Koneksi Database
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "trash2power_db";

// Menggunakan mysqli tanpa @ agar error bisa terlihat saat didebug
$conn = new mysqli($host, $user, $pass, $db);

// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 2. Proteksi Login & Ambil ID User
// Jika session kosong, arahkan ke login.php agar tidak error di beranda
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
$id_user = $_SESSION['id_user'];

// Inisialisasi Data Default
$nama_user = "Pengguna";
$saldo = 0;
$total_botol = 0;
$total_kaleng = 0;
$foto_profil = "";
$gender = "";

// 3. Ambil Data User
$q_user = $conn->query("SELECT nama, saldo, gender, foto_profil FROM users WHERE id_user = $id_user");
if ($row = $q_user->fetch_assoc()) {
    $nama_user = $row['nama'];
    $saldo = $row['saldo'];
    $gender = $row['gender'];
    $foto_profil = $row['foto_profil'];
}

// 4. Statistik Botol (Kategori 1)
$q_botol = $conn->query("SELECT SUM(jumlah_sampah) as total FROM setoran WHERE id_warga = $id_user AND id_kategori = 1");
$res_botol = $q_botol->fetch_assoc();
$total_botol = $res_botol['total'] ?? 0;

// 5. Statistik Kaleng (Kategori 2)
$q_kaleng = $conn->query("SELECT SUM(jumlah_sampah) as total FROM setoran WHERE id_warga = $id_user AND id_kategori = 2");
$res_kaleng = $q_kaleng->fetch_assoc();
$total_kaleng = $res_kaleng['total'] ?? 0;

// 6. Logika Tampilan Foto Profil
// Default awal berdasarkan gender
$tampilan_foto = ($gender == 'Pria') ? 'pria.png' : 'wanita.png';

if (!empty($foto_profil)) {
    // Cek apakah file ada di folder uploads
    if (file_exists("uploads/" . $foto_profil)) {
        $tampilan_foto = "uploads/" . $foto_profil;
    }
    // Cek jika path tersimpan lengkap di database
    elseif (file_exists($foto_profil)) {
        $tampilan_foto = $foto_profil;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
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

        .mobile-nav-active {
            color: #34A853 !important;
        }

        .stat-card {
            padding: 2rem;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border: 1px solid white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .info-box-custom {
            background-color: #EBF7EE;
            border-radius: 35px;
            padding: 2.5rem;
            border: 1px solid white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 3rem;
        }

        .scan-banner-custom {
            background-color: #F1F9F3;
            border-radius: 30px;
            padding: 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
        }

        .modal-fade {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body class="min-h-screen pb-32 md:pb-0">

    <nav class="w-full bg-white border-b sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 md:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3 md:min-w-[200px]">
                <div class="w-10 h-10 md:w-12 md:h-12"><img src="https://cdn-icons-png.flaticon.com/512/3299/3299935.png" alt="Logo"></div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-lg md:text-xl leading-none italic">Trash</span>
                    <span class="text-[#1A5319] font-extrabold text-lg md:text-xl leading-none italic">2Power</span>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-14 font-bold text-gray-500">
                <a href="beranda.php" class="nav-active pb-1">Beranda</a>
                <a href="scan.php" class="hover:text-green-600 transition">Scan</a>
                <a href="reward.php" class="hover:text-green-600 transition">Reward</a>
            </div>

            <div class="flex items-center justify-end md:min-w-[200px]">
                <a href="#" id="btn-logout-trigger" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-red-50 transition group">
                    <img src="logout.png" alt="Logout" class="w-6 h-6 group-hover:opacity-70">
                </a>
            </div>
        </div>
    </nav>

    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 py-4 pb-5 z-[100] flex justify-around items-center shadow-[0_-8px_20px_rgba(0,0,0,0.08)] rounded-t-[30px]">

        <a href="beranda.php" class="flex flex-col items-center gap-1.5">
            <img src="beranda.png" alt="Beranda"
                class="w-7 h-7 object-contain"
                style="filter: invert(48%) sepia(79%) saturate(455%) hue-rotate(86deg) brightness(90%) contrast(90%);">
            <span class="text-xs font-bold text-[#34A853]">Beranda</span>
        </a>

        <a href="scan.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="scan.png" alt="Scan" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Scan</span>
        </a>

        <a href="reward.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="reward.png" alt="Reward" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Reward</span>
        </a>

        <a href="edit-profile.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="profile.png" alt="Profil" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Profil</span>
        </a>
    </div>

    <main class="max-w-6xl mx-auto px-6 py-10">
        <h2 class="text-2xl font-extrabold text-[#34A853] mb-8">Beranda</h2>
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-10">
            <div class="flex items-center gap-5 w-full md:w-auto">
                <div class="w-20 h-20 bg-gray-200 rounded-full overflow-hidden border-4 border-white shadow-md flex-shrink-0">
                    <img src="<?php echo $tampilan_foto; ?>" alt="Avatar" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800 italic leading-tight">Halo, <?php echo htmlspecialchars($nama_user); ?></h1>
                    <p class="text-gray-500 font-medium text-sm md:text-base">Yuk terus kumpulkan sampah dan raih reward</p>
                </div>
            </div>
            <a href="edit-profile.php" class="w-full md:w-auto text-center bg-[#34A853] text-white px-8 py-2.5 rounded-full font-bold shadow-lg hover:bg-[#2d9147] transition">
                Edit Profile
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="stat-card bg-[#EBF7EE] border-none shadow-sm">
                <div class="w-16 h-16 flex items-center justify-center">
                    <img src="saldo.png" alt="Saldo"
                        class="w-12 h-12 object-contain"
                        style="filter: invert(48%) sepia(79%) saturate(455%) hue-rotate(86deg) brightness(90%) contrast(90%);">
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Saldo</p>
                    <h3 class="text-2xl font-black text-gray-900">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h3>
                </div>
            </div>

            <div class="stat-card bg-[#E2F0F9] border-none shadow-sm">
                <div class="w-16 h-16 flex items-center justify-center">
                    <img src="setor.png" alt="Setor"
                        class="w-12 h-12 object-contain"
                        style="filter: invert(0%) sepia(0%) saturate(200%) hue-rotate(0deg) brightness(0%) contrast(10%);">
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Disetor</p>
                    <h3 class="text-xl font-black text-gray-900"><?php echo (int)$total_botol; ?> Botol & <?php echo (int)$total_kaleng; ?> Kaleng</h3>
                </div>
            </div>

            <div class="stat-card bg-[#E5E7EB] border-none shadow-sm cursor-pointer transition group">
                <div class="w-16 h-16 flex items-center justify-center">
                    <img src="riwayat.png" alt="Riwayat"
                        class="w-12 h-12 object-contain"
                        style="filter: invert(95%) sepia(40%) saturate(1000%) hue-rotate(320deg) brightness(100%) contrast(120%);">
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Riwayat</p>
                    <h3 class="text-xl font-black text-gray-900 group-hover:text-[#F7D678] group-active:text-[#F7D678] transition-colors duration-200">
                        Lihat Semua
                    </h3>
                </div>
            </div>
        </div>

        <div class="info-box-custom">
            <h3 class="text-xl font-extrabold text-gray-900 mb-4">Selamat Datang di Trash2Power</h3>
            <p class="text-gray-700 leading-relaxed font-medium">
                Trash2Power adalah platform inovatif yang memungkinkan pengguna mengonversi sampah terpilah menjadi nilai guna, seperti pulsa dan token listrik. Kami berkomitmen untuk mendorong kebiasaan daur ulang yang berkelanjutan melalui teknologi yang mudah diakses.
            </p>
        </div>

        <div class="info-box-custom">
            <h2 class="text-xl font-extrabold text-gray-900 mb-6">Sampah yang Dapat Diproses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><img src="botol.png" alt="Botol Plastik" class="w-6 h-6 object-contain" style="filter: invert(89%) sepia(22%) saturate(543%) hue-rotate(167deg) brightness(90%) contrast(92%);"> Botol Plastik:</h3>
                    <ul class=" text-gray-700 leading-relaxed font-medium space-y-2 text-sm">
                        <li>1. Dalam kondisi bersih dan bebas dari sisa cairan</li>
                        <li>2. Barcode atau label masih utuh dan dapat dipindai</li>
                        <li>3. Tidak rusak, sobek, atau terdeformasi berat</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><img src="kaleng.png" alt="Kaleng Aluminium" class="w-6 h-6 object-contain" style="filter: invert(81%) sepia(40%) saturate(601%) hue-rotate(305deg) brightness(90%) contrast(102%);"> Kaleng Aluminium:</h3>
                    <ul class="text-gray-700 leading-relaxed font-medium space-y-2 text-sm">
                        <li>1. Dalam kondisi bersih dan kering</li>
                        <li>2. Barcode/label masih tersedia dan dapat dipindai</li>
                        <li>3. Belum pernah dipindai sebelumnya</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="scan-banner-custom mb-12 flex-col md:flex-row text-center md:text-left">
            <div class="z-10 mb-6 md:mb-0">
                <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Ayo Scan Sampah!</h3>
                <p class="text-gray-500 font-medium mb-6">Scan botol atau kaleng untuk tambah point</p>
                <a href="scan.php" class="bg-[#34A853] text-white px-12 py-3 rounded-xl font-bold text-lg shadow-lg hover:bg-[#2d9147] transition inline-block">
                    Mulai Scan
                </a>
            </div>
            <div class="hidden md:block">
                <img src="logoscan.png" alt="Scan Icon" class="w-48 h-48 object-contain">
            </div>
        </div>
    </main>

    <div id="logout-modal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-[30px] p-8 max-w-sm w-full mx-4 shadow-2xl flex flex-col items-center border border-green-100 modal-fade">
            <div class="mb-5"><img src="logout.png" alt="Logout Icon" class="w-24 h-24"></div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-2 text-center">Oh, tidak! Kamu mau pergi...</h3>
            <p class="text-gray-500 font-medium mb-8 text-center text-sm">Apakah kamu yakin ingin keluar dari akun ini?</p>
            <div class="w-full space-y-3">
                <button id="btn-cancel-logout" class="w-full bg-[#34A853] text-white py-3 rounded-full font-bold text-lg shadow-lg hover:bg-[#2d9147] transition">Nggak, cuma bercanda!</button>
                <button id="btn-confirm-logout" class="block w-full text-center border-2 border-[#34A853] text-[#34A853] py-3 rounded-full font-bold text-lg hover:bg-green-50 transition cursor-pointer">Ya, Keluar</button>
            </div>
        </div>
    </div>

    <div id="success-modal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-[30px] p-10 max-w-sm w-full mx-4 shadow-2xl flex flex-col items-center border-t-8 border-[#34A853] modal-fade">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-[#34A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-black text-[#1A5319] mb-2 text-center">Logout Berhasil!</h3>
            <p class="text-gray-600 font-medium text-center text-sm mb-6">Terima kasih telah berkontribusi untuk bumi hari ini. Sampai jumpa lagi!</p>
            <div class="flex items-center gap-2 text-[#34A853] font-bold">
                <div class="w-4 h-4 border-2 border-[#34A853] border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs italic">Mengalihkan ke halaman login...</span>
            </div>
        </div>
    </div>

    <footer class="py-10 text-center text-gray-400 text-sm mb-16 md:mb-0">
        &copy; 2026 Trash2Power - Sampah Jadi Uang
    </footer>

    <script>
        const logoutModal = document.getElementById('logout-modal');
        const successModal = document.getElementById('success-modal');
        const triggerLogout = document.getElementById('btn-logout-trigger');
        const cancelBtn = document.getElementById('btn-cancel-logout');
        const confirmBtn = document.getElementById('btn-confirm-logout');

        triggerLogout.onclick = (e) => {
            e.preventDefault();
            logoutModal.classList.replace('hidden', 'flex');
        };
        cancelBtn.onclick = () => logoutModal.classList.replace('flex', 'hidden');
        confirmBtn.onclick = () => {
            logoutModal.classList.replace('flex', 'hidden');
            successModal.classList.replace('hidden', 'flex');
            setTimeout(() => window.location.href = 'login.php', 1500);
        };
    </script>
</body>

</html>