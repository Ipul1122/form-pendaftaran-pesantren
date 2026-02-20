<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// Menghitung jumlah data berdasarkan status
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

        <h2 class="text-primary mb-4">Selamat Datang di Dashboard!</h2>

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

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <p class="text-muted fs-5">Gunakan menu di samping untuk mengelola data pendaftaran calon siswa.</p>
                <hr>
                <div class="alert alert-info border-0 shadow-sm">
                    <strong>Informasi:</strong> Sidebar di sebelah kiri bersifat responsif. Anda dapat menyembunyikannya dengan menekan tombol <strong>☰ Menu</strong> di pojok kiri atas.
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