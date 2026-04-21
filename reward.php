<?php
session_start(); // ← TAMBAHAN BARU

// Proteksi login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// 1. KONEKSI DATABASE
$host = "127.0.0.1";
$user = "root"; 
$pass = "";     
$db   = "trash2power_db";

$conn = @new mysqli($host, $user, $pass, $db);

$saldo = 0;
$res_riwayat = null;

if (!$conn->connect_error) {
    $id_warga = $_SESSION['id_user']; // ← DIUBAH, sebelumnya = 1

    // Ambil Saldo
    $query_user = "SELECT saldo FROM users WHERE id_user = $id_warga";
    $res_user = $conn->query($query_user);
    if ($res_user && $row = $res_user->fetch_assoc()) {
        $saldo = $row['saldo'];
    }

    // Ambil Riwayat
    $query_riwayat = "SELECT * FROM penukaran_saldo WHERE id_warga = $id_warga ORDER BY tgl_penukaran DESC";
    $res_riwayat = $conn->query($query_riwayat);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Reward - Sampah Jadi Uang</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfcfc; }
        
        /* CSS Navbar (Tailwind based) */
        .nav-active { border-bottom: 3px solid #34A853; color: #34A853; }
        .mobile-nav-active { color: #34A853 !important; }

        /* Modal Animasi */
        .modal-fade { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        /* CSS Reward / DANA Style */
        .card-saldo {
            background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
            border: none;
            border-radius: 15px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .card-saldo::after {
            content: '💰'; 
            font-size: 80px;
            position: absolute;
            right: 20px;
            bottom: -15px;
            opacity: 0.5;
        }
        .dana-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            text-align: center;
        }
        @media (max-width: 576px) {
            .dana-grid { grid-template-columns: repeat(3, 1fr); }
        }
        .menu-item {
            text-decoration: none;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.2s;
            cursor: pointer;
        }
        .menu-item:hover { transform: scale(1.05); }
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 8px;
            background-color: #f1f3f5;
        }
        .menu-text { font-size: 13px; font-weight: 500; }
        .icon-pulsa { color: #e74c3c; }
        .icon-listrik { color: #f1c40f; }
        .icon-ewallet { color: #2ecc71; }
        
        /* CSS Riwayat */
        .riwayat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }
        .status-berhasil { color: #28a745; background-color: #d4edda; padding: 5px 15px; border-radius: 20px; font-weight: bold;}
        .status-gagal { color: #721c24; background-color: #f8d7da; padding: 5px 15px; border-radius: 20px; font-weight: bold;}
    </style>
</head>
<body class="m-0 p-0 text-gray-800 min-h-screen pb-32 md:pb-0">

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
                <a href="tentang_kami.php" class="hover:text-green-600 transition">Tentang Kami</a>
                <a href="beranda.php" class="hover:text-green-600 transition">Beranda</a>
                <a href="scan.php" class="hover:text-green-600 transition">Scan</a>
                <a href="reward.php" class="nav-active pb-1">Penukaran</a>
            </div>

            <div class="flex items-center justify-end md:min-w-[200px]">
                <a href="#" id="btn-logout-trigger" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-red-50 transition group">
                    <img src="logout.png" alt="Logout" class="w-6 h-6 group-hover:opacity-70">
                </a>
            </div>
        </div>
    </nav>

    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 py-4 pb-5 z-[100] flex justify-around items-center shadow-[0_-8px_20px_rgba(0,0,0,0.08)] rounded-t-[30px]">
         
        <a href="tentang_kami.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="info.png" alt="Tentang kami" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Tentang  Kami</span>
            
        </a> <a href="beranda.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="beranda.png" alt="Beranda" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Beranda</span>
        </a>

        <a href="scan.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="scan.png" alt="Scan" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Scan</span>
        </a>

        <a href="reward.php" class="flex flex-col items-center gap-1.5">
            <img src="reward.png" alt="Penukaran" 
                class="w-7 h-7 object-contain"
                style="filter: invert(48%) sepia(79%) saturate(455%) hue-rotate(86deg) brightness(90%) contrast(90%);">
            <span class="text-xs font-bold text-[#34A853]">Penukaran</span>
        </a>

        <a href="edit-profile.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="profile.png" alt="Profil" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Profil</span>
        </a>
    </div>

    <div class="container pb-5 mt-5 md:mt-0">
        <main class="max-w-6xl mx-auto px-6 py-5">
        <h5 class="text-2xl font-extrabold text-[#34A853] mb-8">Tukarkan</h5>
        
        <div class="card card-saldo p-4 mb-4 shadow-sm">
            <p class="mb-1">Saldo Kamu</p>
            <h2 class="mb-0 fw-bold">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h2>
        </div>

        <h5 class="mb-3 font-bold">Pilih Reward</h5>
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-5">
            <div class="dana-grid">
                
                <a href="reward_pulsa.php" class="menu-item">
                    <div class="icon-box">
                        <i class="bi bi-phone icon-pulsa"></i>
                    </div>
                    <span class="menu-text">Pulsa</span>
                </a>

                <a href="reward_listrik.php" class="menu-item">
                    <div class="icon-box">
                        <i class="bi bi-lightning-charge-fill icon-listrik"></i>
                    </div>
                    <span class="menu-text">Listrik</span>
                </a>

                <a href="reward_ewallet.php" class="menu-item">
                    <div class="icon-box">
                        <i class="bi bi-wallet2 icon-ewallet"></i>
                    </div>
                    <span class="menu-text">e-Wallet</span>
                </a>


            </div>
        </div>

        <h5 class="mb-3 font-bold">Riwayat Penukaran</h5>
        <div>
            <?php 
            if($res_riwayat && $res_riwayat->num_rows > 0) {
                while($row = $res_riwayat->fetch_assoc()) { 
                    
                    $tanggal = date('d M Y, H:i', strtotime($row['tgl_penukaran']));
                    
                    $icon_riwayat = "bi-bag-check"; 
                    if(strtolower($row['jenis_penukaran']) == 'pulsa') $icon_riwayat = "bi-phone";
                    if(strtolower($row['jenis_penukaran']) == 'listrik') $icon_riwayat = "bi-lightning-charge";
                    if(strtolower($row['jenis_penukaran']) == 'ewallet') $icon_riwayat = "bi-wallet2";
                    
                    // Lakukan override status pending menjadi berhasil
                    $status_db = strtolower($row['status']);
                    if($status_db == 'pending') {
                        $status_db = 'berhasil';
                    }

                    // Tentukan class CSS berdasarkan status yang sudah dimodifikasi
                    $status_class = ($status_db == 'berhasil') ? "status-berhasil" : "status-gagal";
            ?>
            
            <div class="card riwayat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-light me-3" style="width: 50px; height: 50px; font-size: 24px;">
                            <i class="bi <?php echo $icon_riwayat; ?> text-dark"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($row['jenis_penukaran']) . " " . htmlspecialchars($row['provider']); ?></h6>
                            <small class="text-muted"><?php echo htmlspecialchars($row['nomor_tujuan']); ?></small>
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="mb-1 text-danger fw-bold">- <?php echo number_format($row['nominal'], 0, ',', '.'); ?></p>
                        <small class="text-muted d-block mb-2"><?php echo $tanggal; ?></small>
                        <span class="<?php echo $status_class; ?> fs-6"><?php echo ucfirst($status_db); ?></span>
                    </div>
                </div>
            </div>

            <?php 
                } 
            } else {
                echo "<p class='text-muted text-center py-4'>Belum ada riwayat penukaran.</p>";
            }
            ?>
        </div>
       </main>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script Logika Modal Logout
        const logoutModal = document.getElementById('logout-modal');
        const successModal = document.getElementById('success-modal');
        const triggerLogout = document.getElementById('btn-logout-trigger');
        const cancelBtn = document.getElementById('btn-cancel-logout');
        const confirmBtn = document.getElementById('btn-confirm-logout');

        if(triggerLogout) {
            triggerLogout.onclick = (e) => {
                e.preventDefault();
                logoutModal.classList.replace('hidden', 'flex');
            };
        }
        if(cancelBtn) {
            cancelBtn.onclick = () => logoutModal.classList.replace('flex', 'hidden');
        }
        if(confirmBtn) {
            confirmBtn.onclick = () => {
                logoutModal.classList.replace('flex', 'hidden');
                successModal.classList.replace('hidden', 'flex');
                setTimeout(() => window.location.href = 'login.php', 1500);
            };
        }
    </script>

    <footer class="py-10 text-center text-gray-400 text-sm mb-16 md:mb-0">
        &copy; 2026 Trash2Power - Sampah Jadi Uang
    </footer>

</body>
</html>