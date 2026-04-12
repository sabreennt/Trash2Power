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