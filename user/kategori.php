<?php
// Memanggil file koneksi database
require_once '../config/config.php';

// Menyiapkan daftar urutan kelas yang valid
$semua_kelas = ['3 SD', '4 SD', '5 SD', '6 SD', '1 SMP', '2 SMP', '3 SMP'];

// Menyiapkan array kosong berdasarkan kunci kelas
$data_kategori = [];
foreach ($semua_kelas as $kls) {
    $data_kategori[$kls] = [];
}

// Mengambil data pendaftar dari database, diurutkan berdasarkan abjad nama
$query = "SELECT nama_anak, kelas, status FROM pendaftar ORDER BY nama_anak ASC";
$result = $conn->query($query);

// Memasukkan data dari database ke dalam array kategori yang sesuai
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $kelas_anak = $row['kelas'];
        // Pastikan kelas ada di dalam daftar yang kita buat
        if (array_key_exists($kelas_anak, $data_kategori)) {
            $data_kategori[$kelas_anak][] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Kelas Pendaftar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">

    <?php include '../layouts/navbar.php'; ?>  

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="mb-3">
                <a href="../index.php" class="btn btn-outline-secondary">&larr; Kembali ke Beranda</a>
            </div>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title text-center mb-0">Daftar Anak Berdasarkan Kelas</h4>
                </div>
                <div class="card-body bg-white p-4">
                    <p class="text-muted text-center mb-4">
                        Klik pada masing-masing kelas untuk melihat daftar nama anak yang telah terdaftar beserta statusnya.
                    </p>
                    
                    <div class="accordion shadow-sm" id="accordionKelas">
                        
                        <?php 
                        $index = 1; // Untuk membuat ID unik pada setiap accordion
                        foreach ($semua_kelas as $kls): 
                            $jumlah_anak = count($data_kategori[$kls]);
                        ?>
                            <div class="accordion-item border-bottom">
                                <h2 class="accordion-header" id="heading-<?= $index ?>">
                                    <button class="accordion-button <?= $jumlah_anak == 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $index ?>" aria-expanded="<?= $jumlah_anak > 0 ? 'true' : 'false' ?>" aria-controls="collapse-<?= $index ?>">
                                        <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                            <span class="fs-5">Kelas <strong><?= $kls ?></strong></span>
                                            <span class="badge <?= $jumlah_anak > 0 ? 'bg-primary' : 'bg-secondary' ?> rounded-pill">
                                                <?= $jumlah_anak ?> Anak
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-<?= $index ?>" class="accordion-collapse collapse <?= $jumlah_anak > 0 ? 'show' : '' ?>" aria-labelledby="heading-<?= $index ?>" data-bs-parent="#accordionKelas">
                                    <div class="accordion-body p-0">
                                        
                                        <?php if ($jumlah_anak > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped mb-0 align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th width="10%" class="text-center">No</th>
                                                            <th width="65%">Nama Anak</th>
                                                            <th width="25%" class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 1; foreach ($data_kategori[$kls] as $anak): ?>
                                                            <tr>
                                                                <td class="text-center"><?= $no++ ?></td>
                                                                <td class="fw-semibold"><?= htmlspecialchars($anak['nama_anak']) ?></td>
                                                                <td class="text-center">
                                                                    <?php 
                                                                        if($anak['status'] == 'proses') echo '<span class="badge bg-warning text-dark px-2 py-1">Proses</span>';
                                                                        elseif($anak['status'] == 'diterima') echo '<span class="badge bg-success px-2 py-1">Diterima</span>';
                                                                        else echo '<span class="badge bg-danger px-2 py-1">Ditolak</span>';
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4 text-muted bg-light">
                                                Belum ada anak yang terdaftar di kelas ini.
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        <?php 
                        $index++; 
                        endforeach; 
                        ?>
                        
                    </div> </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>