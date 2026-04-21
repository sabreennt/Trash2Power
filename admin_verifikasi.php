<?php
session_start();
// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "trash2power_db";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// --- PROSES APPROVAL ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_setoran = mysqli_real_escape_string($koneksi, $_GET['id']);
    $action = $_GET['action'];

    // Ambil detail setoran (jumlah pendapatan & id warga)
    $res = mysqli_query($koneksi, "SELECT id_warga, hasil_pendapatan FROM setoran WHERE id_setoran = '$id_setoran'");
    $data = mysqli_fetch_assoc($res);

    if ($data) {
        $id_warga = $data['id_warga'];
        $nominal = $data['hasil_pendapatan'];

        if ($action === 'setujui') {
            // 1. Update status setoran (Sesuaikan dengan ENUM di phpMyAdmin kamu)
            mysqli_query($koneksi, "UPDATE setoran SET status = 'saldo berhasil masuk' WHERE id_setoran = '$id_setoran'");

            // 2. Tambah saldo ke tabel users
            mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $nominal WHERE id_user = '$id_warga'");

            $msg = "Berhasil! Saldo Rp " . number_format($nominal, 0, ',', '.') . " telah dikirim ke warga.";
        } else if ($action === 'tolak') {
            // 3. Update status jadi dibatalkan
            mysqli_query($koneksi, "UPDATE setoran SET status = 'dibatalkan' WHERE id_setoran = '$id_setoran'");
            $msg = "Setoran ID $id_setoran telah ditolak.";
        }

        header("Location: admin_verifikasi.php?msg=" . urlencode($msg));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Verifikasi Trash2Power</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F9FBFC;
        }
    </style>
</head>

<body class="p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 italic">Admin <span class="text-[#34A853]">Trash2Power</span></h1>
                <p class="text-gray-500 font-medium">Konfirmasi setoran sampah untuk pengiriman saldo.</p>
            </div>
            <div class="flex gap-3">
                <a href="beranda.php" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-2xl text-sm font-bold hover:bg-gray-50 transition">
                    Halaman User
                </a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 mb-8 rounded-[24px] flex items-center gap-3">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="font-bold text-sm"><?= htmlspecialchars($_GET['msg']) ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">Antrean Persetujuan</h2>
                <span class="bg-yellow-100 text-yellow-700 text-[10px] font-black px-3 py-1 rounded-full uppercase">Waiting Action</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] uppercase font-black">
                        <tr>
                            <th class="px-8 py-5">Warga</th>
                            <th class="px-8 py-5">Informasi Sampah</th>
                            <th class="px-8 py-5">Nominal Saldo</th>
                            <th class="px-8 py-5">Waktu Scan</th>
                            <th class="px-8 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php
                        // SINKRONISASI: Menggunakan 'sedang diproses' sesuai database kamu
                        $query_text = "SELECT s.*, k.jenis_sampah, u.nama 
                                       FROM setoran s 
                                       JOIN kategori_sampah k ON s.id_kategori = k.id_kategori 
                                       JOIN users u ON s.id_warga = u.id_user 
                                       WHERE s.status = 'sedang diproses' 
                                       ORDER BY s.tgl_penyetoran ASC";

                        $q = mysqli_query($koneksi, $query_text);

                        if (mysqli_num_rows($q) == 0): ?>
                            <tr>
                                <td colspan="5" class="p-20 text-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" class="w-16 h-16 mx-auto opacity-20 mb-4" alt="">
                                    <p class="text-gray-400 font-bold italic text-sm">Belum ada setoran masuk yang perlu diverifikasi.</p>
                                </td>
                            </tr>
                        <?php endif;

                        while ($r = mysqli_fetch_assoc($q)): ?>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-8 py-6">
                                    <p class="font-bold text-gray-800"><?= $r['nama'] ?></p>
                                    <p class="text-[10px] text-gray-400 font-medium">ID USER: #<?= $r['id_warga'] ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                        <?= $r['jenis_sampah'] ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="font-extrabold text-[#34A853]">Rp <?= number_format($r['hasil_pendapatan'], 0, ',', '.') ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-gray-500 text-xs font-medium"><?= date('d/m/Y', strtotime($r['tgl_penyetoran'])) ?></p>
                                    <p class="text-gray-400 text-[10px]"><?= date('H:i', strtotime($r['tgl_penyetoran'])) ?> WIB</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex gap-2 justify-center">
                                        <a href="?action=setujui&id=<?= $r['id_setoran'] ?>"
                                            onclick="return confirm('Konfirmasi pengiriman saldo?')"
                                            class="bg-[#34A853] text-white px-5 py-2 rounded-xl text-xs font-bold hover:shadow-lg hover:shadow-green-100 transition shadow-sm">
                                            Setujui
                                        </a>
                                        <a href="?action=tolak&id=<?= $r['id_setoran'] ?>"
                                            onclick="return confirm('Tolak setoran ini?')"
                                            class="bg-white border border-red-100 text-red-500 px-5 py-2 rounded-xl text-xs font-bold hover:bg-red-50 transition">
                                            Tolak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-center mt-10 text-gray-300 text-[10px] font-bold uppercase tracking-widest">Trash2Power Management System v1.0</p>
    </div>

</body>

</html>