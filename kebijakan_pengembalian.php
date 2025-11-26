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
    <title>Kebijakan Pengembalian & Garansi - Material Super</title>
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
        <a href="index.php">Home</a> / Kebijakan Pengembalian
    </div>

    <main class="legal-main container">
        <div class="legal-document">
            <h1>Kebijakan Pengembalian, Klaim, dan Garansi</h1>
            <p class="subtitle">Ketentuan ini mengatur prosedur penanganan kerusakan, kekurangan kuantitas, atau ketidaksesuaian material saat serah terima di lokasi proyek.</p>

            <h2 id="prinsip">1. Prinsip Dasar Klaim</h2>
            <p>1.1. Klaim (kerusakan/kekurangan) hanya dapat diajukan pada saat **Serah Terima Barang** di lokasi proyek, atau maksimal 2 jam setelah barang tiba.</p>
            <p>1.2. Pelunasan 70% TIDAK wajib dilakukan sebelum klaim diselesaikan (jika klaim tersebut mengurangi total tagihan atau memerlukan penggantian barang). </p>

            <h2 id="kerusakan">2. Klaim Kerusakan Material</h2>
            <h3>2.1. Material Rusak Saat Pengiriman</h3>
            <ul>
                <li>Jika material (misalnya, keramik pecah, semen basah) ditemukan rusak saat tim logistik kami tiba, Pembeli wajib segera membuat catatan di surat jalan dan memotret kerusakan tersebut.</li>
                <li>Tim kami akan memverifikasi kerusakan di tempat. Jika klaim disetujui, kami akan segera menjadwalkan pengiriman unit pengganti.</li>
            </ul>

            <h2 id="kurang">3. Klaim Kekurangan Kuantitas atau Salah Kirim</h2>
            <h3>3.1. Prosedur Klaim Kuantitas</h3>
            <p>Jika kuantitas yang diterima kurang dari yang tertera pada invoice (setelah pembayaran DP 30%), Pembeli wajib melaporkan saat tim logistik masih di lokasi.</p>
            <ul>
                <li>Jika kekurangan terbukti, unit yang kurang akan dikirimkan menyusul, dan Pelunasan 70% baru dilakukan setelah unit kekurangan tiba dan diterima.</li>
                <li>Jika terjadi salah kirim tipe atau jenis material, material akan segera ditarik dan diganti dengan barang yang benar tanpa biaya tambahan.</li>
            </ul>

            <div class="clause-box danger" style="background-color: #fceae9; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #dc3545; margin-top: 0;"><i class="fas fa-times-circle"></i> Klaim Setelah Pelunasan 70%</h3>
                <p style="color: #721c24; margin-bottom: 0;">Pembeli dianggap telah menerima dan menyetujui kuantitas serta kondisi barang secara penuh setelah **Pelunasan 70% dilakukan**. Klaim yang diajukan setelah pelunasan final TIDAK dapat diterima.</p>
            </div>

            <h2 id="garansi">4. Garansi Produk</h2>
            <p>4.1. Garansi produk (misalnya: warna cat, kekuatan semen) tunduk pada kebijakan garansi dari pabrikan material yang bersangkutan.</p>
            <p>4.2. Material Super tidak menanggung kerusakan yang diakibatkan oleh kesalahan pemasangan, penyimpanan yang buruk di lokasi proyek, atau bencana alam setelah serah terima.</p>
            
            <p class="mt-40" style="margin-top: 40px; font-weight: bold;">Untuk mengajukan klaim, hubungi tim kami di +62 812 3456 7890 (WA) atau melalui menu Kontak Kami.</p>
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