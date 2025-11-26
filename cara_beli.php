<?php
session_start();
// Cek status login untuk mengatur link di navbar
$is_logged_in = isset($_SESSION['user_id']);
$dashboard_url = $is_logged_in ? 'dashboard.php' : 'login.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cara Beli & Sistem DP 30% - Material Super</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar-static">
        <div class="navbar container">
            <div class="logo">PLAVON PVC</div>
            <nav>
                <a href="index.php">Beranda</a>
                <a href="katalog.php">Katalog Produk</a>
                <a href="cara_beli.php" class="active">Cara Beli & DP</a>
                <a href="kontak_kami.php">Kontak Kami</a>
            </nav>
            <div class="utility-nav">
                <a href="cart.php">🛒</a>
                <div class="user-dropdown">
                    <a href="<?php echo $dashboard_url; ?>" class="user-icon <?php echo $is_logged_in ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i>
                    </a> 
                    <div class="dropdown-content">
                        <a href="<?php echo $dashboard_url; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard Saya</a>
                        <a href="<?php echo $is_logged_in ? 'logout.php' : 'login.php'; ?>">
                            <i class="fas fa-sign-out-alt"></i> <?php echo $is_logged_in ? 'Logout' : 'Login'; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="breadcrumb container">
        <a href="index.php">Home</a> / Cara Beli & DP
    </div>

    <main class="cara-beli-main container">
        <h1>Panduan Pembelian dengan Skema DP 30%</h1>
        <p class="subtitle">Nikmati proses transaksi yang aman dan hemat modal dengan skema pembayaran dua tahap kami.</p>

        <section class="alur-section">
            <h2>Alur Transaksi (DP 30% di Awal, 70% di Akhir)</h2>
            <div class="langkah-wrapper">
                
                <div class="langkah-card">
                    <i class="fas fa-hand-holding-usd step-icon"></i>
                    <h3>1. Bayar Down Payment (30%)</h3>
                    <p>Setelah *checkout*, Anda hanya perlu melunasi **30% dari total tagihan**. Pembayaran ini mengunci harga dan mengamankan material dari gudang kami.</p>
                </div>

                <i class="fas fa-arrow-right arrow-icon"></i>
                
                <div class="langkah-card">
                    <i class="fas fa-truck-moving step-icon"></i>
                    <h3>2. Pengiriman & Cek Barang</h3>
                    <p>Kami mengirimkan material ke alamat proyek Anda. Anda memiliki waktu untuk memeriksa kuantitas dan kualitas barang saat tiba di lokasi.</p>
                </div>

                <i class="fas fa-arrow-right arrow-icon"></i>
                
                <div class="langkah-card">
                    <i class="fas fa-money-check-alt step-icon"></i>
                    <h3>3. Pelunasan Sisa 70%</h3>
                    <p>Setelah puas dengan material yang diterima, Anda melunasi sisa **70% tagihan** melalui Dashboard Akun Anda. Transaksi selesai!</p>
                </div>
            </div>
        </section>
        
        <hr class="separator">

        <section class="faq-section">
            <h2>Tanya Jawab Seputar DP & Kebijakan</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">Apakah DP 30% hangus jika saya batalkan pesanan?</button>
                    <div class="faq-answer">Ya, sesuai Syarat & Ketentuan kami, DP 30% tidak dapat dikembalikan karena sudah digunakan untuk mengikat stok dan biaya administrasi.</div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">Kapan saya harus melunasi sisa 70%?</button>
                    <div class="faq-answer">Pelunasan harus dilakukan maksimal 1x24 jam setelah Anda mengonfirmasi penerimaan material di lokasi proyek.</div>
                </div>
                
                <div class="faq-item">
                    <button class="faq-question">Bagaimana jika ada perbedaan kuantitas saat barang tiba?</button>
                    <div class="faq-answer">Tolong segera hubungi tim kami saat serah terima. Kami akan melakukan verifikasi di tempat sebelum Anda melakukan pelunasan sisa 70%.</div>
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
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.nextElementSibling;
                button.classList.toggle('active');
                if (button.classList.contains('active')) {
                    answer.style.maxHeight = answer.scrollHeight + "px";
                } else {
                    answer.style.maxHeight = null;
                }
            });
        });
    </script>

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