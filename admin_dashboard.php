<?php
// --- LOGIKA PHP ---
session_start();
include 'db_connect.php'; 

// Cek Keamanan: Pastikan user login sebagai admin/logistics
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'logistics')) {
    header("Location: login.php"); exit();
}

$admin_name = $_SESSION['user_name'] ?? 'Admin'; // Ambil nama dari sesi login
$conn = new mysqli($servername, $username, $password, $dbname);

// Data Default
$metrics = ['new_orders' => 0, 'awaiting_70' => 0, 'overdue_70' => 0, 'total_sales' => 0];
$critical_orders = [];

if (!$conn->connect_error) {
    // 1. METRIK DASHBOARD
    // Pesanan Baru (DP Lunas, siap diproses)
    $q_new = "SELECT COUNT(order_id) FROM Orders WHERE payment_status = 'DP_PAID'";
    $metrics['new_orders'] = $conn->query($q_new)->fetch_row()[0] ?? 0;
    
    // Menunggu Pelunasan 70%
    $q_awaiting = "SELECT COUNT(order_id) FROM Orders WHERE payment_status = '70_PENDING'";
    $metrics['awaiting_70'] = $conn->query($q_awaiting)->fetch_row()[0] ?? 0;
    
    // Total Penjualan (Lunas)
    $q_sales = "SELECT SUM(total_amount) FROM Orders WHERE payment_status = '70_PAID'";
    $metrics['total_sales'] = $conn->query($q_sales)->fetch_row()[0] ?? 0;
    
    // Simulasi Overdue (bisa dikembangkan dengan logika tanggal)
    $metrics['overdue_70'] = 0; 

    // 2. MENGAMBIL PESANAN KRITIS (Butuh Aksi)
    // Mengambil 5 pesanan terakhir yang statusnya 'DP_PAID' (Perlu Dikirim) atau '70_PENDING' (Perlu Ditagih)
    $q_critical = "
        SELECT o.order_id, o.payment_status, o.shipping_status, o.delivery_address 
        FROM Orders o 
        WHERE o.payment_status IN ('DP_PAID', '70_PENDING') 
        ORDER BY o.order_id DESC LIMIT 5
    ";
    $res_critical = $conn->query($q_critical);
    if ($res_critical) {
        while($row = $res_critical->fetch_assoc()) {
            $critical_orders[] = $row;
        }
    }
}

$conn->close();

function formatCurrency($amount) { return 'Rp ' . number_format($amount, 0, ',', '.'); }
function getStatusBadge($status) {
    switch ($status) {
        case '70_PENDING': return '<span class="status-badge status-warning">Menunggu 70%</span>';
        case 'DP_PAID': return '<span class="status-badge status-paid">DP Lunas</span>';
        default: return '<span class="status-badge">' . $status . '</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PUNCAK JAYA PLAVON PVC</title> 
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        
        <aside class="admin-sidebar">
            <div class="admin-logo">MS ADMIN</div>
            <ul class="admin-menu">
                <li><a href="admin_dashboard.php" class="admin-active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_orders.php"><i class="fas fa-boxes"></i> Manajemen Pesanan</a></li>
                <li><a href="admin_stock.php"><i class="fas fa-warehouse"></i> Manajemen Stok</a></li>
                <li><a href="admin_reports.php"><i class="fas fa-file-invoice-dollar"></i> Laporan Keuangan</a></li>
                <li class="logout-link"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h2>Ringkasan Operasional Hari Ini</h2>
                <div class="admin-profile">
                    <span>Admin Logistik (<?php echo htmlspecialchars($admin_name); ?>)</span>
                </div>
            </header>

            <section class="admin-metrics">
                <div class="metric-card bg-blue">
                    <div class="metric-icon"><i class="fas fa-truck-loading"></i></div>
                    <div class="metric-info">
                        <h3>Pesanan Baru (DP Masuk)</h3>
                        <p><?php echo $metrics['new_orders']; ?> Pesanan</p>
                    </div>
                </div>
                <div class="metric-card bg-orange">
                    <div class="metric-icon"><i class="fas fa-hourglass-half"></i></div>
                    <div class="metric-info">
                        <h3>Menunggu Pelunasan 70%</h3>
                        <p><?php echo $metrics['awaiting_70']; ?> Pesanan</p>
                    </div>
                </div>
                <div class="metric-card bg-red">
                    <div class="metric-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="metric-info">
                        <h3>Pelunasan Jatuh Tempo</h3>
                        <p><?php echo $metrics['overdue_70']; ?> Pesanan</p>
                    </div>
                </div>
                <div class="metric-card bg-green">
                    <div class="metric-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="metric-info">
                        <h3>Total Penjualan Lunas</h3>
                        <p><?php echo formatCurrency($metrics['total_sales']); ?></p>
                    </div>
                </div>
            </section>
            
            <section class="admin-critical-orders">
                <h3><i class="fas fa-list-alt"></i> Pesanan Kritis (Perlu Aksi Cepat)</h3>
                
                <table class="admin-table full-width-table">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Status Pembayaran</th>
                            <th>Status Kirim</th>
                            <th>Alamat Proyek</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($critical_orders)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:20px; color:#777;">Tidak ada pesanan kritis saat ini. Kerja bagus!</td></tr>
                        <?php else: ?>
                            <?php foreach ($critical_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo getStatusBadge($order['payment_status']); ?></td>
                                <td><?php echo $order['shipping_status']; ?></td>
                                <td><?php echo substr($order['delivery_address'], 0, 30) . '...'; ?></td>
                                <td>
                                    <?php if ($order['payment_status'] == '70_PENDING'): ?>
                                        <a href="admin_orders_detail.php?id=<?php echo $order['order_id']; ?>" class="btn-warning btn-small" style="text-decoration:none;">Cek Pelunasan</a>
                                    <?php elseif ($order['payment_status'] == 'DP_PAID'): ?>
                                        <a href="admin_orders_detail.php?id=<?php echo $order['order_id']; ?>" class="btn-primary btn-small" style="text-decoration:none;">Proses Kirim</a>
                                    <?php else: ?>
                                        <a href="admin_orders_detail.php?id=<?php echo $order['order_id']; ?>" class="btn-detail-order" style="text-decoration:none;">Detail</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="admin_orders.php" class="btn-view-all">Lihat Semua Pesanan &raquo;</a>
            </section>
            
        </main>
    </div>
</body>
</html>