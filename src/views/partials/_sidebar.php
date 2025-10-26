<?php
// Mendapatkan nama file saat ini untuk menandai link aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- ==================== SIDEBAR ==================== -->
<div class="sidebar-fixed">
    <div class="bg-primary text-light text-center">
        <h4 class="fw-bold pt-4 me-3 px-3">
            <a class="navbar-brand" href="index.php">
                <div class="pe-4">
                    PEMBUKUAN
                </div>
                <div class="ps-5">
                    LAWAKFEST
                </div>
            </a>
            <br>
        </h4>
    </div>
    <div class="p-3">
        <h5 class="mb-3 text-primary">Menu Navigasi</h5>
        <ul class="nav nav-pills flex-column">
            <li class="nav-item mb-2">
                <a href="index.php" class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>">
                    <i class="bi bi-house-door me-2"></i> Home
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="export_import.php" class="nav-link <?= ($current_page == 'export_import.php') ? 'active' : '' ?>">
                    <i class="bi bi-upload me-2"></i> Import / Export
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="beli_tiket.php" class="nav-link <?= ($current_page == 'beli_tiket.php') ? 'active' : '' ?>">
                    <i class="bi bi-pencil-square me-2"></i> Registrasi Tamu
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="scan.php" class="nav-link <?= ($current_page == 'scan.php') ? 'active' : '' ?>">
                    <i class="bi bi-qr-code-scan me-2"></i> Scan QR
                </a>
            </li>
        </ul>
    </div>
</div>