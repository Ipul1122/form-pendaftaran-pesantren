<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// =====================================================================
// AJAX HANDLER: MENYIMPAN SKOR & SISA WAKTU TANPA REFRESH
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_save'])) {
    $kelompok = intval($_POST['kelompok']);
    $skor = intval($_POST['skor']);
    $sisa_waktu = intval($_POST['sisa_waktu']);

    $stmt = $conn->prepare("UPDATE skor_susun_ayat SET skor = ?, sisa_waktu = ? WHERE kelompok = ?");
    $stmt->bind_param("iii", $skor, $sisa_waktu, $kelompok);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

// =====================================================================
// HANDLER RESET SEMUA DATA
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_all'])) {
    $conn->query("UPDATE skor_susun_ayat SET skor = 0, sisa_waktu = 300");
    header("Location: susunAyat.php");
    exit;
}

// =====================================================================
// AMBIL DATA & TENTUKAN PEMENANG (Berdasarkan Skor lalu Sisa Waktu)
// =====================================================================
$data_klp = [];
$res = $conn->query("SELECT * FROM skor_susun_ayat ORDER BY kelompok ASC");
while ($row = $res->fetch_assoc()) {
    $data_klp[$row['kelompok']] = $row;
}

// Cari siapa yang sedang memimpin
$pemenang = [];
$top_skor = 0;
$top_waktu = 0;

$q_leader = "SELECT skor, sisa_waktu FROM skor_susun_ayat WHERE skor > 0 ORDER BY skor DESC, sisa_waktu DESC LIMIT 1";
$res_leader = $conn->query($q_leader);

