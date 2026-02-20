<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// Proses Aksi (Ubah Status & Hapus)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    // Jika aksi adalah DELETE (Hapus)
    if ($action == 'delete') {
        // 1. Ambil nama file foto dari database untuk dihapus dari folder
        $stmt_foto = $conn->prepare("SELECT foto FROM pendaftar WHERE id = ?");
        $stmt_foto->bind_param("i", $id);
        $stmt_foto->execute();
        $res_foto = $stmt_foto->get_result();
        
        if ($row_foto = $res_foto->fetch_assoc()) {
            $path_foto = "../user/uploads/" . $row_foto['foto'];
            // Cek apakah file benar-benar ada, lalu hapus
            if (file_exists($path_foto) && is_file($path_foto)) {
                unlink($path_foto); 
            }
        }
        
        // 2. Hapus data pendaftar dari database
        $stmt_del = $conn->prepare("DELETE FROM pendaftar WHERE id = ?");
        $stmt_del->bind_param("i", $id);
        $stmt_del->execute();
        
        // Redirect kembali
        header("Location: statusPendaftaran.php");
        exit;
    } 
    // Jika aksi adalah Ubah Status (Terima / Tolak / Proses)
    else {
        $status_baru = 'proses';
        if ($action == 'terima') $status_baru = 'diterima';
        elseif ($action == 'tolak') $status_baru = 'ditolak';

        // 1. Update Database
        $stmt = $conn->prepare("UPDATE pendaftar SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status_baru, $id);
        $stmt->execute();
        
        // 2. Logika Redirect ke WhatsApp (Hanya untuk Terima dan Tolak)
        if ($action == 'terima' || $action == 'tolak') {
            $stmt_get = $conn->prepare("SELECT nama_anak, no_telpon FROM pendaftar WHERE id = ?");
            $stmt_get->bind_param("i", $id);
            $stmt_get->execute();
            $res = $stmt_get->get_result();
            
            if ($row_user = $res->fetch_assoc()) {
                $nama_anak = $row_user['nama_anak'];
                
                // Format Nomor Telepon
                $no_hp = preg_replace('/[^0-9]/', '', $row_user['no_telpon']); 
                if (substr($no_hp, 0, 1) == '0') {
                    $no_hp = '62' . substr($no_hp, 1);
                }

                // Tentukan Pesan
                if ($action == 'terima') {
                    $pesan = "Selamat anak anda dengan nama $nama_anak, diterima di pesantren ramadhan pada tanggal 07 - 08  Maret 2026 dengan infaq yang disediakan 20.000 Rp ketika hadir di pesantren pada pukul 16:00";
                } else {
                    $pesan = "Maaf anak anda belum bisa kami terima di pesantren ramadhan 2026";
                }

                // Redirect ke API WhatsApp
                $wa_url = "https://api.whatsapp.com/send?phone=" . $no_hp . "&text=" . urlencode($pesan);
                header("Location: " . $wa_url);
                exit;
            }
        }
        
        header("Location: statusPendaftaran.php");
        exit;
    }
}

// Ambil semua data
$result = $conn->query("SELECT * FROM pendaftar ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <h3 class="mb-4">Manajemen Data Pendaftaran</h3>
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive bg-white">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Foto</th>
                                <th width="15%">Nama Anak</th>
                                <th width="10%">Kelas</th>
                                <th width="25%">Data Orang Tua & Kontak</th>
                                <th width="10%">Status</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center">
                                    <?php 
                                        $foto_path = "../user/uploads/" . htmlspecialchars($row['foto']);
                                        // Pastikan file gambar benar-benar ada di folder sebelum ditampilkan
                                        if (file_exists($foto_path) && !empty($row['foto'])): 
                                    ?>
                                        <img src="<?= $foto_path ?>" alt="Foto" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak ada foto</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama_anak']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['kelas']) ?></td>
                                <td>
                                    <strong>Ayah:</strong> <?= htmlspecialchars($row['nama_ayah']) ?><br>
                                    <strong>Ibu:</strong> <?= htmlspecialchars($row['nama_ibu']) ?><br>
                                    <strong>Telp:</strong> <a href="https://wa.me/<?= htmlspecialchars($row['no_telpon']) ?>" target="_blank" class="text-decoration-none"><?= htmlspecialchars($row['no_telpon']) ?></a><br>
                                    <strong>Alamat:</strong> <?= htmlspecialchars($row['alamat']) ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        if($row['status'] == 'proses') echo '<span class="badge bg-warning text-dark px-2 py-1">Proses</span>';
                                        elseif($row['status'] == 'diterima') echo '<span class="badge bg-success px-2 py-1">Diterima</span>';
                                        else echo '<span class="badge bg-danger px-2 py-1">Ditolak</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm" role="group">
                                        <a href="?action=terima&id=<?= $row['id'] ?>" target="_blank" onclick="setTimeout(function(){ window.location.reload(); }, 1000);" class="btn btn-sm btn-outline-success">Terima</a>
                                        <a href="?action=tolak&id=<?= $row['id'] ?>" target="_blank" onclick="setTimeout(function(){ window.location.reload(); }, 1000);" class="btn btn-sm btn-outline-warning">Tolak</a>
                                        <a href="?action=proses&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary">Proses</a>
                                    </div>
                                    
                                    <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger mt-1" onclick="return confirm('Apakah Anda yakin ingin menghapus permanen data <?= htmlspecialchars($row['nama_anak']) ?>? Foto juga akan terhapus.');">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if($result->num_rows == 0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data pendaftar.</td>
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