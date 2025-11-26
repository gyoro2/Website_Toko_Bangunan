<?php
session_start();
include '../../db_connect.php';

header("Content-Type: application/json; charset=UTF-8");

// 1. Cek Login
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);
$order_id = $conn->real_escape_string($input['order_id']);
$user_id = $_SESSION['user_id'];

// 2. Mulai Transaksi (Penting karena kita mengubah Stok dan Order)
$conn->begin_transaction();

try {
    // A. Cek Status Pesanan (Hanya boleh batal jika DP_PENDING)
    $check_query = "SELECT payment_status FROM Orders WHERE order_id = '$order_id' AND user_id = '$user_id' FOR UPDATE";
    $result = $conn->query($check_query);
    
    if ($result->num_rows === 0) {
        throw new Exception("Pesanan tidak ditemukan.");
    }
    
    $order_data = $result->fetch_assoc();
    if ($order_data['payment_status'] !== 'DP_PENDING') {
        throw new Exception("Pesanan tidak dapat dibatalkan karena sudah diproses/dibayar.");
    }

    // B. Kembalikan Stok (Unlock Stock)
    // Kita harus tahu item apa saja yang ada di order ini
    $items_query = "SELECT product_id, quantity FROM Order_Items WHERE order_id = '$order_id'";
    $items_result = $conn->query($items_query);

    while ($item = $items_result->fetch_assoc()) {
        $p_id = $item['product_id'];
        $qty = $item['quantity'];

        // Kurangi Locked, Tambah Available (Balikin ke gudang)
        $update_stock = "UPDATE Stock 
                         SET quantity_locked = quantity_locked - $qty, 
                             quantity_available = quantity_available + $qty 
                         WHERE product_id = $p_id";
        
        if (!$conn->query($update_stock)) {
            throw new Exception("Gagal mengembalikan stok.");
        }
    }

    // C. Update Status Pesanan jadi CANCELLED
    $cancel_query = "UPDATE Orders SET payment_status = 'CANCELLED' WHERE order_id = '$order_id'";
    if (!$conn->query($cancel_query)) {
        throw new Exception("Gagal update status pesanan.");
    }

    // Commit Transaksi
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dibatalkan.']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>