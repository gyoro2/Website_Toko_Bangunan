<?php
session_start();

// 1. Kosongkan array $_SESSION
$_SESSION = array();

// 2. Hapus cookie session jika ada (PENTING untuk logout bersih)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan Sesi
session_destroy();

// 4. Redirect ke Login (Bukan Index, untuk memastikan user sadar sudah keluar)
header("Location: login.php");
exit();
?>