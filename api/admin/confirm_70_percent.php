<?php
// Koneksi database
include '../../../db_connect.php';
// Asumsi sudah ada logika verifikasi Admin yang aktif di sini

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("message" => "Method tidak diizinkan."));
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->order_id)) {
    $order_id = $conn->real_escape_string($data->order_id);

    // Mulai transaksi database (KRITIS: Melepaskan stok terikat dan finalisasi pembayaran)
    $conn->begin_transaction();
    
    try {
        // 1. Ambil detail item yang dipesan untuk melepaskan stok
        $items_query = "SELECT product_id, quantity FROM Order_Items WHERE order_id = '$order_id'";
        $items_result = $conn->query($items_query);

        if ($items_result->num_rows > 0) {
            
            // 2. Update Stok: Kurangi quantity_locked untuk setiap item
            while ($item = $items_result->fetch_assoc()) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];

                $update_stock_query = "UPDATE Stock SET quantity_locked = quantity_locked - $quantity WHERE product_id = $product_id";
                if ($conn->query($update_stock_query) !== TRUE) {
                    throw new Exception("Gagal melepaskan stok terkunci untuk Product ID $product_id.");
                }
            }

            // 3. Finalisasi Status Pesanan
            $update_order_query = "UPDATE Orders SET payment_status = '70_PAID', shipping_status = 'COMPLETED' WHERE order_id = '$order_id' AND payment_status = '70_PENDING'";
            if ($conn->query($update_order_query) !== TRUE) {
                throw new Exception("Gagal finalisasi status pesanan.");
            }
            
            $conn->commit();
            http_response_code(200);
            echo json_encode(array("message" => "Pelunasan 70% berhasil dikonfirmasi. Stok terkunci telah dibebaskan."));

        } else {
            throw new Exception("Item pesanan tidak ditemukan.");
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(array("message" => "Aksi gagal diproses: " . $e->getMessage()));
    }

} else {
    http_response_code(400);
    echo json_encode(array("message" => "ID Pesanan tidak ditemukan."));
}

$conn->close();
?>