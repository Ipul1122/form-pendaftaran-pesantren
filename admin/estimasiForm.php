<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// Proses Update Status Form
if (isset($_POST['ubah_status'])) {
    $status_baru = $_POST['status_form'];
    $stmt = $conn->prepare("UPDATE pengaturan SET nilai_pengaturan = ? WHERE nama_pengaturan = 'status_pendaftaran'");
    $stmt->bind_param("s", $status_baru);
    $stmt->execute();
    
    // Redirect untuk menghindari form resubmission
    header("Location: estimasiForm.php?success=1");
    exit;
}

// Ambil Status Saat Ini
$query = $conn->query("SELECT nilai_pengaturan FROM pengaturan WHERE nama_pengaturan = 'status_pendaftaran'");
$data = $query->fetch_assoc();
$status_saat_ini = $data['nilai_pengaturan'] ?? 'buka';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Form Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <h3 class="text-primary mb-4">Pengaturan Akses Pendaftaran</h3>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Status form pendaftaran berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        <h5 class="mb-4">Status Pendaftaran Saat Ini:</h5>
                        
                        <?php if ($status_saat_ini == 'buka'): ?>
                            <div class="alert alert-success fw-bold fs-4">🟢 DIBUKA</div>
                            <p class="text-muted">Calon siswa saat ini <strong>dapat</strong> mengisi formulir pendaftaran.</p>
                        <?php else: ?>
                            <div class="alert alert-danger fw-bold fs-4">🔴 DITUTUP</div>
                            <p class="text-muted">Calon siswa saat ini <strong>tidak dapat</strong> mengisi formulir pendaftaran.</p>
                        <?php endif; ?>

                        <hr class="my-4">

                        <form action="" method="POST">
                            <?php if ($status_saat_ini == 'buka'): ?>
                                <input type="hidden" name="status_form" value="tutup">
                                <button type="submit" name="ubah_status" class="btn btn-danger btn-lg w-100 fw-bold" onclick="return confirm('Yakin ingin MENUTUP form pendaftaran? User tidak akan bisa mendaftar lagi.');">
                                    🔒 Tutup Pendaftaran Sekarang
                                </button>
                            <?php else: ?>
                                <input type="hidden" name="status_form" value="buka">
                                <button type="submit" name="ubah_status" class="btn btn-success btn-lg w-100 fw-bold" onclick="return confirm('Yakin ingin MEMBUKA form pendaftaran? User akan bisa mendaftar kembali.');">
                                    🔓 Buka Pendaftaran Sekarang
                                </button>
                            <?php endif; ?>
                        </form>

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