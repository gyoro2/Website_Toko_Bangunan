<?php
session_start();
include 'db_connect.php'; 

$products = [];
$conn = new mysqli($servername, $username, $password, $dbname);

// --- LOGIKA PENCARIAN & FILTER ---
$where_clauses = [];

// 1. Cek Stok Tersedia (Wajib)
$where_clauses[] = "((s.quantity_available - s.quantity_locked) > 0 OR s.quantity_available IS NULL)";

// 2. Filter Kategori (Tombol)
if (isset($_GET['cat']) && $_GET['cat'] != 'all') {
    $cat = $conn->real_escape_string($_GET['cat']);
    $where_clauses[] = "(p.name LIKE '%$cat%' OR p.description LIKE '%$cat%')"; 
}

// 3. Filter Pencarian (Search Bar)
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $where_clauses[] = "(p.name LIKE '%$keyword%' OR p.description LIKE '%$keyword%' OR p.sku LIKE '%$keyword%')"; 
}

$where_sql = implode(" AND ", $where_clauses);

// Query Data (Database Sederhana: Products + Stock)
$query = "
    SELECT 
        p.product_id, p.name, p.price, p.unit, p.description, p.image_url,
        (s.quantity_available - s.quantity_locked) AS available_stock
    FROM Products p
    LEFT JOIN Stock s ON p.product_id = s.product_id
    WHERE $where_sql
    ORDER BY p.product_id DESC
";

$result = $conn->query($query);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();

