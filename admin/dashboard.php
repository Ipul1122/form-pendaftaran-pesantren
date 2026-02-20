<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// 1. Menghitung jumlah data berdasarkan status secara umum
$query_count = "SELECT 
                    SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as total_diterima,
                    SUM(CASE WHEN status = 'proses' THEN 1 ELSE 0 END) as total_proses,
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as total_ditolak,
                    COUNT(*) as total_pendaftar
                FROM pendaftar";
$result_count = $conn->query($query_count);
$data_count = $result_count->fetch_assoc();

$diterima = $data_count['total_diterima'] ?? 0;
$proses = $data_count['total_proses'] ?? 0;
$ditolak = $data_count['total_ditolak'] ?? 0;
$total = $data_count['total_pendaftar'] ?? 0;

// 2. Menghitung jumlah pendaftar berdasarkan kelas
// Diurutkan secara spesifik dari 3 SD sampai 3 SMP
$query_kelas = "SELECT kelas, COUNT(*) as jumlah 
                FROM pendaftar 
                GROUP BY kelas 
                ORDER BY FIELD(kelas, '3 SD', '4 SD', '5 SD', '6 SD', '1 SMP', '2 SMP', '3 SMP')";
$result_kelas = $conn->query($query_kelas);

// Masukkan data hasil query ke dalam array agar mudah dipanggil
$data_kelas = [];
if ($result_kelas) {
    while ($row = $result_kelas->fetch_assoc()) {
        $data_kelas[$row['kelas']] = $row['jumlah'];
    }
}

// Daftar urutan kelas standar untuk ditampilkan (memastikan kelas yang 0 pendaftar tetap muncul)
$semua_kelas = ['3 SD', '4 SD', '5 SD', '6 SD', '1 SMP', '2 SMP', '3 SMP'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary mb-0">Selamat Datang di Dashboard!</h2>
            
            <a href="cetakKartu.php" target="_blank" class="btn btn-danger shadow-sm fw-bold">
                📄 Export PDF Kartu Nama
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Telah Diterima</h5>
                        <h1 class="display-4 fw-bold"><?= $diterima ?></h1>
                        <p class="card-text">Anak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Sedang Diproses</h5>
                        <h1 class="display-4 fw-bold"><?= $proses ?></h1>
                        <p class="card-text">Anak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Ditolak</h5>
                        <h1 class="display-4 fw-bold"><?= $ditolak ?></h1>
                        <p class="card-text">Anak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Pendaftar</h5>
                        <h1 class="display-4 fw-bold"><?= $total ?></h1>
                        <p class="card-text">Anak</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-dark text-white fw-bold">
                        📊 Rincian Pendaftar Berdasarkan Kelas
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($semua_kelas as $kls): ?>
                            <?php 
                                // Jika kelas ada di database, ambil jumlahnya. Jika tidak, set 0.
                                $jumlah_perkelas = isset($data_kelas[$kls]) ? $data_kelas[$kls] : 0; 
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="fw-semibold">Kelas <?= $kls ?></span>
                                <span class="badge <?= $jumlah_perkelas > 0 ? 'bg-primary' : 'bg-secondary' ?> rounded-pill px-3 py-2">
                                    <?= $jumlah_perkelas ?> Anak
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-bold mb-3">Panduan Admin</h5>
                        <p class="text-muted fs-6">Gunakan menu di samping untuk mengelola data pendaftaran calon siswa secara keseluruhan.</p>
                        
                        <div class="alert alert-info border-0 shadow-sm mt-4">
                            <strong>Informasi:</strong> Sidebar di sebelah kiri bersifat responsif. Anda dapat menyembunyikannya dengan menekan tombol <strong>☰ Menu</strong> di pojok kiri atas untuk memperluas area kerja.
                        </div>

                        <div class="alert alert-warning border-0 shadow-sm mt-3">
                            <strong>Tips:</strong> Pastikan selalu mengecek menu <strong>Status Pendaftaran</strong> untuk memproses anak-anak yang masih berstatus "Sedang Diproses".
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> 
    </div> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('btnToggleSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('toggled');
    });
</script>
</body>
</html>