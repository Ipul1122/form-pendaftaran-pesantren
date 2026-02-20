<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// =========================================================================
// 1. BLOK AJAX: MENERIMA REQUEST PINDAH KELOMPOK (DARI DRAG ATAU SELECT)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anak']) && isset($_POST['kelompok_baru'])) {
    $id_anak = intval($_POST['id_anak']);
    $kelompok_baru = intval($_POST['kelompok_baru']);
    
    $stmt = $conn->prepare("UPDATE pendaftar SET kelompok = ? WHERE id = ?");
    $stmt->bind_param("ii", $kelompok_baru, $id_anak);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    exit; // Hentikan eksekusi agar tidak mencetak HTML di response AJAX
}

// =========================================================================
// 2. BLOK AUTO-ASSIGN: MEMASUKKAN ANAK BARU KE KELOMPOK SECARA MERATA
// =========================================================================
// Cari anak yang sudah diterima tapi belum punya kelompok (kelompok = 0)
$unassigned_query = $conn->query("SELECT id FROM pendaftar WHERE status = 'diterima' AND (kelompok = 0 OR kelompok IS NULL) ORDER BY FIELD(kelas, '3 SMP', '2 SMP', '1 SMP', '6 SD', '5 SD', '4 SD', '3 SD')");

if ($unassigned_query && $unassigned_query->num_rows > 0) {
    // Hitung jumlah anggota tiap kelompok saat ini untuk mencari yang paling kosong
    $group_counts = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0];
    $cnt_query = $conn->query("SELECT kelompok, COUNT(id) as total FROM pendaftar WHERE status = 'diterima' AND kelompok IN (1,2,3,4,5) GROUP BY kelompok");
    while ($r = $cnt_query->fetch_assoc()) {
        $group_counts[$r['kelompok']] = $r['total'];
    }
    
    // Masukkan anak ke kelompok yang paling sedikit anggotanya
    while ($row = $unassigned_query->fetch_assoc()) {
        $target_group = array_keys($group_counts, min($group_counts))[0]; // Cari grup dengan member terkecil
        
        $upd = $conn->prepare("UPDATE pendaftar SET kelompok = ? WHERE id = ?");
        $upd->bind_param("ii", $target_group, $row['id']);
        $upd->execute();
        
        $group_counts[$target_group]++; // Tambah hitungan agar seimbang
    }
}

