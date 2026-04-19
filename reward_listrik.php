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
$saldo_saat_ini = $user_data['saldo'];

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
    } elseif (!preg_match('/^[0-9]{8}$/', $nomor_tujuan)) {
        $pesan_error = "Maaf, ID Pelanggan harus berjumlah tepat 8 angka!";
    } elseif ($saldo_saat_ini < $harga) {
        $pesan_error = "Maaf, saldo Anda tidak mencukupi untuk penukaran ini.";
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
            $pesan_sukses = "Penukaran berhasil diproses! Saldo Anda telah dipotong.";
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
    <title>Trash2Power - Mobile Recharge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f8f9fa; color: #333; }
        
        /* CSS UNTUK NAVBAR BARU */
        .w-full { width: 100%; }
        .bg-white { background-color: #fff; }
        .border-b { border-bottom: 1px solid #e5e7eb; }
        .sticky { position: sticky; }
        .top-0 { top: 0; }
        .z-50 { z-index: 50; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .max-w-7xl { max-width: 1280px; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .px-8 { padding-left: 2rem; padding-right: 2rem; }
        .h-20 { height: 5rem; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        .gap-3 { gap: 0.75rem; }
        .w-12 { width: 3rem; }
        .h-12 { height: 3rem; }
        .w-12 img { width: 100%; height: auto; }
        .text-xl { font-size: 1.25rem; }
        .font-extrabold { font-weight: 800; }
        .leading-none { line-height: 1; }
        .text-\[#34A853\] { color: #34A853; }
        .text-\[#1A5319\] { color: #1A5319; }

        /* Main Container */
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        
        /* Saldo Card */
        .saldo-card { background: linear-gradient(135deg, #34A853, #1A5319); color: white; padding: 25px 30px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(52, 168, 83, 0.3); }
        .saldo-info h2 { font-size: 28px; font-weight: 700; }

        .section-title { font-size: 18px; font-weight: 600; margin-bottom: 15px; color: #444; }

        /* Alert Messages */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 500; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Recharge Form */
        .recharge-container { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.03); }
        .input-group { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px 20px; display: flex; align-items: center; margin-bottom: 25px; }
        .input-wrapper label { display: block; font-size: 12px; color: #888; margin-bottom: 5px; font-weight: 600; }
        .input-wrapper input { width: 100%; border: none; font-size: 18px; font-weight: 600; outline: none; }
        
        .tabs { display: flex; border-bottom: 2px solid #f0f0f0; margin-bottom: 25px; }
        .tab-btn { flex: 1; padding: 15px; text-align: center; cursor: pointer; font-weight: 600; color: #888; border-bottom: 3px solid transparent; }
        .tab-btn.active { color: #34A853; border-bottom-color: #34A853; }

        /* Product Grid */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
        .product-card-item { border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; cursor: pointer; transition: all 0.2s ease; background: #fff; }
        .product-card-item:hover { border-color: #34A853; box-shadow: 0 4px 12px rgba(52, 168, 83, 0.15); }
        .product-card-item.selected { border-color: #34A853; background-color: #f2fcf5; border-width: 2px; }
        .product-nominal { font-size: 24px; font-weight: 700; color: #333; }
        .product-price { font-size: 14px; color: #34A853; font-weight: 600; }

        .btn-submit { width: 100%; padding: 15px; background: #34A853; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; margin-top: 30px; cursor: pointer; opacity: 0.5; pointer-events: none; }
        .btn-submit.active { opacity: 1; pointer-events: auto; }
    </style>
</head>
<body>

    <nav class="w-full bg-white border-b sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-8 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12">
                    <img src="https://cdn-icons-png.flaticon.com/512/3299/3299935.png" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-xl leading-none">Trash</span>
                    <span class="text-[#1A5319] font-extrabold text-xl leading-none">2Power</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <?php if($pesan_sukses): ?>
            <div class="alert alert-success"><?= $pesan_sukses ?></div>
        <?php endif; ?>
        <?php if($pesan_error): ?>
            <div class="alert alert-danger"><?= $pesan_error ?></div>
        <?php endif; ?>

        <div class="saldo-card">
            <div class="saldo-info">
                <p>Saldo Kamu</p>
                <h2>Rp <?= number_format($saldo_saat_ini, 0, ',', '.') ?></h2>
            </div>
            <div class="saldo-icon" style="font-size: 40px;">💰</div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
            <a href="reward.php" style="text-decoration: none; font-size: 20px; color: #666;">⬅️</a>
            <h3 class="section-title" style="margin-bottom: 0;">Isi Token Listrik</h3>
        </div>
        
        <div class="recharge-container">
            <form action="" method="POST" id="formPenukaran">
                <div class="input-group">
                    <div class="input-wrapper" style="flex: 1;">
                        <label>No Meter/ID Pelanggan (8 Angka)</label>
                        <input type="text" 
                               name="nomor_tujuan" 
                               id="nomorTujuan" 
                               placeholder="Contoh: 12345678" 
                               required 
                               maxlength="8" 
                               inputmode="numeric" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, ''); checkInputs()">
                    </div>
                </div>

                <div class="tabs">
                    <div class="tab-btn active">Listrik PLN</div>
                </div>

                <input type="hidden" name="jenis_penukaran" id="inputJenis" value="Listrik">
                <input type="hidden" name="nominal" id="inputNominal" value="">
                <input type="hidden" name="harga" id="inputHarga" value="">

                <div class="product-grid" id="gridPulsa">
                    <div class="product-card-item" onclick="selectProduct(this, 20000, 20000)">
                        <div class="product-nominal">20<span>K</span></div>
                        <div class="product-price">Harga Rp20.000</div>
                    </div>
                    <div class="product-card-item" onclick="selectProduct(this, 50000, 50000)">
                        <div class="product-nominal">50<span>K</span></div>
                        <div class="product-price">Harga Rp50.000</div>
                    </div>
                    <div class="product-card-item" onclick="selectProduct(this, 100000, 100000)">
                        <div class="product-nominal">100<span>K</span></div>
                        <div class="product-price">Harga Rp100.000</div>
                    </div>
                    <div class="product-card-item" onclick="selectProduct(this, 200000, 200000)">
                        <div class="product-nominal">200<span>K</span></div>
                        <div class="product-price">Harga Rp200.000</div>
                    </div>
                </div>

                <button type="submit" name="submit_penukaran" class="btn-submit" id="btnSubmit">Konfirmasi Pembelian</button>
            </form>
        </div>
    </div>

    <script>
        function selectProduct(element, nominal, harga) {
            const cards = document.querySelectorAll('.product-card-item');
            cards.forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            
            document.getElementById('inputNominal').value = nominal;
            document.getElementById('inputHarga').value = harga;
            
            checkInputs();
        }

        function checkInputs() {
            const nomor = document.getElementById('nomorTujuan').value;
            const nominal = document.getElementById('inputNominal').value;
            const btnSubmit = document.getElementById('btnSubmit');

            if (nomor.length === 8 && nominal !== "") {
                btnSubmit.classList.add('active');
                btnSubmit.textContent = `Beli Sekarang - Rp ${parseInt(nominal).toLocaleString('id-ID')}`;
            } else {
                btnSubmit.classList.remove('active');
                if (nomor.length !== 8 && nomor.length > 0) {
                    btnSubmit.textContent = "ID Pelanggan harus 8 angka";
                } else {
                    btnSubmit.textContent = "Lengkapi Data Diatas";
                }
            }
        }
    </script>
</body>
</html>