<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// Proses Ubah Status
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    $status_baru = 'proses';
    if ($action == 'terima') $status_baru = 'diterima';
    elseif ($action == 'tolak') $status_baru = 'ditolak';

    $stmt = $conn->prepare("UPDATE pendaftar SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status_baru, $id);
    $stmt->execute();
    
    // Redirect agar URL bersih kembali
    header("Location: statusPendaftaran.php");
    exit;
}

// Ambil semua data
$result = $conn->query("SELECT * FROM pendaftar ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <h3 class="mb-4">Manajemen Data Pendaftaran</h3>
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive bg-white">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Nama Anak</th>
                                <th width="10%">Kelas</th>
                                <th width="35%">Data Orang Tua & Kontak</th>
                                <th width="10%">Status</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama_anak']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['kelas']) ?></td>
                                <td>
                                    <strong>Ayah:</strong> <?= htmlspecialchars($row['nama_ayah']) ?><br>
                                    <strong>Ibu:</strong> <?= htmlspecialchars($row['nama_ibu']) ?><br>
                                    <strong>Telp:</strong> <a href="https://wa.me/<?= htmlspecialchars($row['no_telpon']) ?>" target="_blank" class="text-decoration-none"><?= htmlspecialchars($row['no_telpon']) ?></a><br>
                                    <strong>Alamat:</strong> <?= htmlspecialchars($row['alamat']) ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        if($row['status'] == 'proses') echo '<span class="badge bg-warning text-dark px-2 py-1">Proses</span>';
                                        elseif($row['status'] == 'diterima') echo '<span class="badge bg-success px-2 py-1">Diterima</span>';
                                        else echo '<span class="badge bg-danger px-2 py-1">Ditolak</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        <a href="?action=terima&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success">Terima</a>
                                        <a href="?action=tolak&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger">Tolak</a>
                                        <a href="?action=proses&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary">Proses</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if($result->num_rows == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data pendaftar.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script JavaScript untuk hide/show sidebar
    document.getElementById('btnToggleSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('toggled');
    });
</script>
</body>
</html>