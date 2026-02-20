<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include '../layouts/sidebarAdmin.php'; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="text-primary">Selamat Datang di Dashboard!</h2>
                <p class="text-muted fs-5">Gunakan menu di samping untuk mengelola data pendaftaran calon siswa.</p>
                <hr>
                <div class="alert alert-info border-0 shadow-sm">
                    <strong>Informasi:</strong> Sidebar di sebelah kiri bersifat responsif. Anda dapat menyembunyikannya dengan menekan tombol <strong>☰ Menu</strong> di pojok kiri atas.
                </div>
            </div>
        </div>

    </div> 
    </div> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script JavaScript untuk hide/show sidebar
    document.getElementById('btnToggleSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('toggled');
    });
</script>
</body>
</html>