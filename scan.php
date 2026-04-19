<?php
// --- BAGIAN 1: PROSES DATA (PHP) ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "trash2power_db";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $id_user = $_POST['id_user'] ?? '1';
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

$userData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama, saldo FROM users WHERE id_user = 1"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Sampah - Trash2Power</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

        :root {
            --primary: #34A853;
            --bg: #F8FAFC;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            margin: 0;
        }

        /* --- CSS KHUSUS NAVBAR (DARI PEMISAHAN SEBELUMNYA) --- */
        .nav-active {
            border-bottom: 3px solid #34A853;
            color: #34A853;
        }

        /* --- LAYOUT UTAMA --- */
        .main-content {
            max-width: 1100px;
            width: 90%;
            margin: 40px auto;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 25px;
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

        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
            min-height: 420px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 1px solid #f1f5f9;
        }

        .card-success {
            background: #EBF7EE;
            border: 1px solid #34A853;
        }

        .check-icon {
            width: 80px;
            height: 80px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 20px;
        }

        #reader {
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            border: 2px dashed #cbd5e1;
        }

        .info-card {
            background: white;
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .item-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 18px;
            padding: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            background: #fafcf8;
            border-radius: 16px;
        }

        .saldo-text {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin: 10px 0;
        }

        #bonus-added {
            color: #34A853;
            font-size: 20px;
        }

        #success-view {
            display: none;
        }
    </style>
</head>

