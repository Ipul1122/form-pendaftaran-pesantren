<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// =====================================================================
// PROSES PENAMBAHAN / PENGURANGAN SKOR
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kelompok']) && isset($_POST['poin']) && isset($_POST['aksi'])) {
    $kelompok = intval($_POST['kelompok']);
    $poin = intval($_POST['poin']);
    $aksi = $_POST['aksi'];

    if ($poin > 0 && in_array($kelompok, [1, 2, 3, 4, 5])) {
        if ($aksi === 'tambah') {
            $stmt = $conn->prepare("UPDATE skor_kelompok SET skor = skor + ? WHERE kelompok = ?");
        } else if ($aksi === 'kurang') {
            $stmt = $conn->prepare("UPDATE skor_kelompok SET skor = skor - ? WHERE kelompok = ?");
        }
        
        if (isset($stmt)) {
            $stmt->bind_param("ii", $poin, $kelompok);
            $stmt->execute();
        }
    }
    
    // Redirect untuk menghindari pengiriman ulang form jika browser direfresh
    header("Location: cerdasCermatDigital.php");
    exit;
}

// =====================================================================
// AMBIL DATA SKOR SAAT INI
// =====================================================================
$skor_data = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$result = $conn->query("SELECT kelompok, skor FROM skor_kelompok");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $skor_data[$row['kelompok']] = $row['skor'];
    }
}

// Mencari skor tertinggi (Pemenang Sementara)
$skor_tertinggi = max($skor_data);
$pemenang = [];
if ($skor_tertinggi > 0) { // Hanya anggap menang jika skor lebih dari 0
    foreach ($skor_data as $klp => $skor) {
        if ($skor == $skor_tertinggi) {
            $pemenang[] = $klp;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerdas Cermat Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .score-card { transition: transform 0.2s; }
        .score-card:hover { transform: translateY(-5px); }
        .score-number { font-size: 3.5rem; font-weight: 900; }
        .crown-icon { font-size: 2rem; color: #ffc107; position: absolute; top: -15px; right: -10px; transform: rotate(15deg); }
    </style>
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <div class="mb-4 text-center">
            <h2 class="text-primary fw-bold text-uppercase">🎮 Cerdas Cermat Digital 🎮</h2>
            <p class="text-muted fs-5">Panel Penilaian Langsung (Live Scoring) Antar Kelompok</p>
        </div>

        <?php if (!empty($pemenang)): ?>
            <div class="alert alert-warning text-center border-warning shadow-sm mb-5 fs-4">
                🏆 <strong>Pemimpin Saat Ini:</strong> Kelompok <?= implode(", ", $pemenang) ?> dengan <strong><?= $skor_tertinggi ?> Poin!</strong> 🏆
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php $is_winner = in_array($i, $pemenang); ?>
                
                <div class="col-md-4 col-lg-auto mb-4" style="min-width: 220px;">
                    <div class="card shadow-sm border-0 score-card h-100 <?= $is_winner ? 'bg-primary text-white border-primary shadow' : 'bg-white' ?>">
                        <div class="card-body text-center position-relative p-4">
                            
                            <?php if ($is_winner): ?>
                                <div class="crown-icon">👑</div>
                            <?php endif; ?>

                            <h5 class="card-title fw-bold text-uppercase mb-1">Kelompok <?= $i ?></h5>
                            <hr class="<?= $is_winner ? 'border-light' : 'border-secondary' ?>">
                            
                            <div class="score-number my-3 <?= $is_winner ? 'text-warning' : 'text-dark' ?>">
                                <?= $skor_data[$i] ?>
                            </div>
                            <div class="mb-3 <?= $is_winner ? 'text-light' : 'text-muted' ?>">Poin</div>

                            <form action="" method="POST" class="mt-3">
                                <input type="hidden" name="kelompok" value="<?= $i ?>">
                                
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text fw-bold">Poin:</span>
                                    <input type="number" name="poin" class="form-control text-center fw-bold" value="10" min="1" required>
                                </div>
                                
                                <div class="btn-group w-100 shadow-sm" role="group">
                                    <button type="submit" name="aksi" value="tambah" class="btn btn-success fw-bold">+ Tambah</button>
                                    <button type="submit" name="aksi" value="kurang" class="btn btn-danger fw-bold">- Kurang</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="text-center mt-5">
            <form action="" method="POST" onsubmit="return confirm('⚠️ PERHATIAN! Apakah Anda yakin ingin mereset/menghapus semua skor kembali ke 0?');">
                <input type="hidden" name="aksi" value="reset">
                <?php
                // Logika Reset Tersembunyi
                if (isset($_POST['aksi']) && $_POST['aksi'] == 'reset') {
                    $conn->query("UPDATE skor_kelompok SET skor = 0");
                    echo "<script>window.location.href='cerdasCermatDigital.php';</script>";
                }
                ?>
                <button type="submit" class="btn btn-outline-danger">🔄 Reset Semua Skor ke 0</button>
            </form>
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