// =========================================================================
// 3. AMBIL DATA UNTUK DITAMPILKAN
// =========================================================================
$kelompok = [1 => [], 2 => [], 3 => [], 4 => [], 5 => []];
$result = $conn->query("SELECT id, nama_anak, kelas, kelompok FROM pendaftar WHERE status = 'diterima' AND kelompok IN (1,2,3,4,5) ORDER BY kelas DESC, nama_anak ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $kelompok[$row['kelompok']][] = $row;
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        /* Desain untuk item yang bisa di-drag */
        .drag-item {
            cursor: grab;
            background-color: #fff;
            transition: all 0.2s ease;
        }
        .drag-item:active {
            cursor: grabbing;
        }
        .sortable-ghost {
            opacity: 0.4;
            background-color: #f8f9fa;
            border: 2px dashed #0d6efd;
        }
        /* Memastikan list memiliki minimal tinggi agar tetap bisa di-drop saat kosong */
        .sortable-list {
            min-height: 50px;
        }
    </style>
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-primary mb-1">Manajemen Kelompok</h3>
                <p class="text-muted mb-0">Tarik nama anak (Drag & Drop) atau gunakan pilihan (Select) untuk memindahkan kelompok secara permanen.</p>
            </div>
            <span id="toastNotif" class="badge bg-success p-2 fs-6" style="display: none; transition: 0.5s;">✅ Berhasil dipindah!</span>
        </div>

        <div class="row">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100 bg-light">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Kelompok <?= $i ?></h5>
                        <span class="badge bg-light text-dark count-badge" id="count-<?= $i ?>"><?= count($kelompok[$i]) ?> Anak</span>
                    </div>
                    
                    <div class="card-body p-2">
                        <ul class="list-group sortable-list" data-kelompok="<?= $i ?>" id="list-<?= $i ?>">
                            
                            <?php foreach ($kelompok[$i] as $anak): ?>
                            <li class="list-group-item drag-item mb-1 shadow-sm rounded border d-flex justify-content-between align-items-center" data-id="<?= $anak['id'] ?>">
                                
                                <div>
                                    <span class="fw-semibold d-block"><?= htmlspecialchars($anak['nama_anak']) ?></span>
                                    <span class="badge bg-info text-dark mt-1"><?= htmlspecialchars($anak['kelas']) ?></span>
                                </div>
                                
                                <select class="form-select form-select-sm select-pindah" style="width: auto;" data-id="<?= $anak['id'] ?>">
                                    <option value="1" <?= $i == 1 ? 'selected' : '' ?>>Klp 1</option>
                                    <option value="2" <?= $i == 2 ? 'selected' : '' ?>>Klp 2</option>
                                    <option value="3" <?= $i == 3 ? 'selected' : '' ?>>Klp 3</option>
                                    <option value="4" <?= $i == 4 ? 'selected' : '' ?>>Klp 4</option>
                                    <option value="5" <?= $i == 5 ? 'selected' : '' ?>>Klp 5</option>
                                </select>

                            </li>
                            <?php endforeach; ?>

                        </ul>
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
    // Mengaktifkan sidebar toggle
    document.getElementById('btnToggleSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('toggled');
    });

    // Fungsi untuk memperbarui database via AJAX
    function updateDatabase(idAnak, kelompokBaru) {
        let formData = new FormData();
        formData.append('id_anak', idAnak);
        formData.append('kelompok_baru', kelompokBaru);

        fetch('', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(data => {
            if(data.trim() === 'success') {
                let notif = document.getElementById('toastNotif');
                notif.style.display = 'block';
                setTimeout(() => { notif.style.display = 'none'; }, 2000);
            }
        });
    }

    // Fungsi untuk memperbarui angka jumlah anak di masing-masing header kelompok
    function updateCounts() {
        for (let i = 1; i <= 5; i++) {
            let listElement = document.getElementById('list-' + i);
            let countElement = document.getElementById('count-' + i);
            let total = listElement.querySelectorAll('li').length;
            countElement.innerText = total + " Anak";
        }
    }

    // 1. Logika untuk Drag & Drop menggunakan SortableJS
    document.querySelectorAll('.sortable-list').forEach(function(listEl) {
        new Sortable(listEl, {
            group: 'shared', // Mengizinkan tarik-menarik antar list
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                let itemEl = evt.item;  // Element anak yang ditarik
                let toList = evt.to;    // List tujuan
                
                let idAnak = itemEl.getAttribute('data-id');
                let kelompokBaru = toList.getAttribute('data-kelompok');
                
                // Sinkronkan nilai select box setelah di-drag
                itemEl.querySelector('.select-pindah').value = kelompokBaru;

                updateCounts();
                updateDatabase(idAnak, kelompokBaru);
            }
        });
    });

    // 2. Logika untuk Select Box (Dropdown)
    document.querySelectorAll('.select-pindah').forEach(function(selectEl) {
        selectEl.addEventListener('change', function() {
            let idAnak = this.getAttribute('data-id');
            let kelompokBaru = this.value;
            let listItem = this.closest('li'); // Ambil kotak anak tersebut

            // Pindahkan elemen HTML-nya ke kelompok yang dipilih
            let targetList = document.getElementById('list-' + kelompokBaru);
            targetList.appendChild(listItem);

            updateCounts();
            updateDatabase(idAnak, kelompokBaru);
        });
    });
</script>
</body>
</html>