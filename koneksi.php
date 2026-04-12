<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "trash2power_db"; // Pastikan nama ini sama dengan yang di phpMyAdmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
} 

// Kalau muncul tulisan ini, berarti sudah berhasil!
// echo "Koneksi Berhasil!"; 
?>

// Kegunaan Utamanya:
// Menghubungkan PHP ke MySQL:
// PHP (bahasa kodingan kamu) dan MySQL (database kamu) adalah dua hal yang berbeda. koneksi.php memberitahu PHP: "Eh, databasenya ada di alamat 'localhost', namanya 'trash2power_db', dan cara masuknya lewat sini."

// Syarat Utama Fitur Login:
// Nanti pas user ngetik username dan password di halaman Login, file koneksi.php inilah yang bertugas pergi ke database untuk ngecek: "Ada gak user dengan nama ini? Kalau ada, passwordnya cocok gak?"

// Menyimpan Setoran Sampah:
// Pas warga input berat sampah di website Trash2Power, koneksi.php yang bakal membukakan jalan agar angka berat sampah itu bisa masuk dan tersimpan rapi di tabel setoran.