<body>

    <nav class="w-full bg-white border-b sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 md:px-8 h-20 flex items-center">

            <div class="flex items-center gap-3 flex-1">
                <div class="w-10 h-10 md:w-12 md:h-12">
                    <img src="logosmile.png" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-lg md:text-xl leading-none italic">Trash</span>
                    <span class="text-[#1A5319] font-extrabold text-lg md:text-xl leading-none italic">2Power</span>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-14 font-bold text-gray-500">
                <a href="beranda.php" class="hover:text-green-600 transition">Beranda</a>
                <a href="scan.php" class="nav-active pb-1">Scan</a>
                <a href="reward.php" class="hover:text-green-600 transition">Reward</a>
            </div>

            <div class="flex items-center justify-end flex-1">
                <a href="#" id="btn-logout-trigger" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-red-50 transition group">
                    <img src="logout.png" alt="Logout" class="w-6 h-6 group-hover:opacity-70">
                </a>
            </div>

        </div>
    </nav>

    <main class="main-content">
        <h2 class="text-2xl font-extrabold text-[#34A853] mb-8">Hasil Scan</h2>

        <div class="grid-container">
            <div id="scanner-view" class="card">
                <div id="reader"></div>
                <p id="scan-instruction" class="mt-5 text-gray-500 font-semibold">Scan Barcode Sampahmu Yuk!</p>

                <div class="mt-6 w-full pt-6 border-t border-dashed border-gray-200">
                    <label class="block text-left text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Atau Masukkan Angka Barcode Manual</label>
                    <div class="flex gap-2">
                        <input type="number" id="manual-input" placeholder="Masukkan Angka Barcode Ya..."
                            class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#34A853] text-sm font-medium">
                        <button onclick="submitManual()"
                            class="px-6 py-3 bg-[#34A853] text-white font-bold rounded-xl hover:bg-[#2d9147] transition shadow-md active:scale-95">
                            Kirim
                        </button>
                    </div>
                </div>
            </div>

            <div id="success-view" class="card card-success">
                <div class="check-icon">✓</div>
                <h3 class="text-2xl font-black text-[#1A5319]">Scan Berhasil!</h3>
                <p class="text-green-700 font-medium">Saldo Akan Masuk Ke Akunmu!</p>
            </div>

            <div class="flex flex-col gap-6">
                <div class="info-card">
                    <strong class="text-gray-800 text-lg">Detail sampah</strong>
                    <div class="item-row">
                        <img src="botol.png" alt="Botol" class="w-10 h-10 object-contain" style="filter: invert(89%) sepia(22%) saturate(543%) hue-rotate(167deg) brightness(90%) contrast(92%);">
                        <div>
                            <div class="font-bold text-slate-700">Botol Plastik</div>
                            <div class="text-[#34A853] font-bold text-sm">+ Rp 50</div>
                        </div>
                    </div>
                    <div class="item-row">
                        <img src="kaleng.png" alt="Kaleng" class="w-11 h-11 object-contain" style="filter: invert(81%) sepia(40%) saturate(601%) hue-rotate(305deg) brightness(90%) contrast(102%);">
                        <div>
                            <div class="font-bold text-slate-700">Kaleng Alumunium</div>
                            <div class="text-[#34A853] font-bold text-sm">+ Rp 40</div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <strong class="text-gray-800 text-lg">Total Saldo Kamu</strong>
                    <div class="saldo-text">
                        Rp <span id="current-saldo"><?= number_format($userData['saldo'], 0, ',', '.') ?></span>
                        <span id="bonus-added" class="ml-2 font-bold" style="display:none;">(+ Rp 0)</span>
                    </div>
                    <div class="mt-6">
                        <button class="w-full bg-[#34A853] text-white py-4 rounded-xl font-bold shadow-lg hover:bg-[#2d9147] transition transform active:scale-[0.98]"
                            onclick="location.reload()">
                            Scan Lagi
                        </button>
                    </div>
                </div>
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

    <script>
        let isProcessing = false;
        const html5QrCode = new Html5Qrcode("reader");

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
                "8992761136178"
            ];
            const barcodeKaleng = [
                "8992761112011", "8992761133016", "8992761131012", "8992761111014", "8992761113018", "8996001601004", "8998009010618",
                "8999999000202", "8999999000219", "8999999000226", "8999999000233", "8999988778570", "8999988778594", "8999988778679",
                "8999988778686", "8999988778808", "8999988778815", "8999988778822", "8999988778839", "8999988778846", "8999988778853",
                "8999988778860", "8999988778884", "8999988778976", "8999988778983", "8999988888804", "8999988888811", "8999988888828",
                "8999988888835", "8999988888842", "8999988888859", "8999988888866", "8999988888972", "8999988888989"
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
            } else {
                isProcessing = true;
                instructionText.innerText = "Sampah tidak masuk kategori yang dapat ditukarkan";
                instructionText.classList.replace('text-gray-500', 'text-red-500');

                setTimeout(() => {
                    instructionText.innerText = "Siap menscan barcode sampah...";
                    instructionText.classList.replace('text-red-500', 'text-gray-500');
                    isProcessing = false;
                }, 3000);

                return;
            }

            isProcessing = true;
            let formData = new FormData();
            formData.append('id_user', '1');
            formData.append('id_kategori', kategori);
            formData.append('barcode', decodedText);

            fetch('scan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('scanner-view').style.display = 'none';
                        document.getElementById('success-view').style.display = 'flex';

                        const successTitle = document.querySelector('#success-view h3');
                        if (successTitle) {
                            successTitle.innerText = `Sampah ${namaSampah} Terdeteksi!`;
                        }

                        const bonusSpan = document.getElementById('bonus-added');
                        bonusSpan.innerText = `(+ Rp ${data.harga})`;
                        bonusSpan.style.display = 'inline';
                    } else {
                        alert(data.message);
                        isProcessing = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    isProcessing = false;
                });
        }

        html5QrCode.start({
                facingMode: "environment"
            }, {
                fps: 15,
                qrbox: {
                    width: 250,
                    height: 150
                }
            },
            onScanSuccess
        ).catch(err => {
            console.error("Gagal menjalankan kamera: ", err);
        });

        /* Logout Notification */
        const logoutModal = document.getElementById('logout-modal');
        const successModal = document.getElementById('success-modal');
        const triggerLogout = document.getElementById('btn-logout-trigger');
        const cancelBtn = document.getElementById('btn-cancel-logout');
        const confirmBtn = document.getElementById('btn-confirm-logout');

        // Munculkan modal konfirmasi saat tombol logout di klik
        triggerLogout.onclick = (e) => {
            e.preventDefault();
            logoutModal.classList.replace('hidden', 'flex');
        };

        // Tutup modal jika user memilih batal
        cancelBtn.onclick = () => logoutModal.classList.replace('flex', 'hidden');

        // Jika user yakin logout:
        confirmBtn.onclick = () => {
            // 1. Sembunyikan modal konfirmasi
            logoutModal.classList.replace('flex', 'hidden');
            // 2. Munculkan modal sukses
            successModal.classList.replace('hidden', 'flex');
            // 3. Pindah ke halaman login setelah 1.5 detik
            setTimeout(() => window.location.href = 'login.php', 1500);
        };
    </script>
</body>

</html>