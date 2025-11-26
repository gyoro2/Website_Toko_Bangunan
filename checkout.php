<?php
session_start();
include 'db_connect.php';

// 1. Cek Login: Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) { 
    $_SESSION['redirect_to_cart'] = true; 
    header("Location: login.php"); 
    exit(); 
}

// 2. Cek Keranjang: Jika kosong, redirect ke katalog
if (empty($_SESSION['cart'])) { 
    header("Location: katalog.php"); 
    exit(); 
}

// 3. Ambil Subtotal Material
$subtotal_material = 0;
foreach ($_SESSION['cart'] as $item) { 
    $subtotal_material += $item['price'] * $item['qty']; 
}

// 4. Ambil Setting Gudang (Koordinat Toko) dari Database
$settings_query = "SELECT * FROM Shop_Settings WHERE id = 1";
$result = $conn->query($settings_query);

if ($result && $result->num_rows > 0) {
    $settings = $result->fetch_assoc();
    $shopLat = $settings['shop_latitude'];
    $shopLng = $settings['shop_longitude'];
    $pricePerKm = $settings['price_per_km'];
    $minKm = $settings['min_km'];
} else {
    // Fallback (Koordinat Default)
    $shopLat = -6.38423335;
    $shopLng = 106.85251730;
    $pricePerKm = 10000;
    $minKm = 5;
}

