<?php
session_start();
include 'db_connect.php';

// 1. Cek Login: Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// 2. Validasi ID Pesanan
$order_id = $_GET['id'] ?? null;
if (!$order_id) { 
    // Jika tidak ada ID, lempar kembali ke dashboard
    header("Location: dashboard.php"); 
    exit(); 
}

// 3. Ambil Detail Pesanan dari Database
$safe_id = $conn->real_escape_string($order_id);
$user_id = $_SESSION['user_id'];

// Pastikan pesanan milik user yang sedang login
$query = "SELECT * FROM Orders WHERE order_id = '$safe_id' AND user_id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    // Tampilkan pesan error yang user-friendly jika tidak ditemukan
    die('
    <!DOCTYPE html>
    <html>
    <head><title>Error</title><link rel="stylesheet" href="style.css"></head>
    <body style="display:flex; justify-content:center; align-items:center; height:100vh; text-align:center; background:#f4f4f9;">
        <div style="background:white; padding:30px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
            <h2 style="color:#dc3545;">Pesanan Tidak Ditemukan</h2>
            <p>Mohon maaf, pesanan #'.$safe_id.' tidak ditemukan atau bukan milik akun Anda.</p>
            <a href="dashboard.php" class="btn-primary" style="margin-top:20px; display:inline-block; text-decoration:none; padding:10px 20px;">Kembali ke Dashboard</a>
        </div>
    </body>
    </html>
    ');
}

$order = $result->fetch_assoc();

// 4. Format Rupiah
function formatRupiah($angka) { return number_format($angka, 0, ',', '.'); }

// 5. Logika Tampilan Judul (DP atau Pelunasan)
$page_title = "Pembayaran DP";
$amount_label = "Total DP Harus Dibayar:";
$amount_value = $order['dp_paid_amount'];
$amount_note = "(30% dari Total Transaksi)";

// Jika ini adalah link pelunasan (dari dashboard, status 70_PENDING atau ARRIVED_LOC)
if (isset($_GET['type']) && $_GET['type'] == 'pelunasan') {
    $page_title = "Pelunasan Pesanan (70%)";
    $amount_label = "Sisa Tagihan Pelunasan:";
    $amount_value = $order['remaining_70_amount'];
    $amount_note = "(70% Sisa Tagihan)";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - PUNCAK JAYA PLAVON PVC</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .payment-container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
        .amount-box { background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; border: 2px dashed #2196F3; }
        .amount-title { font-size: 0.9em; color: #555; margin-bottom: 5px; }
        .amount-value { font-size: 2em; font-weight: 700; color: #007bff; }
        .bank-info { text-align: left; margin: 20px 0; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .bank-logo { font-weight: bold; font-size: 1.2em; color: #333; }
        .rek-number { font-size: 1.4em; font-family: monospace; letter-spacing: 1px; color: #333; margin: 5px 0; }
        .copy-btn { font-size: 0.8em; color: #007bff; cursor: pointer; text-decoration: underline; }
        .btn-confirm-wa { display: block; width: 100%; padding: 15px; background-color: #25D366; color: white; text-decoration: none; font-weight: bold; border-radius: 6px; margin-top: 20px; transition: 0.3s; }
        .btn-confirm-wa:hover { background-color: #128C7E; }
        .btn-later { display: block; margin-top: 15px; color: #666; font-size: 0.9em; text-decoration: none;}
        .btn-later:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <header class="navbar-static">
        <div class="navbar container">
            <div class="logo">PUNCAK JAYA PLAVON PVC</div>
        </div>
    </header>

    <main class="payment-container">
        <i class="fas fa-check-circle" style="font-size: 4em; color: #28a745; margin-bottom: 20px;"></i>
        <h1>Pesanan Berhasil Dibuat!</h1>
        <p>ID Pesanan: <strong>#<?php echo $order['order_id']; ?></strong></p>
        <p>Silakan transfer pembayaran untuk segera memproses pesanan Anda.</p>

        <div class="amount-box">
            <div class="amount-title"><?php echo $amount_label; ?></div>
            <div class="amount-value">Rp <?php echo formatRupiah($amount_value); ?></div>
            <p style="font-size: 0.8em; color: #666; margin-top: 5px;"><?php echo $amount_note; ?></p>
        </div>

        <div class="bank-info">
            <p style="margin-bottom: 15px;">Silakan transfer kerekening berikut:</p>
            
            <div style="margin-bottom: 20px;">
                <div class="bank-logo"><i class="fas fa-university"></i> BANK BRI</div>
                <div class="rek-number">015501048189508</div>
                <div>a.n. <strong>Lukman</strong></div>
            </div>
        <?php
            // Buat Pesan WhatsApp Otomatis
            $wa_number = "6281279483767"; // GANTI DENGAN NOMOR WA ANDA (Format 62...)
            
            // Pesan berbeda untuk DP atau Pelunasan
            $type_msg = (isset($_GET['type']) && $_GET['type'] == 'pelunasan') ? "PELUNASAN 70%" : "DP 30%";
            
            $user_name = $_SESSION['user_name'] ?? 'Pelanggan';
            $message = "Halo Admin Material Super,%0A%0ASaya ingin konfirmasi pembayaran *$type_msg* untuk:%0AOrder ID: #{$order['order_id']}%0ANama: {$user_name}%0AJumlah: Rp " . formatRupiah($amount_value) . "%0A%0ABerikut saya lampirkan bukti transfer.";
            
            // Encode URL agar aman
            $wa_link = "https://wa.me/{$wa_number}?text={$message}";
        ?>

        <a href="<?php echo $wa_link; ?>" target="_blank" class="btn-confirm-wa">
            <i class="fab fa-whatsapp"></i> Konfirmasi Pembayaran Sekarang
        </a>

        <a href="dashboard.php" class="btn-later">Bayar Nanti & Ke Dashboard</a>
    </main>

</body>
</html>