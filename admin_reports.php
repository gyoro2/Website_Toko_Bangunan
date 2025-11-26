<?php
// --- LOGIKA PHP ---
session_start();
// Asumsi db_connect.php berada di root
include 'db_connect.php'; 

// Cek Keamanan
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); exit();
}

// Inisialisasi data default
$financial_data = [
    'total_70_paid' => 0,
    'total_dp_paid' => 0,
    'piutang_70' => 0,
];

$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn->connect_error) {
    // Query: Menghitung total Pelunasan 70% (Total Harga Penuh dari order yang 70_PAID)
    $q_70_paid = "SELECT SUM(total_amount) FROM Orders WHERE payment_status = '70_PAID'";
    $financial_data['total_70_paid'] = $conn->query($q_70_paid)->fetch_row()[0] ?? 0;

    // Query: Menghitung total DP 30% yang sudah masuk
    $q_dp_paid = "SELECT SUM(dp_paid_amount) FROM Orders WHERE payment_status IN ('DP_PAID', '70_PENDING', '70_PAID')";
    $financial_data['total_dp_paid'] = $conn->query($q_dp_paid)->fetch_row()[0] ?? 0;
    
    // Query: Menghitung Piutang (Menunggu Pelunasan 70% yang masih beredar)
    $q_piutang = "SELECT SUM(remaining_70_amount) FROM Orders WHERE payment_status = '70_PENDING'";
    $financial_data['piutang_70'] = $conn->query($q_piutang)->fetch_row()[0] ?? 0;
}
$conn->close();

function formatCurrency($amount) { return 'Rp ' . number_format($amount, 0, ',', '.'); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Admin PUNCAK JAYA PLAVON PVC</title> 
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        
        <aside class="admin-sidebar">
            <div class="admin-logo">MS ADMIN</div>
            <ul class="admin-menu">
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_orders.php"><i class="fas fa-boxes"></i> Manajemen Pesanan</a></li>
                <li><a href="admin_stock.php"><i class="fas fa-warehouse"></i> Manajemen Stok</a></li>
                <li><a href="admin_reports.php" class="admin-active"><i class="fas fa-file-invoice-dollar"></i> Laporan Keuangan</a></li>
                <li class="logout-link"><a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h2>Laporan Keuangan & Arus Kas</h2>
                <div class="admin-profile">
                    <label for="report-filter">Periode:</label>
                    <select id="report-filter" class="admin-select-filter">
                        <option>Bulan Ini</option>
                        <option>Bulan Lalu</option>
                        <option>Tahun Ini</option>
                    </select>
                </div>
            </header>

            <section class="admin-metrics metrics-financial">
                <div class="metric-card bg-green">
                    <div class="metric-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="metric-info">
                        <h3>Total Pelunasan 70% (Bersih)</h3>
                        <p><?php echo formatCurrency($financial_data['total_70_paid']); ?></p>
                    </div>
                </div>
                <div class="metric-card bg-blue">
                    <div class="metric-icon"><i class="fas fa-money-check"></i></div>
                    <div class="metric-info">
                        <h3>Total DP 30% Diterima</h3>
                        <p><?php echo formatCurrency($financial_data['total_dp_paid']); ?></p>
                    </div>
                </div>
                <div class="metric-card bg-orange">
                    <div class="metric-icon"><i class="fas fa-bell"></i></div>
                    <div class="metric-info">
                        <h3>Piutang (70% Belum Dibayar)</h3>
                        <p><?php echo formatCurrency($financial_data['piutang_70']); ?></p>
                    </div>
                </div>
                <div class="metric-card bg-red">
                    <div class="metric-icon"><i class="fas fa-chart-area"></i></div>
                    <div class="metric-info">
                        <h3>Target Tercapai</h3>
                        <p>85%</p>
                    </div>
                </div>
            </section>
            
            <section class="detail-box">
                <h3><i class="fas fa-chart-bar"></i> Analisis Pembayaran (DP vs Pelunasan)</h3>
                <div class="chart-placeholder">
                    [Placeholder untuk Grafik Bar: Membandingkan Pendapatan dari DP vs Pelunasan 70% per bulan]
                </div>
            </section>
        </main>
    </div>
</body>
</html>