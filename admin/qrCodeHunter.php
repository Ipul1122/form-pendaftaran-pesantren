<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// =====================================================================
// PROSES PENAMBAHAN DATA (QR & POIN)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah' && isset($_POST['kelompok']) && isset($_POST['qr_ditemukan']) && isset($_POST['poin_diberikan'])) {
        $kelompok = intval($_POST['kelompok']);
        $qr_baru = intval($_POST['qr_ditemukan']);
        $poin_baru = intval($_POST['poin_diberikan']);

        if ($kelompok >= 1 && $kelompok <= 5) {
            $stmt = $conn->prepare("UPDATE skor_qr_hunter SET jumlah_qr = jumlah_qr + ?, total_poin = total_poin + ? WHERE kelompok = ?");
            $stmt->bind_param("iii", $qr_baru, $poin_baru, $kelompok);
            $stmt->execute();
        }
    } 
    elseif ($aksi === 'reset') {
        $conn->query("UPDATE skor_qr_hunter SET jumlah_qr = 0, total_poin = 0");
    }

    // Redirect untuk mencegah resubmission form saat refresh
    header("Location: qrCodeHunter.php");
    exit;
}

// =====================================================================
// AMBIL DATA SKOR SAAT INI
// =====================================================================
$data_klp = [
    1 => ['qr' => 0, 'poin' => 0],
    2 => ['qr' => 0, 'poin' => 0],
    3 => ['qr' => 0, 'poin' => 0],
    4 => ['qr' => 0, 'poin' => 0],
    5 => ['qr' => 0, 'poin' => 0],
];

$result = $conn->query("SELECT kelompok, jumlah_qr, total_poin FROM skor_qr_hunter");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data_klp[$row['kelompok']]['qr'] = $row['jumlah_qr'];
        $data_klp[$row['kelompok']]['poin'] = $row['total_poin'];
    }
}

// Mencari skor tertinggi (Pemenang Sementara berdasarkan Poin, lalu jumlah QR)
$pemenang = [];
$top_poin = 0;
$top_qr = 0;

$q_leader = "SELECT total_poin, jumlah_qr FROM skor_qr_hunter WHERE total_poin > 0 ORDER BY total_poin DESC, jumlah_qr DESC LIMIT 1";
$res_leader = $conn->query($q_leader);

if ($res_leader && $res_leader->num_rows > 0) {
    $top = $res_leader->fetch_assoc();
    $top_poin = $top['total_poin'];
    $top_qr = $top['jumlah_qr'];
    
    // Jika ada yang seri poin dan QR-nya, tampilkan semuanya
    $q_winners = $conn->query("SELECT kelompok FROM skor_qr_hunter WHERE total_poin = $top_poin AND jumlah_qr = $top_qr");
    while($w = $q_winners->fetch_assoc()){
        $pemenang[] = $w['kelompok'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Hunter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hunter-card { transition: all 0.3s ease; }
        .hunter-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .stat-box { background: #f8f9fa; border-radius: 8px; padding: 10px; border: 1px solid #dee2e6; }
        .stat-value { font-size: 2rem; font-weight: 900; line-height: 1; margin-top: 5px;}
    </style>
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <div class="mb-4 text-center">
            <h2 class="text-success fw-bold text-uppercase">🕵️‍♂️ QR Code Hunter</h2>
            <p class="text-muted fs-6">Input jumlah QR yang disetorkan kelompok dan berikan poin sesuai kebijakan panitia.</p>
        </div>

        <?php if (!empty($pemenang)): ?>
            <div class="alert alert-success text-center shadow-sm mb-4 fs-4 border-success">
                🏆 <strong>Memimpin:</strong> Kelompok <?= implode(", ", $pemenang) ?> 
                <br>
                <small class="fs-6 text-dark">
                    (Mengumpulkan <strong><?= $top_qr ?> QR Code</strong> dengan Total <strong><?= $top_poin ?> Poin</strong>)
                </small>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <?php for ($i = 1; $i <= 5; $i++): 
                $is_winner = in_array($i, $pemenang);
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 hunter-card h-100 <?= $is_winner ? 'border-2 border-success' : '' ?>">
                        <div class="card-header <?= $is_winner ? 'bg-success text-white' : 'bg-dark text-white' ?> text-center fw-bold fs-5">
                            <?= $is_winner ? '👑 ' : '' ?>Kelompok <?= $i ?>
                        </div>
                        <div class="card-body p-3">
                            
                            <div class="row text-center mb-4 g-2">
                                <div class="col-6">
                                    <div class="stat-box">
                                        <small class="text-muted d-block fw-bold">Total QR</small>
                                        <div class="stat-value text-primary"><?= $data_klp[$i]['qr'] ?></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <small class="text-muted d-block fw-bold">Total Poin</small>
                                        <div class="stat-value text-success"><?= $data_klp[$i]['poin'] ?></div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <form action="" method="POST">
                                <input type="hidden" name="aksi" value="tambah">
                                <input type="hidden" name="kelompok" value="<?= $i ?>">
                                
                                <div class="mb-2">
                                    <label class="form-label text-muted small fw-bold mb-1">Setor Berapa QR Code?</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">🔍</span>
                                        <input type="number" name="qr_ditemukan" class="form-control" placeholder="Contoh: 3" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold mb-1">Nilai Poin dari Panitia</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">⭐</span>
                                        <input type="number" name="poin_diberikan" class="form-control" placeholder="Contoh: 50" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                                    ➕ Tambah Data
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="text-center mt-4 mb-5">
            <form action="" method="POST" onsubmit="return confirm('⚠️ Yakin ingin mereset/menghapus semua data QR dan Poin kembali ke 0?');">
                <input type="hidden" name="aksi" value="reset">
                <button type="submit" class="btn btn-outline-danger">🔄 Reset Semua Data Game</button>
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