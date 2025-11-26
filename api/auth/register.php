<?php
// Koneksi database
include '../../db_connect.php';

// Pastikan method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("message" => "Method tidak diizinkan."));
    exit();
}

// Ambil data JSON dari request body
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->full_name) && !empty($data->email) && !empty($data->password)) {
    
    $full_name = $conn->real_escape_string($data->full_name);
    $email = $conn->real_escape_string($data->email);
    $phone = $conn->real_escape_string($data->phone);
    $password = $conn->real_escape_string($data->password);

    // 1. Cek Email Unik
    $check_query = "SELECT user_id FROM Users WHERE email = '$email'";
    $result = $conn->query($check_query);
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(array("message" => "Pendaftaran gagal. Email sudah terdaftar."));
        exit();
    }

    // 2. HASH PASSWORD (PENTING: Gunakan password_hash() untuk keamanan nyata)
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 3. Simpan Data
    $query = "INSERT INTO Users (full_name, email, phone, password_hash) VALUES ('$full_name', '$email', '$phone', '$password_hash')";

    if ($conn->query($query) === TRUE) {
        http_response_code(201);
        echo json_encode(array("message" => "Pendaftaran berhasil!"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Pendaftaran gagal: " . $conn->error));
    }

} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}

$conn->close();
?>