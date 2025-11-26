<?php
session_start();
include 'db_connect.php'; 

// 1. Cek Keamanan
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'logistics')) {
    header("Location: login.php"); exit();
}

$order_id = $_GET['id'] ?? null;
$message = ""; // Untuk menampung pesan sukses/gagal

// --- LOGIKA 1: UPDATE STATUS PENGIRIMAN (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $new_status = $_POST['shipping_status'];
    $safe_oid = $conn->real_escape_string($order_id);
    
    $conn->begin_transaction();
    try {
        $payment_update_sql = "";
        
        // Logika Otomatisasi Status Pembayaran
        if ($new_status == 'DP_PAID') { 
            // Admin pilih "DP Diterima" -> Set status kirim READY_SHIP, Bayar DP_PAID
            $final_shipping = 'READY_SHIP';
            $payment_update_sql = ", payment_status = 'DP_PAID'";
        } elseif ($new_status == 'ARRIVED_LOC') {
            // Admin pilih "Tiba di Lokasi" -> Set status kirim ARRIVED_LOC, Bayar 70_PENDING
            $final_shipping = 'ARRIVED_LOC';
            $payment_update_sql = ", payment_status = '70_PENDING'";
        } else {
            $final_shipping = $new_status;
        }

        $sql = "UPDATE Orders SET shipping_status = '$final_shipping' $payment_update_sql WHERE order_id = '$safe_oid'";
        if (!$conn->query($sql)) throw new Exception($conn->error);
        
        $conn->commit();
        $message = "<div class='alert-admin alert-success'>Status berhasil diperbarui menjadi $final_shipping.</div>";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "<div class='alert-admin alert-error'>Gagal: " . $e->getMessage() . "</div>";
    }
}

// --- LOGIKA 2: KONFIRMASI LUNAS 70% (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_lunas') {
    $safe_oid = $conn->real_escape_string($order_id);
    
    $conn->begin_transaction();
    try {
        // 1. Lepaskan Stok Terkunci
        $items = $conn->query("SELECT product_id, quantity FROM Order_Items WHERE order_id = '$safe_oid'");
        while ($item = $items->fetch_assoc()) {
            $pid = $item['product_id'];
            $qty = $item['quantity'];
            $conn->query("UPDATE Stock SET quantity_locked = quantity_locked - $qty WHERE product_id = $pid");
        }

        // 2. Update Status Jadi Selesai
        $conn->query("UPDATE Orders SET payment_status = '70_PAID', shipping_status = 'COMPLETED' WHERE order_id = '$safe_oid'");
        
        $conn->commit();
        $message = "<div class='alert-admin alert-success'>Pesanan Lunas & Selesai! Stok terkunci telah dibebaskan.</div>";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "<div class='alert-admin alert-error'>Gagal: " . $e->getMessage() . "</div>";
    }
}

// --- AMBIL DATA PESANAN (GET) ---
$order = null;
$items = [];

if ($order_id) {
    $safe_id = $conn->real_escape_string($order_id);
    
    // Ambil Detail Order
    $res = $conn->query("SELECT o.*, u.full_name, u.email, u.phone FROM Orders o LEFT JOIN Users u ON o.user_id = u.user_id WHERE o.order_id = '$safe_id'");
    if ($res && $res->num_rows > 0) $order = $res->fetch_assoc();

    // Ambil Item
    if ($order) {
        $res_items = $conn->query("SELECT oi.*, p.name, p.unit FROM Order_Items oi JOIN Products p ON oi.product_id = p.product_id WHERE oi.order_id = '$safe_id'");
        while ($row = $res_items->fetch_assoc()) $items[] = $row;
    }
}

