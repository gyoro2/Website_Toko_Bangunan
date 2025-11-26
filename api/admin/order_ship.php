<?php
include '../../../db_connect.php';
session_start();

// Cek Keamanan: Hanya Admin/Logistik yang bisa mengakses
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'logistics')) {
    http_response_code(403);
    echo json_encode(array("message" => "Akses ditolak. Hanya Admin/Logistik yang diizinkan."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("message" => "Method tidak diizinkan."));
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->order_id) && !empty($data->new_status)) {
    $order_id = $conn->real_escape_string($data->order_id);
    $input_status = $conn->real_escape_string($data->new_status);
    
    $conn->begin_transaction();
    
    try {
        $payment_update_sql = "";
        $final_shipping_status = $input_status;

        // --- LOGIKA KHUSUS BERDASARKAN PILIHAN ADMIN ---
        
        if ($input_status == 'dp_received') {
            // Jika Admin pilih "DP Diterima":
            // 1. Ubah status pengiriman jadi 'READY_SHIP' (Siap Kirim)
            // 2. Ubah status pembayaran jadi 'DP_PAID' (Lunas DP)
            $final_shipping_status = 'READY_SHIP';
            $payment_update_sql = ", payment_status = 'DP_PAID'";
        
        } elseif ($input_status == 'arrived') {
            // Jika Admin pilih "Tiba di Lokasi":
            // 1. Ubah status pengiriman jadi 'ARRIVED_LOC'
            // 2. Ubah status pembayaran jadi '70_PENDING' (Memicu tagihan pelunasan di user)
            $final_shipping_status = 'ARRIVED_LOC';
            $payment_update_sql = ", payment_status = '70_PENDING'";
        
        } elseif ($input_status == 'shipped') {
            // Jika Admin pilih "Dalam Perjalanan":
            $final_shipping_status = 'SHIPPED';
            // Status pembayaran tidak berubah
        } 
        elseif ($input_status == 'draft') {
             $final_shipping_status = 'DRAFT';
        }

        // Jalankan Query Update
        $update_query = "UPDATE Orders SET shipping_status = '$final_shipping_status' $payment_update_sql WHERE order_id = '$order_id'";
        
        if ($conn->query($update_query) === TRUE) {
            
            // (Opsional) Di sini Anda bisa menambahkan logika kirim Email/WA notifikasi ke user
            
            $conn->commit();
            http_response_code(200);
            echo json_encode(array(
                "message" => "Status berhasil diperbarui! (Status Kirim: $final_shipping_status)"
            ));

        } else {
            throw new Exception("Gagal update status Order: " . $conn->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(array("message" => "Aksi gagal diproses: " . $e->getMessage()));
    }

} else {
    http_response_code(400);
    echo json_encode(array("message" => "ID Pesanan atau Status baru tidak ditemukan."));
}

$conn->close();
?>