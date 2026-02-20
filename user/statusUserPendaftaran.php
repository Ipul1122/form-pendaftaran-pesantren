<?php
// Memanggil file koneksi database
require_once '../config/config.php';

// Mengambil data pendaftar dari database
$query_status = "SELECT nama_anak, kelas, status FROM pendaftar ORDER BY id DESC";
$result_status = $conn->query($query_status);

// Menyiapkan array kosong untuk memisahkan data berdasarkan status
$data_proses = [];
$data_diterima = [];
$data_ditolak = [];

if ($result_status && $result_status->num_rows > 0) {
    while ($row = $result_status->fetch_assoc()) {
        if ($row['status'] == 'proses') {
            $data_proses[] = $row;
        } elseif ($row['status'] == 'diterima') {
            $data_diterima[] = $row;
        } elseif ($row['status'] == 'ditolak') {
            $data_ditolak[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran Anak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include '../layouts/navbar.php'; ?>  

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="mb-3">
                <a href="pendaftaran.php" class="btn btn-outline-secondary">&larr; Kembali ke Pendaftaran</a>
            </div>

            <div class="card shadow border-0">
                <div class="card-header bg-info text-white">
                    <h4 class="card-title text-center mb-0">Informasi Status Pendaftaran</h4>
                </div>
                <div class="card-body bg-white p-4">
                    <p class="text-muted text-center mb-4"><small><em>*Demi menjaga privasi, data kontak dan alamat orang tua disembunyikan.</em></small></p>
                    
                    <ul class="nav nav-tabs nav-fill mb-4" id="statusTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-warning" id="proses-tab" data-bs-toggle="tab" data-bs-target="#proses" type="button" role="tab" aria-controls="proses" aria-selected="true">
                                ⏳ Sedang Diproses (<?= count($data_proses) ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-success" id="diterima-tab" data-bs-toggle="tab" data-bs-target="#diterima" type="button" role="tab" aria-controls="diterima" aria-selected="false">
                                ✅ Diterima (<?= count($data_diterima) ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-danger" id="ditolak-tab" data-bs-toggle="tab" data-bs-target="#ditolak" type="button" role="tab" aria-controls="ditolak" aria-selected="false">
                                ❌ Ditolak (<?= count($data_ditolak) ?>)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="statusTabsContent">
                        
                        <div class="tab-pane fade show active" id="proses" role="tabpanel" aria-labelledby="proses-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered text-center align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="10%">No</th>
                                            <th width="50%">Nama Anak</th>
                                            <th width="20%">Kelas</th>
                                            <th width="20%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($data_proses) > 0): ?>
                                            <?php $no = 1; foreach($data_proses as $data): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td class="text-start fw-semibold"><?= htmlspecialchars($data['nama_anak']) ?></td>
                                                    <td><?= htmlspecialchars($data['kelas']) ?></td>
                                                    <td><span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Sedang Diproses</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada data pendaftar yang sedang diproses.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="diterima" role="tabpanel" aria-labelledby="diterima-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered text-center align-middle">
                                    <thead class="table-success align-middle">
                                        <tr>
                                            <th width="10%">No</th>
                                            <th width="50%">Nama Anak</th>
                                            <th width="20%">Kelas</th>
                                            <th width="20%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($data_diterima) > 0): ?>
                                            <?php $no = 1; foreach($data_diterima as $data): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td class="text-start fw-semibold"><?= htmlspecialchars($data['nama_anak']) ?></td>
                                                    <td><?= htmlspecialchars($data['kelas']) ?></td>
                                                    <td><span class="badge bg-success px-3 py-2 rounded-pill">Diterima</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data pendaftar yang diterima.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="ditolak" role="tabpanel" aria-labelledby="ditolak-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered text-center align-middle">
                                    <thead class="table-danger align-middle">
                                        <tr>
                                            <th width="10%">No</th>
                                            <th width="50%">Nama Anak</th>
                                            <th width="20%">Kelas</th>
                                            <th width="20%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($data_ditolak) > 0): ?>
                                            <?php $no = 1; foreach($data_ditolak as $data): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td class="text-start fw-semibold"><?= htmlspecialchars($data['nama_anak']) ?></td>
                                                    <td><?= htmlspecialchars($data['kelas']) ?></td>
                                                    <td><span class="badge bg-danger px-3 py-2 rounded-pill">Ditolak</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada data pendaftar yang ditolak.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div> </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>