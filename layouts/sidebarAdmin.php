<?php
// Mendeteksi halaman mana yang sedang diakses agar menu sidebar menyala (active)
$halaman_aktif = basename($_SERVER['PHP_SELF']);

// Array halaman yang termasuk dalam kategori Games
$halaman_games = ['cerdasCermatDigital.php', 'susunAyat.php', 'qrCodeHunter.php', 'tebakSiapaAku.php'];
$is_games_active = in_array($halaman_aktif, $halaman_games);
?>
<style>
    /* Mengatur Layout Flexbox */
    .admin-wrapper {
        display: flex;
        align-items: stretch;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Styling Sidebar Base */
    #sidebar {
        min-width: 260px;
        max-width: 260px;
        background: #212529; /* Warna gelap modern */
        color: #fff;
        transition: all 0.3s ease-in-out;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    /* Class untuk menyembunyikan sidebar di Desktop */
    #sidebar.toggled {
        margin-left: -260px;
    }

    #sidebar .sidebar-header {
        padding: 25px 20px;
        background: #1a1d20;
        border-bottom: 1px solid #343a40;
    }

    /* Styling Menu Item */
    #sidebar ul.components {
        padding: 15px 10px;
    }

    #sidebar a.nav-link-custom {
        padding: 12px 15px;
        display: flex;
        align-items: center;
        color: #adb5bd;
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 8px; /* UI membulat modern */
        margin-bottom: 5px;
        font-weight: 500;
    }

    /* Menu Aktif & Hover */
    #sidebar a.nav-link-custom:hover, 
    #sidebar a.nav-link-custom.active {
        background: #0d6efd; /* Biru Bootstrap */
        color: #fff;
        box-shadow: 0 4px 6px rgba(13, 110, 253, 0.2);
    }

    /* Spesifik untuk menu Logout */
    #sidebar a.nav-link-logout {
        color: #dc3545;
        border: 1px solid transparent;
    }
    #sidebar a.nav-link-logout:hover {
        background: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }

    /* --- DROPDOWN GAYA MODERN --- */
    .dropdown-toggle::after {
        display: inline-block;
        margin-left: auto; /* Mendorong panah ke ujung kanan */
        vertical-align: .255em;
        content: "";
        border-top: .3em solid;
        border-right: .3em solid transparent;
        border-bottom: 0;
        border-left: .3em solid transparent;
        transition: transform 0.3s ease;
    }
    /* Memutar panah saat dropdown terbuka */
    .dropdown-toggle[aria-expanded="true"]::after {
        transform: rotate(-180deg);
    }
    
    /* Sub-menu (Children) */
    ul.sub-menu {
        padding-left: 0;
        list-style: none;
        background: #1a1d20;
        border-radius: 8px;
        margin-bottom: 5px;
        overflow: hidden;
    }
    ul.sub-menu a {
        padding: 10px 15px 10px 45px; /* Indentasi ke dalam */
        display: block;
        color: #adb5bd;
        text-decoration: none;
        font-size: 0.9em;
        transition: 0.2s;
    }
    ul.sub-menu a:hover, ul.sub-menu a.active {
        color: #fff;
        background: rgba(255,255,255,0.05);
        border-left: 3px solid #0d6efd;
    }

    /* Konten Utama */
    #content-wrapper {
        width: 100%;
        min-height: 100vh;
        transition: all 0.3s ease-in-out;
        background-color: #f4f6f9;
    }

    /* Responsif untuk HP */
    @media (max-width: 768px) {
        #sidebar {
            margin-left: -260px; 
            position: absolute;
            height: 100%;
        }
        #sidebar.toggled {
            margin-left: 0; 
        }
    }
</style>

<div class="admin-wrapper">
    <nav id="sidebar">
        <div class="sidebar-header text-center">
            <h4 class="mb-0 fw-bold text-white">⚙️ Panel Admin</h4>
            <small class="text-muted">Pesantren Ramadhan</small>
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="dashboard.php" class="nav-link-custom <?= ($halaman_aktif == 'dashboard.php') ? 'active' : '' ?>">
                    <span class="me-3">🏠</span> Dashboard
                </a>
            </li>
            <li>
                <a href="statusPendaftaran.php" class="nav-link-custom <?= ($halaman_aktif == 'statusPendaftaran.php') ? 'active' : '' ?>">
                    <span class="me-3">📋</span> Status Pendaftaran
                </a>
            </li>
            <li>
                <a href="pembagianKelompok.php" class="nav-link-custom <?= ($halaman_aktif == 'pembagianKelompok.php') ? 'active' : '' ?>">
                    <span class="me-3">🧑‍🤝‍🧑</span> Pembagian Kelompok
                </a>
            </li>
            <li>
                <a href="cetakKartu.php" class="nav-link-custom <?= ($halaman_aktif == 'cetakKartu.php') ? 'active' : '' ?>">
                    <span class="me-3">🖨️</span> Cetak Kartu Peserta
                </a>
            </li>
            <li>
                <a href="verifikasiUser.php" class="nav-link-custom <?= ($halaman_aktif == 'verifikasiUser.php') ? 'active' : '' ?>">
                    <span class="me-3">✅</span> Verifikasi Kehadiran
                </a>
            </li>
            <li>
                <a href="estimasiForm.php" class="nav-link-custom <?= ($halaman_aktif == 'estimasiForm.php') ? 'active' : '' ?>">
                    <span class="me-3">⚙️</span> Pengaturan Form
                </a>
            </li>

            <li>
                <a href="#gamesMenu" data-bs-toggle="collapse" aria-expanded="<?= $is_games_active ? 'true' : 'false' ?>" class="nav-link-custom dropdown-toggle <?= $is_games_active ? 'active' : '' ?>">
                    <span class="me-3">🎮</span> Games
                </a>
                <ul class="collapse sub-menu <?= $is_games_active ? 'show' : '' ?>" id="gamesMenu">
                    <li>
                        <a href="cerdasCermatDigital.php" class="<?= ($halaman_aktif == 'cerdasCermatDigital.php') ? 'active' : '' ?>">
                            Cerdas Cermat
                        </a>
                    </li>
                    <li>
                        <a href="susunAyat.php" class="<?= ($halaman_aktif == 'susunAyat.php') ? 'active' : '' ?>">
                            Susun Ayat
                        </a>
                    </li>
                    <li>
                        <a href="qrCodeHunter.php" class="<?= ($halaman_aktif == 'qrCodeHunter.php') ? 'active' : '' ?>">
                            QR Code Hunter
                        </a>
                    </li>
                    <li>
                        <a href="tebakSiapaAku.php" class="<?= ($halaman_aktif == 'tebakSiapaAku.php') ? 'active' : '' ?>">
                            Tebak Siapa Aku
                        </a>
                    </li>
                </ul>
            </li>

            <hr class="border-secondary my-3">
            
            <li>
                <a href="logout.php" class="nav-link-custom nav-link-logout mt-auto">
                    <span class="me-3">🚪</span> Logout
                </a>
            </li>
        </ul>
    </nav>

    <div id="content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
            <div class="container-fluid">
                <button type="button" id="btnToggleSidebar" class="btn btn-outline-dark border-0 shadow-sm">
                    ☰ <span class="d-none d-md-inline ms-1">Menu</span>
                </button>
                <span class="navbar-text ms-auto fw-bold text-dark d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=Admin+Ipul&background=0d6efd&color=fff" alt="Avatar" class="rounded-circle me-2" width="30">
                    Halo, Admin Ipul!
                </span>
            </div>
        </nav>
        
        <div class="container-fluid px-4 pb-5">