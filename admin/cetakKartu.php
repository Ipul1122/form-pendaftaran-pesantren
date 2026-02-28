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

// PERUBAHAN UTAMA: Ubah angka 6 menjadi 10 (2 kolom x 5 baris)
$chunks = array_chunk($data_anak, 10);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Peserta</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0; /* Wajib 0 agar muat 10 kartu */
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #6c757d;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Layout Halaman A4 */
        .a4-page {
            width: 210mm;
            height: 297mm;
            background: white;
            margin: 10mm auto;
            
            /* GRID SYSTEM: 2 Kolom x 5 Baris */
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(5, 1fr); /* 5 Baris proporsional */
            
            justify-items: center;
            align-items: center;
            
            /* Padding dan Gap yang diperketat agar muat */
            padding: 10mm 5mm; 
            row-gap: 2mm;      /* Jarak antar baris diperkecil */
            column-gap: 5mm;   /* Jarak antar kolom */
            
            box-sizing: border-box;
            page-break-after: always;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }

        @media print {
            body { background: white; margin: 0; }
            .a4-page { margin: 0; box-shadow: none; border: none; height: 296mm; /* Sedikit dikurangi dari 297mm untuk mencegah auto page-break */ width: 100%; }
        }
        
        /* Kartu Nama (Ukuran Tetap 90x55mm) */
        .kartu {
            width: 90mm;
            height: 54mm; /* Dikurangi 1mm untuk keamanan layout */
            border: 1px dashed #bbb;
            border-radius: 4px;
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff, #f1f8ff);
        }

        /* Bagian Kiri (Foto) */
        .foto-container {
            width: 28mm;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #198754; 
            border-right: 3px solid #146c43;
        }
        .foto-container img {
            width: 22mm;
            height: 30mm;
            object-fit: cover;
            border: 2px solid white;
            border-radius: 4px;
            background: white;
        }

        /* Bagian Kanan (Data) */
        .data-container {
            flex: 1;
            padding: 2mm 3mm; /* Padding diperkecil sedikit */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-kartu {
            font-size: 8px;
            font-weight: 900;
            color: #198754;
            text-align: left;
            border-bottom: 2px solid #198754;
            padding-bottom: 1mm;
            margin-bottom: 1.5mm;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .nama {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1.5mm;
            color: #212529;
            line-height: 1.1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .detail {
            font-size: 8px;
            margin-bottom: 0.5mm;
            color: #495057;
        }
        
        .badge-kelompok {
            display: inline-block;
            background: #ffc107;
            color: #000;
            padding: 0px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7px;
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
                    $foto_path = "../user/uploads/" . htmlspecialchars($anak['foto']);
                    if (!file_exists($foto_path) || empty($anak['foto'])) {
                        $foto_path = "https://via.placeholder.com/100x140?text=FOTO"; 
                    }
                    $kelompok = (isset($anak['kelompok']) && $anak['kelompok'] > 0) ? $anak['kelompok'] : '-';
                ?>
                
                <div class="kartu">
                    <div class="foto-container">
                        <img src="<?= $foto_path ?>" alt="Foto">
                    </div>
                    <div class="data-container">
                        <div class="header-kartu">Pesantren Ramadhan</div>
                        <div class="nama"><?= htmlspecialchars($anak['nama_anak']) ?></div>
                        <div class="detail"><strong>Kelas:</strong> <?= htmlspecialchars($anak['kelas']) ?></div>
                        <div class="detail">
                            <strong>Kelompok:</strong> 
                            <span class="badge-kelompok"><?= htmlspecialchars($kelompok) ?></span>
                        </div>
                        <div class="detail"><strong>ID:</strong> P-<?= sprintf("%04d", $anak['id']) ?></div>
                    </div>
                </div>

            <?php endforeach; ?>
            
        </div>
    <?php endforeach; ?>

</body>
</html>