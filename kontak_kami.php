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
    <title>Kontak Kami - Material Super</title>
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
                <a href="kontak_kami.php" class="active">Kontak Kami</a>
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
        <a href="index.php">Home</a> / Kontak Kami
    </div>

    <main class="container" style="padding:40px 20px;">
        <h1 class="text-center" style="margin-bottom:30px;">Hubungi Tim Kami</h1>
        
        <div style="display:flex; gap:30px; flex-wrap:wrap;">
            <div style="flex:1; background:white; padding:30px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05); min-width:300px;">
                <h2>Kirim Pesan</h2>
                <form action="#" method="POST" onsubmit="alert('Pesan terkirim! Kami akan segera menghubungi Anda.'); return false;">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" placeholder="Nama Anda" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="email@contoh.com" required>
                    </div>
                    <div class="form-group">
                        <label>Pesan</label>
                        <textarea rows="4" placeholder="Tulis pertanyaan Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="btn-primary full-width-btn">Kirim Pesan</button>
                </form>
            </div>

            <div style="flex:1; background:#f8f9fa; padding:30px; border-radius:8px; min-width:300px;">
                <h2>Informasi Kontak</h2>
                
                <div style="margin-bottom:20px;">
                    <i class="fas fa-phone-alt" style="color:#007bff; margin-right:10px; font-size:1.2em;"></i>
                    <strong>Telepon / WhatsApp:</strong><br>
                    <span style="margin-left:30px; color:#555;">+62 812 7948 3767 (Fast Respon)</span>
                </div>
                
                <div style="margin-bottom:20px;">
                    <i class="fas fa-envelope" style="color:#007bff; margin-right:10px; font-size:1.2em;"></i>
                    <strong>Email Dukungan:</strong><br>
                    <span style="margin-left:30px; color:#555;">Gibrancastro21@gmail.com</span>
                </div>
                
                <div style="margin-bottom:20px;">
                    <i class="fas fa-map-marker-alt" style="color:#007bff; margin-right:10px; font-size:1.2em;"></i>
                    <strong>Alamat Gudang:</strong><br>
                    <span style="margin-left:30px; color:#555;">Jl. Raya Material No. 88, Jakarta Selatan.</span>
                </div>
                
                <hr style="margin:20px 0; border:0; border-top:1px solid #ddd;">
                
                <h3>Jam Operasional</h3>
                <p style="color:#555; line-height:1.8;">
                    <strong>Senin - Jumat:</strong> 08.00 - 17.00 WIB<br>
                    <strong>Sabtu:</strong> 08.00 - 14.00 WIB<br>
                    <strong>Minggu & Libur:</strong> Tutup
                </p>
            </div>
        </div>
        
        <div style="margin-top:40px; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
             <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126906.9985476398!2d106.7294694030288!3d-6.284562661776418!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf0fdad786a2!2sJakarta%20Selatan%2C%20Kota%20Jakarta%20Selatan%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1708229999999!5m2!1sid!2sid" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
                    e.stopPropagation();
                });
                document.addEventListener('click', () => dropdownContent.style.display = 'none');
            });
        });
    </script>
</body>
</html>