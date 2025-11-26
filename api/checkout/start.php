<?php
session_start();
include '../../db_connect.php'; // Sesuaikan path ke db_connect.php

header("Content-Type: application/json; charset=UTF-8");

// 1. Cek Login & Method
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak atau sesi habis.']);
    exit();
}

// 2. Ambil Data JSON dari Checkout.php
$input = json_decode(file_get_contents("php://input"), true);

// 3. Validasi Data Keranjang (Server Side)
if (empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Keranjang belanja kosong.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$delivery_address = $conn->real_escape_string($input['delivery_address']);
$delivery_phone = $conn->real_escape_string($input['delivery_phone']);
// Pastikan format angka benar (hapus karakter non-angka jika ada)
$delivery_fee = (float) $input['delivery_fee']; 

// Hitung ulang total di server untuk keamanan (jangan percaya data JS 100%)
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

$total_amount = $subtotal + $delivery_fee;
$dp_amount = $total_amount * 0.30;
$remaining_amount = $total_amount * 0.70;

// --- MULAI TRANSAKSI DATABASE ---
$conn->begin_transaction();

try {
    // A. Masukkan ke Tabel ORDERS
    $sql_order = "INSERT INTO Orders (user_id, total_amount, dp_paid_amount, remaining_70_amount, payment_status, shipping_status, delivery_address, delivery_fee) 
                  VALUES (?, ?, ?, ?, 'DP_PENDING', 'DRAFT', ?, ?)";
    
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param("idddsd", $user_id, $total_amount, $dp_amount, $remaining_amount, $delivery_address, $delivery_fee);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal membuat pesanan utama.");
    }
    
    $new_order_id = $conn->insert_id; // Dapatkan ID Order yang baru dibuat

    // B. Masukkan Item & Update Stok
    $sql_item = "INSERT INTO Order_Items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    // Query untuk Mengunci Stok
    $sql_stock = "UPDATE Stock 
                  SET quantity_available = quantity_available - ?, 
                      quantity_locked = quantity_locked + ? 
                  WHERE product_id = ? AND quantity_available >= ?";
    $stmt_stock = $conn->prepare($sql_stock);

    foreach ($_SESSION['cart'] as $item) {
        $p_id = $item['id'];
        $qty = $item['qty'];
        $price = $item['price'];

        // 1. Simpan Item
        $stmt_item->bind_param("iiid", $new_order_id, $p_id, $qty, $price);
        if (!$stmt_item->execute()) {
            throw new Exception("Gagal menyimpan detail item produk ID: " . $p_id);
        }

        // 2. Kunci Stok (Pindahkan dari Available ke Locked)
        $stmt_stock->bind_param("iiii", $qty, $qty, $p_id, $qty);
        $stmt_stock->execute();

        if ($stmt_stock->affected_rows === 0) {
            // Jika gagal update (misal stok tidak cukup karena dibeli orang lain duluan)
            throw new Exception("Stok tidak mencukupi untuk produk ID: " . $p_id);
        }
    }

    // C. Jika Semua Berhasil, COMMIT Transaksi
    $conn->commit();

    // Kosongkan Keranjang
    unset($_SESSION['cart']);
    unset($_SESSION['totalHargaPenuh']); // Hapus session temp

    // Kirim Respon Sukses
    echo json_encode([
        'success' => true, 
        'message' => 'Pesanan berhasil dibuat!',
        'order_id' => $new_order_id,
        'redirect' => 'dashboard.php' // Nanti bisa diarahkan ke halaman pembayaran (Payment Gateway)
    ]);

} catch (Exception $e) {
    // D. Jika ada error, ROLLBACK (Batalkan semua perubahan)
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Transaksi Gagal: ' . $e->getMessage()]);
}

$conn->close();
?>