<?php
// Ganti detail koneksi sesuai konfigurasi XAMPP Anda
$servername = "localhost";
$username = "root"; // Username default XAMPP
$password = "";     // Password default XAMPP (kosong)
$dbname = "material_db"; // Ganti dengan nama database Anda

// Buat koneksi (tanpa pemeriksaan error JSON di sini)
$conn = new mysqli($servername, $username, $password, $dbname);

// File ini hanya membuat objek koneksi $conn
?>