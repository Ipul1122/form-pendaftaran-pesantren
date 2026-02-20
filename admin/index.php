<?php
session_start();

// Cek apakah admin sudah login
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Jika sudah login, langsung arahkan ke Dashboard
    header("Location: dashboard.php");
    exit;
} else {
    // Jika belum login, arahkan ke halaman Login
    header("Location: login.php");
    exit;
}
?>