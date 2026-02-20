<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">


<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h1>BACA SEBELUM DAFTAR</h1>
        </div>
        <div class="card-body p-4">
            
            <h4 class="text-danger">Larangan</h4>
            <ul>
                <li>Dilarang memalsukan data diri anak dan orang tua.</li>
                <li>Dilarang mengunggah file foto atau dokumen yang tidak pantas.</li>
                <li>Dilarang Membawa Handphone atau barang elektronik lainnya.</li>
                <li>Dilarang Membawa mainan atau alat yang dapat melukai seseorang.</li>
                <li>Tidak diperbolehkan jika keadaan Anak sedang sakit</li>
            </ul>

            <h4 class="text-warning mt-4">Syarat</h4>
            <ul>
                <li>Calon peserta didik minimal <b>Kelas 3 SD Sampai 3 SMP.</b></li>
                <li>Menyiapkan pas foto atau dokumen identitas dalam format JPG/JPEG/PNG/PDF.</li>
                <li>Ukuran file maksimal 5MB.</li>
                <li>Kami hanya menerima pendaftaran sekitar <b>40 Anak</b></li>
                <li>Link pendaftaran ditutup pada <b>Jumat, 06 Maret 2026</b></li>
                <li>Pesantren Dilaksanakan pada <b>Sabtu, 07 Maret 2026 Pukul 16:00 WIB</b></li>
                <li>Lokasi <b>Masjid Nurul Haq</b></li>
                <li>Adapun Infaq yang diperlukan sebesar <b>Rp 20.000</b></li>
            </ul>

            <h4 class="text-success mt-4">Ketentuan</h4>
            <ul>
                <li>Data yang sudah disubmit tidak dapat diubah secara mandiri.</li>
                <li>Panitia berhak membatalkan pendaftaran jika ditemukan ketidaksesuaian data.</li>
                <li>Dibolehkan Membawa Snack dari rumah</li>
                <li>Dibolehkan Membawa Alat Tulis & Alat Mandi</li>
                <li>Dibolehkan Membawa Bantal, Guling, Selimut</li>
                <li>Diwajibkan Membawa <b>Salinan Baju Ganti</b></li>
                <li>Jika ada yang ingin ditanyakan Hub
                    <br>
                    <a href="https://wa.me/6285693672730" class="btn btn-success mt-2">
                        📞 Hubungi Kak Syaiful
                    </a>
                    <a href="https://wa.me/6281959269130" class="btn btn-success mt-2">
                        📞 Hubungi Kak Zidan
                    </a>

                </li>
            </ul>

            <hr class="my-4">
            <div class="text-center">
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#konfirmasiModal">
                    Lanjut ke Pendaftaran
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Persyaratan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body fs-5 text-center">
        <strong>SAYA SUDAH MEMBACA</strong><br>
        Apakah Anda yakin ingin melanjutkan?
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
        <a href="user/pendaftaran.php" class="btn btn-success">Ya, Lanjutkan</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>