<?php
session_start();
include 'db_connect.php'; // Menghubungkan ke database

$featured_products = [];

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn->connect_error) {
    // Mengambil 3 produk pertama untuk ditampilkan di Halaman Depan
    $query = "SELECT * FROM Products ORDER BY product_id ASC LIMIT 3";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $featured_products[] = $row;
        }
    }
}
$conn->close();

// Fungsi format rupiah
function formatRupiah($angka) {
    return number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUNCAK JAYA PLAVON PVC - Material Interior Terbaik</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="hero">
        <div class="navbar">
            <div class="logo">PUNCAK JAYA PLAVON PVC</div>
            <nav>
                <a href="index.php" class="active">Beranda</a>
                <a href="katalog.php">Katalog Produk</a>
                <a href="cara_beli.php">Cara Beli</a>
                <a href="kontak_kami.php">Kontak Kami</a>
            </nav>
            <div class="utility-nav">
                <a href="cart.php">🛒</a>
                <div class="user-dropdown">
                    <a href="login.php" class="user-icon">👤</a> 
                    <div class="dropdown-content">
                        <a href="login.php"><i class="fas fa-tachometer-alt"></i> Dashboard Saya</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-content">
            <h1>Proyek Lancar, Modal Hemat: Beli Material Bangunan Cukup DP 30% Saja!</h1>
            <p>Amankan Material Terbaik, Bayar Sisanya Setelah Barang Sampai di Lokasi.</p>
            <a href="katalog.php" class="cta-button">Lihat Katalog Sekarang</a>
        </div>
    </header>

    <section class="keunggulan">
        <div class="feature">
            <h2>💰 Hemat Modal Awal</h2>
            <p>Tidak perlu bayar 100% di depan. Jaga arus kas Anda tetap lancar.</p>
        </div>
        <div class="feature">
            <h2>🔒 Jaminan Ketersediaan</h2>
            <p>Pesanan Anda langsung diproses dan diamankan setelah DP terbayar.</p>
        </div>
        <div class="feature">
            <h2>🤝 Kepastian Transaksi</h2>
            <p>Kesepakatan resmi terjalin. Anda bayar lunas setelah material tiba.</p>
        </div>
    </section>

    <section class="produk-unggulan">
        <div class="container">
            <h2 class="text-center">Produk Unggulan Kami</h2>
            <div class="produk-list">
                
                <?php if (empty($featured_products)): ?>
                    <p style="text-align:center; width:100%;">Belum ada produk unggulan.</p>
                <?php else: ?>
                    <?php foreach ($featured_products as $product): 
                        $dpPrice = $product['price'] * 0.30;
                        $detailLink = "detail_produk.php?id=" . $product['product_id'];
                    ?>
                    <div class="produk-card">
                        <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="harga-total">Harga Total: Rp <?php echo formatRupiah($product['price']); ?> / <?php echo htmlspecialchars($product['unit']); ?></p>
                        <p class="harga-dp">DP 30%: **Rp <?php echo formatRupiah($dpPrice); ?>**</p>
                        <a href="<?php echo $detailLink; ?>" class="btn-detail">Lihat Detail</a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <section class="alur-pembelian">
        <h2>Alur Pembelian Mudah dengan DP 30%</h2>
        <div class="langkah-list">
            <div class="langkah">1. Pesan & Bayar 30% DP</div>
            <div class="arrow">>></div>
            <div class="langkah">2. Material Dikirim</div>
            <div class="arrow">>></div>
            <div class="langkah">3. Lunasi 70% Sisanya</div>
        </div>
    </section>

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