function formatRupiah($angka) { return number_format($angka, 0, ',', '.'); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Material Super</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        #map { height: 350px; width: 100%; border-radius: 8px; margin-bottom: 15px; border: 2px solid #ddd; z-index: 1; }
        .info-box { background: #e3f2fd; padding: 12px; border-radius: 4px; font-size: 0.95em; margin-bottom: 15px; border-left: 5px solid #2196F3; display: flex; align-items: center; gap: 10px; }
        .leaflet-control-geocoder { z-index: 9999; }
        .summary-highlight { color: #007bff; font-weight: bold; }
    </style>
</head>
<body>

    <header class="navbar-static">
        <div class="navbar container">
            <div class="logo">MATERIAL SUPER</div>
            <nav>
                <a href="index.php">Beranda</a>
                <a href="katalog.php">Katalog Produk</a>
                <a href="cara_beli.php">Cara Beli & DP</a>
                <a href="kontak_kami.php">Kontak Kami</a>
            </nav>
            <div class="utility-nav">
                <a href="cart.php">🛒</a>
                <div class="user-dropdown">
                    <a href="dashboard.php" class="user-icon active"><i class="fas fa-user"></i></a> 
                    <div class="dropdown-content">
                        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard Saya</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="checkout-main container">
        <h1>Checkout Pengiriman</h1>
        
        <div class="checkout-wrapper">
            <div class="checkout-form">
                <h2>1. Tentukan Lokasi Proyek</h2>
                
                <div class="info-box">
                    <i class="fas fa-map-marker-alt"></i> 
                    <span>Gunakan <strong>Pencarian</strong> atau <strong>Klik Peta</strong> untuk menandai lokasi proyek.</span>
                </div>

                <div id="map"></div>

                <form id="shipping-form">
                    <div class="form-group">
                        <label>Jarak Estimasi (Dihitung Otomatis)</label>
                        <input type="text" id="distance-display" value="0 km" disabled style="background:#f8f9fa; font-weight:bold; color:#333;">
                    </div>

                    <div class="form-group">
                        <label>Nama Penerima</label>
                        <input type="text" id="nama" required value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon (WA)</label>
                        <input type="tel" id="telepon" required>
                    </div>
                    <div class="form-group">
                        <label>Detail Alamat (Jalan, No Rumah, RT/RW)</label>
                        <textarea id="alamat" rows="2" required placeholder="Contoh: Jl. Melati No. 5, Pagar Hitam..."></textarea>
                    </div>
                </form>
            </div>

            <div class="ringkasan-pembayaran">
                <h2>Ringkasan Biaya</h2>
                
                <div class="summary-item">
                    <span>Subtotal Material</span>
                    <span>Rp <?php echo formatRupiah($subtotal_material); ?></span>
                </div>
                
                <div class="summary-item">
                    <span>Ongkos Kirim (<span id="km-count">0</span> km)</span>
                    <span id="ongkir-display" class="summary-highlight">Rp 0</span>
                </div>
                
                <hr>

                <div class="summary-item total-penuh">
                    <strong>TOTAL HARGA PENUH</strong>
                    <strong id="total-harga-penuh-display">Rp <?php echo formatRupiah($subtotal_material); ?></strong>
                </div>

                <div class="dp-calculation-box" style="margin-top: 20px; padding:15px; background:#fff3cd; border:1px solid #ffeeba; border-radius:4px;">
                    <h3>PEMBAYARAN SAAT INI (DP 30%)</h3>
                    <div class="summary-item dp-amount">
                        <span>Wajib Dibayar Sekarang</span>
                        <span id="dp-amount-display" style="font-size:1.3em; font-weight:bold; color:#e67e22;">Rp 0</span>
                    </div>
                </div>

                <div class="terms-check" style="margin-top:15px;">
                    <input type="checkbox" id="agree-terms" required>
                    <label for="agree-terms">Saya setuju dengan <a href="syarat_ketentuan.php" target="_blank">Syarat & Ketentuan</a>.</label>
                </div>

                <button class="btn-checkout-final" onclick="submitCheckout()" style="width:100%; padding:15px; background:#28a745; color:white; border:none; border-radius:6px; font-size:1.1em; cursor:pointer; margin-top:10px; font-weight:bold;">
                    <i class="fas fa-lock"></i> Bayar DP Sekarang
                </button>
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    
    <script>
        const shopLat = <?php echo $shopLat; ?>;
        const shopLng = <?php echo $shopLng; ?>;
        const pricePerKm = <?php echo $pricePerKm; ?>;
        const minKm = <?php echo $minKm; ?>;
        const subtotalMaterial = <?php echo $subtotal_material; ?>;

        let currentOngkir = 0;
        let currentDistance = 0;
        let userMarker = null;
        let routeLine = null;

        const map = L.map('map').setView([shopLat, shopLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const shopIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
        });
        L.marker([shopLat, shopLng], {icon: shopIcon}).addTo(map)
            .bindPopup("<b>Gudang Kami</b><br>Titik Pengiriman").openPopup();

        L.Control.geocoder({ defaultMarkGeocode: false })
        .on('markgeocode', function(e) {
            const center = e.geocode.center;
            updateUserLocation(center.lat, center.lng);
            map.setView(center, 15);
        })
        .addTo(map);

        map.on('click', function(e) {
            updateUserLocation(e.latlng.lat, e.latlng.lng);
        });

        function updateUserLocation(lat, lng) {
            if (userMarker) map.removeLayer(userMarker);
            if (routeLine) map.removeLayer(routeLine);

            const userIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                iconSize: [25, 41], iconAnchor: [12, 41]
            });
            
            userMarker = L.marker([lat, lng], {icon: userIcon, draggable: true}).addTo(map);
            userMarker.on('dragend', function(event) {
                const pos = event.target.getLatLng();
                updateUserLocation(pos.lat, pos.lng);
            });

            routeLine = L.polyline([[shopLat, shopLng], [lat, lng]], {color: 'red', weight: 3, opacity: 0.7, dashArray: '10, 10'}).addTo(map);
            calculateDistance(lat, lng);
        }

        function calculateDistance(lat2, lon2) {
            const R = 6371; 
            const dLat = deg2rad(lat2 - shopLat);
            const dLon = deg2rad(lon2 - shopLng);
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(deg2rad(shopLat)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            let d = R * c; 
            d = d * 1.3; 
            if (d < minKm) d = minKm;
            updateCost(d);
        }

        function deg2rad(deg) { return deg * (Math.PI/180); }

        function updateCost(dist) {
            currentDistance = dist.toFixed(1);
            let rawPrice = dist * pricePerKm;
            currentOngkir = Math.ceil(rawPrice / 500) * 500;

            document.getElementById('distance-display').value = currentDistance + " km";
            document.getElementById('km-count').innerText = currentDistance;

            const totalFull = subtotalMaterial + currentOngkir;
            const dpAmount = totalFull * 0.30;
            const sisaAmount = totalFull * 0.70;

            document.getElementById('ongkir-display').innerText = formatRupiahJS(currentOngkir);
            document.getElementById('total-harga-penuh-display').innerText = formatRupiahJS(totalFull);
            document.getElementById('dp-amount-display').innerText = formatRupiahJS(dpAmount);
            document.getElementById('sisa-amount-display').innerText = formatRupiahJS(sisaAmount);
        }

        function formatRupiahJS(angka) {
            return "Rp " + new Intl.NumberFormat('id-ID').format(angka);
        }

        function submitCheckout() {
            if (!document.getElementById('agree-terms').checked) {
                alert("Mohon setujui Syarat & Ketentuan."); return;
            }
            if (currentOngkir === 0) {
                alert("Silakan pilih lokasi pengiriman di peta."); return;
            }

            const nama = document.getElementById('nama').value;
            const alamat = document.getElementById('alamat').value;
            const telepon = document.getElementById('telepon').value;

            if(!nama || !alamat || !telepon) {
                alert("Mohon lengkapi detail penerima."); return;
            }

            const totalFull = subtotalMaterial + currentOngkir;
            const dpAmount = totalFull * 0.30;
            
            const orderData = {
                delivery_recipient: nama,
                delivery_address: alamat + " (Jarak: " + currentDistance + "km)",
                delivery_phone: telepon,
                delivery_fee: currentOngkir
            };

            const btn = document.querySelector('.btn-checkout-final');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;

            // Panggil API (Pastikan path benar)
            fetch('api/checkout/start.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // REDIRECT KE PAYMENT.PHP
                    window.location.href = 'payment.php?id=' + data.order_id;
                } else {
                    alert('Gagal: ' + data.message);
                    btn.innerHTML = 'Bayar DP Sekarang';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
                btn.innerHTML = 'Bayar DP Sekarang';
                btn.disabled = false;
            });
        }

        // Dropdown
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