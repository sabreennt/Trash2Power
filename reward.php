<?php
// 1. KONEKSI DATABASE
$host = "127.0.0.1";
$user = "root"; 
$pass = "";     
$db   = "trash2power_db";

$conn = @new mysqli($host, $user, $pass, $db);

$saldo = 0;
$riwayat = [];

if (!$conn->connect_error) {
    $id_warga = 1; // Sesuaikan dengan session id user kamu

    // Ambil Saldo
    $query_user = "SELECT saldo FROM users WHERE id_user = $id_warga";
    $res_user = $conn->query($query_user);
    if ($res_user && $row = $res_user->fetch_assoc()) {
        $saldo = $row['saldo'];
    }

    // Ambil Riwayat
    $query_riwayat = "SELECT * FROM penukaran_saldo WHERE id_warga = $id_warga ORDER BY tgl_penukaran DESC";
    $res_riwayat = $conn->query($query_riwayat);
    if ($res_riwayat) {
        while ($row = $res_riwayat->fetch_assoc()) {
            $riwayat[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reward - Sampah Jadi Uang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfcfc; }
        .nav-active { border-bottom: 3px solid #34A853; color: #34A853; }
        .reward-card { 
            transition: all 0.3s ease; 
            border-radius: 24px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.03); 
            border: 1px solid #f1f1f1;
        }
        .reward-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body class="m-0 p-0 text-gray-800">

    <nav class="w-full bg-white border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-10 h-24 flex justify-between items-center">
            
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-[#34A853]">
                        <path d="M7 21L3 17M3 17L7 13M3 17H13.5C15.3565 17 17.137 16.2625 18.4497 14.9497C19.7625 13.637 20.5 11.8565 20.5 10C20.5 8.14348 19.7625 6.36301 18.4497 5.05025C17.137 3.7375 15.3565 3 13.5 3H11" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 3L21 7M21 7L17 11M21 7H10.5C8.64348 7 6.86301 7.7375 5.55025 9.05025C4.2375 10.363 3.5 12.1435 3.5 14C3.5 14.89 3.67 15.74 3.98 16.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-xl leading-none">Sampah</span>
                    <span class="text-[#1A5319] font-extrabold text-xl leading-none">Jadi Uang</span>
                </div>
            </div>

            <div class="flex gap-12 font-bold text-gray-500">
                <a href="home.php" class="hover:text-green-600 transition">Beranda</a>
                <a href="scan.php" class="hover:text-green-600 transition">Scan</a>
                <a href="reward.php" class="nav-active pb-2">Reward</a>
            </div>
            
            <div class="w-10 h-10 bg-gray-100 rounded-full border border-gray-200"></div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-12">
        
        <h2 class="text-xl font-bold mb-5 tracking-tight">Tukarkan</h2>
        
        <div class="w-full bg-[#34A853] rounded-[30px] p-12 text-white flex justify-between items-center mb-12 relative overflow-hidden shadow-xl">
            <div class="z-10">
                <p class="text-lg font-semibold opacity-90">Saldo Kamu</p>
                <h1 class="text-6xl font-black mt-2">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h1>
            </div>
            <div class="z-10 mr-5">
                <svg width="140" height="140" viewBox="0 0 100 100" class="drop-shadow-2xl">
                    <circle cx="50" cy="50" r="42" fill="#FACC15" />
                    <circle cx="50" cy="50" r="35" fill="none" stroke="#EAB308" stroke-width="2" stroke-dasharray="4 2"/>
                    <text x="50" y="66" font-size="42" text-anchor="middle" fill="#CA8A04" font-family="Arial" font-weight="bold">$</text>
                </svg>
            </div>
        </div>

        <h2 class="text-xl font-bold mb-8 tracking-tight">Pilih Reward</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-20">
            <?php
            $items = [
                ['name' => 'Pulsa 5.000', 'icon' => '📱', 'color' => 'text-blue-500'],
                ['name' => 'Pulsa 15.000', 'icon' => '📱', 'color' => 'text-blue-500'],
                ['name' => 'Pulsa 25.000', 'icon' => '📱', 'color' => 'text-blue-500'],
                ['name' => 'Pulsa 35.000', 'icon' => '📱', 'color' => 'text-blue-500'],
                ['name' => 'Token Listrik 25k', 'icon' => '⚡', 'color' => 'text-yellow-500'],
                ['name' => 'Token Listrik 50k', 'icon' => '⚡', 'color' => 'text-yellow-500'],
                ['name' => 'Token Listrik 100k', 'icon' => '⚡', 'color' => 'text-yellow-500'],
                ['name' => 'E-Wallet 25.000', 'icon' => '👛', 'color' => 'text-orange-500']
            ];

            foreach ($items as $item) {
                echo '
                <div class="reward-card bg-white p-10 flex flex-col items-center justify-center cursor-pointer group">
                    <div class="text-6xl mb-5 group-hover:scale-110 transition-transform duration-300">'.$item['icon'].'</div>
                    <span class="font-bold text-gray-800 text-center">'.$item['name'].'</span>
                </div>';
            }
            ?>
        </div>

        <h2 class="text-xl font-bold mb-8 tracking-tight">Riwayat Penukaran</h2>

        <div class="flex flex-col gap-6">
            <?php if (empty($riwayat)): ?>
                <div class="p-16 text-center bg-gray-50 rounded-[30px] border-2 border-dashed border-gray-200 text-gray-400 font-medium">
                    Belum ada riwayat penukaran saat ini.
                </div>
            <?php else: ?>
                <?php foreach ($riwayat as $r): ?>
                <div class="w-full bg-[#F9FAFB] rounded-[24px] p-7 flex justify-between items-center border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-gray-50">
                            <?php 
                                if(strpos(strtolower($r['jenis_penukaran']), 'pulsa') !== false) echo "📱";
                                else if(strpos(strtolower($r['jenis_penukaran']), 'token') !== false) echo "⚡";
                                else echo "👛";
                            ?>
                        </div>
                        <div>
                            <h4 class="font-bold text-xl text-gray-900 leading-tight"><?php echo $r['jenis_penukaran']." ".$r['provider']." ".number_format($r['nominal'], 0, '', '.'); ?></h4>
                            <p class="text-gray-500 font-medium mt-1">
                                -<?php echo number_format($r['nominal'], 0, '', '.'); ?> <span class="mx-3 text-gray-300">|</span> <?php echo date('d M Y, H:i', strtotime($r['tgl_penukaran'])); ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <span class="bg-[#DCFCE7] text-[#15803D] px-10 py-3 rounded-2xl font-bold text-sm tracking-wide shadow-sm">
                            <?php echo ucfirst($r['status']); ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <div class="h-24"></div>
</body>
</html>
