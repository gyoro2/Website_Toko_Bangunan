<?php
// FILE: generate_hash.php
$password_baru = 'fenomeno1234'; // GANTI dengan password yang ingin Anda pakai!
$hash_yang_benar = password_hash($password_baru, PASSWORD_DEFAULT);

echo "Hash untuk password '{$password_baru}' adalah: <br><br>";
echo "<strong>{$hash_yang_benar}</strong>";
?>