<?php
// PATH: Asumsi file koneksi ada dua tingkat di atas
include '../../db_connect.php'; 

// Cek Keamanan: Pastikan metode request adalah GET (untuk mengambil data)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(array("message" => "Method tidak diizinkan. Gunakan GET."));
    exit();
}

// Set header JSON
header("Content-Type: application/json; charset=UTF-8");

// Query SQL untuk mengambil data produk dan stok yang tersedia
// Menggunakan LEFT JOIN untuk menggabungkan Products dengan Stock
$query = "
    SELECT 
        p.product_id, 
        p.name, 
        p.sku, 
        p.price, 
        p.unit, 
        p.image_url,
        (s.quantity_available - s.quantity_locked) AS actual_stock 
    FROM Products p
    LEFT JOIN Stock s ON p.product_id = s.product_id
    WHERE (s.quantity_available - s.quantity_locked) > 0 OR s.quantity_available IS NULL
    ORDER BY p.product_id DESC
";

$result = $conn->query($query);
$products = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Format harga ke float untuk perhitungan JS (jika diperlukan)
        $row['price'] = (float) $row['price'];
        $row['actual_stock'] = (int) $row['actual_stock'];
        
        $products[] = $row;
    }
    http_response_code(200);
    echo json_encode(array("success" => true, "products" => $products));
} else {
    // Jika tidak ada produk
    http_response_code(200);
    echo json_encode(array("success" => true, "products" => array(), "message" => "Tidak ada produk ditemukan."));
}

$conn->close();
?>