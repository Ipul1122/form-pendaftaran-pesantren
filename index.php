<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Pendaftaran - Pesantren Ramadhan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            background-image: radial-gradient(#e0e8e5 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        /* Custom Card Styles */
        .card-custom {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
        }

        /* Gradients */
        .grad-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
        .grad-success { background: linear-gradient(135deg, #198754 0%, #20c997 100%); }
        .grad-warning { background: linear-gradient(135deg, #ffc107 0%, #ffcd39 100%); }

        /* Custom List for Syarat & Ketentuan */
        .list-syarat {
            list-style: none;
            padding-left: 0;
        }
        .list-syarat li {
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            font-size: 1.05rem;
            color: #495057;
        }
        .list-syarat li i {
            position: absolute;
            left: 0;
            top: 2px;
            font-size: 1.2rem;
        }
        .icon-check { color: #198754; }
        .icon-cross { color: #dc3545; }
        .icon-info { color: #0d6efd; }

        /* Buttons */
        .btn-custom {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .btn-wa {
            background-color: #25D366;
            color: white;
            border: none;
        }
        .btn-wa:hover {
            background-color: #128C7E;
            color: white;
        }

        /* Header Hero */
        .hero-section {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            color: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(4, 120, 87, 0.3);
        }
    </style>
</head>
<body>

    <?php include 'layouts/navbar.php'; ?>

<div class="container mt-4 mb-5">
    
    <div class="hero-section">
        <h1 class="fw-bold mb-2 display-5"><i class="bi bi-moon-stars-fill text-warning me-2"></i> Pesantren Ramadhan</h1>
        <h4 class="fw-light mb-0">Masjid Nurul Haq - 2026</h4>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            
            <div class="card card-custom mb-4">
                <div class="card-header grad-primary text-white p-3 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2"></i> Informasi Kegiatan</h5>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3 text-primary fs-4">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Tanggal Pelaksanaan</p>
                            <h6 class="fw-bold mb-0">Sabtu - Ahad, 07 - 08 Maret 2026</h6>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3 text-primary fs-4">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Waktu</p>
                            <h6 class="fw-bold mb-0">Pukul 16.00 WIB - Selesai</h6>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-3 rounded-circle me-3 text-primary fs-4">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Lokasi</p>
                            <h6 class="fw-bold mb-0">Masjid Nurul Haq</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-header grad-warning text-dark p-3 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Catatan Penting</h5>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
                        <i class="bi bi-hourglass-bottom me-2 fw-bold"></i> Pendaftaran ditutup pada <strong>Jumat, 06 Maret 2026</strong>
                    </div>
                    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-0">
                        <i class="bi bi-people-fill me-2 fw-bold"></i> Kuota Terbatas! <strong>(Hanya 40 Peserta)</strong>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-7">
            <div class="card card-custom h-100">
                <div class="card-header grad-success text-white p-3 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-card-checklist me-2"></i> Syarat dan Ketentuan</h5>
                </div>
                <div class="card-body p-4 p-md-5 bg-white d-flex flex-column justify-content-between">
                    
                    <ul class="list-syarat mb-4">
                        <li>
                            <i class="bi bi-person-check-fill icon-info"></i> 
                            Peserta minimal <strong>Kelas 3 SD - 3 SMP</strong>.
                        </li>
                        <li>
                            <i class="bi bi-wallet-fill icon-info"></i> 
                            Membayar infaq kegiatan sebesar <strong>Rp 20.000</strong>.
                        </li>
                        <li>
                            <i class="bi bi-journal-bookmark-fill icon-check"></i> 
                            <strong>Wajib</strong> membawa alat tulis dan Al-Quran/Iqro.
                        </li>
                        <li>
                            <i class="bi bi-bag-check-fill icon-check"></i> 
                            <strong>Wajib</strong> membawa salinan baju ganti.
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill icon-check"></i> 
                            <em>Diperbolehkan</em> membawa bantal dan selimut.
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill icon-check"></i> 
                            <em>Diperbolehkan</em> membawa snack/makanan ringan.
                        </li>
                        <li>
                            <i class="bi bi-x-circle-fill icon-cross"></i> 
                            <strong class="text-danger">Tidak diperkenankan</strong> membawa handphone (HP) / alat elektronik.
                        </li>
                    </ul>

                    <hr class="text-muted">

                    <div class="mt-3 text-center">
                        <p class="text-muted small fw-bold mb-3">Punya pertanyaan? Hubungi Narahubung:</p>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                            <a href="https://wa.me/6285693672730" target="_blank" class="btn btn-wa btn-custom text-white shadow-sm">
                                <i class="bi bi-whatsapp me-2"></i>Kak Syaiful
                            </a>
                            <a href="https://wa.me/6281959269130" target="_blank" class="btn btn-wa btn-custom text-white shadow-sm">
                                <i class="bi bi-whatsapp me-2"></i>Kak Zidan
                            </a>
                        </div>

                        <button type="button" class="btn btn-primary btn-custom btn-lg w-100 shadow" data-bs-toggle="modal" data-bs-target="#konfirmasiModal">
                            <i class="bi bi-pencil-square me-2"></i> Lanjut ke Pendaftaran
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
      <div class="modal-header grad-warning border-0">
        <h5 class="modal-title fw-bold text-dark" id="konfirmasiModalLabel">
            <i class="bi bi-shield-exclamation me-2"></i> Konfirmasi Persyaratan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <i class="bi bi-clipboard-check text-success mb-3 d-block" style="font-size: 4rem;"></i>
        <h5 class="fw-bold">SAYA SUDAH MEMBACA</h5>
        <p class="text-muted mb-0">Apakah Anda yakin telah membaca dan menyetujui seluruh Syarat dan Ketentuan pendaftaran?</p>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
        <button type="button" class="btn btn-light btn-custom px-4 text-secondary" data-bs-dismiss="modal">Batalkan</button>
        <a href="user/pendaftaran.php" class="btn btn-success btn-custom px-4">Ya, Lanjutkan Pendaftaran</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>a