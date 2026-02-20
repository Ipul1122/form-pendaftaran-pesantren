<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// =====================================================================
// PROSES PENAMBAHAN DATA (TEBAKAN BENAR -> POIN)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah' && isset($_POST['kelompok']) && isset($_POST['tebakan_benar'])) {
        $kelompok = intval($_POST['kelompok']);
        $tebakan_benar = intval($_POST['tebakan_benar']);
        
        // Aturan: 1 Gerakan Benar = 20 Poin
        $poin_tambahan = $tebakan_benar * 20;

        if ($kelompok >= 1 && $kelompok <= 5 && $tebakan_benar > 0) {
            $stmt = $conn->prepare("UPDATE skor_tebak_siapa SET jumlah_benar = jumlah_benar + ?, skor = skor + ? WHERE kelompok = ?");
            $stmt->bind_param("iii", $tebakan_benar, $poin_tambahan, $kelompok);
            $stmt->execute();
        }
    } 
    elseif ($aksi === 'reset') {
        $conn->query("UPDATE skor_tebak_siapa SET jumlah_benar = 0, skor = 0");
    }

    // Redirect untuk mencegah form disubmit ulang saat direfresh
    header("Location: tebakSiapaAku.php");
    exit;
}

// =====================================================================
// AMBIL DATA SKOR SAAT INI
// =====================================================================
$data_klp = [
    1 => ['benar' => 0, 'skor' => 0],
    2 => ['benar' => 0, 'skor' => 0],
    3 => ['benar' => 0, 'skor' => 0],
    4 => ['benar' => 0, 'skor' => 0],
    5 => ['benar' => 0, 'skor' => 0],
];

$result = $conn->query("SELECT kelompok, jumlah_benar, skor FROM skor_tebak_siapa");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data_klp[$row['kelompok']]['benar'] = $row['jumlah_benar'];
        $data_klp[$row['kelompok']]['skor'] = $row['skor'];
    }
}

// Mencari skor tertinggi (Pemenang Sementara berdasarkan Skor)
$pemenang = [];
$top_skor = 0;
$top_benar = 0;

$q_leader = "SELECT skor, jumlah_benar FROM skor_tebak_siapa WHERE skor > 0 ORDER BY skor DESC LIMIT 1";
$res_leader = $conn->query($q_leader);

if ($res_leader && $res_leader->num_rows > 0) {
    $top = $res_leader->fetch_assoc();
    $top_skor = $top['skor'];
    $top_benar = $top['jumlah_benar'];
    
    // Cek jika seri
    $q_winners = $conn->query("SELECT kelompok FROM skor_tebak_siapa WHERE skor = $top_skor");
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
    <title>Tebak Siapa Aku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .game-card { transition: all 0.3s ease; }
        .game-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .stat-box { background: #f8f9fa; border-radius: 8px; padding: 10px; border: 1px solid #dee2e6; }
        .stat-value { font-size: 2rem; font-weight: 900; line-height: 1; margin-top: 5px;}
    </style>
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <div class="mb-4 text-center">
            <h2 class="text-info fw-bold text-uppercase">🎭 Tebak Siapa Aku</h2>
            <p class="text-muted fs-6">Masukkan jumlah gerakan yang berhasil ditebak. <br> <span class="badge bg-warning text-dark">Aturan: 1 Tebakan Benar = 20 Poin</span></p>
        </div>

        <?php if (!empty($pemenang)): ?>
            <div class="alert alert-info text-center shadow-sm mb-4 fs-4 border-info">
                🏆 <strong>Peringkat 1:</strong> Kelompok <?= implode(", ", $pemenang) ?> 
                <br>
                <small class="fs-6 text-dark">
                    (Berhasil menebak <strong><?= $top_benar ?> Gerakan</strong> dengan Total <strong><?= $top_skor ?> Poin</strong>)
                </small>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <?php for ($i = 1; $i <= 5; $i++): 
                $is_winner = in_array($i, $pemenang);
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 game-card h-100 <?= $is_winner ? 'border-2 border-info' : '' ?>">
                        <div class="card-header <?= $is_winner ? 'bg-info text-dark' : 'bg-dark text-white' ?> text-center fw-bold fs-5">
                            <?= $is_winner ? '👑 ' : '' ?>Kelompok <?= $i ?>
                        </div>
                        <div class="card-body p-3">
                            
                            <div class="row text-center mb-4 g-2">
                                <div class="col-6">
                                    <div class="stat-box">
                                        <small class="text-muted d-block fw-bold">Tebakan Benar</small>
                                        <div class="stat-value text-primary"><?= $data_klp[$i]['benar'] ?></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box">
                                        <small class="text-muted d-block fw-bold">Total Poin</small>
                                        <div class="stat-value text-info"><?= $data_klp[$i]['skor'] ?></div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <form action="" method="POST">
                                <input type="hidden" name="aksi" value="tambah">
                                <input type="hidden" name="kelompok" value="<?= $i ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold mb-1">Berapa Gerakan yang Berhasil Ditebak?</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">🙋‍♂️</span>
                                        <input type="number" name="tebakan_benar" class="form-control fw-bold text-center" placeholder="Contoh: 3" min="1" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-info w-100 fw-bold shadow-sm text-white">
                                    ➕ Tambahkan
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="text-center mt-4 mb-5">
            <form action="" method="POST" onsubmit="return confirm('⚠️ Yakin ingin mereset semua skor game Tebak Siapa Aku kembali ke 0?');">
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