<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$database = "trash2power_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Gunakan session ID yang benar
$id_user = $_SESSION['id_user'] ?? 1;

// Ambil data user terbaru
$result = $conn->query("SELECT * FROM users WHERE id_user='$id_user'");
$user = $result->fetch_assoc();

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $gender = $_POST['gender'];

    // Ambil input password
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'] ?? '';

    // --- LOGIKA FOTO ---
    $foto_new_name = $user['foto_profil'] ?? ''; // Default: gunakan foto lama

    // Cek apakah ada file foto yang diupload dan tidak ada error
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_name = $_FILES['foto']['name'];
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

        // 1. Validasi ekstensi file (Penting untuk keamanan!)
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($foto_ext, $allowed_ext)) {
            $foto_new_name = "profile_" . $id_user . "_" . time() . "." . $foto_ext;

            // 2. Buat folder 'uploads' jika belum ada
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            // 3. Pindahkan file dan cek apakah berhasil
            if (!move_uploaded_file($foto_temp, "uploads/" . $foto_new_name)) {
                $error_msg = "Gagal memindahkan file foto! Cek permission folder 'uploads'.";
                $foto_new_name = $user['foto_profil']; // Kembalikan ke foto lama karena upload gagal
            }
        } else {
            $error_msg = "Format foto ditolak! Hanya boleh JPG, PNG, GIF, atau WEBP.";
        }
    } else if (isset($_FILES['foto']['error']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Tangkap jika file terlalu besar atau ada error upload lainnya
        $error_msg = "Gagal upload file! Kode error server: " . $_FILES['foto']['error'];
    }

    // --- LOGIKA UPDATE DATA & PASSWORD ---
    $update_pw_query = "";

    // Jika user mengisi password saat ini (berarti ingin ganti password)
    if (!empty($current_password)) {
        // 1. Verifikasi apakah password lama cocok dengan hash di DB
        if (password_verify($current_password, $user['password'])) {

            // 2. Jika cocok, cek apakah password baru diisi
            if (!empty($new_password)) {
                $hash_baru = password_hash($new_password, PASSWORD_DEFAULT);
                $update_pw_query = ", password='$hash_baru'";
            } else {
                $error_msg = "Masukkan password baru jika ingin mengubahnya!";
            }
        } else {
            $error_msg = "Password saat ini salah!";
        }
    }

    // Jalankan Update jika tidak ada error
    if (empty($error_msg)) {
        $sql = "UPDATE users SET 
                nama='$nama', 
                email='$email', 
                no_hp='$no_hp', 
                gender='$gender', 
                foto_profil='$foto_new_name' 
                $update_pw_query 
                WHERE id_user='$id_user'";

        if ($conn->query($sql) === TRUE) {
            header("Location: beranda.php?status=success");
            exit();
        } else {
            $error_msg = "Gagal mengupdate profil!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Trash2Power</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f9f4;
            display: flex;
            height: 100vh;
            flex-direction: column;
        }

        .sidebar {
            width: 250px;
            background: white;
            padding: 30px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            display: none;
        }

        #mobile-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 65%;
            height: 100%;
            background: white;
            z-index: 200;
            transition: 0.4s;
            padding: 20px;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.1);
        }

        #mobile-sidebar.active {
            left: 0;
        }

        .tab-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: white;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            z-index: 100;
            border-top: 1px solid #eee;
        }

        .tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 11px;
            color: #888;
            text-decoration: none;
        }

        .tab-item.active {
            color: #34A853;
            font-weight: bold;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 80px;
        }

        .mobile-header {
            background: #34A853;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            /* Nilai default disesuaikan untuk tampilan mobile */
            border-radius: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin: -50px 20px 20px 20px;
        }

        #preview {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #34A853;
            background: white;
            display: block;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        /* Input Wrapper for Password */
        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            width: 100%;
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            width: 20px;
            opacity: 0.6;
        }

        .btn-save {
            background: #34A853;
            color: white;
            padding: 15px;
            width: 100%;
            border-radius: 12px;
            font-weight: bold;
            margin-top: 25px;
            transition: 0.3s;
        }

        #overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            display: none;
            z-index: 150;

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
        }

        /* ========================================= */
        /* MEDIA QUERY: DESKTOP (min-width: 768px)   */
        /* ========================================= */
        @media (min-width: 768px) {
            body {
                flex-direction: row;
            }

            .sidebar {
                display: block;
            }

            .tab-bar {
                display: none;
            }

            .main-content {
                padding-bottom: 0;
            }

            .mobile-header {
                display: none;
            }

            .card {
                margin: 20px;
                border-radius: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="flex items-center gap-2 mb-10">
            <img src="logosmile.png" alt="Logo" class="w-8">
            <h2 class="text-[#34A853] text-2xl font-bold">Trash2Power</h2>
        </div>
        <div class="space-y-2">
            <a href="beranda.php" class="block p-3 text-gray-600 font-semibold rounded-lg hover:bg-gray-50">Beranda</a>
            <a href="#" class="block p-3 bg-[#EBF7EE] text-[#34A853] font-semibold rounded-lg">Edit Profil</a>
            <a href="#" class="btn-logout-trigger block p-3 text-gray-600 font-semibold rounded-lg hover:bg-red-50 hover:text-red-500">Log out</a>
        </div>
    </div>

    <div id="overlay"></div>
    <div id="mobile-sidebar">
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-2">
                <img src="logosmile.png" alt="Logo" class="w-6">
                <h2 class="text-[#34A853] text-lg font-bold">Trash2Power</h2>
            </div>
            <button id="close-menu" class="text-gray-400 text-2xl">✕</button>
        </div>
        <div class="space-y-4">
            <a href="beranda.php" class="block text-gray-600 font-medium">Beranda</a>
            <a href="#" class="block text-[#34A853] font-bold">Edit Profil</a>
            <a href="#" class="btn-logout-trigger block text-red-500 font-medium">Log out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="mobile-header">
            <button id="open-menu"><svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg></button>
            <h1 class="text-white text-lg font-bold">Ubah Profil</h1>
            <div class="w-7"></div>
        </div>

        <div class="card">
            <form method="POST" enctype="multipart/form-data">

                <div class="flex flex-col items-center justify-center text-center mb-8">
                    <?php
                    $foto_db = $user['foto_profil'] ?? '';
                    $path = (!empty($foto_db) && file_exists("uploads/" . $foto_db)) ? "uploads/" . $foto_db : (($user['gender'] == 'Pria') ? 'pria.png' : 'wanita.png');
                    ?>

                    <div class="mb-4">
                        <img id="preview" src="<?= $path ?>" alt="Preview Profil" class="w-24 h-24 rounded-full object-cover border-4 border-[#34A853] shadow-md">
                    </div>

                    <div class="flex items-center justify-center gap-3 w-full">
                        <label for="foto-input" class="cursor-pointer bg-[#EBF7EE] text-[#34A853] px-5 py-2 rounded-full font-bold text-xs hover:bg-[#d4ecd9] transition-all border border-[#34A853]/20 whitespace-nowrap">
                            Choose File
                        </label>

                        <input type="file" name="foto" id="foto-input" accept="image/*" class="hidden">

                        <span id="file-name" class="text-[11px] text-gray-500 truncate max-w-[150px]">No File Choosen</span>
                    </div>
                </div>

                <div class="form-grid mb-6">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600">Gender</label>
                        <select name="gender" onchange="changeAvatar(this.value)" class="w-full p-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:border-[#34A853]">
                            <option value="Wanita" <?= ($user['gender'] == 'Wanita') ? 'selected' : '' ?>>Wanita</option>
                            <option value="Pria" <?= ($user['gender'] == 'Pria') ? 'selected' : '' ?>>Pria</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#34A853]" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#34A853]" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600">Nomor HP</label>
                        <input type="text" name="no_hp" value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#34A853]" required>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600">Password Saat Ini</label>
                        <div class="password-container">
                            <input type="password" id="current_password" name="current_password" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#34A853]" placeholder="Password saat ini">
                            <img src="close-eye.png" id="toggleIcon" class="toggle-password" onclick="togglePasswordVisibility()" alt="Show Password">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600">Ganti Password?</label>
                        <a href="reset-sandi.php" class="flex items-center justify-between w-full p-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-400 text-sm hover:border-[#34A853] transition">
                            <span>Klik untuk Membuat Sandi Baru</span>
                            <svg class="w-4 h-4 text-[#34A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <button type="submit" class="btn-save shadow-md hover:shadow-lg">Simpan Perubahan</button>
            </form>
        </div>
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

    <script>
        // Sidebar & Menu Logic
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const overlay = document.getElementById('overlay');
        document.getElementById('open-menu').onclick = () => {
            mobileSidebar.classList.add('active');
            overlay.style.display = 'block';
        };
        document.getElementById('close-menu').onclick = overlay.onclick = () => {
            mobileSidebar.classList.remove('active');
            overlay.style.display = 'none';
        };

        // Preview image
        document.getElementById('foto-input').onchange = function() {
            const [file] = this.files;
            if (file) {
                // Update Preview Gambar
                document.getElementById('preview').src = URL.createObjectURL(file);
                // Update Teks Nama File di sebelah tombol
                document.getElementById('file-name').textContent = file.name;
                document.getElementById('file-name').classList.replace('text-gray-500', 'text-[#34A853]');
            }
        };

        function changeAvatar(val) {
            if (document.getElementById('foto-input').files.length === 0) {
                document.getElementById('preview').src = (val === 'Pria') ? 'pria.png' : 'wanita.png';
            }
        }

        // Toggle Password Visibility
        function togglePasswordVisibility() {
            const passInput = document.getElementById('current_password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passInput.type === "password") {
                passInput.type = "text";
                toggleIcon.src = "view.png"; // Gambar saat password terlihat
            } else {
                passInput.type = "password";
                toggleIcon.src = "close-eye.png"; // Gambar saat password tersembunyi
            }
        }

        // Logout logic
        // Ambil elemen modal
        const logoutModal = document.getElementById('logout-modal');
        const successModal = document.getElementById('success-modal');

        // Ambil SEMUA tombol pemicu logout (desktop & mobile)
        // Pastikan di HTML, tag <a> atau <button> logout kamu punya class="btn-logout-trigger"
        const triggerLogouts = document.querySelectorAll('.btn-logout-trigger');

        const cancelBtn = document.getElementById('btn-cancel-logout');
        const confirmBtn = document.getElementById('btn-confirm-logout');

        // Munculkan modal KONFIRMASI (logout-modal)
        triggerLogouts.forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                logoutModal.classList.replace('hidden', 'flex');
            };
        });

        // Tombol "Nggak, cuma bercanda!" (Menutup modal konfirmasi)
        cancelBtn.onclick = () => {
            logoutModal.classList.replace('flex', 'hidden');
        };

        // Tombol "Ya, Keluar" (Pemicu success-modal yang kamu kirim)
        confirmBtn.onclick = () => {
            // 1. Sembunyikan modal konfirmasi
            logoutModal.classList.replace('flex', 'hidden');

            // 2. Munculkan modal sukses (ID success-modal sesuai HTML-mu)
            successModal.classList.replace('hidden', 'flex');

            // 3. Pindah halaman setelah 1.5 detik
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1500);
        };
    </script>
</body>

</html>