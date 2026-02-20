<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../config/config.php';

// Ambil data hanya yang diterima
$query = "SELECT * FROM pendaftar WHERE status = 'diterima' ORDER BY kelompok ASC, nama_anak ASC";
$result = $conn->query($query);

$data_anak = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_anak[] = $row;
    }
}

// Pecah array menjadi potongan berisi 3 data per halaman A4
$chunks = array_chunk($data_anak, 3);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Peserta</title>
    <style>
        /* Konfigurasi Ukuran Kertas A4 Portrait */
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #6c757d;
            /* Memaksa background color ikut terprint */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        /* Layout HVS/A4 di Layar */
        .a4-page {
            width: 210mm;
            height: 297mm;
            background: white;
            margin: 10mm auto;
            padding: 30mm 0; 
            box-sizing: border-box;
            page-break-after: always;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 20mm; /* Jarak antar 3 kartu */
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        /* Menghilangkan margin dan shadow saat mode Print */
        @media print {
            body { background: white; margin: 0; }
            .a4-page { margin: 0; box-shadow: none; border: none; }
        }
        
        /* Spesifikasi Kartu Nama: 90 x 55 mm */
        .kartu {
            width: 90mm;
            height: 55mm;
            border: 1.5px dashed #888; /* Garis panduan gunting */
            border-radius: 4px;
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff, #f1f8ff);
        }
        /* Bagian Kiri (Foto) */
        .foto-container {
            width: 35mm;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #198754; /* Hijau pesantren */
            border-right: 3px solid #146c43;
        }
        .foto-container img {
            width: 25mm;
            height: 35mm;
            object-fit: cover;
            border: 2px solid white;
            border-radius: 4px;
            background: white;
        }
        /* Bagian Kanan (Data Teks) */
        .data-container {
            width: 55mm;
            padding: 4mm 5mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-kartu {
            font-size: 10px;
            font-weight: 900;
            color: #198754;
            text-align: center;
            border-bottom: 2px solid #198754;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
            letter-spacing: 0.5px;
        }
        .nama {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 2mm 0;
            color: #212529;
            line-height: 1.2;
        }
        .detail {
            font-size: 10px;
            margin: 0 0 1.5mm 0;
            color: #495057;
        }
    </style>
</head>
<body onload="window.print()">

    <?php if (count($chunks) == 0): ?>
        <h2 style="text-align: center; color: white; margin-top: 50px;">Belum ada data anak yang diterima.</h2>
    <?php endif; ?>

    <?php foreach ($chunks as $halaman): ?>
        <div class="a4-page">
            
            <?php foreach ($halaman as $anak): ?>
                <?php 
                    // Set foto, jika kosong atau error beri foto default
                    $foto_path = "../user/uploads/" . htmlspecialchars($anak['foto']);
                    if (!file_exists($foto_path) || empty($anak['foto'])) {
                        $foto_path = "https://via.placeholder.com/100x140?text=FOTO"; 
                    }
                    
                    // Cek jika kolom kelompok sudah ada nilainya
                    $kelompok = (isset($anak['kelompok']) && $anak['kelompok'] > 0) ? $anak['kelompok'] : 'Belum Dibagi';
                ?>
                
                <div class="kartu">
                    <div class="foto-container">
                        <img src="<?= $foto_path ?>" alt="Foto">
                    </div>
                    <div class="data-container">
                        <div class="header-kartu">PESANTREN RAMADHAN</div>
                        <div class="nama"><?= htmlspecialchars($anak['nama_anak']) ?></div>
                        <div class="detail"><strong>Kelas:</strong> <?= htmlspecialchars($anak['kelas']) ?></div>
                        <div class="detail"><strong>Kelompok:</strong> <?= htmlspecialchars($kelompok) ?></div>
                        <div class="detail"><strong>ID:</strong> P-<?= sprintf("%04d", $anak['id']) ?></div>
                    </div>
                </div>

            <?php endforeach; ?>
            
        </div>
    <?php endforeach; ?>

</body>
</html>