if ($res_leader && $res_leader->num_rows > 0) {
    $top = $res_leader->fetch_assoc();
    $top_skor = $top['skor'];
    $top_waktu = $top['sisa_waktu'];
    
    // Jika ada yang seri, tampilkan semuanya
    $q_winners = $conn->query("SELECT kelompok FROM skor_susun_ayat WHERE skor = $top_skor AND sisa_waktu = $top_waktu");
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
    <title>Game Susun Ayat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .timer-display { font-size: 3rem; font-family: monospace; font-weight: bold; }
        .score-display { font-size: 2.5rem; font-weight: 900; margin: 0 20px; width: 60px; text-align: center; }
    </style>
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-primary fw-bold text-uppercase">🧩 Susun Ayat</h2>
                <p class="text-muted fs-6 mb-0">Setiap kelompok memiliki waktu 5 Menit. Kumpulkan ayat terbanyak!</p>
            </div>
            <div>
                <button class="btn btn-outline-primary shadow-sm" onclick="window.location.reload()">🔄 Refresh Peringkat</button>
            </div>
        </div>

        <?php if (!empty($pemenang)): ?>
            <div class="alert alert-success text-center shadow-sm mb-4 fs-4 border-success">
                🏆 <strong>Peringkat 1 Saat Ini:</strong> Kelompok <?= implode(", ", $pemenang) ?> 
                <br>
                <small class="fs-6 text-dark">
                    (Berhasil menyusun <strong><?= $top_skor ?> Ayat</strong> dengan sisa waktu <strong><?= floor($top_waktu/60) ?> Menit <?= $top_waktu%60 ?> Detik</strong>)
                </small>
            </div>
        <?php endif; ?>

        <div id="toastSave" class="alert alert-info position-fixed top-0 start-50 translate-middle-x mt-3 shadow" style="display: none; z-index: 9999;">
            ✅ Data berhasil disimpan! Klik <b>Refresh Peringkat</b> untuk memperbarui pemenang.
        </div>

        <div class="row justify-content-center">
            <?php for ($i = 1; $i <= 5; $i++): 
                $skor_awal = $data_klp[$i]['skor'] ?? 0;
                $waktu_awal = $data_klp[$i]['sisa_waktu'] ?? 300;
            ?>
                <div class="col-md-6 col-xl-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-dark text-white text-center fs-5 fw-bold">
                            Kelompok <?= $i ?>
                        </div>
                        <div class="card-body text-center p-4">
                            
                            <div class="text-muted mb-1">Sisa Waktu</div>
                            <div class="timer-display <?= $waktu_awal <= 60 ? 'text-danger' : 'text-dark' ?>" id="waktu-<?= $i ?>">
                                <?= sprintf("%02d:%02d", floor($waktu_awal/60), $waktu_awal%60) ?>
                            </div>
                            
                            <button id="btn-timer-<?= $i ?>" class="btn btn-primary btn-sm px-4 rounded-pill mt-2 mb-4 shadow-sm" onclick="toggleTimer(<?= $i ?>)">
                                ▶ Mulai Timer
                            </button>

                            <hr>

                            <div class="text-muted mb-2">Ayat Berhasil Disusun</div>
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <button class="btn btn-outline-danger fw-bold fs-5 px-3" onclick="ubahSkor(<?= $i ?>, -1)">-</button>
                                <div class="score-display text-primary" id="skor-<?= $i ?>"><?= $skor_awal ?></div>
                                <button class="btn btn-outline-success fw-bold fs-5 px-3" onclick="ubahSkor(<?= $i ?>, 1)">+</button>
                            </div>

                            <button class="btn btn-success w-100 fw-bold shadow-sm" onclick="simpanData(<?= $i ?>)">
                                💾 Simpan Progress
                            </button>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="text-center mt-5 mb-5">
            <form action="" method="POST" onsubmit="return confirm('⚠️ Yakin ingin mereset waktu ke 5 Menit dan skor ke 0 untuk SEMUA KELOMPOK?');">
                <input type="hidden" name="reset_all" value="1">
                <button type="submit" class="btn btn-outline-danger">⚠️ Reset Seluruh Data Game</button>
            </form>
        </div>

    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Inisialisasi Data dari PHP ke JavaScript
    let skorData = {
        1: <?= $data_klp[1]['skor'] ?? 0 ?>,
        2: <?= $data_klp[2]['skor'] ?? 0 ?>,
        3: <?= $data_klp[3]['skor'] ?? 0 ?>,
        4: <?= $data_klp[4]['skor'] ?? 0 ?>,
        5: <?= $data_klp[5]['skor'] ?? 0 ?>
    };

    let waktuData = {
        1: <?= $data_klp[1]['sisa_waktu'] ?? 300 ?>,
        2: <?= $data_klp[2]['sisa_waktu'] ?? 300 ?>,
        3: <?= $data_klp[3]['sisa_waktu'] ?? 300 ?>,
        4: <?= $data_klp[4]['sisa_waktu'] ?? 300 ?>,
        5: <?= $data_klp[5]['sisa_waktu'] ?? 300 ?>
    };

    let timers = {}; // Objek untuk menyimpan interval masing-masing kelompok

    // Fungsi format detik menjadi MM:SS
    function formatWaktu(detik) {
        let m = Math.floor(detik / 60).toString().padStart(2, '0');
        let s = (detik % 60).toString().padStart(2, '0');
        return m + ":" + s;
    }

    // Fungsi merubah skor (tambah/kurang)
    function ubahSkor(klp, nilai) {
        let skorBaru = skorData[klp] + nilai;
        if (skorBaru < 0) skorBaru = 0; // Tidak boleh minus
        skorData[klp] = skorBaru;
        document.getElementById('skor-' + klp).innerText = skorBaru;
    }

    // Fungsi Mulai / Pause Timer
    function toggleTimer(klp) {
        let btn = document.getElementById('btn-timer-' + klp);
        let display = document.getElementById('waktu-' + klp);

        // Jika timer sedang jalan -> PAUSE
        if (timers[klp]) {
            clearInterval(timers[klp]);
            timers[klp] = null;
            btn.innerText = "▶ Lanjut Timer";
            btn.classList.replace('btn-warning', 'btn-primary');
        } 
        // Jika sedang berhenti -> MULAI
        else {
            if (waktuData[klp] <= 0) return; // Waktu habis
            
            btn.innerText = "⏸ Pause Timer";
            btn.classList.replace('btn-primary', 'btn-warning');
            
            timers[klp] = setInterval(function() {
                waktuData[klp]--;
                display.innerText = formatWaktu(waktuData[klp]);

                // Efek merah jika waktu <= 1 menit
                if (waktuData[klp] <= 60) {
                    display.classList.add('text-danger');
                }

                // Jika waktu habis
                if (waktuData[klp] <= 0) {
                    clearInterval(timers[klp]);
                    timers[klp] = null;
                    display.innerText = "00:00";
                    btn.innerText = "Waktu Habis";
                    btn.disabled = true;
                    simpanData(klp); // Simpan otomatis saat habis
                }
            }, 1000);
        }
    }

    // Fungsi simpan data ke Database via AJAX
    function simpanData(klp) {
        let formData = new FormData();
        formData.append('ajax_save', '1');
        formData.append('kelompok', klp);
        formData.append('skor', skorData[klp]);
        formData.append('sisa_waktu', waktuData[klp]);

        fetch('susunAyat.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            if(data.trim() === 'success') {
                // Munculkan toast notifikasi
                let toast = document.getElementById('toastSave');
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 4000);
            } else {
                alert("Gagal menyimpan data Kelompok " + klp);
            }
        });
    }

    // Toggle Sidebar Action
    document.getElementById('btnToggleSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('toggled');
    });
</script>
</body>
</html>