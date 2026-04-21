<?php
session_start();

// Proteksi login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// 1. KONEKSI DATABASE
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'trash2power_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_warga = $_SESSION['id_user']; // ← DIUBAH, pakai session 

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_ewallet'])) {
    $nomor_tujuan = $_POST['nomor_tujuan'];
    $nominal = $_POST['nominal'];
    $jenis_penukaran = 'ewallet';
    $provider = $_POST['provider']; 
    $harga = $nominal;

    if (empty($nomor_tujuan) || empty($nominal) || empty($provider)) {
        $pesan_error = "Provider, nomor tujuan, dan nominal harus diisi!";
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
            $pesan_sukses = "Penukaran saldo ke $provider berhasil diproses!";
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
    <title>Top Up e-Wallet - Trash2Power</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #f4f7f6; color: #333; }
        
        /* Utility Classes for New Navbar */
        .w-full { width: 100%; }
        .bg-white { background-color: #fff; }
        .border-b { border-bottom: 1px solid #e5e7eb; }
        .sticky { position: sticky; }
        .top-0 { top: 0; }
        .z-50 { z-index: 50; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .max-w-7xl { max-width: 80rem; }
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
        .text-xl { font-size: 1.25rem; }
        .font-extrabold { font-weight: 800; }
        .leading-none { line-height: 1; }

        /* Color Helpers */
        .text-\[\#34A853\] { color: #34A853; }
        .text-\[\#1A5319\] { color: #1A5319; }
        
        /* Main Layout */
        .main-wrapper { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        
        /* Header Section */
        .header-ewallet { background-color: #34A853; border-radius: 16px 16px 0 0; padding: 30px; color: white; margin-bottom: -20px; }
        .btn-back { display: inline-flex; align-items: center; color: white; text-decoration: none; font-weight: 600; font-size: 18px; margin-bottom: 20px; transition: opacity 0.2s; }
        .btn-back:hover { opacity: 0.8; }
        .btn-back i { margin-right: 10px; font-size: 24px; }
        
        /* Search Bar */
        .search-container { position: relative; width: 100%; }
        .search-container i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #999; font-size: 18px; }
        .search-input { width: 100%; padding: 18px 20px 18px 50px; border: none; border-radius: 12px; font-size: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); outline: none; }
        
        /* Card & Content */
        .card-container { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #111; }
        .ewallet-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 25px; text-align: center; }
        .ewallet-item { cursor: pointer; transition: transform 0.2s; padding: 10px; border-radius: 12px; border: 2px solid transparent; }
        .ewallet-item:hover { transform: translateY(-5px); background: #f9f9f9; }
        .ewallet-item.selected { border-color: #34A853; background-color: #f0fdf4; }
        
        .icon-circle { width: 65px; height: 65px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px auto; font-size: 30px; font-weight: bold; color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .ewallet-text { font-size: 15px; font-weight: 600; color: #333; }
        
        .bg-gopay { background: #00A5CF; }
        .bg-ovo { background: #4C3494; }
        .bg-dana { background: #118EEA; }
        .bg-shopee { background: #EE4D2D; }

        .saldo-widget { background: #f0fdf4; border: 1px dashed #34A853; border-radius: 12px; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .topup-form-section { display: none; margin-top: 30px; border-top: 2px dashed #eee; padding-top: 30px; }
        .input-phone { width: 100%; border: 2px solid #eee; border-radius: 12px; padding: 16px 20px; font-size: 18px; font-weight: 600; outline: none; transition: border 0.3s; margin-bottom: 25px; }
        .input-phone:focus { border-color: #34A853; }

        .nominal-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .nominal-card { border: 2px solid #eee; border-radius: 12px; padding: 20px; cursor: pointer; transition: all 0.2s; }
        .nominal-card.selected { border-color: #34A853; background: #34A853; color: white; }

        .btn-submit { width: 100%; padding: 18px; background: #34A853; color: white; border: none; border-radius: 12px; font-size: 18px; font-weight: 700; cursor: pointer; opacity: 0.5; pointer-events: none; }
        .btn-submit.active { opacity: 1; pointer-events: auto; }

        .alert { padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 600; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <nav class="w-full bg-white border-b sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-8 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12">
                    <img src="https://cdn-icons-png.flaticon.com/512/3299/3299935.png" alt="Logo" class="w-full h-full">
                </div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-xl leading-none">Trash</span>
                    <span class="text-[#1A5319] font-extrabold text-xl leading-none">2Power</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-wrapper">
        
        <?php if($pesan_sukses): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= $pesan_sukses ?></div>
        <?php endif; ?>
        <?php if($pesan_error): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= $pesan_error ?></div>
        <?php endif; ?>

        <div class="header-ewallet">
            <a href="reward.php" class="btn-back">
                <i class="bi bi-chevron-left"></i> Kirim ke E-Wallet
            </a>
            <div class="search-container">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari e-wallet..." onkeyup="filterEwallet()">
            </div>
        </div>

        <div class="card-container">
            <h3 class="section-title">Kirim ke penerima baru</h3>
            
            <div class="ewallet-grid" id="ewalletList">
                <div class="ewallet-item" onclick="selectProvider(this, 'GoPay')">
                    <div class="icon-circle bg-gopay"><i class="bi bi-wallet2"></i></div>
                    <div class="ewallet-text">GoPay</div>
                </div>
                <div class="ewallet-item" onclick="selectProvider(this, 'DANA')">
                    <div class="icon-circle bg-dana"><i class="bi bi-wallet"></i></div>
                    <div class="ewallet-text">DANA</div>
                </div>
                <div class="ewallet-item" onclick="selectProvider(this, 'OVO')">
                    <div class="icon-circle bg-ovo" style="font-size:20px;">OVO</div>
                    <div class="ewallet-text">OVO</div>
                </div>
                <div class="ewallet-item" onclick="selectProvider(this, 'ShopeePay')">
                    <div class="icon-circle bg-shopee"><i class="bi bi-bag-fill"></i></div>
                    <div class="ewallet-text">ShopeePay</div>
                </div>
            </div>

            <div class="topup-form-section" id="formTopupSection">
                <div class="saldo-widget">
                    <p style="font-weight: 600; color: #555;">Saldo Trash2Power Kamu</p>
                    <h4 style="color: #1A5319; font-size: 20px; font-weight: 800;">Rp <?= number_format($saldo_saat_ini, 0, ',', '.') ?></h4>
                </div>

                <form action="" method="POST" id="formEwallet">
                    <input type="hidden" name="provider" id="inputProvider">
                    <input type="hidden" name="nominal" id="inputNominal">

                    <div class="input-group">
                        <label id="labelTujuan" style="display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 8px;">Nomor HP / Akun Tujuan</label>
                        <input type="number" name="nomor_tujuan" id="inputNomor" class="input-phone" placeholder="Contoh: 081234567890" onkeyup="checkValidasi()">
                    </div>

                    <h3 class="section-title">Pilih Nominal Top Up</h3>
                    <div class="nominal-grid">
                        <div class="nominal-card" onclick="selectNominal(this, 10000)">
                            <div style="font-size: 22px; font-weight: 800;">Rp 10.000</div>
                            <div style="font-size: 14px;">Potong Saldo Rp 10.000</div>
                        </div>
                        <div class="nominal-card" onclick="selectNominal(this, 20000)">
                            <div style="font-size: 22px; font-weight: 800;">Rp 20.000</div>
                            <div style="font-size: 14px;">Potong Saldo Rp 20.000</div>
                        </div>
                        <div class="nominal-card" onclick="selectNominal(this, 50000)">
                            <div style="font-size: 22px; font-weight: 800;">Rp 50.000</div>
                            <div style="font-size: 14px;">Potong Saldo Rp 50.000</div>
                        </div>
                        <div class="nominal-card" onclick="selectNominal(this, 100000)">
                            <div style="font-size: 22px; font-weight: 800;">Rp 100.000</div>
                            <div style="font-size: 14px;">Potong Saldo Rp 100.000</div>
                        </div>
                    </div>

                    <button type="submit" name="submit_ewallet" class="btn-submit" id="btnSubmit">Konfirmasi Penukaran</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function filterEwallet() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let items = document.getElementsByClassName('ewallet-item');
            for (let i = 0; i < items.length; i++) {
                let text = items[i].getElementsByClassName('ewallet-text')[0].innerText.toLowerCase();
                items[i].style.display = text.indexOf(input) > -1 ? "" : "none";
            }
        }

        function selectProvider(element, providerName) {
            const items = document.querySelectorAll('.ewallet-item');
            items.forEach(item => item.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('inputProvider').value = providerName;
            document.getElementById('formTopupSection').style.display = 'block';
            document.getElementById('labelTujuan').innerText = `Nomor HP Akun ${providerName}`;
            document.getElementById('formTopupSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
            checkValidasi();
        }

        function selectNominal(element, nominalValue) {
            const cards = document.querySelectorAll('.nominal-card');
            cards.forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('inputNominal').value = nominalValue;
            checkValidasi();
        }

        function checkValidasi() {
            const provider = document.getElementById('inputProvider').value;
            const nomor = document.getElementById('inputNomor').value;
            const nominal = document.getElementById('inputNominal').value;
            const btn = document.getElementById('btnSubmit');
            if (provider !== "" && nomor.length >= 10 && nominal !== "") {
                btn.classList.add('active');
                btn.innerText = `Kirim Rp ${parseInt(nominal).toLocaleString('id-ID')} ke ${provider}`;
            } else {
                btn.classList.remove('active');
                btn.innerText = "Lengkapi Data Terlebih Dahulu";
            }
        }
    </script>
</body>
</html>