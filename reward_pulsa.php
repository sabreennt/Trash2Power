<?php
session_start();

// 1. KONEKSI DATABASE
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'trash2power_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Simulasi user login
$id_warga = 1; 

// 2. AMBIL DATA SALDO USER
$query_user = "SELECT nama, saldo FROM users WHERE id_user = ?";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $id_warga);
$stmt->execute();
$result_user = $stmt->get_result();
$user_data = $result_user->fetch_assoc();
$saldo_saat_ini = $user_data['saldo'] ?? 0;

// 3. LOGIKA PROSES PENUKARAN
$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_penukaran'])) {
    $nomor_tujuan = $_POST['nomor_tujuan'];
    $nominal = $_POST['nominal'];
    $jenis_penukaran = $_POST['jenis_penukaran'];
    $provider = "Telkomsel"; 
    $harga = $_POST['harga'];

    if (empty($nomor_tujuan) || empty($nominal)) {
        $pesan_error = "Nomor tujuan dan nominal harus diisi!";
    } elseif ($saldo_saat_ini < $harga) {
        $pesan_error = "Maaf, saldo Anda tidak mencukupi.";
    } else {
        $conn->begin_transaction();
        try {
            $sisa_saldo = $saldo_saat_ini - $harga;
            $update_saldo = "UPDATE users SET saldo = ? WHERE id_user = ?";
            $stmt_update = $conn->prepare($update_saldo);
            $stmt_update->bind_param("di", $sisa_saldo, $id_warga);
            $stmt_update->execute();

            $status = 'pending';
            $insert_riwayat = "INSERT INTO penukaran_saldo (id_warga, jenis_penukaran, provider, nomor_tujuan, nominal, status) 
                               VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_riwayat);
            $stmt_insert->bind_param("isssds", $id_warga, $jenis_penukaran, $provider, $nomor_tujuan, $nominal, $status);
            $stmt_insert->execute();

            $conn->commit();
            $pesan_sukses = "Penukaran berhasil diproses!";
            $saldo_saat_ini = $sisa_saldo;
        } catch (Exception $e) {
            $conn->rollback();
            $pesan_error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Pulsa - Trash2Power</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfcfc; }
        
        .nav-active { color: #34A853 !important; border-bottom: 3px solid #34A853; }
        
        /* Sidebar/Card Styling */
        .glass-card { 
            background: white; border-radius: 24px; border: 1px solid #f0f0f0; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
        }

        .saldo-gradient {
            background: linear-gradient(135deg, #34A853 0%, #1A5319 100%);
            border-radius: 20px; color: white;
        }

        /* Form Logic Styling */
        .product-item { 
            border: 2px solid #f3f4f6; border-radius: 16px; padding: 20px; 
            cursor: pointer; transition: all 0.2s ease; background: white;
        }
        .product-item:hover { border-color: #34A853; transform: translateY(-2px); }
        .product-item.selected { border-color: #34A853; background-color: #f0fdf4; border-width: 2px; }

        .btn-submit { 
            width: 100%; padding: 18px; background: #e5e7eb; color: #9ca3af; 
            border: none; border-radius: 16px; font-size: 16px; font-weight: 800; 
            transition: all 0.3s ease; cursor: not-allowed;
        }
        .btn-submit.active { 
            background: #34A853; color: white; cursor: pointer;
            box-shadow: 0 10px 20px rgba(52, 168, 83, 0.2);
        }
        .btn-submit.active:hover { background: #2d9147; }
    </style>
</head>
<body>

    <nav class="w-full bg-white border-b sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 md:px-8 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12">
                    <img src="https://cdn-icons-png.flaticon.com/512/3299/3299935.png" alt="Logo" class="w-full h-full">
                </div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-xl leading-none">Trash</span>
                    <span class="text-[#1A5319] font-extrabold text-xl leading-none">2Power</span>
                </div>
            </div>
        
            <div class="w-10 md:hidden"></div> </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">
        
        <?php if($pesan_sukses): ?>
            <div class="alert alert-success border-0 rounded-4 shadow-sm mb-6 p-4">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $pesan_sukses ?>
            </div>
        <?php endif; ?>

        <?php if($pesan_error): ?>
            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-6 p-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $pesan_error ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-4 space-y-6">
                <div class="glass-card p-6 saldo-gradient shadow-lg">
                    <p class="text-green-100 text-sm font-semibold mb-1">Saldo Tersedia</p>
                    <h2 class="text-3xl font-black mb-0">Rp <?= number_format($saldo_saat_ini, 0, ',', '.') ?></h2>
                </div>

                <div class="glass-card p-6">
                    <a href="reward.php" class="flex items-center gap-3 text-gray-600 hover:text-green-600 transition font-bold no-underline">
                        <i class="bi bi-arrow-left-short fs-3"></i>
                        Kembali ke Menu Reward
                    </a>
                </div>
                
                <div class="hidden lg:block glass-card p-6">
                    <h6 class="font-bold mb-3 text-gray-800">Tips Penukaran</h6>
                    <p class="text-sm text-gray-500 leading-relaxed">Pastikan nomor handphone yang Anda masukkan aktif. Proses penukaran memerlukan waktu sekitar 1-5 menit.</p>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="glass-card p-8">
                    <h4 class="font-black text-gray-800 mb-6">Pilih Nominal Pulsa</h4>
                    
                    <form action="" method="POST" id="formPenukaran">
                        <div class="mb-8">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Masukkan Nomor Tujuan</label>
                            <div class="relative">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold">+62</span>
                                <input type="number" name="nomor_tujuan" id="nomorTujuan" 
                                    class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-4 pl-14 pr-6 text-xl font-bold focus:border-[#34A853] focus:bg-white transition outline-none" 
                                    placeholder="812345678xx" required oninput="checkInputs()">
                            </div>
                        </div>

                        <input type="hidden" name="jenis_penukaran" value="pulsa">
                        <input type="hidden" name="nominal" id="inputNominal">
                        <input type="hidden" name="harga" id="inputHarga">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="product-item" onclick="selectProduct(this, 10000, 10000)">
                                <span class="block text-gray-400 text-xs font-bold mb-1">Nominal</span>
                                <div class="flex justify-between items-center">
                                    <h3 class="product-nominal mb-0">10.000</h3>
                                    <span class="product-price">10rb</span>
                                </div>
                            </div>

                            <div class="product-item" onclick="selectProduct(this, 25000, 25000)">
                                <span class="block text-gray-400 text-xs font-bold mb-1">Nominal</span>
                                <div class="flex justify-between items-center">
                                    <h3 class="product-nominal mb-0">25.000</h3>
                                    <span class="product-price">25rb</span>
                                </div>
                            </div>

                            <div class="product-item" onclick="selectProduct(this, 50000, 50000)">
                                <span class="block text-gray-400 text-xs font-bold mb-1">Nominal</span>
                                <div class="flex justify-between items-center">
                                    <h3 class="product-nominal mb-0">50.000</h3>
                                    <span class="product-price">50rb</span>
                                </div>
                            </div>

                            <div class="product-item" onclick="selectProduct(this, 100000, 100000)">
                                <span class="block text-gray-400 text-xs font-bold mb-1">Nominal</span>
                                <div class="flex justify-between items-center">
                                    <h3 class="product-nominal mb-0">100.000</h3>
                                    <span class="product-price">100rb</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="submit_penukaran" class="btn-submit mt-10" id="btnSubmit" disabled>
                            Pilih Nominal
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <script>
        function selectProduct(element, nominal, harga) {
            document.querySelectorAll('.product-item').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            
            document.getElementById('inputNominal').value = nominal;
            document.getElementById('inputHarga').value = harga;
            checkInputs();
        }

        function checkInputs() {
            const nomor = document.getElementById('nomorTujuan').value;
            const nominal = document.getElementById('inputNominal').value;
            const btnSubmit = document.getElementById('btnSubmit');

            if (nomor.length >= 9 && nominal !== "") {
                btnSubmit.classList.add('active');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<i class="bi bi-cart-check-fill me-2"></i> Konfirmasi Penukaran Sekarang`;
            } else {
                btnSubmit.classList.remove('active');
                btnSubmit.disabled = true;
                btnSubmit.textContent = "Pilih Nominal & Nomor";
            }
        }
    </script>
</body>
</html>