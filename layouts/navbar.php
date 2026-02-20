<?php
// Logika pintar untuk mengatur path/URL agar link tidak rusak 
// saat dipanggil dari file di luar atau di dalam folder 'user'
$current_dir = dirname($_SERVER['PHP_SELF']);
$is_user_dir = (basename($current_dir) == 'user');

$link_beranda = $is_user_dir ? '../index.php' : 'index.php';
$link_daftar  = $is_user_dir ? 'pendaftaran.php' : 'user/pendaftaran.php';
$link_status  = $is_user_dir ? 'statusUserPendaftaran.php' : 'user/statusUserPendaftaran.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $link_beranda ?>">Pendaftaran Siswa</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto fs-5">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link_beranda ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link_daftar ?>">Daftar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link_status ?>">Status</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $is_user_dir ? 'kategori.php' : 'user/kategori.php' ?>">Kategori</a>
                </li>
            </ul>
        </div>
    </div>
</nav>