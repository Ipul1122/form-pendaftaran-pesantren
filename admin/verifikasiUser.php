<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// =====================================================================
// PROSES VERIFIKASI KEHADIRAN & PEMBAYARAN INFAQ
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifikasi'])) {
    $id_anak = intval($_POST['id_anak']);
    $nominal_infaq = intval($_POST['nominal_infaq']);

    // Update status kehadiran dan jumlah infaq
    $stmt = $conn->prepare("UPDATE pendaftar SET is_hadir = 1, jumlah_infaq = ? WHERE id = ?");
    $stmt->bind_param("ii", $nominal_infaq, $id_anak);
    
    if ($stmt->execute()) {
        $msg = "success";
    } else {
        $msg = "error";
    }
}

// =====================================================================
// AMBIL DATA TOTAL INFAQ YANG TERKUMPUL
// =====================================================================
$query_total = "SELECT SUM(jumlah_infaq) as total_semua FROM pendaftar WHERE is_hadir = 1";
$result_total = $conn->query($query_total);
$data_total = $result_total->fetch_assoc();
$total_infaq = $data_total['total_semua'] ?? 0;

// =====================================================================
// AMBIL DATA ANAK YANG SUDAH DITERIMA
// =====================================================================
$query = "SELECT id, nama_anak, kelas, nama_ayah, is_hadir, jumlah_infaq 
          FROM pendaftar 
          WHERE status = 'diterima' 
          ORDER BY is_hadir ASC, nama_anak ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kehadiran & Infaq</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-stats {
            border-left: 5px solid #0d6efd;
        }
    </style>
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <div class="mb-4">
            <h2 class="text-primary fw-bold">✔️ Verifikasi Kedatangan Santri</h2>
            <p class="text-muted">Kelola kehadiran dan pembayaran infaq santri di Masjid Nurul Haq.</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0 card-stats bg-white">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted small fw-bold">Total Infaq Terkumpul</h6>
                        <h2 class="text-primary fw-bold mb-0">Rp <?= number_format($total_infaq, 0, ',', '.') ?></h2>
                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">Berdasarkan santri yang sudah hadir</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if(isset($msg) && $msg == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                ✅ <strong>Berhasil!</strong> Data kehadiran dan infaq santri telah diperbarui.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold">
                Daftar Santri (Status: Diterima)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama Anak</th>
                                <th>Kelas</th>
                                <th>Nama Ayah</th>
                                <th class="text-center">Status Kehadiran</th>
                                <th class="text-center" width="300">Aksi / Input Infaq</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($row['nama_anak']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['kelas']) ?></span></td>
                                <td><?= htmlspecialchars($row['nama_ayah']) ?></td>
                                <td class="text-center">
                                    <?php if($row['is_hadir']): ?>
                                        <span class="badge bg-success px-3 py-2">
                                            Hadir (Rp <?= number_format($row['jumlah_infaq'], 0, ',', '.') ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary px-3 py-2">Belum Tiba</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!$row['is_hadir']): ?>
                                        <form action="" method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="id_anak" value="<?= $row['id'] ?>">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light">Rp</span>
                                                <input type="number" name="nominal_infaq" class="form-control fw-bold" value="20000" min="0" required>
                                            </div>
                                            <button type="submit" name="verifikasi" class="btn btn-sm btn-primary fw-bold" onclick="return confirm('Konfirmasi kedatangan <?= htmlspecialchars($row['nama_anak']) ?>?')">Verifikasi</button>
                                        </form>
                                    <?php else: ?>
                                        <div class="text-center">
                                            <button class="btn btn-sm btn-outline-success w-75 fw-bold" disabled>✔️ Terverifikasi</button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if($result->num_rows == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <p class="mb-0">Belum ada santri yang berstatus <strong>'Diterima'</strong>.</p>
                                        <small>Silakan proses pendaftaran di menu Status Pendaftaran terlebih dahulu.</small>
                                    </td>
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
    document.getElementById('btnToggleSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('toggled');
    });
</script>
</body>
</html>