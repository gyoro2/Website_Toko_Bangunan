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
    <title>Syarat & Ketentuan - PUNCAK JAYA PLAVON PVC</title>
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
        <a href="index.php">Home</a> / Syarat & Ketentuan
    </div>

    <main class="legal-main container">
        <div class="legal-document">
            <h1>Syarat dan Ketentuan Layanan (Terms & Conditions)</h1>
            <p class="subtitle">Berlaku efektif sejak 17 November 2025. Dengan menggunakan layanan Material Super, Anda dianggap telah membaca dan menyetujui seluruh ketentuan di bawah ini.</p>

            <h2 id="umum">1. Ketentuan Umum</h2>
            <p>1.1. Layanan Material Super adalah platform jual beli material bangunan yang memfasilitasi transaksi dengan skema pembayaran bertahap.</p>
            <p>1.2. Pengguna wajib memberikan data yang valid dan akurat, termasuk alamat pengiriman proyek yang jelas dan dapat diakses truk logistik.</p>

            <h2 id="pembayaran">2. Sistem Pembayaran Down Payment (DP) dan Pelunasan</h2>
            
            <h3>2.1. Kewajiban DP 30%</h3>
            <p>Setiap pesanan yang dibuat wajib diamankan dengan membayar Down Payment (DP) sebesar **30% dari Total Harga Penuh** (termasuk biaya material dan biaya pengiriman).</p>

            <h3>2.2. Pelunasan Sisa 70%</h3>
            <p>Sisa tagihan sebesar **70%** wajib dilunasi oleh Pembeli setelah material tiba dan diterima di lokasi proyek. Pembayaran dilakukan melalui tautan pelunasan di Dashboard Akun Pembeli.</p>

            <div class="clause-box warning" style="background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #856404; margin-top: 0;"><i class="fas fa-clock"></i> Batas Waktu Pelunasan 70%</h3>
                <p>Pelunasan 70% wajib dilakukan dalam waktu maksimal **1 x 24 Jam** setelah Tim Logistik kami mengonfirmasi status "Tiba di Lokasi/Serah Terima Barang". Keterlambatan pelunasan dapat dikenakan denda atau penahanan pengiriman material berikutnya.</p>
            </div>

            <h2 id="pembatalan">3. Kebijakan Pembatalan</h2>
            
            <h3>3.1. Pembatalan oleh Pembeli</h3>
            <p>Jika Pembeli mengajukan pembatalan setelah pembayaran DP 30% terkonfirmasi, maka:</p>
            <ul>
                <li>Jika pembatalan dilakukan sebelum material dimuat dan dikirim (status: DP Diterima), Pembeli akan dikenakan **Biaya Administrasi 10%** dari nilai DP. Sisa DP akan dikembalikan.</li>
                <li>Jika pembatalan dilakukan setelah material dimuat dan dalam perjalanan (status: Dalam Perjalanan/Shipped), maka **seluruh DP 30% akan hangus** dan tidak dapat dikembalikan.</li>
            </ul>

            <div class="clause-box danger" style="background-color: #fceae9; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #dc3545; margin-top: 0;"><i class="fas fa-exclamation-triangle"></i> DP HANGUS JIKA BARANG DI KIRIM</h3>
                <p style="color: #721c24;">Dengan menyetujui S&K ini, Pembeli mengakui dan setuju bahwa **DP 30% akan hangus sepenuhnya** apabila Pembatalan dilakukan setelah proses pengiriman material telah dimulai, karena DP berfungsi sebagai jaminan biaya pengadaan dan logistik awal.</p>
            </div>

            <h2 id="pengiriman">4. Pengiriman dan Pemeriksaan Barang</h2>
            <p>4.1. Material Super bertanggung jawab memastikan material yang dikirim sesuai dengan spesifikasi pesanan saat keluar dari gudang.</p>
            <p>4.2. **Kewajiban Pemeriksaan:** Pembeli atau perwakilan Pembeli wajib melakukan pemeriksaan visual kuantitas dan kondisi material segera setelah tiba di lokasi proyek, sebelum konfirmasi penerimaan diberikan.</p>
            <p>4.3. Klaim kerusakan atau kekurangan material hanya diterima jika diajukan **pada saat serah terima barang** (sebelum pelunasan 70% dilakukan). Klaim setelah pelunasan 70% tidak akan dilayani.</p>
            
            <p class="mt-40" style="margin-top: 40px; font-weight: bold;">**Material Super**</p>
            <p>Hubungi kami jika ada pertanyaan mengenai Syarat & Ketentuan ini melalui menu Kontak Kami.</p>
        </div>
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