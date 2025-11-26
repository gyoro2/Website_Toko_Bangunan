<?php
session_start();
include 'db_connect.php'; 

// 1. Cek Keamanan
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'logistics')) {
    header("Location: login.php"); exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$message = ""; 

// --- 2. LOGIKA HAPUS PESANAN (DIPERBAIKI) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = $conn->real_escape_string($_GET['id']);
    $filter_redirect = $_GET['filter'] ?? 'all'; // Supaya balik ke tab filter yang sama
    
    // Cek Status Dulu
    $check_q = "SELECT payment_status FROM Orders WHERE order_id = '$delete_id'";
    $res = $conn->query($check_q);
    
    if ($res && $res->num_rows > 0) {
        $status = $res->fetch_assoc()['payment_status'];
        
        // Hanya boleh hapus jika status CANCELLED atau 70_PAID (Selesai)
        if ($status == 'CANCELLED' || $status == '70_PAID') {
            
            // Mulai Transaksi agar aman
            $conn->begin_transaction();
            try {
                // LANGKAH KRITIS: Hapus Item Pesanan (Child) DULU
                $conn->query("DELETE FROM Order_Items WHERE order_id = '$delete_id'");
                
                // BARU Hapus Pesanan Utama (Parent)
                $conn->query("DELETE FROM Orders WHERE order_id = '$delete_id'");
                
                $conn->commit();
                $message = "<div class='alert-admin alert-success'>Pesanan ID #$delete_id berhasil dihapus permanen.</div>";
            } catch (Exception $e) {
                $conn->rollback();
                $message = "<div class='alert-admin alert-error'>Gagal menghapus: " . $e->getMessage() . "</div>";
            }
        } else {
            $message = "<div class='alert-admin alert-error'>Hanya pesanan 'Dibatalkan' atau 'Selesai' yang boleh dihapus.</div>";
        }
    }
    // Jangan redirect header() agar pesan $message terlihat
}

// --- 3. LOGIKA FILTER (DIPERBAIKI) ---
$filter = $_GET['filter'] ?? 'all';
$where_clause = "";

// Pastikan nilai ENUM di sini SAMA PERSIS dengan yang ada di database
switch ($filter) {
    case 'dp_pending': 
        $where_clause = "WHERE o.payment_status = 'DP_PENDING'"; 
        break;
    case 'process': 
        // Menampilkan yang sudah DP Lunas, Sedang Kirim, atau Tiba di Lokasi
        $where_clause = "WHERE o.payment_status = 'DP_PAID' OR o.payment_status = '70_PENDING' OR o.shipping_status = 'SHIPPED' OR o.shipping_status = 'ARRIVED_LOC'"; 
        break;
    case 'completed': 
        $where_clause = "WHERE o.payment_status = '70_PAID'"; 
        break;
    case 'cancelled': 
        $where_clause = "WHERE o.payment_status = 'CANCELLED'"; 
        break;
    default: 
        $where_clause = ""; // Tampilkan Semua
}

$orders = [];
// Query SQL dengan Filter
$query = "
    SELECT o.*, u.full_name 
    FROM Orders o
    LEFT JOIN Users u ON o.user_id = u.user_id
    $where_clause
    ORDER BY o.order_date DESC
";

$result = $conn->query($query);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$conn->close();

// Helper Functions
function getStatusBadge($status) {
    switch ($status) {
        case '70_PENDING': return '<span class="status-badge status-warning">Menunggu Pelunasan</span>';
        case 'DP_PAID': return '<span class="status-badge status-paid">DP Lunas (Proses)</span>';
        case 'DP_PENDING': return '<span class="status-badge status-pending">Menunggu DP</span>';
        case '70_PAID': return '<span class="status-badge status-completed-admin">Selesai</span>';
        case 'CANCELLED': return '<span class="status-badge" style="background:#dc3545; color:white;">Dibatalkan</span>';
        default: return '<span class="status-badge">' . $status . '</span>';
    }
}
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan - Admin PUNCAK JAYA PLAVON PVC</title> 
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styling Filter Tab */
        .filter-bar-container { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-card {
            flex: 1; min-width: 140px; background: white; padding: 15px; border-radius: 8px;
            text-align: center; text-decoration: none; color: #555; border: 1px solid #eee;
            transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .filter-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .filter-card.active { background-color: #007bff; color: white; border-color: #007bff; }
        .filter-card.active i { color: white; }
        .filter-card i { font-size: 1.5em; margin-bottom: 5px; color: #007bff; }
        
        /* Alert Styles */
        .alert-admin { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align:center; }
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
                <h2>Manajemen Semua Pesanan</h2>
            </header>

            <?php echo $message; ?>
            
            <div class="filter-bar-container">
                <a href="admin_orders.php?filter=all" class="filter-card <?php echo $filter == 'all' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> <span>Semua</span>
                </a>
                <a href="admin_orders.php?filter=dp_pending" class="filter-card <?php echo $filter == 'dp_pending' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> <span>Menunggu DP</span>
                </a>
                <a href="admin_orders.php?filter=process" class="filter-card <?php echo $filter == 'process' ? 'active' : ''; ?>">
                    <i class="fas fa-truck-loading"></i> <span>Proses / Kirim</span>
                </a>
                <a href="admin_orders.php?filter=completed" class="filter-card <?php echo $filter == 'completed' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i> <span>Selesai & Lunas</span>
                </a>
                <a href="admin_orders.php?filter=cancelled" class="filter-card <?php echo $filter == 'cancelled' ? 'active' : ''; ?>" style="border-color: #dc3545; color: #dc3545;">
                    <i class="fas fa-times-circle"></i> <span>Dibatalkan</span>
                </a>
            </div>

            <section class="admin-order-list">
                <h3>Data Pesanan (<?php echo count($orders); ?>)</h3>
                
                <table class="admin-table full-width-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status Bayar</th>
                            <th>Status Kirim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 30px; color: #777;">Tidak ada pesanan dalam kategori ini.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['full_name'] ?? 'User Dihapus'; ?></td>
                            <td><?php echo formatCurrency($order['total_amount']); ?></td>
                            <td><?php echo getStatusBadge($order['payment_status']); ?></td>
                            <td><?php echo $order['shipping_status']; ?></td>
                            <td>
                                <a href="admin_orders_detail.php?id=<?php echo $order['order_id']; ?>" class="btn-detail-order" title="Lihat Detail"><i class="fas fa-info-circle"></i></a>
                                
                                <?php if ($order['payment_status'] == 'CANCELLED' || $order['payment_status'] == '70_PAID'): ?>
                                    <a href="admin_orders.php?action=delete&id=<?php echo $order['order_id']; ?>&filter=<?php echo $filter; ?>" 
                                       class="btn-detail-order" 
                                       style="background-color:#dc3545;" 
                                       onclick="return confirm('Yakin ingin MENGHAPUS PERMANEN pesanan ini? Data tidak bisa dikembalikan.')" 
                                       title="Hapus Permanen">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>