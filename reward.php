<?php
// 1. KONEKSI DATABASE
$host = "127.0.0.1";
$user = "root"; 
$pass = "";     
$db   = "trash2power_db";

$conn = @new mysqli($host, $user, $pass, $db);

$saldo = 0;
$res_riwayat = null; // Diubah jadi null agar tidak error saat dilooping

if (!$conn->connect_error) {
    $id_warga = 1; // Sesuaikan dengan session id user kamu

    // Ambil Saldo
    $query_user = "SELECT saldo FROM users WHERE id_user = $id_warga";
    $res_user = $conn->query($query_user);
    if ($res_user && $row = $res_user->fetch_assoc()) {
        $saldo = $row['saldo'];
    }

    // Ambil Riwayat (Kita simpan ke $res_riwayat)
    $query_riwayat = "SELECT * FROM penukaran_saldo WHERE id_warga = $id_warga ORDER BY tgl_penukaran DESC";
    $res_riwayat = $conn->query($query_riwayat);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reward - Sampah Jadi Uang</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfcfc; }
        
        /* CSS Navbar (Tailwind based) */
        .nav-active { border-bottom: 3px solid #34A853; color: #34A853; }
        
        /* CSS Reward / DANA Style (dikembalikan) */
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
        .status-pending { color: #856404; background-color: #fff3cd; padding: 5px 15px; border-radius: 20px; font-weight: bold;}
        .status-gagal { color: #721c24; background-color: #f8d7da; padding: 5px 15px; border-radius: 20px; font-weight: bold;}
    </style>
</head>
<body class="m-0 p-0 text-gray-800">

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
                <a href="beranda.php" class="hover:text-green-600 transition">Beranda</a>
                <a href="scan.php" class="hover:text-green-600 transition">Scan</a>
                <a href="reward.php" class="nav-active pb-1">Reward</a>
            </div>
            <a href="logout.php" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-green-50 transition">
                <img src="logout.png" alt="Logout" class="w-6 h-6">
            </a>
        </div>
    </nav>
    <div class="container pb-5">
        
       <main class=" max-w-6xl mx-auto px-6 py-5">
        <h5 class="text-2xl font-extrabold text-[#34A853] mb-8">Tukarkan</h5>
        
        <div class="card card-saldo p-4 mb-4 shadow-sm">
            <p class="mb-1">Saldo Kamu</p>
            <h2 class="mb-0 fw-bold">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h2>
        </div>

        <h5 class="mb-3">Pilih Reward</h5>
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-5">
            <div class="dana-grid">
                
                <a href="reward_pulsa.php" class="menu-item">
                    <div class="icon-box">
                        <i class="bi bi-phone icon-pulsa"></i>
                    </div>
                    <span class="menu-text">Pulsa</span>
                </a>

                <a href="#" class="menu-item">
                    <div class="icon-box">
                        <i class="bi bi-lightning-charge-fill icon-listrik"></i>
                    </div>
                    <span class="menu-text">Listrik</span>
                </a>

                <a href="#" class="menu-item">
                    <div class="icon-box">
                        <i class="bi bi-wallet2 icon-ewallet"></i>
                    </div>
                    <span class="menu-text">e-Wallet</span>
                </a>

                <a href="#" class="menu-item">
                    <div class="icon-box">
                        <i class="bi bi-grid-fill text-secondary"></i>
                    </div>
                    <span class="menu-text">View All</span>
                </a>

            </div>
        </div>

        <h5 class="mb-3">Riwayat Penukaran</h5>
        <div>
            <?php 
            // PERBAIKAN: Diubah ke format mysqli OOP (Object Oriented)
            if($res_riwayat && $res_riwayat->num_rows > 0) {
                while($row = $res_riwayat->fetch_assoc()) { 
                    
                    $tanggal = date('d M Y, H:i', strtotime($row['tgl_penukaran']));
                    
                    $icon_riwayat = "bi-bag-check"; 
                    if(strtolower($row['jenis_penukaran']) == 'pulsa') $icon_riwayat = "bi-phone";
                    if(strtolower($row['jenis_penukaran']) == 'listrik') $icon_riwayat = "bi-lightning-charge";
                    if(strtolower($row['jenis_penukaran']) == 'ewallet') $icon_riwayat = "bi-wallet2";
                    
                    $status_class = "";
                    if($row['status'] == 'berhasil') $status_class = "status-berhasil";
                    elseif($row['status'] == 'pending') $status_class = "status-pending";
                    else $status_class = "status-gagal";
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
                        <span class="<?php echo $status_class; ?> fs-6"><?php echo ucfirst($row['status']); ?></span>
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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <footer class="py-10 text-center text-gray-400 text-sm">
        &copy; 2026 Trash2Power - Sampah Jadi Uang
    </footer>

</body>
</html>