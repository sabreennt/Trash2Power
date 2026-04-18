<?php
session_start();

// 1. KONEKSI DATABASE (Sesuaikan dengan kredensial Anda)
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'trash2power_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Simulasi user login (Misal id_user = 1)
// Dalam aplikasi nyata, gunakan $_SESSION['id_user']
$id_warga = 1; 

// 2. AMBIL DATA SALDO USER
$query_user = "SELECT nama, saldo FROM users WHERE id_user = ?";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $id_warga);
$stmt->execute();
$result_user = $stmt->get_result();
$user_data = $result_user->fetch_assoc();
$saldo_saat_ini = $user_data['saldo'];

// 3. LOGIKA PROSES PENUKARAN (Jika form disubmit)
$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_penukaran'])) {
    $nomor_tujuan = $_POST['nomor_tujuan'];
    $nominal = $_POST['nominal'];
    $jenis_penukaran = $_POST['jenis_penukaran']; // 'pulsa' atau 'data'
    $provider = "Telkomsel"; // Bisa dibuat dinamis dengan deteksi prefix nomor (JS/PHP)
    $harga = $_POST['harga']; // Harga potong saldo (bisa sama dengan nominal atau berbeda)

    if (empty($nomor_tujuan) || empty($nominal)) {
        $pesan_error = "Nomor tujuan dan nominal harus diisi!";
    } elseif ($saldo_saat_ini < $harga) {
        $pesan_error = "Maaf, saldo Anda tidak mencukupi untuk penukaran ini.";
    } else {
        // Mulai transaksi
        $conn->begin_transaction();
        try {
            // Kurangi saldo user
            $sisa_saldo = $saldo_saat_ini - $harga;
            $update_saldo = "UPDATE users SET saldo = ? WHERE id_user = ?";
            $stmt_update = $conn->prepare($update_saldo);
            $stmt_update->bind_param("di", $sisa_saldo, $id_warga);
            $stmt_update->execute();

            // Insert ke riwayat penukaran
            $status = 'pending'; // Default status
            $insert_riwayat = "INSERT INTO penukaran_saldo (id_warga, jenis_penukaran, provider, nomor_tujuan, nominal, status) 
                               VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_riwayat);
            $stmt_insert->bind_param("isssds", $id_warga, $jenis_penukaran, $provider, $nomor_tujuan, $nominal, $status);
            $stmt_insert->execute();

            $conn->commit();
            $pesan_sukses = "Penukaran berhasil diproses! Saldo Anda telah dipotong.";
            $saldo_saat_ini = $sisa_saldo; // Update variabel untuk tampilan
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f8f9fa; color: #333; }
        
        /* Navbar Styling */
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 50px; background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .nav-brand { font-size: 24px; font-weight: 700; color: #28a745; display: flex; align-items: center; gap: 10px; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { text-decoration: none; color: #666; font-weight: 500; font-size: 16px; }
        .nav-links a.active { color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 5px; }
        .user-profile { width: 40px; height: 40px; background-color: #e9ecef; border-radius: 50%; }

        /* Main Container */
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        
        /* Saldo Card */
        .saldo-card { background: linear-gradient(135deg, #32b555, #228c3d); color: white; padding: 25px 30px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); }
        .saldo-info p { font-size: 14px; margin-bottom: 5px; opacity: 0.9; }
        .saldo-info h2 { font-size: 28px; font-weight: 700; }
        .saldo-icon { font-size: 40px; }

        /* =========================================
           TAMBAHAN CSS UNTUK MENU PILIHAN REWARD
           ========================================= */
        .section-title { font-size: 18px; font-weight: 600; margin-bottom: 15px; color: #444; }
        .pilih-reward-container { display: flex; gap: 20px; margin-bottom: 30px; }
        .reward-card { background: #fff; border: 2px solid #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; flex: 1; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .reward-card:hover { border-color: #28a745; transform: translateY(-3px); box-shadow: 0 6px 15px rgba(40, 167, 69, 0.1); }
        /* Style khusus jika menu sedang aktif dipilih */
        .reward-card.active { border-color: #28a745; background-color: #f2fcf5; }
        .icon-box { font-size: 35px; margin-bottom: 10px; }
        .reward-card p { font-size: 16px; font-weight: 600; color: #333; }
        .link-reward { text-decoration: none; color: inherit; flex: 1; display: block; }

        /* Alert Messages */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 500; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Recharge Section */
        .recharge-container { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.03); }
        
        /* Promo Banner Placeholder */
        .promo-banner { width: 100%; height: 120px; background: linear-gradient(90deg, #108ee9, #5db2ff); border-radius: 10px; margin-bottom: 25px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: 700; text-shadow: 1px 1px 2px rgba(0,0,0,0.2); }

        /* Phone Input */
        .input-group { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; transition: border 0.3s; }
        .input-group:focus-within { border-color: #108ee9; }
        .input-wrapper { flex: 1; }
        .input-wrapper label { display: block; font-size: 12px; color: #888; margin-bottom: 5px; font-weight: 600; }
        .input-wrapper input { width: 100%; border: none; font-size: 18px; font-weight: 600; outline: none; color: #333; }
        
        /* Tabs */
        .tabs { display: flex; border-bottom: 2px solid #f0f0f0; margin-bottom: 25px; }
        .tab-btn { flex: 1; padding: 15px; text-align: center; cursor: pointer; font-size: 16px; font-weight: 600; color: #888; border-bottom: 3px solid transparent; transition: all 0.3s; }
        .tab-btn.active { color: #108ee9; border-bottom-color: #108ee9; }

        /* Product Grid */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
        .product-card-item { border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; cursor: pointer; position: relative; transition: all 0.2s ease; background: #fff; text-align: left; }
        .product-card-item:hover { border-color: #108ee9; box-shadow: 0 4px 12px rgba(16, 142, 233, 0.15); }
        .product-card-item.selected { border-color: #108ee9; background-color: #f0f8ff; border-width: 2px; }
        .product-nominal { font-size: 24px; font-weight: 700; color: #333; margin-bottom: 5px; display: flex; align-items: baseline; }
        .product-nominal span { font-size: 16px; margin-left: 2px; }
        .product-price { font-size: 14px; color: #108ee9; font-weight: 600; }
        
        /* Badge */
        .badge { position: absolute; top: -10px; right: -10px; background: #ff9800; color: #fff; font-size: 11px; font-weight: bold; padding: 5px 10px; border-radius: 12px 12px 12px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

        /* Submit Button */
        .btn-submit { width: 100%; padding: 15px; background: #108ee9; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; margin-top: 30px; cursor: pointer; transition: background 0.3s; opacity: 0.5; pointer-events: none; }
        .btn-submit.active { opacity: 1; pointer-events: auto; }
        .btn-submit.active:hover { background: #0e7bce; }

    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">
            <span style="font-size:28px;">♻️</span> Trash2Power
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
            <div class="saldo-icon">💰</div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
    <a href="reward.php" style="text-decoration: none; font-size: 20px; color: #666;">⬅️</a>
    <h3 class="section-title" style="margin-bottom: 0;">Pilih Kategori Reward</h3>
</div>
        
            <form action="" method="POST" id="formPenukaran">
                <div class="input-group">
                    <div class="input-wrapper">
                        <label>Phone Number</label>
                        <input type="text" name="nomor_tujuan" id="nomorTujuan" placeholder="Contoh: 081234567890" required onkeyup="checkInputs()">
                    </div>
                    
                </div>

                <div class="tabs">
                    <div class="tab-btn active" onclick="switchTab('pulsa')">Pulsa</div>
    
                </div>

                <input type="hidden" name="jenis_penukaran" id="inputJenis" value="pulsa">
                <input type="hidden" name="nominal" id="inputNominal" value="">
                <input type="hidden" name="harga" id="inputHarga" value="">

                <div class="product-grid" id="gridPulsa">
                    <div class="product-card-item" onclick="selectProduct(this, 20000, 20000)">
                        <div class="product-nominal">20<span>K</span></div>
                        <div class="product-price">Price Rp20.000</div>
                    </div>
                    <div class="product-card-item" onclick="selectProduct(this, 25000, 25000)">
                        
                        <div class="product-nominal">25<span>K</span></div>
                        <div class="product-price">Price Rp25.000</div>
                    </div>
                    <div class="product-card-item" onclick="selectProduct(this, 40000, 40000)">
                      
                        <div class="product-nominal">40<span>K</span></div>
                        <div class="product-price">Price Rp40.000</div>
                    </div>
                    <div class="product-card-item" onclick="selectProduct(this, 50000, 50000)">
                        
                        <div class="product-nominal">50<span>K</span></div>
                        <div class="product-price">Price Rp50.000</div>
                    </div>
                    <div class="product-card-item" onclick="selectProduct(this, 100000, 100000)">
                      
                        <div class="product-nominal">100<span>K</span></div>
                        <div class="product-price">Price Rp100.000</div>
                    </div>
                    <div class="product-card-item" onclick="selectProduct(this, 150000, 150000)">
                      
                        <div class="product-nominal">150<span>K</span></div>
                        <div class="product-price">Price Rp150.000</div>
                    </div>
                </div>

                <button type="submit" name="submit_penukaran" class="btn-submit" id="btnSubmit">Beli Sekarang</button>
            </form>
        </div>
    </div>

    <script>
        // Logika Tab
        function switchTab(type) {
            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('inputJenis').value = type;
            
            // Hapus pilihan sebelumnya saat ganti tab
            const cards = document.querySelectorAll('.product-card-item');
            cards.forEach(c => c.classList.remove('selected'));
            document.getElementById('inputNominal').value = '';
            document.getElementById('inputHarga').value = '';
            checkInputs();
        }

        // Logika Pilih Nominal
        function selectProduct(element, nominal, harga) {
            // Remove selected class from all cards
            const cards = document.querySelectorAll('.product-card-item');
            cards.forEach(card => card.classList.remove('selected'));
            
            // Add selected class to clicked card
            element.classList.add('selected');
            
            // Set hidden values for form
            document.getElementById('inputNominal').value = nominal;
            document.getElementById('inputHarga').value = harga;
            
            checkInputs();
        }

        // Validasi Tombol Beli
        function checkInputs() {
            const nomor = document.getElementById('nomorTujuan').value;
            const nominal = document.getElementById('inputNominal').value;
            const btnSubmit = document.getElementById('btnSubmit');

            if (nomor.length >= 10 && nominal !== "") {
                btnSubmit.classList.add('active');
                btnSubmit.textContent = `Beli - Rp ${parseInt(nominal).toLocaleString('id-ID')}`;
            } else {
                btnSubmit.classList.remove('active');
                btnSubmit.textContent = "Pilih Produk & Masukkan Nomor";
            }
        }
    </script>
</body>
</html>