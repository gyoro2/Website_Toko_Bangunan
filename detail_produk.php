<?php
session_start();
include 'db_connect.php'; 

// --- Cek Status Login ---
$is_logged_in = isset($_SESSION['user_id']);

// --- LOGIKA PHP: Mengambil Data Detail Produk ---
$product = null;
$error_message = "";
$productId = $_GET['id'] ?? null;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $error_message = "Koneksi Database Gagal.";
} elseif ($productId) {
    $safe_id = $conn->real_escape_string($productId);
    
    // Ambil Data Produk & Stok
    $query = "
        SELECT 
            p.*, 
            (s.quantity_available - s.quantity_locked) AS available_stock
        FROM Products p
        LEFT JOIN Stock s ON p.product_id = s.product_id
        WHERE p.product_id = '{$safe_id}'
        LIMIT 1
    ";

    $result = $conn->query($query);

    if ($result && $result->num_rows === 1) {
        $product = $result->fetch_assoc();
    } else {
        $error_message = "Produk dengan ID {$productId} tidak ditemukan.";
    }
    $conn->close();
} else {
    $error_message = "ID produk tidak valid.";
}

function formatRupiah($angka) { return number_format($angka, 0, ',', '.'); }

$product_exists = ($product !== null);
$basePrice = $product_exists ? $product['price'] : 0;
$dashboard_url = $is_logged_in ? 'dashboard.php' : 'login.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product_exists ? $product['name'] : 'Produk Tidak Ditemukan'; ?> - Material Super</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS Tambahan Khusus Halaman Detail agar lebih Fresh */
        .product-detail-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
            overflow: hidden;
            padding: 0;
        }
        
        .product-info-grid {
            padding: 40px;
            gap: 50px;
        }

        .product-gallery img {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .badge-stock {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85em;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .badge-stock.empty {
            background: #ffebee;
            color: #c62828;
        }

        .product-title { font-size: 2.2em; margin-bottom: 10px; line-height: 1.2; color: #2c3e50; }
        .product-sku { color: #999; font-size: 0.9em; margin-bottom: 20px; display: block; }

        .price-highlight-box {
            background: linear-gradient(to right, #f8f9fa, #fff);
            border-left: 5px solid #FFA500;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .main-price-label { font-size: 0.9em; color: #666; margin-bottom: 5px; }
        .main-price-val { font-size: 1.4em; color: #333; text-decoration: line-through; margin-right: 10px; }
        
        .dp-price-label { font-size: 1.1em; font-weight: 700; color: #e67e22; margin-top: 10px; display: block; }
        .dp-price-val { font-size: 2.2em; font-weight: 800; color: #e67e22; }

        .action-panel {
            background: #fff;
            border: 1px solid #eee;
            padding: 25px;
            border-radius: 10px;
            margin-top: 30px;
        }

        /* --- CSS KHUSUS UNTUK MEMPERBESAR TOMBOL KUANTITAS --- */
        .qty-input-group {
            display: flex;
            align-items: center;
        }
        
        .qty-input-group button {
            width: 50px;        /* Lebar tombol diperbesar */
            height: 50px;       /* Tinggi tombol diperbesar */
            font-size: 1.5em;   /* Ukuran ikon +/- diperbesar */
            font-weight: bold;
            cursor: pointer;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        
        .qty-input-group button:hover {
            background-color: #e0e0e0;
            color: #000;
        }

        /* Membuat sudut tombol melengkung di sisi luar saja */
        #kurang-qty { border-radius: 8px 0 0 8px; }
        #tambah-qty { border-radius: 0 8px 8px 0; }

        .qty-input-group input {
            width: 80px;        /* Lebar input diperbesar */
            height: 50px;       /* Tinggi input mengikuti tombol */
            font-size: 1.3em;   /* Angka di dalam lebih besar */
            text-align: center;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            border-left: none;
            border-right: none;
            margin: 0;
            font-weight: 600;
        }

        /* Hilangkan panah kecil di input number browser */
        .qty-input-group input::-webkit-outer-spin-button,
        .qty-input-group input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body>

    <header class="navbar-static">
        <div class="navbar container">
            <div class="logo">PLAVON PVC</div>
            <nav>
                <a href="index.php">Beranda</a>
                <a href="katalog.php">Katalog</a>
                <a href="cara_beli.php">Cara Beli</a>
                <a href="kontak_kami.php">Kontak</a>
            </nav>
            <div class="utility-nav">
                <a href="cart.php">🛒</a>
                <div class="user-dropdown">
                    <a href="<?php echo $dashboard_url; ?>" class="user-icon <?php echo $is_logged_in ? 'active' : ''; ?>"><i class="fas fa-user"></i></a> 
                    <div class="dropdown-content">
                        <a href="<?php echo $dashboard_url; ?>">Dashboard</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="breadcrumb container" style="margin-top: 20px;">
        <a href="index.php">Home</a> / <a href="katalog.php">Katalog</a> / <span style="color:#555;"><?php echo $product_exists ? $product['name'] : '-'; ?></span>
    </div>

    <main class="product-detail container">
        
        <?php if ($product_exists): 
            $stock = $product['available_stock'];
            $is_available = $stock > 0;
        ?>
        
        <div class="product-detail-card">
            <div class="product-info-grid">
                
                <div class="product-gallery">
                    <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <div class="product-details">
                    
                    <?php if ($is_available): ?>
                        <span class="badge-stock"><i class="fas fa-check-circle"></i> Stok Tersedia: <?php echo $stock; ?> <?php echo $product['unit']; ?></span>
                    <?php else: ?>
                        <span class="badge-stock empty"><i class="fas fa-times-circle"></i> Stok Habis</span>
                    <?php endif; ?>

                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <span class="product-sku">Kode SKU: <?php echo htmlspecialchars($product['sku']); ?></span>

                    <div class="price-highlight-box">
                        <div class="main-price-row">
                            <span class="main-price-label">Harga Normal:</span>
                            <span class="main-price-val">Rp <?php echo formatRupiah($basePrice); ?></span>
                            <span style="font-size:0.9em; color:#666;">/ <?php echo $product['unit']; ?></span>
                        </div>
                        
                        <div class="dp-price-row">
                            <?php $dpUnit = $basePrice * 0.3; ?>
                            <span class="dp-price-label">Cukup Bayar DP 30%:</span>
                            <span class="dp-price-val">Rp <?php echo formatRupiah($dpUnit); ?></span>
                        </div>
                    </div>

                    <p class="deskripsi-singkat" style="font-size:1.05em; color:#555;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>

                    <div class="action-panel">
                        <div class="qty-control" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                            <label style="font-weight:bold; font-size: 1.1em;">Jumlah Pembelian:</label>
                            <div class="qty-input-group">
                                <button id="kurang-qty" <?php echo !$is_available ? 'disabled' : ''; ?>><i class="fas fa-minus"></i></button>
                                <input type="number" id="kuantitas" value="1" min="1" max="<?php echo $stock; ?>" data-base-price="<?php echo $basePrice; ?>" <?php echo !$is_available ? 'disabled' : ''; ?>> 
                                <button id="tambah-qty" <?php echo !$is_available ? 'disabled' : ''; ?>><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        
                        <div class="total-dp-calculation" style="border-top:1px dashed #ddd; padding-top:15px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
                            <span style="color:#666;">Total DP yg harus dibayar:</span>
                            <strong id="final-dp-amount" style="font-size:1.4em; color:#007bff;">Rp <?php echo formatRupiah($dpUnit); ?></strong>
                        </div>

                        <button id="add-to-cart-btn" class="btn-keranjang full-width-btn" style="padding:15px; font-size:1.2em; border-radius: 8px;" <?php echo !$is_available ? 'disabled style="background:#ccc; cursor:not-allowed;"' : ''; ?>>
                            <i class="fas fa-shopping-cart"></i> <?php echo $is_available ? 'Tambah ke Keranjang' : 'Stok Habis'; ?>
                        </button>
                    </div>

                </div>
            </div>
        </div>
        
        <section class="product-specs-tabs">
            <div class="tab-links">
                <button class="tablink active" onclick="openTab(event, 'Deskripsi')">Deskripsi & Spesifikasi</button>
                <button class="tablink" onclick="openTab(event, 'Pengiriman')">Info Pengiriman</button>
            </div>

            <div id="Deskripsi" class="tabcontent" style="display:block;">
                <h3 style="margin-bottom:15px; color:#333;">Detail Produk</h3>
                <p style="line-height:1.8; color:#555;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            <div id="Pengiriman" class="tabcontent">
                <h3 style="margin-bottom:15px; color:#333;">Kebijakan Pengiriman</h3>
                <ul style="list-style:disc; margin-left:20px; color:#555; line-height:1.8;">
                    <li>Pengiriman dilakukan menggunakan armada toko (Pickup/Truk).</li>
                    <li>Ongkos kirim dihitung berdasarkan jarak kilometer dari gudang kami.</li>
                    <li>Barang akan dikirim setelah pembayaran DP 30% terkonfirmasi.</li>
                    <li>Estimasi pengiriman 1-2 hari kerja.</li>
                </ul>
            </div>
        </section>

        <?php else: ?>
             <div class="container text-center" style="padding:50px;">
                <i class="fas fa-exclamation-circle" style="font-size:3em; color:#ccc; margin-bottom:20px;"></i>
                <h2>Produk Tidak Ditemukan</h2>
                <p><?php echo $error_message; ?></p>
                <a href="katalog.php" class="btn-primary" style="margin-top:20px;">Kembali ke Katalog</a>
             </div>
        <?php endif; ?>
    </main>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-col"><h4>PLAVON PVC</h4><p>Solusi interior modern & hemat biaya.</p></div>
            <div class="footer-col"><h4>Informasi</h4><ul><li><a href="syarat_ketentuan.html">Syarat & Ketentuan</a></li><li><a href="kebijakan_privasi.html">Kebijakan Privasi</a></li></ul></div>
        </div>
        <div class="copyright">&copy; 2024 Plavon PVC Super.</div>
    </footer>

    <script>
        const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
        const currentProductId = "<?php echo $product['product_id'] ?? ''; ?>";
        const maxStock = <?php echo $product['available_stock'] ?? 0; ?>;

        document.addEventListener('DOMContentLoaded', () => {
            setupDropdown();
            setupQuantityControl();
            setupAddToCart();
            
            // Tab Default
            const tabLinks = document.querySelectorAll('.tablink');
            if(tabLinks.length > 0) { tabLinks[0].click(); }
        });

        // --- 1. LOGIKA HITUNG DP ---
        function hitungDP() {
            const kuantitasInput = document.getElementById('kuantitas');
            const qty = parseInt(kuantitasInput.value);
            const basePrice = parseFloat(kuantitasInput.dataset.basePrice); 

            if (isNaN(qty) || qty < 1 || !basePrice) {
                document.getElementById('final-dp-amount').textContent = 'Rp 0';
                return;
            }

            const totalHarga = basePrice * qty;
            const totalDP = totalHarga * 0.3;
            
            document.getElementById('final-dp-amount').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(totalDP)}`;
        }

        // --- 2. KONTROL KUANTITAS ---
        function setupQuantityControl() {
            const kuantitasInput = document.getElementById('kuantitas');
            if (!kuantitasInput) return; 

            const updateHandler = () => hitungDP();

            document.getElementById('tambah-qty').addEventListener('click', () => {
                let currentVal = parseInt(kuantitasInput.value);
                if(currentVal < maxStock) {
                    kuantitasInput.value = currentVal + 1;
                    updateHandler();
                } else {
                    alert('Maksimal stok tersedia tercapai.');
                }
            });

            document.getElementById('kurang-qty').addEventListener('click', () => {
                let currentQty = parseInt(kuantitasInput.value);
                if (currentQty > 1) { 
                    kuantitasInput.value = currentQty - 1;
                }
                updateHandler();
            });

            kuantitasInput.addEventListener('change', () => {
                let val = parseInt(kuantitasInput.value);
                if(val < 1) kuantitasInput.value = 1;
                if(val > maxStock) kuantitasInput.value = maxStock;
                updateHandler();
            });
        }

        // --- 3. LOGIKA TOMBOL ADD TO CART ---
        function setupAddToCart() {
            const btn = document.getElementById('add-to-cart-btn');
            if(!btn) return;
            
            btn.addEventListener('click', function() {
                // Cek Login
                if (!isLoggedIn) {
                    if(confirm("Anda harus Login untuk membeli barang. Ke halaman Login sekarang?")) {
                        window.location.href = 'login.php';
                    }
                    return;
                }

                // Jika sudah login, lanjut ke cart
                const qty = document.getElementById('kuantitas').value;
                if(currentProductId) {
                    window.location.href = `cart.php?action=add&id=${currentProductId}&qty=${qty}`;
                }
            });
        }

        // --- 4. FUNGSI TAB ---
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            if(evt && evt.currentTarget) {
                evt.currentTarget.className += " active";
            }
        }
        
        // --- 5. DROPDOWN ---
        function setupDropdown() {
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
        }
    </script>
</body>
</html>