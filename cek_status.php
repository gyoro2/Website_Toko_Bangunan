<?php
// --- LOGIKA SERVER (PHP) ---
include 'db_connect.php'; // Asumsi file ada di root

$order_data = null;
$search_error = "";
$timeline_status = 0; // 1=DP, 2=Kirim, 3=Tiba/Lunas

// Cek apakah ada pencarian ID
if (isset($_GET['order_id'])) {
    $input_id = $_GET['order_id'];
    
    // Sanitasi input
    $safe_id = $conn->real_escape_string(trim($input_id));

    if (!empty($safe_id)) {
        // Query ke tabel Orders
        $query = "SELECT * FROM Orders WHERE order_id = '$safe_id'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $order_data = $result->fetch_assoc();
            
            // Tentukan Posisi Timeline berdasarkan Status DB
            $p_status = $order_data['payment_status'];
            $s_status = $order_data['shipping_status'];
            
            if ($p_status == 'DP_PENDING') {
                $timeline_status = 1; // Tahap Awal
                $status_message = "Menunggu Pembayaran DP 30%";
                $status_class = "status-pending";
            } elseif ($p_status == 'DP_PAID' && $s_status == 'DRAFT') {
                $timeline_status = 2; // DP Masuk, Menunggu Proses
                $status_message = "DP Diterima, Sedang Diproses";
                $status_class = "status-paid";
            } elseif ($s_status == 'SHIPPED') {
                $timeline_status = 3; // Dalam Perjalanan
                $status_message = "Pesanan Dalam Perjalanan";
                $status_class = "status-shipped";
            } elseif ($s_status == 'ARRIVED_LOC' || $p_status == '70_PENDING') {
                $timeline_status = 4; // Tiba, Menunggu Pelunasan
                $status_message = "Tiba di Lokasi - Menunggu Pelunasan 70%";
                $status_class = "status-warning";
            } elseif ($p_status == '70_PAID' || $s_status == 'COMPLETED') {
                $timeline_status = 5; // Selesai
                $status_message = "Pesanan Selesai & Lunas";
                $status_class = "status-completed";
            } else {
                $status_message = "Status: " . $p_status;
                $status_class = "";
            }

        } else {
            $search_error = "Pesanan dengan ID <strong>" . htmlspecialchars($safe_id) . "</strong> tidak ditemukan.";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pesanan - PUNCAK JAYA PLAVON PVC</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar-static">
        <div class="navbar container">
            <div class="logo">PUNCAK JAYA PLAVON PVC</div>
            <nav>
                <a href="index.php">Beranda</a>
                <a href="katalog.php">Katalog Produk</a>
                <a href="cara_beli.php">Cara Beli & DP</a>
                <a href="kontak_kami.php">Kontak Kami</a>
            </nav>
            <div class="utility-nav">
                <a href="cart.php">🛒</a>
                <div class="user-dropdown">
                    <a href="login.php" class="user-icon">👤</a> 
                    <div class="dropdown-content">
                        <a href="login.php"><i class="fas fa-tachometer-alt"></i> Dashboard Saya</a>
                        <a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="breadcrumb container">
        <a href="index.php">Home</a> / Cek Status Pesanan
    </div>

    <main class="status-main container">
        <h1>Lacak Pesanan Material Anda</h1>
        <p class="subtitle">Masukkan ID Pesanan Anda untuk melihat status pengiriman material saat ini.</p>

        <section class="tracking-form-section">
            <form action="cek_status.php" method="GET" class="tracking-input-box">
                <input type="text" name="order_id" placeholder="Contoh ID: 1" value="<?php echo isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : ''; ?>" required>
                <button type="submit" class="btn-check-status"><i class="fas fa-search"></i> Cek Status</button>
            </form>
        </section>

        <section id="tracking-result" class="tracking-result-section">
            
            <?php if ($search_error): ?>
                <p class="status-error"><?php echo $search_error; ?></p>
            <?php elseif ($order_data): ?>
                
                <h2 class="status-title <?php echo $status_class; ?>">Status Terkini: <?php echo $status_message; ?></h2>
                
                <div class="tracking-timeline">
                    <h3>Riwayat Perjalanan:</h3>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot <?php echo ($timeline_status >= 1) ? 'active-dot' : ''; ?>"></div>
                        <div class="timeline-content">
                            <p class="timeline-text">Pesanan Dibuat (Menunggu DP)</p>
                            <p class="timeline-date"><?php echo $order_data['order_date']; ?></p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot <?php echo ($timeline_status >= 2) ? 'active-dot' : ''; ?>"></div>
                        <div class="timeline-content">
                            <p class="timeline-text">Pembayaran DP Diterima</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot <?php echo ($timeline_status >= 3) ? 'active-dot' : ''; ?>"></div>
                        <div class="timeline-content">
                            <p class="timeline-text">Material Dalam Perjalanan</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot <?php echo ($timeline_status >= 4) ? 'active-dot' : ''; ?>"></div>
                        <div class="timeline-content">
                            <p class="timeline-text">Tiba di Lokasi (Cek Fisik & Pelunasan)</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot <?php echo ($timeline_status >= 5) ? 'active-dot' : ''; ?>"></div>
                        <div class="timeline-content">
                            <p class="timeline-text">Transaksi Selesai</p>
                        </div>
                    </div>

                </div>

                <?php if ($timeline_status == 4): ?>
                    <p class="pelunasan-link">Barang sudah tiba? <a href="login.php">Login ke Dashboard</a> untuk melakukan pelunasan 70%.</p>
                <?php endif; ?>

            <?php else: ?>
                <p class="initial-message">Masukkan ID Pesanan (Angka) dan tekan 'Cek Status'.</p>
            <?php endif; ?>

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
        document.addEventListener('DOMContentLoaded', () => {
            const dropdowns = document.querySelectorAll('.user-dropdown');

            dropdowns.forEach(dropdown => {
                const userIcon = dropdown.querySelector('.user-icon');
                const dropdownContent = dropdown.querySelector('.dropdown-content');

                userIcon.addEventListener('click', (e) => {
                    e.preventDefault(); 
                    
                    const isVisible = dropdownContent.style.display === 'block';
                    dropdownContent.style.display = isVisible ? 'none' : 'block';

                    document.querySelectorAll('.dropdown-content').forEach(content => {
                        if (content !== dropdownContent) {
                            content.style.display = 'none';
                        }
                    });

                    e.stopPropagation();
                });

                document.addEventListener('click', () => {
                    dropdownContent.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>