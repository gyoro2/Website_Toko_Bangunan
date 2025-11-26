<?php
session_start();
include 'db_connect.php'; 

// 1. Cek Keamanan
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); exit();
}

$product_id = $_GET['id'] ?? null;
$product = null;
$message = "";

// 2. LOGIKA UPDATE DATA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product_id) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = (float) $_POST['price'];
    $unit = $conn->real_escape_string($_POST['unit']);
    $description = $conn->real_escape_string($_POST['description']);
    $new_stock_available = (int) $_POST['quantity_available'];

    $conn->begin_transaction();
    try {
        // --- LOGIKA UPLOAD GAMBAR BARU ---
        $image_sql_part = ""; // Default: tidak ubah gambar
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "uploads/";
            // Buat folder jika belum ada
            if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
            
            $file_name = time() . "_" . basename($_FILES["image"]["name"]); // Nama file unik
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Jika upload sukses, tambahkan ke query update
                $image_sql_part = ", image_url = '$target_file'";
            } else {
                throw new Exception("Gagal mengupload gambar.");
            }
        }
        // ---------------------------------

        // Update Info Produk (Termasuk Gambar jika ada)
        $q_prod = "UPDATE Products SET name='$name', price='$price', unit='$unit', description='$description' $image_sql_part WHERE product_id='$product_id'";
        if (!$conn->query($q_prod)) throw new Exception($conn->error);

        // Update Stok
        $q_stock = "UPDATE Stock SET quantity_available='$new_stock_available' WHERE product_id='$product_id'";
        if (!$conn->query($q_stock)) throw new Exception($conn->error);

        $conn->commit();
        $message = "<div class='alert-admin alert-success'>Produk berhasil diperbarui!</div>";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "<div class='alert-admin alert-error'>Gagal update: " . $e->getMessage() . "</div>";
    }
}

// 3. AMBIL DATA PRODUK (GET)
if ($product_id) {
    $safe_id = $conn->real_escape_string($product_id);
    $query = "
        SELECT p.*, s.quantity_available, s.quantity_locked 
        FROM Products p 
        LEFT JOIN Stock s ON p.product_id = s.product_id 
        WHERE p.product_id = '$safe_id'
    ";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin PUNCAK JAYA PLAVON PVC</title> 
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .form-group-admin { margin-bottom: 15px; }
        .form-group-admin label { display: block; font-weight: 600; margin-bottom: 5px; }
        .form-group-admin input, .form-group-admin textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .readonly-field { background-color: #e9ecef; cursor: not-allowed; color: #666; }
        .alert-admin { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .current-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; margin-top: 10px; }
    </style>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        
        <aside class="admin-sidebar">
            <div class="admin-logo">MS ADMIN</div>
            <ul class="admin-menu">
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="admin_orders.php"><i class="fas fa-boxes"></i> Manajemen Pesanan</a></li>
                <li><a href="admin_stock.php" class="admin-active"><i class="fas fa-warehouse"></i> Manajemen Stok</a></li>
                <li><a href="admin_reports.php"><i class="fas fa-file-invoice-dollar"></i> Laporan Keuangan</a></li>
                <li class="logout-link"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h2>Edit Produk & Stok</h2>
                <div class="admin-profile">
                    <a href="admin_stock.php" class="btn-back" style="background:#6c757d; color:white; padding:8px 15px; text-decoration:none; border-radius:4px;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </header>

            <?php echo $message; ?>

            <?php if ($product): ?>
            <section class="detail-box" style="background:white; padding:30px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                
                <form method="POST" enctype="multipart/form-data">
                    
                    <div style="display:flex; gap:30px; flex-wrap:wrap;">
                        <div style="flex:1; min-width: 300px;">
                            <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Informasi Material</h3>
                            
                            <div class="form-group-admin">
                                <label>Nama Produk</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>
                            
                            <div class="form-group-admin">
                                <label>SKU (Kode)</label>
                                <input type="text" value="<?php echo htmlspecialchars($product['sku']); ?>" disabled class="readonly-field">
                            </div>
                            
                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group-admin" style="flex: 1;">
                                    <label>Harga Satuan (Rp)</label>
                                    <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
                                </div>
                                <div class="form-group-admin" style="width: 100px;">
                                    <label>Satuan</label>
                                    <input type="text" name="unit" value="<?php echo htmlspecialchars($product['unit']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group-admin">
                                <label>Ganti Gambar (Opsional)</label>
                                <input type="file" name="image" accept="image/*">
                                <?php if(!empty($product['image_url'])): ?>
                                    <div style="margin-top:5px;">
                                        <span style="font-size:0.8em; color:#666;">Gambar Saat Ini:</span><br>
                                        <img src="<?php echo $product['image_url']; ?>" class="current-img" alt="Current Image">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div style="flex:1; min-width: 300px; background: #f8f9fa; padding: 20px; border-radius: 8px;">
                            <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">Manajemen Stok</h3>
                            
                            <div class="form-group-admin">
                                <label style="color:#dc3545;">Stok Terkunci (Sedang Dipesan)</label>
                                <input type="text" value="<?php echo $product['quantity_locked']; ?>" disabled class="readonly-field" style="font-weight: bold;">
                            </div>

                            <div class="form-group-admin">
                                <label style="color:#28a745;">Stok Fisik Total (Gudang)</label>
                                <input type="number" name="quantity_available" value="<?php echo $product['quantity_available']; ?>" min="<?php echo $product['quantity_locked']; ?>" required style="border: 2px solid #28a745;">
                            </div>

                            <div class="form-group-admin">
                                <label>Deskripsi</label>
                                <textarea name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <hr style="margin:30px 0; border:0; border-top:1px solid #eee;">
                    
                    <button type="submit" class="btn-primary full-width-btn" style="width:100%; padding:15px; font-size:1.1em; font-weight: bold;">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </section>
            <?php else: ?>
                <div class="alert-admin alert-error">Produk tidak ditemukan.</div>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>