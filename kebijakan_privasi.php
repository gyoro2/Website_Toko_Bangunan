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
    <title>Kebijakan Privasi - Material Super</title>
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
        <a href="index.php">Home</a> / Kebijakan Privasi
    </div>

    <main class="legal-main container">
        <div class="legal-document">
            <h1>Kebijakan Privasi Data Pelanggan</h1>
            <p class="subtitle">Kami sangat menghargai privasi Anda. Dokumen ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.</p>

            <h2 id="kumpulkan">1. Informasi yang Kami Kumpulkan</h2>
            <p>Kami mengumpulkan informasi yang Anda berikan langsung kepada kami saat mendaftar akun, melakukan pemesanan (DP 30%), atau menghubungi layanan pelanggan.</p>

            <h3>1.1. Data Personal</h3>
            <ul>
                <li>Nama, Alamat Email, Nomor Telepon/WhatsApp.</li>
                <li>Data Akun (Username dan Kata Sandi terenkripsi).</li>
            </ul>

            <h3>1.2. Data Proyek dan Transaksi (Sangat Penting)</h3>
            <ul>
                <li>**Alamat Pengiriman Proyek:** Diperlukan untuk proses logistik material.</li>
                <li>**Rincian Transaksi:** Termasuk bukti pembayaran DP 30% dan status pelunasan 70%.</li>
            </ul>

            <h2 id="penggunaan">2. Penggunaan Informasi</h2>
            <p>Kami menggunakan data Anda hanya untuk tujuan yang sah, termasuk:</p>
            <ul>
                <li>Memproses pesanan dan memfasilitasi sistem pembayaran DP 30% dan Pelunasan 70%.</li>
                <li>Mengirimkan material ke lokasi proyek yang akurat.</li>
                <li>Melakukan komunikasi terkait status pesanan dan pelunasan (misalnya, notifikasi bahwa material telah tiba dan 70% harus segera dilunasi).</li>
                <li>Meningkatkan kualitas layanan dan produk kami.</li>
            </ul>

            <h2 id="keamanan">3. Keamanan Data</h2>
            <p>Kami berkomitmen melindungi informasi pribadi Anda. Kami menerapkan langkah-langkah keamanan fisik dan elektronik yang memadai untuk melindungi data dari akses, penggunaan, atau pengungkapan yang tidak sah.</p>
            <ul>
                <li>Kata sandi disimpan dalam format terenkripsi (hash).</li>
                <li>Akses ke data pelanggan dibatasi hanya pada personel yang berwenang (Admin Logistik, Admin Keuangan).</li>
            </ul>

            <h2 id="umum">4. Ketentuan Lain</h2>
            <p>4.1. Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Pengguna akan diberitahu melalui email jika ada perubahan signifikan.</p>
            <p>4.2. Dengan terus menggunakan layanan kami, Anda menyetujui Kebijakan Privasi ini.</p>
            
            <p class="mt-40">**Tanggal Revisi Terakhir:** 17 November 2025</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi kami melalui menu Kontak Kami.</p>
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