function formatRupiah($angka) { return number_format($angka, 0, ',', '.'); }
$is_logged_in = isset($_SESSION['user_id']);
$dashboard_url = $is_logged_in ? 'dashboard.php' : 'login.php';
$search_value = $_GET['keyword'] ?? ''; // Untuk mengisi ulang kotak pencarian
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Plavon & Interior - Material Super</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS KHUSUS KATALOG */
        .katalog-main { display: block; padding-top: 30px; } 
        
        .filter-top-bar {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;
            display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: space-between;
        }
        .filter-categories { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
        .cat-btn {
            padding: 8px 20px; border: 1px solid #ddd; border-radius: 25px;
            background: #fff; color: #555; font-size: 0.9em; text-decoration: none;
            transition: 0.3s; white-space: nowrap;
        }
        .cat-btn:hover, .cat-btn.active { background: #2c3e50; color: white; border-color: #2c3e50; }
        
        .search-form { display: flex; gap: 5px; align-items: center; }
        .search-input { padding: 8px 15px; border-radius: 20px; border: 1px solid #ddd; outline: none; width: 200px; transition: 0.3s; }
        .search-input:focus { border-color: #FFA500; box-shadow: 0 0 5px rgba(255,165,0,0.2); }
        .btn-search-small { background: #2c3e50; color: white; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .btn-search-small:hover { background: #FFA500; }

        .katalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 25px; }
        .katalog-card {
            background: white; border-radius: 10px; overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #eee;
            display: flex; flex-direction: column; height: 100%; transition: 0.3s;
        }
        .katalog-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        
        .katalog-card img { width: 100%; height: 200px; object-fit: cover; display: block; }
        .card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; text-align: center; align-items: center; }
        .product-category { background: #e3f2fd; color: #007bff; padding: 4px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 600; margin-bottom: 10px; display: inline-block; }
        .katalog-card h3 { font-size: 1.1em; margin: 5px 0 10px; color: #333; font-weight: 700; width: 100%; }
        .deskripsi-singkat { color: #666; font-size: 0.9em; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 3em; }
        .main-price { font-size: 1.2em; font-weight: bold; color: #2c3e50; margin-bottom: 5px; }
        .dp-label { font-size: 0.9em; color: #e67e22; font-weight: 600; margin-bottom: 15px; }
        
        .btn-action-group { display: flex; gap: 10px; width: 100%; margin-top: auto; }
        .btn-action-group a { flex: 1; text-align: center; padding: 8px; border-radius: 6px; font-size: 0.9em; font-weight: 600; text-decoration: none; }
        .btn-outline { border: 1px solid #2c3e50; color: #2c3e50; }
        .btn-outline:hover { background: #2c3e50; color: white; }
        .btn-fill { background: #FFA500; color: white; border: none; }
        .btn-fill:hover { background: #e67e22; }
    </style>
</head>
<body>

    <header class="navbar-static">
        <div class="navbar container">
            <div class="logo">PLAVON PVC</div>
            <nav>
                <a href="index.php">Beranda</a>
                <a href="katalog.php" class="active">Katalog</a>
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

    <main class="katalog-main container">
        
        <div class="filter-top-bar">
            <div class="filter-categories">
                <a href="katalog.php" class="cat-btn <?php echo (!isset($_GET['cat']) && !isset($_GET['keyword'])) ? 'active' : ''; ?>">Semua</a>
                <a href="katalog.php?cat=PVC" class="cat-btn <?php echo (isset($_GET['cat']) && $_GET['cat']=='PVC')?'active':''; ?>">Plavon PVC</a>
                <a href="katalog.php?cat=WPC" class="cat-btn <?php echo (isset($_GET['cat']) && $_GET['cat']=='WPC')?'active':''; ?>">WPC Panel</a>
                <a href="katalog.php?cat=Lis" class="cat-btn <?php echo (isset($_GET['cat']) && $_GET['cat']=='Lis')?'active':''; ?>">Lis Siku</a>
                <a href="katalog.php?cat=Wallboard" class="cat-btn <?php echo (isset($_GET['cat']) && $_GET['cat']=='Wallboard')?'active':''; ?>">Wallboard</a>
            </div>
            
            <form action="katalog.php" method="GET" class="search-form">
                <input type="text" name="keyword" class="search-input" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search_value); ?>">
                <button type="submit" class="btn-search-small"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="katalog-grid">
            <?php if (empty($products)): ?>
                <div style="grid-column:1/-1; text-align:center; padding:50px;">
                    <i class="fas fa-search" style="font-size:3em; color:#ddd; margin-bottom:15px;"></i>
                    <p style="font-size:1.2em; color:#777;">Produk tidak ditemukan.</p>
                    <a href="katalog.php" class="btn-primary" style="margin-top:10px;">Lihat Semua</a>
                </div>
            <?php endif; ?>

            <?php foreach ($products as $product): 
                $dpPrice = $product['price'] * 0.30;
                $detailLink = "detail_produk.php?id=" . $product['product_id'];
                $cartLink = $is_logged_in 
                    ? "cart.php?action=add&id=" . $product['product_id'] . "&qty=1" 
                    : "login.php";
                
                $deskripsi = htmlspecialchars($product['description']);
            ?>
            <div class="katalog-card">
                <a href="<?php echo $detailLink; ?>" style="width:100%;">
                    <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </a>
                
                <div class="card-content">
                    <span class="product-category">Interior</span>
                    
                    <h3><a href="<?php echo $detailLink; ?>" style="color:#333; text-decoration:none;"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                    
                    <p class="deskripsi-singkat" title="<?php echo $deskripsi; ?>">
                        <?php echo $deskripsi; ?>
                    </p>

                    <p class="main-price">Rp <?php echo formatRupiah($product['price']); ?> / <?php echo $product['unit']; ?></p>
                    <p class="dp-label">DP 30%: Rp <?php echo formatRupiah($dpPrice); ?></p>
                    
                    <div class="btn-action-group">
                        <a href="<?php echo $detailLink; ?>" class="btn-outline">Detail</a>
                        <a href="<?php echo $cartLink; ?>" class="btn-fill" onclick="<?php echo !$is_logged_in ? "alert('Silakan Login dulu.');" : ""; ?>"> 
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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
            // Dropdown
            const dropdowns = document.querySelectorAll('.user-dropdown');
            dropdowns.forEach(d => {
                const icon = d.querySelector('.user-icon');
                const content = d.querySelector('.dropdown-content');
                icon.addEventListener('click', (e) => {
                    e.preventDefault(); e.stopPropagation();
                    content.style.display = content.style.display === 'block' ? 'none' : 'block';
                });
                document.addEventListener('click', () => content.style.display = 'none');
            });
        });
    </script>
</body>
</html>