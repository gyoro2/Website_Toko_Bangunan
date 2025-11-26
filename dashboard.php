<?php
// --- LOGIKA SERVER (PHP) ---
session_start();

// 1. Cek Keamanan: Redirect ke login jika belum ada sesi
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Koneksi Database
include 'db_connect.php'; 

$user_id = $_SESSION['user_id'];
$user_name = 'Pelanggan';
$user_email = '';
$user_phone = '';
$orders = [];

$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn->connect_error) {
    $safe_user_id = $conn->real_escape_string($user_id);

    // Ambil Data Profil Pengguna
    $user_query = "SELECT full_name, email, phone FROM Users WHERE user_id = '{$safe_user_id}'";
    $user_result = $conn->query($user_query);
    if ($user_result && $user_result->num_rows > 0) {
        $u_data = $user_result->fetch_assoc();
        $user_name = $u_data['full_name'];
        $user_email = $u_data['email'];
        $user_phone = $u_data['phone'];
    }

    // Ambil Data Pesanan
    $orders_query = "
        SELECT 
            order_id, 
            DATE_FORMAT(order_date, '%d %b %Y') AS order_date_formatted, 
            total_amount, 
            remaining_70_amount,
            dp_paid_amount,
            payment_status,
            shipping_status
        FROM Orders 
        WHERE user_id = '{$safe_user_id}'
        ORDER BY order_date DESC
    ";

    $orders_result = $conn->query($orders_query);
    if ($orders_result) {
        while($row = $orders_result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    $conn->close();
}

// Fungsi Utility
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
function getStatusBadge($status) {
    switch ($status) {
        case '70_PENDING': return '<span class="status-badge status-warning">Menunggu Pelunasan</span>';
        case 'DP_PAID': return '<span class="status-badge status-paid">DP Lunas</span>';
        case '70_PAID': return '<span class="status-badge status-completed">Lunas Penuh</span>';
        case 'DP_PENDING': return '<span class="status-badge status-pending">Menunggu DP</span>';
        case 'CANCELLED': return '<span class="status-badge" style="background:#dc3545; color:white;">Dibatalkan</span>';
        default: return '<span class="status-badge">' . $status . '</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Akun - Material Super</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS Tambahan untuk transisi section */
        .content-section { display: none; animation: fadeIn 0.3s; }
        .content-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .status-badge { padding: 4px 8px; border-radius: 4px; color: white; font-size: 0.85em; font-weight: 600; }
        .status-warning { background-color: #ffc107; color: #333; }
        .status-paid { background-color: #17a2b8; }
        .status-completed { background-color: #28a745; }
        .status-pending { background-color: #e67e22; }
    </style>
</head>
<body>

    <header class="navbar-static">
        <div class="navbar container">
            <div class="logo">MATERIAL SUPER</div>
            <nav>
                <a href="index.php">Beranda</a>
                <a href="katalog.php">Katalog Produk</a>
                <a href="cara_beli.php">Cara Beli & DP</a>
                <a href="kontak_kami.php">Kontak Kami</a>
            </nav>
            <div class="utility-nav">
                <a href="cart.php">🛒</a>
                <div class="user-dropdown">
                    <a href="dashboard.php" class="user-icon active"><i class="fas fa-user"></i></a> 
                    <div class="dropdown-content">
                        <a href="dashboard.php" class="menu-active-dropdown"><i class="fas fa-tachometer-alt"></i> Dashboard Saya</a> 
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="breadcrumb container">
        <a href="index.php">Home</a> / Dashboard Saya
    </div>

    <main class="dashboard-main container">
        
        <aside class="dashboard-sidebar">
            <h2 id="welcome-message">Hai, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <ul class="sidebar-menu" id="sidebar-menu">
                <li><a href="#utama" class="menu-active" onclick="showSection('utama', this)"><i class="fas fa-home"></i> Dashboard Utama</a></li>
                <li><a href="#riwayat" onclick="showSection('riwayat', this)"><i class="fas fa-history"></i> Riwayat Transaksi</a></li>
                <li><a href="#alamat" onclick="showSection('alamat', this)"><i class="fas fa-map-marker-alt"></i> Daftar Alamat</a></li>
                <li><a href="#profil" onclick="showSection('profil', this)"><i class="fas fa-user-cog"></i> Pengaturan Profil</a></li>
                <li><a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
            </ul>
        </aside>

        <section class="dashboard-content">
            
            <div id="section-utama" class="content-section active">
                <div class="content-box">
                    <h2><i class="fas fa-bell"></i> Tagihan & Pesanan Aktif</h2>
                    <div id="outstanding-payments">
                        <?php 
                        $active_order_found = false;

                        foreach ($orders as $order) {
                            // KASUS 1: Menunggu Pelunasan 70% (Barang Tiba)
                            if (($order['payment_status'] == 'DP_PAID' && $order['shipping_status'] == 'ARRIVED_LOC') || $order['payment_status'] == '70_PENDING') {
                                $active_order_found = true;
                                echo '
                                    <div class="pelunasan-card" style="border-left: 5px solid #ffc107; background: #fff9e6; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                                        <div class="order-summary">
                                            <h3>Pesanan #' . $order['order_id'] . ' - Barang Tiba di Lokasi</h3>
                                            <p>Silakan cek fisik barang. Jika sesuai, segera lunasi sisa tagihan.</p>
                                        </div>
                                        <div class="payment-due" style="text-align: right;">
                                            <span style="display:block; font-size:0.9em; color:#666;">Sisa Tagihan (70%)</span>
                                            <span class="amount-due" style="font-size:1.4em; font-weight:bold; color:#333;">' . formatCurrency($order['remaining_70_amount']) . '</span>
                                            <button onclick="initiatePayment('. $order['order_id'] . ', ' . $order['remaining_70_amount'] . ')" class="btn-lunasi-70" style="background:#28a745; color:white; padding:8px 15px; border:none; border-radius:4px; cursor:pointer; margin-top:5px;">LUNASI SEKARANG <i class="fas fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                ';
                            } 
                            // KASUS 2: Menunggu DP (Baru Checkout)
                            elseif ($order['payment_status'] == 'DP_PENDING') {
                                $active_order_found = true;
                                echo '
                                    <div class="pelunasan-card" style="border-left: 5px solid #e67e22; background: #fff5e6; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                                        <div class="order-summary">
                                            <h3>Pesanan #' . $order['order_id'] . ' - Menunggu DP</h3>
                                            <p>Pesanan Anda sedang menunggu pembayaran uang muka.</p>
                                        </div>
                                        <div class="payment-due" style="text-align: right;">
                                            <span style="display:block; font-size:0.9em; color:#666;">Total DP (30%)</span>
                                            <span class="amount-due" style="font-size:1.4em; font-weight:bold; color:#333;">' . formatCurrency($order['dp_paid_amount']) . '</span>
                                            
                                            <div style="margin-top:10px;">
                                                <a href="payment.php?id='.$order['order_id'].'" class="btn-primary btn-small" style="display:inline-block; text-decoration:none; padding:8px 15px; margin-right:5px; background:#007bff; color:white; border-radius:4px;">Bayar DP</a>
                                                
                                                <button onclick="cancelOrder('.$order['order_id'].')" class="btn-small" style="background-color: #dc3545; color: white; border: none; cursor: pointer; padding:8px 15px; border-radius:4px;">Batal</button>
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }
                        }
                        
                        if (!$active_order_found):
                        ?>
                            <p class="summary-info" style="color:#666; padding:20px; text-align:center;">Tidak ada tagihan aktif saat ini. <a href="katalog.php" style="color:#007bff; font-weight:bold;">Belanja Sekarang?</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="section-riwayat" class="content-section">
                <div class="content-box">
                    <h2>Riwayat Semua Transaksi</h2>
                    <table class="riwayat-table" style="width:100%; border-collapse: collapse; margin-top:15px;">
                        <thead>
                            <tr style="background:#f8f9fa; text-align:left;">
                                <th style="padding:10px;">ID</th>
                                <th style="padding:10px;">Tanggal</th>
                                <th style="padding:10px;">Total</th>
                                <th style="padding:10px;">Status Bayar</th>
                                <th style="padding:10px;">Status Kirim</th>
                                <th style="padding:10px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr><td colspan="6" style="text-align:center; padding:20px;">Anda belum memiliki riwayat transaksi.</td></tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr style="border-bottom:1px solid #eee;">
                                        <td style="padding:10px;">#<?php echo $order['order_id']; ?></td>
                                        <td style="padding:10px;"><?php echo $order['order_date_formatted']; ?></td>
                                        <td style="padding:10px;"><?php echo formatCurrency($order['total_amount']); ?></td>
                                        <td style="padding:10px;"><?php echo getStatusBadge($order['payment_status']); ?></td>
                                        <td style="padding:10px;"><?php echo $order['shipping_status']; ?></td>
                                        <td style="padding:10px;">
                                            <?php if ($order['payment_status'] == 'DP_PENDING'): ?>
                                                <button onclick="cancelOrder(<?php echo $order['order_id']; ?>)" class="btn-small" style="background-color: #dc3545; color: white; border: none; cursor: pointer; padding: 5px 10px; border-radius: 4px;">Batal</button>
                                                <a href="payment.php?id=<?php echo $order['order_id']; ?>" class="btn-small" style="background-color: #28a745; color: white; text-decoration:none; padding: 5px 10px; border-radius: 4px;">Bayar</a>
                                            <?php else: ?>
                                                <span style="color:#999;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="section-alamat" class="content-section">
                <div class="content-box">
                    <h2>Daftar Alamat Proyek</h2>
                    <p>Alamat yang digunakan saat checkout terakhir:</p>
                    <div style="background:#f9f9f9; padding:15px; border:1px solid #eee; border-radius:5px; margin-top:10px;">
                        <strong>Alamat Utama</strong><br>
                        Lokasi Proyek Terakhir<br>
                        <span style="font-size:0.9em; color:#666;">(Alamat tersimpan otomatis saat Anda melakukan checkout)</span>
                    </div>
                </div>
            </div>

            <div id="section-profil" class="content-section">
                <div class="content-box">
                    <h2>Pengaturan Profil Akun</h2>
                    <form action="#" method="POST">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" style="width:100%; padding:10px; margin-top:5px;">
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label>Nomor Telepon</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_phone); ?>" style="width:100%; padding:10px; margin-top:5px;">
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label>Email (Tidak dapat diubah)</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" disabled style="width:100%; padding:10px; margin-top:5px; background-color: #eee;">
                        </div>
                        <button type="button" class="btn-primary" style="margin-top: 20px; padding:10px 20px; background:#007bff; color:white; border:none; border-radius:4px;" onclick="alert('Fitur update profil akan segera hadir!')">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-col">
                <h4>PUNCAK JAYA PLAVON PVC</h4>
                <p>Solusi plavon terpercaya dengan skema pembayaran ringan.</p>
            </div>
            <div class="footer-col">
                <h4>Informasi</h4>
                <ul>
                    <li><a href="syarat_ketentuan.php">Syarat & Ketentuan</a></li>
                    <li><a href="kebijakan_privasi.php">Kebijakan Privasi</a></li>
                    <li><a href="kebijakan_pengembalian.php">Kebijakan Pengembalian</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Hubungi Kami</h4>
                <p>📞 +62 812 7948 3767</p>
                <p>📧 Gibrancastro21@gmail.com</p>
            </div>
        </div>
        <div class="copyright">
            &copy; 2025 PUNCAK JAYA PLAVON PVC. All Rights Reserved.
        </div>
    </footer>

    <script>
        // --- LOGIKA PINDAH SECTION (Tab) ---
        function showSection(sectionId, element) {
            document.querySelectorAll('.content-section').forEach(sec => {
                sec.style.display = 'none';
                sec.classList.remove('active');
            });
            
            const target = document.getElementById('section-' + sectionId);
            if(target) {
                target.style.display = 'block';
                target.classList.add('active');
            }

            if (element) {
                document.querySelectorAll('#sidebar-menu a').forEach(item => item.classList.remove('menu-active'));
                element.classList.add('menu-active');
            }
        }

        // --- LOGIKA PELUNASAN ---
        window.initiatePayment = function(orderId, amount) {
            if (confirm('Lanjutkan ke konfirmasi pembayaran pelunasan?')) {
                window.location.href = 'payment.php?id=' + orderId + '&type=pelunasan';
            }
        }
        
        // --- LOGIKA BATALKAN PESANAN ---
        window.cancelOrder = function(orderId) {
            if (confirm('Yakin ingin membatalkan pesanan #' + orderId + '? Stok barang akan dikembalikan.')) {
                fetch('api/user/cancel_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Pesanan Dibatalkan.");
                        location.reload(); 
                    } else {
                        alert("Gagal: " + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Terjadi kesalahan koneksi.");
                });
            }
        }

        // --- SETUP DROPDOWN ---
        document.addEventListener('DOMContentLoaded', () => {
            const dropdowns = document.querySelectorAll('.user-dropdown');
            dropdowns.forEach(dropdown => {
                const userIcon = dropdown.querySelector('.user-icon');
                const dropdownContent = dropdown.querySelector('.dropdown-content');
                userIcon.addEventListener('click', (e) => {
                    e.preventDefault(); 
                    const isVisible = dropdownContent.style.display === 'block';
                    dropdownContent.style.display = isVisible ? 'none' : 'block';
                    e.stopPropagation();
                });
                document.addEventListener('click', () => dropdownContent.style.display = 'none');
            });
            
            // Pastikan section utama tampil saat load
            showSection('utama', document.querySelector('a[href="#utama"]'));
        });
    </script>
</body>
</html>