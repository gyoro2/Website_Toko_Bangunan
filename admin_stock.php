<?php
session_start();
include 'db_connect.php'; 

// 1. Cek Keamanan
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'logistics')) {
    header("Location: login.php"); exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Koneksi Gagal: " . $conn->connect_error); }

// --- LOGIKA PAGINATION ---
$limit = 20; // Batas 20 item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Hitung Total Data (Untuk menentukan jumlah halaman)
$total_result = $conn->query("SELECT COUNT(*) as total FROM Products");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$stock_data = [];

// --- QUERY UTAMA (FIXED SORTING) ---
// ORDER BY LENGTH(p.sku) ASC, p.sku ASC 
// -> Ini trik agar "2" muncul sebelum "10" (Natural Sort)
$query = "
    SELECT 
        p.product_id, p.name, p.sku, p.unit, 
        s.quantity_available, s.quantity_locked,
        (s.quantity_available - s.quantity_locked) AS actual_stock_for_sale
    FROM Products p
    LEFT JOIN Stock s ON p.product_id = s.product_id
    ORDER BY LENGTH(p.sku) ASC, p.sku ASC 
    LIMIT $start, $limit
";

$result = $conn->query($query);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $stock_data[] = $row;
    }
}

$conn->close();

function getStockStatus($available, $locked) {
    $actual = $available - $locked;
    if ($actual < 10) return '<span class="status-badge status-critical">Kritis!</span>';
    if ($actual <= 50) return '<span class="status-badge status-warning">Peringatan</span>';
    return '<span class="status-badge status-safe">Aman</span>';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok - Admin PUNCAK JAYA PLAVON PVC</title> 
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS Pagination Admin */
        .pagination-container {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        .page-link {
            padding: 10px 15px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #555;
            background-color: white;
            border-radius: 6px;
            transition: 0.2s;
            font-size: 0.95em;
        }
        .page-link:hover {
            background-color: #f1f1f1;
            border-color: #bbb;
        }
        .page-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .page-info {
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>
<body class="admin-body">

    <div class="admin-wrapper">
        
        <aside class="admin-sidebar">
            <div class="admin-logo">MS ADMIN</div>
            <ul class="admin-menu">
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_orders.php"><i class="fas fa-boxes"></i> Manajemen Pesanan</a></li>
                <li><a href="admin_stock.php" class="admin-active"><i class="fas fa-warehouse"></i> Manajemen Stok</a></li>
                <li><a href="admin_reports.php"><i class="fas fa-file-invoice-dollar"></i> Laporan Keuangan</a></li>
                <li class="logout-link"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h2>Manajemen Ketersediaan Stok Material</h2>
                <div class="admin-profile"><span>Admin Gudang</span></div>
            </header>

            <section class="admin-stock-filter">
                <div class="filter-box">
                    <label>Filter Kategori:</label>
                    <select class="admin-select-filter">
                        <option>Semua Kategori</option>
                    </select>
                </div>
                <div class="filter-box">
                    <label>Status Stok:</label>
                    <select class="admin-select-filter">
                        <option>Semua</option>
                    </select>
                </div>
                <a href="admin_product_create.php" class="btn-primary" style="text-decoration:none; padding:10px 15px; border-radius:4px; color:white; display:inline-block;">
                    <i class="fas fa-plus"></i> Tambah Material Baru
                </a>
            </section>
            
            <section class="admin-stock-list detail-box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                    <h3><i class="fas fa-list-ul"></i> Daftar Stok Gudang</h3>
                    <span style="font-size:0.9em; color:#666;">Total Item: <strong><?php echo $total_rows; ?></strong></span>
                </div>
                
                <table class="admin-table full-width-table stock-table">
                    <thead>
                        <tr>
                            <th>SKU <i class="fas fa-sort-numeric-down" style="font-size:0.8em; color:#aaa;"></i></th>
                            <th>Nama Material</th>
                            <th>Satuan</th>
                            <th style="text-align: center;">Stok Tersedia</th>
                            <th style="text-align: center;">Stok Terkunci</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stock_data)): ?>
                            <tr><td colspan="7" style="text-align:center; padding: 30px;">Tidak ada data stok ditemukan.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach ($stock_data as $item): ?>
                        <tr>
                            <td style="font-weight:bold; color:#555;"><?php echo $item['sku']; ?></td>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['unit']; ?></td>
                            
                            <td style="text-align: center;">
                                <span class="<?php echo ($item['actual_stock_for_sale'] < 50) ? 'stock-warning' : 'stock-safe'; ?>" style="font-weight: bold; font-size: 1.1em;">
                                    <?php echo $item['actual_stock_for_sale']; ?>
                                </span>
                            </td>
                            
                            <td style="text-align: center;">
                                <span style="font-weight: 800; font-size: 1.1em; color: <?php echo $item['quantity_locked'] > 0 ? '#dc3545' : '#ccc'; ?>;">
                                    <?php echo $item['quantity_locked']; ?>
                                </span>
                            </td>
                            
                            <td style="text-align: center;"><?php echo getStockStatus($item['quantity_available'], $item['quantity_locked']); ?></td>
                            
                            <td style="text-align: center;">
                                <a href="admin_product_detail.php?id=<?php echo $item['product_id']; ?>" class="btn-primary btn-small" style="text-decoration:none;">Update Stok</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="page-link">&laquo; Prev</a>
                        <?php endif; ?>

                        <?php 
                        // Batasi tampilan angka halaman agar tidak terlalu panjang
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);

                        if ($start_page > 1) { 
                            echo '<a href="?page=1" class="page-link">1</a>';
                            if ($start_page > 2) echo '<span style="padding:10px;">...</span>';
                        }

                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; 

                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) echo '<span style="padding:10px;">...</span>';
                            echo '<a href="?page='.$total_pages.'" class="page-link">'.$total_pages.'</a>';
                        }
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                    <div class="page-info">
                        Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?>
                    </div>
                </div>
                <?php endif; ?>
                
            </section>
        </main>

    </div>
</body>
</html>