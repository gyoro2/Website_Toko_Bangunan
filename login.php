<?php
// --- FILE INI MENANGANI KEDUA SISI: TAMPILAN (HTML) DAN LOGIKA API (POST) ---

// Logika ini hanya berjalan jika ada permintaan POST (dari JavaScript)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Header untuk respons API
    header("Content-Type: application/json; charset=UTF-8");
    
    // --- START: LOGIKA KONEKSI ---
    // Pastikan db_connect.php ada di folder yang sama atau sesuaikan path-nya
    // Jika db_connect.php ada di root dan login.php juga di root, gunakan include 'db_connect.php';
    // Jika db_connect.php ada di root dan login.php ada di /api/auth/, gunakan include '../../db_connect.php';
    
    // Disini kita asumsikan db_connect.php ada di folder yang sama untuk kemudahan, 
    // ATAU kita tulis ulang koneksi di sini agar file ini benar-benar mandiri (standalone).
    
    $servername = "localhost";
    $username = "root"; 
    $password = "";     
    $dbname = "material_db"; // PASTIKAN NAMA INI BENAR

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["message" => "Koneksi Database Gagal: " . $conn->connect_error]);
        exit();
    }
    // --- END: LOGIKA KONEKSI ---

    $data = json_decode(file_get_contents("php://input"));
    
    // Tentukan apakah ini permintaan Registrasi atau Login berdasarkan keberadaan field 'full_name'
    $is_register_attempt = isset($data->full_name);

    if ($is_register_attempt) {
        // ====================================================================
        //                       LOGIKA REGISTRASI
        // ====================================================================
        if (!empty($data->full_name) && !empty($data->email) && !empty($data->password)) {
            
            $full_name = $conn->real_escape_string($data->full_name);
            $email = $conn->real_escape_string($data->email);
            $phone = $conn->real_escape_string($data->phone);
            $password = $conn->real_escape_string($data->password);
            
            // Set role default sebagai 'customer'
            $role = 'customer'; 

            // 1. Cek Email Unik
            $check_query = "SELECT user_id FROM Users WHERE email = '$email'";
            if ($conn->query($check_query)->num_rows > 0) {
                http_response_code(400);
                echo json_encode(["message" => "Pendaftaran gagal. Email sudah terdaftar."]);
                $conn->close();
                exit();
            }

            // 2. HASH PASSWORD
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // 3. Simpan Data
            $query = "INSERT INTO Users (full_name, email, phone, password_hash, role) VALUES ('$full_name', '$email', '$phone', '$password_hash', '$role')";

            if ($conn->query($query) === TRUE) {
                http_response_code(201);
                echo json_encode(["message" => "Pendaftaran berhasil!"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Pendaftaran gagal: SQL Error. " . $conn->error]);
            }

        } else {
            http_response_code(400);
            echo json_encode(["message" => "Data pendaftaran tidak lengkap."]);
        }
    
    } else {
        // ====================================================================
        //                         LOGIKA LOGIN
        // ====================================================================
        if (!empty($data->email) && !empty($data->password)) {
            
            $email = $conn->real_escape_string($data->email);
            $password = $conn->real_escape_string($data->password);

            $query = "SELECT user_id, full_name, password_hash, role FROM Users WHERE email = '$email'";
            $result = $conn->query($query);

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Verifikasi Kata Sandi
                if (password_verify($password, $user['password_hash'])) {
                    
                    // Mulai Sesi
                    session_start();

                    // Tentukan Redirect
                    if ($user['role'] == 'admin' || $user['role'] == 'logistics') {
                        $redirect_page = "admin_dashboard.php";
                    } else {
                        // Cek apakah ada redirect khusus (misal dari cart)
                        $redirect_page = (isset($_SESSION['redirect_to_cart']) && $_SESSION['redirect_to_cart']) ? 'cart.php' : 'dashboard.php';
                        unset($_SESSION['redirect_to_cart']);
                    }

                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['full_name'] = $user['full_name']; // Simpan nama untuk ditampilkan

                    http_response_code(200);
                    echo json_encode([
                        "message" => "Login berhasil.",
                        "user_name" => $user['full_name'],
                        "redirect" => $redirect_page
                    ]);
                    
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Email atau kata sandi salah."]);
                }
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Email atau kata sandi salah."]);
            }

        } else {
            http_response_code(400);
            echo json_encode(["message" => "Data login tidak lengkap."]);
        }
    }
    
    $conn->close();
    exit(); // Hentikan eksekusi setelah API selesai
}

