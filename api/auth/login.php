<?php
// GANTI PATH INI DENGAN PATH YANG ANDA YAKINI BENAR
include '../../db_connect.php'; 

// Ini akan menampilkan error JSON jika koneksi gagal, BUKAN HTML
if ($conn->connect_error) {
    die(json_encode(["message" => "MySQL Connection Failed! Error: " . $conn->connect_error]));
}

// Jika koneksi sukses, tampilkan sukses
echo json_encode(["message" => "Koneksi Database BERHASIL! Sekarang coba login di form."]);

$conn->close();
?>