// Helper
function formatCurrency($amount) { return 'Rp ' . number_format($amount, 0, ',', '.'); }
function getStatusBadgeClass($status) {
    if ($status === '70_PENDING') return 'status-70-pending';
    if ($status === 'DP_PAID') return 'status-30-paid';
    if ($status === '70_PAID') return 'status-completed-admin';
    return 'status-pending-admin';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo $order_id; ?> - Admin PUNCAK JAYA PLAVON PVC</title> 
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .alert-admin { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align:center; font-weight:bold; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        
        <aside class="admin-sidebar">
            <div class="admin-logo">MS ADMIN</div>
            <ul class="admin-menu">
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_orders.php" class="admin-active"><i class="fas fa-boxes"></i> Manajemen Pesanan</a></li>
                <li><a href="admin_stock.php"><i class="fas fa-warehouse"></i> Manajemen Stok</a></li>
                <li><a href="admin_reports.php"><i class="fas fa-file-invoice-dollar"></i> Laporan Keuangan</a></li>
                <li class="logout-link"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h2>Detail Pesanan: #<?php echo $order_id ?? '-'; ?></h2>
                <div class="admin-profile">
                    <a href="admin_orders.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </header>

            <?php echo $message; ?>

            <?php if (!$order): ?>
                <div class="alert-admin alert-error">Data pesanan tidak ditemukan.</div>
            <?php else: ?>
            
            <div class="admin-detail-layout">
                <div class="detail-column-left">
                    <div class="detail-box">
                        <h3><i class="fas fa-user"></i> Informasi Pelanggan</h3>
                        <table style="width:100%; border:none;">
                            <tr><td style="width:150px; color:#666;">Nama:</td><td><strong><?php echo $order['full_name']; ?></strong></td></tr>
                            <tr><td style="color:#666;">Telepon:</td><td><?php echo $order['phone']; ?></td></tr>
                            <tr><td style="color:#666;">Email:</td><td><?php echo $order['email']; ?></td></tr>
                            <tr><td style="color:#666;">Alamat Kirim:</td><td><?php echo $order['delivery_address']; ?></td></tr>
                        </table>
                    </div>

                    <div class="detail-box">
                        <h3><i class="fas fa-box"></i> Barang Dipesan</h3>
                        <table class="admin-table full-width-table">
                            <thead>
                                <tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['quantity']; ?> <?php echo $item['unit']; ?></td>
                                    <td><?php echo formatCurrency($item['price_at_purchase']); ?></td>
                                    <td><?php echo formatCurrency($item['quantity'] * $item['price_at_purchase']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="total-row">
                                    <td colspan="3" style="text-align:right;">Ongkos Kirim:</td>
                                    <td><?php echo formatCurrency($order['delivery_fee']); ?></td>
                                </tr>
                                <tr class="grand-total-row" style="background:#e9ecef; font-weight:bold;">
                                    <td colspan="3" style="text-align:right;">TOTAL TAGIHAN:</td>
                                    <td style="font-size:1.2em; color:#007bff;"><?php echo formatCurrency($order['total_amount']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="detail-column-right">
                    
                    <div class="status-box bg-warning" style="padding:20px; border-radius:8px; color:white; margin-bottom:20px; background: linear-gradient(45deg, #f39c12, #e67e22);">
                        <h3 style="border:none; color:white;">STATUS SAAT INI</h3>
                        <p style="margin-bottom:5px;">Pengiriman: <strong><?php echo $order['shipping_status']; ?></strong></p>
                        <p>Pembayaran: <span class="status-badge <?php echo getStatusBadgeClass($order['payment_status']); ?>" style="background: white; color:#333;"><?php echo $order['payment_status']; ?></span></p>
                    </div>

                    <div class="detail-box admin-actions" style="border-top: 4px solid #007bff;">
                        <h3><i class="fas fa-cogs"></i> Aksi Admin</h3>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_status">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Update Status:</label>
                            <select name="shipping_status" class="admin-select" style="width:100%; padding:10px; margin-bottom:10px;">
                                <option value="DRAFT" <?php echo $order['shipping_status'] == 'DRAFT' ? 'selected' : ''; ?>>Draft / Pending</option>
                                <option value="DP_PAID">DP Diterima (Set Lunas DP)</option>
                                <option value="SHIPPED" <?php echo $order['shipping_status'] == 'SHIPPED' ? 'selected' : ''; ?>>Dalam Perjalanan</option>
                                <option value="ARRIVED_LOC" <?php echo $order['shipping_status'] == 'ARRIVED_LOC' ? 'selected' : ''; ?>>Tiba di Lokasi (Tagih 70%)</option>
                            </select>
                            <button type="submit" class="btn-primary full-width-btn" style="width:100%; padding:10px; margin-bottom:15px; cursor:pointer;">
                                <i class="fas fa-sync-alt"></i> Simpan Status
                            </button>
                        </form>
                        
                        <hr>

                        <form method="POST" action="" onsubmit="return confirm('Konfirmasi Pelunasan 70%? Aksi ini tidak dapat dibatalkan.');">
                            <input type="hidden" name="action" value="confirm_lunas">
                            <button type="submit" class="btn-success full-width-btn" 
                                style="width:100%; padding:10px; background:#28a745; color:white; border:none; cursor:pointer;"
                                <?php echo ($order['payment_status'] != '70_PENDING') ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
                                <i class="fas fa-check-circle"></i> Konfirmasi Lunas 70%
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php endif; ?>
        </main>
    </div>
</body>
</html>