// --- JIKA BUKAN POST/AJAX REQUEST, TAMPILKAN HTML ---
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk / Daftar Akun - Material Super</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">

    <div class="login-container">
        <div class="login-box">
            
            <div class="alert-info-redirect">
                <i class="fas fa-info-circle"></i> Untuk mengamankan pesanan DP 30% dan melanjutkan ke keranjang, silakan **Masuk** atau **Daftar Akun Baru**.
            </div>
            
            <div class="tabs">
                <button class="tablink active" onclick="openLoginTab(event, 'Login')">Masuk</button>
                <button class="tablink" onclick="openLoginTab(event, 'Daftar')">Daftar Akun Baru</button>
            </div>

            <div id="Login" class="tabcontent login-content" style="display:block;">
                <h2>Akses Dashboard Pelanggan</h2>
                <form id="loginForm"> 
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Kata Sandi</label>
                        <input type="password" id="login-password" required>
                    </div>
                    <button type="submit" class="btn-login-submit">Login ke Dashboard</button>
                </form>
                <div class="login-footer">
                    <a href="#">Lupa Kata Sandi?</a>
                </div>
            </div>

            <div id="Daftar" class="tabcontent login-content" style="display:none;">
                <h2>Buat Akun Proyek</h2>
                <form id="registerForm">
                    <div class="form-group">
                        <label for="reg-nama">Nama Lengkap</label>
                        <input type="text" id="reg-nama" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-telepon">Nomor Telepon/WA</label>
                        <input type="tel" id="reg-telepon" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-email">Email</label>
                        <input type="email" id="reg-email" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-password">Kata Sandi</label>
                        <input type="password" id="reg-password" required>
                    </div>
                    <button type="submit" class="btn-login-submit register-btn">Daftar & Lanjut</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function openLoginTab(evt, tabName) {
            // Sembunyikan semua konten tab
            var tabcontents = document.getElementsByClassName("tabcontent");
            for (var i = 0; i < tabcontents.length; i++) {
                tabcontents[i].style.display = "none";
            }
            
            // Hapus class 'active' dari semua tombol tab
            var tablinks = document.getElementsByClassName("tablink");
            for (var i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            
            // Tampilkan tab yang dipilih dan tambahkan class 'active'
            document.getElementById(tabName).style.display = "block";
            
            // Tangani event click manual atau event object dari listener
            if (evt.currentTarget) {
                evt.currentTarget.className += " active";
            } else if (evt.target) {
                 evt.target.className += " active";
            }
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            
            // --- HANDLER PENDAFTARAN ---
            const registerForm = document.getElementById('registerForm');
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const data = {
                    full_name: document.getElementById('reg-nama').value,
                    phone: document.getElementById('reg-telepon').value,
                    email: document.getElementById('reg-email').value,
                    password: document.getElementById('reg-password').value
                };

                // Panggil file ini sendiri (login.php)
                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    // Cek jika respons bukan JSON (misal error PHP fatal)
                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        throw new Error("Respons server bukan JSON. Cek koneksi database.");
                    }
                    return response.json().then(data => ({ status: response.status, body: data }));
                })
                .then(({ status, body }) => {
                    if (status === 201) { // Sukses Created
                        alert('Pendaftaran Berhasil! Silakan masuk.');
                        // Simulasi klik tab Login
                        const loginTabBtn = document.querySelector('.tablink:first-child');
                        if(loginTabBtn) {
                             // Reset form dan pindah tab
                             registerForm.reset();
                             // Panggil fungsi openLoginTab secara manual
                             openLoginTab({ currentTarget: loginTabBtn }, 'Login');
                        }
                    } else {
                        alert('Gagal Daftar: ' + (body.message || 'Kesalahan tidak diketahui'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                });
            });

            // --- HANDLER LOGIN ---
            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const data = {
                    email: document.getElementById('login-email').value,
                    password: document.getElementById('login-password').value
                };

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                         throw new Error("Respons server bukan JSON. Mungkin ada error PHP.");
                    }
                    return response.json().then(data => ({ status: response.status, body: data }));
                })
                .then(({ status, body }) => {
                    if (status === 200) {
                        alert('Login Berhasil! Selamat datang, ' + body.user_name);
                        window.location.href = body.redirect; 
                    } else {
                        alert('Login Gagal: ' + (body.message || 'Email/Password salah'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Login Error: ' + error.message);
                });
            });
            
            // --- INITIALISASI TAB (Klik manual saat load) ---
            // Pastikan tab login terbuka pertama kali
            const defaultTab = document.querySelector('.tablink');
            if(defaultTab) defaultTab.click();
        });
    </script>
</body>
</html>