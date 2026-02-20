<?php
// Mulai session
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session sepenuhnya
session_destroy();

// Arahkan kembali ke halaman login admin
header("Location: login.php");
exit;
?>