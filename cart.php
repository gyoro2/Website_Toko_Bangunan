<?php
session_start();
include 'db_connect.php'; 

// 1. Cek Login (Proteksi Halaman)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to_cart'] = true;
    header("Location: login.php");
    exit();
}

// Inisialisasi Keranjang
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

// --- LOGIKA TAMBAH ITEM ---
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = $conn->real_escape_string($_GET['id']);
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    if ($qty < 1) $qty = 1;

    $query = "SELECT product_id, name, price, image_url, unit FROM Products WHERE product_id = '$product_id' LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $product_data = $result->fetch_assoc();
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $product_id) {
                $_SESSION['cart'][$key]['qty'] += $qty;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product_data['product_id'],
                'name' => $product_data['name'],
                'price' => (float)$product_data['price'],
                'image' => $product_data['image_url'],
                'qty' => $qty,
                'unit' => $product_data['unit']
            ];
        }
    }
    header("Location: cart.php");
    exit();
}

// --- LOGIKA HAPUS ITEM ---
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['index'])) {
    $index = (int)$_GET['index'];
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1); 
    }
    header("Location: cart.php");
    exit();
}

function formatRupiah($angka) { return number_format($angka, 0, ',', '.'); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - PUNCAK JAYA PLAVON PVC</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS Tambahan Khusus Cart agar lebih Fresh */
        .cart-img {
            width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .cart-table th { background: #f1f5f9; color: #475569; text-transform: uppercase; font-size: 0.85em; letter-spacing: 0.5px; }
        .qty-badge {
            background: #e2e8f0; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.9em; display: inline-block;
        }
        .subtotal-val { color: #007bff; font-weight: bold; }
        .btn-remove { color: #ef4444; transition: 0.3s; }
        .btn-remove:hover { color: #b91c1c; transform: scale(1.1); }
    </style>
</head>
<body>

    <header class="navbar-static">
        <div class="navbar container">
            <div class="logo">PUNCAK JAYA PLAVON PVC</div>
            <nav>
                <a href="index.php">Beranda</a>
                <a href="katalog.php">Katalog</a>
                <a href="cara_beli.php">Cara Beli</a>
                <a href="kontak_kami.php">Kontak</a>
            </nav>
            <div class="utility-nav">
                <a href="cart.php" class="active">🛒</a>
                <div class="user-dropdown">
                    <a href="dashboard.php" class="user-icon active"><i class="fas fa-user"></i></a> 
                    <div class="dropdown-content">
                        <a href="dashboard.php">Dashboard</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="breadcrumb container">
        <a href="index.php">Home</a> / Keranjang Belanja
    </div>

    <main class="cart-main container">
        <h1 style="margin-bottom: 30px; font-size: 2em; color: #1e293b;">Keranjang Belanja Anda</h1>

        <div class="cart-wrapper">
            
            <div class="cart-items-list" style="flex: 2;">
                
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Produk</th>
                            <th style="width: 15%;">Harga</th>
                            <th style="width: 15%; text-align: center;">Qty</th>
                            <th style="width: 20%;">Subtotal</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotal_material = 0;
                        
                        if (empty($_SESSION['cart'])): 
                        ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 50px;">
                                    <i class="fas fa-shopping-basket" style="font-size: 3em; color: #ccc; margin-bottom: 15px;"></i>
                                    <p style="font-size:1.1em; color:#666;">Keranjang Anda masih kosong.</p>
                                    <a href="katalog.php" class="btn-primary" style="margin-top:15px; display:inline-block;">Mulai Belanja</a>
                                </td>
                            </tr>
                        <?php else: 
                            foreach ($_SESSION['cart'] as $index => $item): 
                                $line_total = $item['price'] * $item['qty'];
                                $subtotal_material += $line_total;
                        ?>
                            <tr class="cart-item">
                                <td style="display: flex; align-items: center;">
                                    <img src="<?php echo htmlspecialchars($item['image'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-img">
                                    <div>
                                        <h4 style="margin:0; font-size: 1em; color: #333;"><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <span style="font-size:0.8em; color:#888;">Unit: <?php echo $item['unit']; ?></span>
                                    </div>
                                </td>
                                <td>Rp <?php echo formatRupiah($item['price']); ?></td>
                                <td style="text-align: center;">
                                    <span class="qty-badge"><?php echo $item['qty']; ?></span>
                                </td>
                                <td class="subtotal-val">Rp <?php echo formatRupiah($line_total); ?></td>
                                <td style="text-align: center;">
                                    <a href="cart.php?action=remove&index=<?php echo $index; ?>" class="btn-remove" onclick="return confirm('Hapus item ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>

                <div style="margin-top: 20px;">
                    <a href="katalog.php" style="color: #007bff; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-arrow-left"></i> Lanjutkan Belanja
                    </a>
                </div>
            </div>

            <div class="cart-summary-box" style="flex: 1; position: sticky; top: 20px;">
                <?php 
                    $delivery_fee = 75000; // Estimasi Awal
                    $total_full = $subtotal_material > 0 ? ($subtotal_material + $delivery_fee) : 0;
                    $dp_amount = $total_full * 0.30;
                ?>
                <h2 style="font-size: 1.3em; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Ringkasan Pesanan</h2>
                
                <div class="summary-item">
                    <span>Total Material</span>
                    <span style="font-weight: 600;">Rp <?php echo formatRupiah($subtotal_material); ?></span>
                </div>
                <div class="summary-item">
                    <span>Estimasi Ongkir</span>
                    <span style="font-weight: 600;">Rp <?php echo ($subtotal_material > 0) ? formatRupiah($delivery_fee) : '0'; ?></span>
                </div>
                
                <hr style="margin: 15px 0; border: 0; border-top: 1px dashed #ccc;">

                <div class="summary-item total-penuh">
                    <span style="font-size: 1.1em;">Total Tagihan</span>
                    <strong style="font-size: 1.3em; color: #2c3e50;">Rp <?php echo formatRupiah($total_full); ?></strong>
                </div>

                <div class="dp-info-summary" style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404;">
                    <p style="font-size: 0.9em; margin: 0;">
                        <i class="fas fa-info-circle"></i> Anda cukup membayar DP 30% sekarang:
                        <br><strong style="font-size: 1.2em; display: block; margin-top: 5px;">Rp <?php echo formatRupiah($dp_amount); ?></strong>
                    </p>
                </div>

                <?php if (!empty($_SESSION['cart'])): ?>
                    <a href="checkout.php" class="btn-checkout-cart" style="background: #e67e22;">
                        Lanjut ke Pembayaran <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                    </a>
                <?php else: ?>
                    <button class="btn-checkout-cart" disabled style="background-color: #e2e8f0; color: #94a3b8; cursor: not-allowed;">Keranjang Kosong</button>
                <?php endif; ?>
            </div>

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
        // Dropdown Logic
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