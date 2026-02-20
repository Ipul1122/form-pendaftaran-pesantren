<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// Ambil data hanya yang berstatus 'diterima'
// Diurutkan berdasarkan kelas agar pembagian kelompoknya merata per kelas
$query = "SELECT nama_anak, kelas, no_telpon 
          FROM pendaftar 
          WHERE status = 'diterima' 
          ORDER BY FIELD(kelas, '3 SMP', '2 SMP', '1 SMP', '6 SD', '5 SD', '4 SD', '3 SD')";
$result = $conn->query($query);

// Siapkan 5 Array untuk 5 Kelompok
$kelompok = [
    1 => [],
    2 => [],
    3 => [],
    4 => [],
    5 => []
];

// Algoritma Pembagian Round-Robin (Bergiliran)
$index_kelompok = 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $kelompok[$index_kelompok][] = $row;
        
        // Pindah ke kelompok berikutnya, jika sudah 5 kembali ke 1
        $index_kelompok++;
        if ($index_kelompok > 5) {
            $index_kelompok = 1;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembagian Kelompok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <h3 class="mb-4 text-primary">Pembagian Kelompok Pesantren Ramadhan</h3>
        <p class="text-muted">Anak-anak yang telah <strong>Diterima</strong> otomatis dibagikan secara merata ke dalam 5 kelompok berdasarkan jenjang kelasnya.</p>

        <div class="row">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-dark text-white text-center">
                        <h5 class="mb-0">Kelompok <?= $i ?></h5>
                        <small>Total: <?= count($kelompok[$i]) ?> Anak</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 text-center align-middle">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($kelompok[$i]) > 0): ?>
                                        <?php $no = 1; foreach ($kelompok[$i] as $anak): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="text-start"><?= htmlspecialchars($anak['nama_anak']) ?></td>
                                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($anak['kelas']) ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-muted py-3">Belum ada anggota.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
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