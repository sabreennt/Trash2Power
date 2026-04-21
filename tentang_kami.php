<?php
session_start();

// Proteksi Login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Tentang Kami - Trash2Power</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
        }

        .nav-active {
            border-bottom: 3px solid #34A853;
            color: #34A853;
        }

        .about-hero {
            /* Membuat background gradient hijau melengkung */
            background: linear-gradient(135deg, #2D5A27 0%, #34A853 50%, #6CC385 100%);
            border-radius: 40px;
            /* Membuat sudut tumpul sesuai gambar */
            padding: 4rem 3rem;
            /* Memberikan ruang dalam (spasi) agar tidak mepet */
            margin: 2rem 0;
            /* Jarak luar box */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .about-overlay {
            /* Menghilangkan batasan lebar agar teks bisa lebih penuh */
            width: 100%;
            color: white;
        }

        /* Memberikan jarak antar paragraf */
        .about-overlay p {
            margin-bottom: 1.5rem;
            text-align: justify;
            /* Agar teks rapi rata kanan-kiri seperti di gambar */
        }

        .content-card {
            background-color: white;
            border-radius: 24px;
            padding: 2.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background-color: #EBF7EE;
            color: #34A853;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
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

<body class="min-h-screen pb-32 md:pb-0 flex flex-col">

    <nav class="w-full bg-white border-b sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 md:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3 md:min-w-[200px]">
                <div class="w-10 h-10 md:w-12 md:h-12"><img src="logosmile.png" alt="Logo"></div>
                <div class="flex flex-col">
                    <span class="text-[#34A853] font-extrabold text-lg md:text-xl leading-none italic">Trash</span>
                    <span class="text-[#1A5319] font-extrabold text-lg md:text-xl leading-none italic">2Power</span>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-14 font-bold text-gray-500">
                <a href="tentang_kami.php" class="nav-active pb-1">Tentang Kami</a>
                <a href="beranda.php" class="hover:text-green-600 transition">Beranda</a>
                <a href="scan.php" class="hover:text-green-600 transition">Scan</a>
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

        <a href="tentang_kami.php" class="flex flex-col items-center gap-1.5">
            <img src="info.png" alt="Tentang Kami" class="w-7 h-7 object-contain" style="filter: invert(48%) sepia(79%) saturate(455%) hue-rotate(86deg) brightness(90%) contrast(90%);">
            <span class="text-xs font-bold text-[#34A853]">Tentang Kami</span>
        </a>
        <a href="beranda.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="beranda.png" alt="Beranda" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Beranda</span>
        </a>
        <a href="scan.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="scan.png" alt="Scan" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Scan</span>
        </a>
        <a href="reward.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="reward.png" alt="Penukaran" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Penukaran</span>
        </a>
        <a href="edit-profile.php" class="flex flex-col items-center gap-1.5 text-gray-400">
            <img src="profile.png" alt="Penukaran" class="w-7 h-7 object-contain opacity-40">
            <span class="text-xs font-bold">Profil</span>
        </a>
    </div>

    <div class="w-full">
        <img src="tentang.png" alt="Tentang Trash2Power" class="w-full h-auto object-cover shadow-md">
    </div>

    <main class="max-w-5xl mx-auto px-6 py-10 flex-grow w-full">

        <div class="about-hero">
            <div class="about-overlay">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-8 leading-tight tracking-tight">
                    Melihat Potensi Bukan Hanya Masalah
                </h1>

                <div class="space-y-6">
                    <p class="text-lg md:text-xl text-green-50 font-medium leading-relaxed">
                        Di <span class="font-bold text-white underline decoration-green-400">Trash2Power</span>, kami melihat sampah bukan sebagai masalah akhir, melainkan sebagai potensi besar yang belum terjamah. Jika sampah adalah masalah, maka insentif adalah kuncinya. Kami menghadirkan sebuah ekosistem daur ulang digital, sebuah platform inovatif yang menjembatani masyarakat dengan rantai daur ulang melalui teknologi.
                    </p>

                    <p class="text-lg md:text-xl text-green-50 font-medium leading-relaxed">
                        Kami percaya bahwa setiap botol plastik dan kaleng aluminium yang kamu miliki memiliki nilai nyata. Oleh karena itu, kami menciptakan solusi untuk memastikan sampah tersebut tidak terbuang sia-sia. Setiap tahun, Indonesia menghasilkan puluhan juta ton sampah, namun angka daur ulang nasional kita masih sangat memprihatinkan. Sebagian besar sampah botol plastik dan kaleng hanya berakhir di TPA, mencemari tanah, atau hanyut ke lautan.
                    </p>
                </div>
            </div>
        </div>

        <h3 class="text-2xl font-extrabold text-gray-800 mb-6 mt-10">Apa yang kami lakukan?</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="content-card mb-0 h-full">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-bold text-gray-800 mb-3">Memberdayakan Masyarakat</h4>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Kami memberikan akses mudah bagi semua orang untuk mendaur ulang sampah anorganik mereka langsung dari rumah.
                </p>
            </div>

            <div class="content-card mb-0 h-full border-t-4 border-t-[#34A853]">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-bold text-gray-800 mb-3">Sampah Jadi Saldo</h4>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Melalui sistem pemindaian barcode yang canggih, kami mengubah setiap botol dan kaleng yang dikumpulkan menjadi saldo digital.
                </p>
            </div>

            <div class="content-card mb-0 h-full">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-bold text-gray-800 mb-3">Mendukung Ekonomi Sirkular</h4>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Menyetorkan sampah melalui Trash2Power berarti membersihkan lingkungan sekaligus menggerakkan roda ekonomi pengelola sampah lokal.
                </p>
            </div>
        </div>

        <div class="bg-[#1A5319] rounded-[32px] p-8 md:p-12 text-center text-white mt-8 shadow-xl">
            <h2 class="text-2xl md:text-3xl font-extrabold mb-4">Mengapa membuang jika bisa menghasilkan?</h2>
            <p class="text-green-100 mb-8 max-w-2xl mx-auto font-medium">
                Mari bergabung dalam gerakan Trash2Power. Karena di tangan yang tepat, sampah bukan lagi beban, melainkan kekuatan untuk masa depan yang lebih hijau.
            </p>
            <div class="inline-block bg-white text-[#1A5319] font-black px-8 py-4 rounded-full text-lg shadow-lg">
                Trash2Power: Scan Sampahnya, Ambil Saldonya!
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

    <footer class="py-8 text-center text-gray-400 text-sm mt-auto mb-16 md:mb-0">
        &copy; 2026 Trash2Power - Sampah Jadi Uang
    </footer>

    <script>
        const logoutModal = document.getElementById('logout-modal');
        const successModal = document.getElementById('success-modal');
        const triggerLogout = document.getElementById('btn-logout-trigger');
        const cancelBtn = document.getElementById('btn-cancel-logout');
        const confirmBtn = document.getElementById('btn-confirm-logout');

        triggerLogout.onclick = (e) => {
            e.preventDefault();
            logoutModal.classList.replace('hidden', 'flex');
        };
        cancelBtn.onclick = () => logoutModal.classList.replace('flex', 'hidden');
        confirmBtn.onclick = () => {
            logoutModal.classList.replace('flex', 'hidden');
            successModal.classList.replace('hidden', 'flex');
            setTimeout(() => window.location.href = 'login.php', 1500);
        };
    </script>
</body>

</html>