<?php
session_start();
// Pastikan file db_connect.php ada di root
include 'db_connect.php'; 

$message = null;
$message_type = ''; 

// 1. Cek Keamanan Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}

// 2. Proses Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = $_POST['name'] ?? '';
    $sku = $_POST['sku'] ?? '';
    $price = $_POST['price'] ?? 0;
    $unit = $_POST['unit'] ?? ''; 
    $description = $_POST['description'] ?? '';
    $initial_stock = $_POST['initial_stock'] ?? 0;
    
    // Default gambar jika tidak ada upload
    $image_url = 'placeholder.jpg'; 

    // Validasi Dasar
    if (empty($name) || $price <= 0 || $initial_stock < 0) {
        $message = "Gagal: Nama, Harga, dan Stok Awal wajib diisi dengan benar.";
        $message_type = 'error';
    } else {
        
        // --- LOGIKA UPLOAD GAMBAR ---
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            // Buat folder jika belum ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = basename($_FILES['image']['name']);
            // Tambah timestamp agar nama file unik
            $targetFilePath = $uploadDir . time() . "_" . $fileName; 
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            // Validasi tipe file
            $allowTypes = array('jpg','png','jpeg','gif');
            if(in_array(strtolower($fileType), $allowTypes)){
                if(move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)){
                    $image_url = $targetFilePath; // Simpan path ini ke DB
                } else {
                    $message = "Gagal mengupload gambar.";
                    $message_type = 'error';
                }
            } else {
                $message = "Format file tidak didukung (Hanya JPG, PNG, GIF).";
                $message_type = 'error';
            }
        }
        // -----------------------------

        if (empty($message)) { 
            $name = $conn->real_escape_string($name);
            $sku = $conn->real_escape_string($sku);
            $price = (float) $price;
            $unit = $conn->real_escape_string($unit);
            $description = $conn->real_escape_string($description);
            $initial_stock = (int) $initial_stock;
            $image_url = $conn->real_escape_string($image_url);

            // Mulai Transaksi Database
            $conn->begin_transaction();

            try {
                // 1. INSERT Produk
                $query_product = "INSERT INTO Products (name, sku, price, unit, description, image_url) VALUES ('$name', '$sku', $price, '$unit', '$description', '$image_url')";
                
                if ($conn->query($query_product) !== TRUE) {
                    throw new Exception("Gagal menyimpan data produk: " . $conn->error);
                }
                
                $new_product_id = $conn->insert_id;
                
                // 2. INSERT Stok Awal
                $query_stock = "INSERT INTO Stock (product_id, quantity_available, quantity_locked) VALUES ($new_product_id, $initial_stock, 0)";
                
                if ($conn->query($query_stock) !== TRUE) {
                    throw new Exception("Gagal menyimpan stok awal.");
                }

                $conn->commit();
                $message = "Sukses! Material <strong>{$name}</strong> berhasil ditambahkan.";
                $message_type = 'success';

            } catch (Exception $e) {
                $conn->rollback();
                $message = "Terjadi Kesalahan: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Material Baru - Admin PUNCAK JAYA PLAVON PVC</title> 
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .alert-admin { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
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
                <h2>Tambah Material Baru</h2>
                <div class="admin-profile">
                    <a href="admin_stock.php" class="btn-small" style="background:#6c757d; color:white; text-decoration:none; padding: 8px 15px;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </header>
            
            <?php if ($message): ?>
            <div class="alert-admin alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <section class="detail-box" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <form method="POST" action="" enctype="multipart/form-data"> 
                    
                    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                        
                        <div style="flex: 1; min-width: 300px;">
                            <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #333;">Informasi Dasar</h3>
                            
                            <div class="form-group-admin">
                                <label>Nama Material <span style="color:red">*</span></label>
                                <input type="text" name="name" placeholder="Contoh: Plavon PVC Motif Kayu" required>
                            </div>
                            
                            <div class="form-group-admin">
                                <label>SKU / Kode Barang</label>
                                <input type="text" name="sku" placeholder="Contoh: PVC-KY-01">
                            </div>

                            <div class="form-group-admin">
                                <label>Harga Satuan (Rp) <span style="color:red">*</span></label>
                                <input type="number" name="price" min="0" placeholder="0" required>
                            </div>
                            
                            <div class="form-group-admin">
                                <label>Satuan Unit <span style="color:red">*</span></label>
                                <input type="text" name="unit" placeholder="Contoh: Lembar, Batang, Dus" required>
                            </div>
                        </div>

                        <div style="flex: 1; min-width: 300px;">
                            <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #333;">Stok & Gambar</h3>
                            
                            <div class="form-group-admin">
                                <label>Stok Awal <span style="color:red">*</span></label>
                                <input type="number" name="initial_stock" min="0" value="0" required>
                                <small style="color:#666;">Jumlah fisik barang yang siap dijual saat ini.</small>
                            </div>
                            
                            <div class="form-group-admin">
                                <label>Upload Gambar</label>
                                <input type="file" name="image" accept="image/*" style="padding: 5px;">
                            </div>

                            <div class="form-group-admin">
                                <label>Deskripsi Produk</label>
                                <textarea name="description" rows="4" placeholder="Jelaskan spesifikasi produk..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                    <button type="submit" class="btn-primary full-width-btn" style="width: 100%; padding: 15px; font-size: 1.1em; font-weight: bold;">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>

                </form>
            </section>
        </main>
    </div>
</body>
</html>