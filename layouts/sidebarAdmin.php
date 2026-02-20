<?php
// Mendeteksi halaman mana yang sedang diakses agar menu sidebar menyala (active)
$halaman_aktif = basename($_SERVER['PHP_SELF']);
?>
<style>
    /* Mengatur Layout Flexbox */
    .admin-wrapper {
        display: flex;
        align-items: stretch;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Styling Sidebar */
    #sidebar {
        min-width: 250px;
        max-width: 250px;
        background: #343a40;
        color: #fff;
        transition: all 0.3s ease-in-out;
    }

    /* Class untuk menyembunyikan sidebar di Desktop */
    #sidebar.toggled {
        margin-left: -250px;
    }

    #sidebar .sidebar-header {
        padding: 20px;
        background: #212529;
        border-bottom: 1px solid #4b545c;
    }

    #sidebar a {
        padding: 15px 20px;
        display: block;
        color: #cfd8dc;
        text-decoration: none;
        transition: 0.2s;
    }

    /* Menu Aktif & Hover */
    #sidebar a:hover, #sidebar a.active {
        background: #495057;
        color: #fff;
    }

    /* Konten Utama */
    #content-wrapper {
        width: 100%;
        min-height: 100vh;
        transition: all 0.3s ease-in-out;
        background-color: #f8f9fa;
    }

    /* Responsif untuk HP (Layar di bawah 768px) */
    @media (max-width: 768px) {
        #sidebar {
            margin-left: -250px; /* Sembunyi secara default di HP */
        }
        #sidebar.toggled {
            margin-left: 0; /* Tampil saat tombol diklik di HP */
        }
    }
</style>

<div class="admin-wrapper">
    <nav id="sidebar">
        <div class="sidebar-header text-center">
            <h4 class="mb-0 fw-bold">Panel Admin</h4>
        </div>
        <ul class="list-unstyled mt-3">
            <li>
                <a href="dashboard.php" class="<?= ($halaman_aktif == 'dashboard.php') ? 'active border-start border-4 border-primary' : '' ?>">
                    🏠 Dashboard
                </a>
            </li>
            <li>
                <a href="statusPendaftaran.php" class="<?= ($halaman_aktif == 'statusPendaftaran.php') ? 'active border-start border-4 border-primary' : '' ?>">
                    📋 Status Pendaftaran
                </a>
            </li>
            <li>
                <a href="pembagianKelompok.php" class="<?= ($halaman_aktif == 'pembagianKelompok.php') ? 'active border-start border-4 border-primary' : '' ?>">
                    🧑‍🤝‍🧑 Pembagian Kelompok
                </a>
            </li>
            <li>
                <a href="cetakKartu.php" class="<?= ($halaman_aktif == 'cetakKartu.php') ? 'active border-start border-4 border-primary' : '' ?>">
                    🖨️ Cetak Kartu Peserta
                </a>
            </li>
            <li>
                <a href="estimasiForm.php" class="<?= ($halaman_aktif == 'estimasiForm.php') ? 'active border-start border-4 border-primary' : '' ?>">
                    � Estimasi Form Pendaftaran
                </a>
            </li>
            <li>
                <a href="logout.php" class="text-danger mt-5 border-top border-secondary">
                    🚪 Logout
                </a>
            </li>
        </ul>
    </nav>

    <div id="content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
            <div class="container-fluid">
                <button type="button" id="btnToggleSidebar" class="btn btn-dark">
                    ☰ Menu
                </button>
                <span class="navbar-text ms-auto fw-bold text-dark">
                    Halo, Admin Ipul!
                </span>
            </div>
        </nav>
        
        <div class="container-fluid px-4 pb-5">