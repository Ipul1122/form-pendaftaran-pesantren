<?php
// Mulai session untuk melacak waktu pengisian form
session_start();

require_once '../config/config.php';

$pesan = "";
$tampil_modal_sukses = false; // Flag untuk menampilkan modal sukses

// Cek apakah user sedang dalam masa tunggu (cooldown) 1 menit
$waktu_tunggu = 60; // 60 detik = 1 menit
$sedang_cooldown = false;
$sisa_waktu = 0;

if (isset($_SESSION['last_submit_time'])) {
    $selisih_waktu = time() - $_SESSION['last_submit_time'];
    if ($selisih_waktu < $waktu_tunggu) {
        $sedang_cooldown = true;
        $sisa_waktu = $waktu_tunggu - $selisih_waktu;
        // Menambahkan ID pada span agar bisa dimanipulasi oleh JavaScript
        $pesan = "<div id='alertCooldown' class='alert alert-danger fw-bold text-center'>⚠️ Anda bertindak terlalu cepat. Harap tunggu <span id='timerAngka'>$sisa_waktu</span> detik sebelum mendaftar lagi.</div>";
    }
}

// Proses form jika di-submit
if (isset($_POST['submit'])) {
    // Jika tidak sedang cooldown, proses data
    if (!$sedang_cooldown) {
        $nama_anak = $conn->real_escape_string($_POST['nama_anak']);
        $kelas = $conn->real_escape_string($_POST['kelas']);
        $alamat = $conn->real_escape_string($_POST['alamat']);
        $nama_ayah = $conn->real_escape_string($_POST['nama_ayah']);
        $nama_ibu = $conn->real_escape_string($_POST['nama_ibu']);
        $no_telpon = $conn->real_escape_string($_POST['no_telpon']);

        // Proses Upload File
        $nama_file = $_FILES['foto']['name'];
        $ukuran_file = $_FILES['foto']['size'];
        $tmp_file = $_FILES['foto']['tmp_name'];
        $error_file = $_FILES['foto']['error'];

        $ekstensi_diperbolehkan = array('jpg', 'jpeg', 'png', 'pdf');
        $x = explode('.', $nama_file);
        $ekstensi = strtolower(end($x));
        $ukuran_maksimal = 5 * 1024 * 1024; // 5 MB

        if ($error_file === 4) {
            $pesan = "<div class='alert alert-danger'>Pilih file foto/dokumen terlebih dahulu!</div>";
        } else {
            if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
                if ($ukuran_file <= $ukuran_maksimal) {
                    $nama_file_baru = uniqid() . '-' . $nama_file;
                    $direktori = 'uploads/'; 
                    
                    if (!is_dir($direktori)) {
                        mkdir($direktori, 0777, true);
                    }

                    if (move_uploaded_file($tmp_file, $direktori . $nama_file_baru)) {
                        $stmt = $conn->prepare("INSERT INTO pendaftar (nama_anak, foto, kelas, alamat, nama_ayah, nama_ibu, no_telpon) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssss", $nama_anak, $nama_file_baru, $kelas, $alamat, $nama_ayah, $nama_ibu, $no_telpon);

                        if ($stmt->execute()) {
                            // Catat waktu pendaftaran berhasil untuk mencegah spam
                            $_SESSION['last_submit_time'] = time();
                            
                            $tampil_modal_sukses = true; // Set true agar modal kedua muncul
                        } else {
                            $pesan = "<div class='alert alert-danger'>Terjadi kesalahan sistem: " . $stmt->error . "</div>";
                        }
                        $stmt->close();
                    } else {
                        $pesan = "<div class='alert alert-danger'>Gagal mengunggah file.</div>";
                    }
                } else {
                    $pesan = "<div class='alert alert-warning'>Ukuran file terlalu besar! Maksimal 5MB.</div>";
                }
            } else {
                $pesan = "<div class='alert alert-warning'>Ekstensi file tidak diperbolehkan! Hanya JPG, JPEG, PNG, atau PDF.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
        }
        .form-disabled {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-light">

    <?php include '../layouts/navbar.php'; ?>  

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title text-center mb-0">Formulir Pendaftaran</h3>
                </div>
                <div class="card-body p-4">
                    
                    <?= $pesan; ?>

                    <form id="formDaftar" action="" method="POST" enctype="multipart/form-data" class="<?= $sedang_cooldown ? 'form-disabled' : '' ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Anak</label>
                            <input type="text" id="nama_anak" name="nama_anak" class="form-control val-input input-form" required <?= $sedang_cooldown ? 'disabled' : '' ?>>
                            <div class="invalid-feedback">Nama anak wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto / Dokumen (Max 5MB: JPG/JPEG/PNG/PDF)</label>
                            <input type="file" id="foto" name="foto" class="form-control val-input input-form" accept=".jpg,.jpeg,.png,.pdf" required <?= $sedang_cooldown ? 'disabled' : '' ?>>
                            <div class="invalid-feedback">File foto/dokumen wajib diunggah.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select id="kelas" name="kelas" class="form-select val-input input-form" required <?= $sedang_cooldown ? 'disabled' : '' ?>>
                                <option value="" disabled selected>-- Pilih Kelas --</option>
                                <option value="3 SD">3 SD</option>
                                <option value="4 SD">4 SD</option>
                                <option value="5 SD">5 SD</option>
                                <option value="6 SD">6 SD</option>
                                <option value="1 SMP">1 SMP</option>
                                <option value="2 SMP">2 SMP</option>
                                <option value="3 SMP">3 SMP</option>
                            </select>
                            <div class="invalid-feedback">Silakan pilih kelas.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea id="alamat" name="alamat" class="form-control val-input input-form" rows="3" required <?= $sedang_cooldown ? 'disabled' : '' ?>></textarea>
                            <div class="invalid-feedback">Alamat lengkap wajib diisi.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Ayah</label>
                                <input type="text" id="nama_ayah" name="nama_ayah" class="form-control val-input input-form" required <?= $sedang_cooldown ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">Nama ayah wajib diisi.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Ibu</label>
                                <input type="text" id="nama_ibu" name="nama_ibu" class="form-control val-input input-form" required <?= $sedang_cooldown ? 'disabled' : '' ?>>
                                <div class="invalid-feedback">Nama ibu wajib diisi.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="number" id="no_telpon" name="no_telpon" class="form-control val-input input-form" placeholder="Contoh: 08123456789" required <?= $sedang_cooldown ? 'disabled' : '' ?>>
                            <div class="invalid-feedback">Nomor telepon wajib diisi.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" id="btnSubmit" class="btn btn-success btn-lg" onclick="validasiForm()" <?= $sedang_cooldown ? 'disabled' : '' ?>>
                                <?= $sedang_cooldown ? "Tunggu $sisa_waktu detik..." : "Daftar Sekarang" ?>
                            </button>
                            <a href="../index.php" id="btnKembali" class="btn btn-outline-secondary <?= $sedang_cooldown ? 'disabled' : '' ?>">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">Konfirmasi Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body fs-5 text-center">
        <strong>Apakah Data Sudah Benar?</strong><br>
        <small class="text-muted">Pastikan semua data yang diisi telah sesuai.</small>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
        <button type="submit" form="formDaftar" name="submit" class="btn btn-success">Ya, Sudah Benar</button>
      </div>
    </div>
  </div>
</div>

<?php if ($tampil_modal_sukses): ?>
<div class="modal fade" id="modalSukses" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Pendaftaran Berhasil!</h5>
      </div>
      <div class="modal-body fs-5 text-center mt-3 mb-3">
        <strong>Ayo Lihat Data Anak Anda Sedang Di Proses</strong>
      </div>
      <div class="modal-footer justify-content-center">
        <a href="statusUserPendaftaran.php" class="btn btn-primary w-100">Cek Status Pendaftaran</a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Variabel JS untuk melacak status cooldown
    let isCooldown = <?= $sedang_cooldown ? 'true' : 'false' ?>;
    let waktuTersisa = <?= $sisa_waktu ?>;

    // Logika Timer Real-time
    if (isCooldown && waktuTersisa > 0) {
        let intervalTimer = setInterval(function() {
            waktuTersisa--;
            
            // Update teks peringatan dan tombol
            let timerElement = document.getElementById('timerAngka');
            if(timerElement) timerElement.innerText = waktuTersisa;
            
            let btnElement = document.getElementById('btnSubmit');
            if(btnElement) btnElement.innerText = "Tunggu " + waktuTersisa + " detik...";

            // Jika waktu habis, buka form otomatis
            if (waktuTersisa <= 0) {
                clearInterval(intervalTimer); // Hentikan hitungan mundur
                isCooldown = false; // Matikan status cooldown

                // Sembunyikan pesan peringatan
                let alertBox = document.getElementById('alertCooldown');
                if (alertBox) alertBox.style.display = 'none';

                // Buka kunci class dan atribut disabled pada form
                document.getElementById('formDaftar').classList.remove('form-disabled');
                
                let inputs = document.querySelectorAll('.input-form');
                inputs.forEach(function(input) {
                    input.removeAttribute('disabled');
                });

                // Aktifkan kembali tombol-tombol
                btnElement.removeAttribute('disabled');
                btnElement.innerText = "Daftar Sekarang";
                document.getElementById('btnKembali').classList.remove('disabled');
            }
        }, 1000); // 1000 ms = 1 detik
    }

    // Menghapus notifikasi error berwarna merah jika user mulai mengetik/memilih data
    document.querySelectorAll('.val-input').forEach(function(input) {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
        input.addEventListener('change', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Custom Validation logic
    function validasiForm() {
        // Cek status isCooldown dari JS, bukan PHP
        if (isCooldown) {
            return;
        }

        var form = document.getElementById('formDaftar');
        var inputs = form.querySelectorAll('.val-input');
        var isValid = true;
        var firstInvalidInput = null;

        inputs.forEach(function(input) {
            // Cek apakah input kosong
            if (!input.value.trim()) {
                input.classList.add('is-invalid'); // Tambah border merah dan teks error
                isValid = false;
                
                // Simpan input pertama yang kosong agar bisa difokuskan
                if (!firstInvalidInput) {
                    firstInvalidInput = input;
                }
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (isValid) {
            // Jika semua kolom sudah terisi, tampilkan modal konfirmasi
            var modalKonfirmasi = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
            modalKonfirmasi.show();
        } else {
            // Jika ada yang belum diisi, scroll halaman ke input pertama yang kosong
            firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalidInput.focus();
        }
    }

    // Memunculkan Modal Sukses otomatis ketika flag dari PHP bernilai true
    <?php if ($tampil_modal_sukses): ?>
    document.addEventListener("DOMContentLoaded", function() {
        var modalSukses = new bootstrap.Modal(document.getElementById('modalSukses'));
        modalSukses.show();
    });
    <?php endif; ?>
</script>
</body>
</html>