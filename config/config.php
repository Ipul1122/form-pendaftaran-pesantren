<?php
// Konfigurasi Database
$host = "localhost";
$user = "root";     // Sesuaikan dengan user database (default XAMPP: root)
$pass = "";         // Sesuaikan dengan password (default XAMPP: kosong)
$db   = "pesantren_ramadhan";

// Membuat koneksi menggunakan MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Pesan error akan muncul jika koneksi gagal
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>