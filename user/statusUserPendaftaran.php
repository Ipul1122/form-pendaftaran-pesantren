<?php
// Memanggil file koneksi database
require_once '../config/config.php';
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


<div class="container mt-5">
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
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="40%">Nama Anak</th>
                                    <th width="20%">Kelas</th>
                                    <th width="30%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Mengambil data pendaftar dari database
                                $query_status = "SELECT nama_anak, kelas, status FROM pendaftar ORDER BY id DESC";
                                $result_status = $conn->query($query_status);
                                $no_urut = 1;
                                
                                if($result_status && $result_status->num_rows > 0):
                                    while ($data = $result_status->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td><?= $no_urut++ ?></td>
                                        <td class="text-start fw-semibold"><?= htmlspecialchars($data['nama_anak']) ?></td>
                                        <td><?= htmlspecialchars($data['kelas']) ?></td>
                                        <td>
                                            <?php 
                                                // Menampilkan badge Bootstrap sesuai status
                                                if($data['status'] == 'proses') {
                                                    echo '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Sedang Diproses</span>';
                                                } elseif($data['status'] == 'diterima') {
                                                    echo '<span class="badge bg-success px-3 py-2 rounded-pill">Diterima</span>';
                                                } elseif($data['status'] == 'ditolak') {
                                                    echo '<span class="badge bg-danger px-3 py-2 rounded-pill">Ditolak</span>';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile; 
                                else:
                                ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data pendaftar saat ini.</td>
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
</body>
</html>