<?php
session_start();

// Proteksi Login & Ambil ID User
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
$id_user_login = $_SESSION['id_user'];

// --- BAGIAN 1: PROSES DATA (PHP) ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "trash2power_db";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Gunakan ID dari SESSION untuk keamanan, jangan dari POST JS
    $id_user = $_SESSION['id_user'];
    $id_kategori = $_POST['id_kategori'] ?? null;

    $resKategori = mysqli_query($koneksi, "SELECT jenis_sampah, harga_sampah FROM kategori_sampah WHERE id_kategori = '$id_kategori'");
    $dataKategori = mysqli_fetch_assoc($resKategori);

    if ($dataKategori) {
        $harga = $dataKategori['harga_sampah'];
        $nama_sampah = $dataKategori['jenis_sampah'];

        mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $harga WHERE id_user = '$id_user'");
        mysqli_query($koneksi, "INSERT INTO setoran (id_warga, id_kategori, jumlah_sampah, hasil_pendapatan, tgl_penyetoran) 
                               VALUES ('$id_user', '$id_kategori', 1, '$harga', NOW())");

        echo json_encode([
            'status' => 'success',
            'message' => 'Scan Berhasil!',
            'harga' => $harga,
            'jenis' => $nama_sampah
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Kategori Tidak Ditemukan']);
    }
    exit;
}

// Ambil data user untuk tampilan Saldo berdasarkan Session, bukan angka 1
$userData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama, saldo FROM users WHERE id_user = '$id_user_login'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Sampah - Trash2Power</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F9FBFC;
        }

        .nav-active {
            border-bottom: 3px solid #34A853;
            color: #34A853;
        }

        .mobile-nav-active {
            color: #34A853 !important;
        }

        .bg-success-card {
            background-color: #D9F3C6;
        }

        .btn-primary {
            background-color: #2D9147;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #1A5319;
            transform: translateY(-2px);
        }

        #reader video {
            border-radius: 24px;
        }

        .card-shadow {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
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
                <a href="tentang_kami.php" class="hover:text-green-600 transition">Tentang Kami</a>
                <a href="beranda.php" class="hover:text-green-600 transition">Beranda</a>
                <a href="scan.php" class="nav-active pb-1">Scan</a>
                <a href="reward.php" class="hover:text-green-600 transition">Penukaran</a>
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
            <span class="text-xs font-bold">Tentang Kami</span>

            <a href="beranda.php" class="flex flex-col items-center gap-1.5 text-gray-400">
                <img src="beranda.png" alt="Beranda" class="w-7 h-7 object-contain opacity-40">
                <span class="text-xs font-bold">Beranda</span>
            </a>

            <a href="scan.php" class="flex flex-col items-center gap-1.5">
                <img src="scan.png" alt="Scan"
                    class="w-7 h-7 object-contain"
                    style="filter: invert(48%) sepia(79%) saturate(455%) hue-rotate(86deg) brightness(90%) contrast(90%);">
                <span class="text-xs font-bold text-[#34A853]">Scan</span>
            </a>

            <a href="reward.php" class="flex flex-col items-center gap-1.5 text-gray-400">
                <img src="reward.png" alt="Penukaran" class="w-7 h-7 object-contain opacity-40">
                <span class="text-xs font-bold">Penukaran</span>
            </a>

            <a href="edit-profile.php" class="flex flex-col items-center gap-1.5 text-gray-400">
                <img src="profile.png" alt="Profil" class="w-7 h-7 object-contain opacity-40">
                <span class="text-xs font-bold">Profil</span>
            </a>
    </div>

    <main class="max-w-6xl mx-auto mt-10 px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Hasil Scan</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

            <div id="view-container">
                <div id="scanner-view" class="bg-white p-6 rounded-[32px] border card-shadow text-center">
                    <div id="reader" class="overflow-hidden mb-4"></div>
                    <p id="scan-instruction" class="text-gray-500 font-semibold italic">Arahkan Kamera Ke Barcode Sampah</p>

                    <div class="mt-4 flex items-center gap-3 bg-gray-50 p-3 rounded-xl">
                        <span class="text-xs font-bold text-gray-400 uppercase">Zoom</span>
                        <input type="range" id="zoom-slider" min="1" max="4" step="0.1" value="2" class="flex-1 accent-[#34A853]" oninput="applyZoom(this.value)">
                        <span id="zoom-label" class="text-xs font-bold text-[#34A853]">2x</span>
                    </div>
                </div>

                <div id="success-view" class="bg-success-card p-10 rounded-[40px] flex-col items-center justify-center text-center hidden min-h-[450px]">
                    <div class="w-24 h-24 bg-[#34A853] rounded-full flex items-center justify-center mb-6 shadow-lg shadow-green-200">
                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 mb-2">Scan Berhasil!</h3>
                    <p class="text-gray-700 font-medium text-lg">Sampah berhasil terdeteksi</p>
                </div>

                <div class="mt-6 bg-white p-6 rounded-[24px] border card-shadow">
                    <p class="text-sm text-gray-400 mb-3 font-bold uppercase tracking-wider">Input Barcode Manual</p>
                    <div class="flex gap-2">
                        <input type="text" id="manual-input" placeholder="Masukkan 13 digit barcode..." class="flex-1 border-2 border-gray-100 bg-gray-50 rounded-2xl px-5 py-3 focus:outline-none focus:border-[#34A853] transition">
                        <button onclick="submitManual()"
                            class="btn-primary flex-shrink-0 text-white px-4 md:px-4 py-2 rounded-xl font-bold text-sm md:text-base shadow-sm">
                            Kirim
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[32px] border card-shadow">
                    <h4 class="text-gray-800 font-bold mb-4">Detail sampah</h4>
                    <div class="space-y-5">
                        <div class="flex items-center gap-4 bg-blue-50 p-2 rounded-xl">
                            <img src="botol.png" alt="Botol"
                                class="w-10 h-10 object-contain"
                                style="filter: invert(70%) sepia(50%) saturate(300%) hue-rotate(160deg) brightness(95%) contrast(100%);">
                            <div>
                                <p class="font-bold text-blue-900">Botol Plastik</p>
                                <p class="text-[#34A853] font-bold text-sm">+ Rp 50</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 bg-pink-50 p-2 rounded-xl">
                            <img src="kaleng.png" alt="Kaleng"
                                class="w-10 h-10 object-contain"
                                style="filter: invert(70%) sepia(30%) saturate(600%) hue-rotate(300deg) brightness(105%) contrast(100%);">
                            <div>
                                <p class="font-bold text-pink-900">Kaleng Aluminium</p>
                                <p class="text-[#34A853] font-bold text-sm">+ Rp 40</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[32px] border-l-8 border-[#34A853] card-shadow">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="bg-green-50 p-3 rounded-2xl">
                            <svg class="w-6 h-6 text-[#34A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-gray-800 uppercase mb-1">Tips Scan Barcode</h5>
                            <p class="text-xs text-gray-500 leading-relaxed font-medium">
                                Pastikan pencahayaan cukup dan barcode berada di dalam kotak scanner. Jika kualitas kamera kurang mendukung, silakan gunakan <button onclick="document.getElementById('manual-input').scrollIntoView({behavior: 'smooth'}); document.getElementById('manual-input').focus();" class="text-[#34A853] font-bold hover:underline cursor-pointer bg-none border-none p-0">input manual</button> untuk mempermudah penyetoran sampah.
                            </p>
                        </div>
                    </div>

                    <div class="border-t pt-6 flex items-start gap-4 cursor-pointer hover:opacity-80 transition" onclick="document.querySelector('.mt-12').scrollIntoView({behavior: 'smooth', block: 'center'});">
                        <div class="bg-blue-50 p-3 rounded-2xl">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-gray-800 uppercase mb-1">Riwayat Penyetoran</h5>
                            <p class="text-xs text-gray-500 leading-relaxed font-medium">
                                Lihat catatan lengkap semua <button class="text-blue-500 font-bold hover:underline bg-none border-none p-0">riwayat penyetoran sampah kamu</button> untuk melacak setiap setoran sampah yang telah dikumpulkan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[32px] border card-shadow">
                    <h4 class="text-gray-800 font-bold mb-2">Total Saldo Kamu</h4>
                    <div class="flex items-baseline gap-2 mb-7">
                        <span class="text-3xl font-black text-[#1A5319]">Rp <span id="current-saldo"><?= number_format($userData['saldo'], 0, ',', '.') ?></span></span>
                        <span id="bonus-added" class="text-xl font-bold text-[#34A853] hidden">(+ Rp 0)</span>
                    </div>

                    <div class="mt-5">
                        <button onclick="location.reload()"
                            class="btn-primary w-full text-white py-3.5 rounded-2xl font-bold text-lg shadow-md shadow-green-100 transition-all active:scale-[0.95]">
                            Scan Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-[32px] border card-shadow overflow-hidden">
            <div class="p-6 md:p-8 border-b flex justify-between items-center">
                <h3 class="text-lg md:text-xl font-bold text-gray-800">Riwayat Penyetoran Sampah</h3>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-black">
                        <tr>
                            <th class="px-8 py-4">ID Setoran</th>
                            <th class="px-8 py-4">Waktu Setoran</th>
                            <th class="px-8 py-4">Jenis Sampah</th>
                            <th class="px-8 py-4">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $q = mysqli_query($koneksi, "SELECT s.*, k.jenis_sampah FROM setoran s JOIN kategori_sampah k ON s.id_kategori = k.id_kategori WHERE s.id_warga = '$id_user_login' ORDER BY s.tgl_penyetoran DESC LIMIT 5");
                        while ($r = mysqli_fetch_assoc($q)):
                        ?>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-8 py-5 font-bold text-gray-600">#STR-<?= $r['id_setoran'] ?></td>
                                <td class="px-8 py-5 text-gray-500 text-sm"><?= date('d M Y, H:i', strtotime($r['tgl_penyetoran'])) ?></td>
                                <td class="px-8 py-5"><span class="bg-green-50 text-[#34A853] px-3 py-1 rounded-full text-[10px] font-black uppercase"><?= $r['jenis_sampah'] ?></span></td>
                                <td class="px-8 py-5 font-bold text-gray-800">Rp <?= number_format($r['hasil_pendapatan'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                <?php
                // Reset pointer jika query sudah dijalankan atau jalankan ulang
                mysqli_data_seek($q, 0);
                while ($r = mysqli_fetch_assoc($q)):
                ?>
                    <div class="p-5 flex justify-between items-center hover:bg-gray-50 transition">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-400">#STR-<?= $r['id_setoran'] ?></span>
                                <span class="bg-green-50 text-[#34A853] px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter"><?= $r['jenis_sampah'] ?></span>
                            </div>
                            <span class="text-[11px] text-gray-500"><?= date('d M Y, H:i', strtotime($r['tgl_penyetoran'])) ?></span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-gray-800">Rp <?= number_format($r['hasil_pendapatan'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
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
        let isProcessing = false;
        let scanCount = 0;

        const html5QrCode = new Html5Qrcode("reader");

        function applyZoom(val) {
            document.getElementById('zoom-label').innerText = val + 'x';
            try {
                const videoEl = document.querySelector('#reader video');
                if (videoEl && videoEl.srcObject) {
                    const track = videoEl.srcObject.getVideoTracks()[0];
                    if (track && track.getCapabilities && track.getCapabilities().zoom) {
                        track.applyConstraints({
                            advanced: [{
                                zoom: parseFloat(val)
                            }]
                        });
                    }
                }
            } catch (e) {
                console.log("Zoom tidak didukung perangkat ini");
            }
        }

        function submitManual() {
            const manualInput = document.getElementById('manual-input');
            const barcode = manualInput.value.trim();
            if (barcode === "") {
                alert("Silakan masukkan angka barcode terlebih dahulu");
                return;
            }
            onScanSuccess(barcode);
            manualInput.value = "";
        }

        function onScanSuccess(decodedText) {
            if (isProcessing) return;

            const barcodeBotolPlastik = [
                "8886008101053", "8886008101077", "8992761002022", "8992761136178", "8992761133153", "8992761141011", "8992761143152",
                "8992761139155", "8992761131159", "8996001600267", "8996001600274", "8996001300716", "8996001300723", "8998009010014",
                "8998009010021", "8999999000028", "8999999000035", "8999999000042", "8999999000059", "8999999000066", "8999999000073",
                "089686993844", "089686770889", "089686770902", "8997009520019", "8997009520026", "8997009520033"
            ];

            const barcodeKaleng = [
                "8992761112011", "8992761133016", "8992761131012", "8992761111014", "8992761113018", "8996001601004", "8998009010618",
                "8999999000202", "8999999000219", "8999999000226", "8999999000233", "8999988778570", "8999988778594", "8999988778679",
                "8999988778686", "8999988778808", "8999988778815", "8999988778822", "8999988778839", "8999988778846", "8999988778853",
                "8999988778860", "8999988778884", "8999988778976", "8999988778983", "8999988888804", "8999988888811", "8999988888828",
                "8999988888835", "8999988888842", "8999988888859", "8999988888866", "8999988888972", "8999988888989",
                "8992752521014", "8992752521021", "8992752521038", "8992696191014", "8992696191021"
            ];

            const instructionText = document.getElementById('scan-instruction');
            let kategori = null;
            let namaSampah = "";

            if (barcodeBotolPlastik.includes(decodedText)) {
                kategori = 1;
                namaSampah = "Botol Plastik";
            } else if (barcodeKaleng.includes(decodedText)) {
                kategori = 2;
                namaSampah = "Kaleng Aluminium";
            } else if (decodedText.length === 13) {
                const plasticPrefixes = ["8886", "8992761", "8996001", "8998009", "0896867", "8997009"];
                const canPrefixes = ["8999988", "8992752", "8992696"];
                if (plasticPrefixes.some(p => decodedText.startsWith(p))) {
                    kategori = 1;
                    namaSampah = "Botol Plastik";
                } else if (canPrefixes.some(p => decodedText.startsWith(p))) {
                    kategori = 2;
                    namaSampah = "Kaleng Aluminium";
                }
            }

            if (kategori === null) {
                isProcessing = true;
                instructionText.innerHTML = `❌ Produk <strong>${decodedText}</strong><br>Tidak Terdaftar!`;
                instructionText.classList.replace('text-gray-500', 'text-red-500');
                if (navigator.vibrate) navigator.vibrate(200);
                setTimeout(() => {
                    instructionText.innerHTML = "Arahkan Kamera Ke Barcode Sampah";
                    instructionText.classList.replace('text-red-500', 'text-gray-500');
                    isProcessing = false;
                }, 3000);
                return;
            }

            isProcessing = true;
            scanCount++;
            instructionText.innerHTML = `✅ ${namaSampah} Terdeteksi!<br>Memproses...`;
            instructionText.classList.replace('text-gray-500', 'text-green-600');
            if (navigator.vibrate) navigator.vibrate([100, 50, 100]);

            let formData = new FormData();
            formData.append('id_kategori', kategori);
            formData.append('barcode', decodedText);

            fetch('scan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        html5QrCode.stop();
                        document.getElementById('scanner-view').style.display = 'none';
                        document.getElementById('success-view').style.display = 'flex';
                        document.querySelector('#success-view h3').innerText = `${namaSampah} Terdeteksi!`;
                        const bonusSpan = document.getElementById('bonus-added');
                        bonusSpan.innerText = `(+ Rp ${data.harga})`;
                        bonusSpan.classList.remove('hidden');
                        const saldoEl = document.getElementById('current-saldo');
                        let currentSaldo = parseInt(saldoEl.innerText.replace(/\./g, ''));
                        saldoEl.innerText = (currentSaldo + parseInt(data.harga)).toLocaleString('id-ID');
                    } else {
                        alert(data.message);
                        isProcessing = false;
                        instructionText.innerHTML = "Arahkan Kamera Ke Barcode Sampah";
                        instructionText.classList.replace('text-green-600', 'text-gray-500');
                    }
                })
                .catch(err => {
                    console.error("❌ Error:", err);
                    alert("Terjadi kesalahan saat memproses. Coba lagi!");
                    isProcessing = false;
                    instructionText.innerHTML = "Arahkan Kamera Ke Barcode Sampah";
                    instructionText.classList.replace('text-green-600', 'text-gray-500');
                });
        }

        const config = {
            fps: 15,
            qrbox: function(viewfinderWidth, viewfinderHeight) {
                return {
                    width: Math.floor(viewfinderWidth * 0.85),
                    height: 120
                };
            },
            aspectRatio: 1.333333,
            formatsToSupport: [
                Html5QrcodeSupportedFormats.QR_CODE,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_93,
                Html5QrcodeSupportedFormats.ITF
            ],
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            },
            verbose: false
        };

        html5QrCode.start({
                    facingMode: "environment"
                },
                config,
                onScanSuccess,
                () => {}
            )
            .then(() => {
                console.log("✅ Kamera berhasil dijalankan");
                setTimeout(() => applyZoom(2), 1000);
            })
            .catch(err => {
                console.warn("Kamera belakang gagal, mencoba kamera depan...", err);
                html5QrCode.start({
                            facingMode: "user"
                        },
                        config,
                        onScanSuccess,
                        () => {}
                    )
                    .then(() => {
                        console.log("✅ Kamera depan berhasil");
                        setTimeout(() => applyZoom(2), 1000);
                    })
                    .catch(err2 => {
                        console.error("❌ Semua kamera gagal:", err2);
                        document.getElementById('scan-instruction').innerHTML =
                            "⚠️ Kamera tidak dapat diakses.<br><small>Pastikan Anda memberikan izin kamera</small>";
                        document.getElementById('scan-instruction').classList.add('text-red-500');
                    });
            });

        document.addEventListener("DOMContentLoaded", function() {
            const logoutModal = document.getElementById('logout-modal');
            const successModal = document.getElementById('success-modal');

            // 1. Ambil pemicu berdasarkan ID (sesuai HTML yang kamu kirim)
            const triggerLogout = document.getElementById('btn-logout-trigger');

            const cancelBtn = document.getElementById('btn-cancel-logout');
            const confirmBtn = document.getElementById('btn-confirm-logout');

            // 2. Fungsi memunculkan modal
            if (triggerLogout) {
                triggerLogout.onclick = (e) => {
                    e.preventDefault();
                    logoutModal.classList.replace('hidden', 'flex');
                };
            }

            // 3. Tombol Batal
            if (cancelBtn) {
                cancelBtn.onclick = () => {
                    logoutModal.classList.replace('flex', 'hidden');
                };
            }

            // 4. Tombol Konfirmasi (Ya, Keluar)
            if (confirmBtn) {
                confirmBtn.onclick = () => {
                    logoutModal.classList.replace('flex', 'hidden');
                    successModal.classList.replace('hidden', 'flex');

                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                };
            }
        });
    </script>
</body>

</html>