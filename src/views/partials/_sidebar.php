<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isLogin = isset($_SESSION['login']) && $_SESSION['login'] === true;
?>

<div class="sidebar-fixed d-flex flex-column justify-content-between">
    <!-- BAGIAN ATAS -->
    <div>
        <div class="bg-primary text-light text-center py-4">
            <h4 class="fw-bold me-3 px-3 mb-1">
                <a class="navbar-brand text-light text-decoration-none" href="index.php">
                    <div class="pe-4">PEMBUKUAN</div>
                    <div class="ps-5">LAWAKFEST</div>
                </a>
            </h4>
        </div>

        <div class="p-3">
            <h5 class="mb-3 text-primary">Menu Navigasi</h5>
            <ul class="nav nav-pills flex-column">

                <!-- HOME -->
                <li class="nav-item mb-2">
                    <a href="index.php" class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>">
                        <i class="bi bi-house-door me-2"></i> Home
                    </a>
                </li>

                <!-- REGISTRASI TAMU (SEMUA BISA) -->
                <li class="nav-item mb-2">
                    <a href="beli_tiket.php" class="nav-link <?= ($current_page == 'beli_tiket.php') ? 'active' : '' ?>">
                        <i class="bi bi-pencil-square me-2"></i> Registrasi Tamu
                    </a>
                </li>

                <!-- MENU ADMIN -->
                <?php if ($isAdmin): ?>
                    <li class="nav-item mb-2">
                        <a href="export_import.php" class="nav-link <?= ($current_page == 'export_import.php') ? 'active' : '' ?>">
                            <i class="bi bi-upload me-2"></i> Import / Export
                        </a>
                    </li>

                    <li class="nav-item mb-2">
                        <a href="scan.php" class="nav-link <?= ($current_page == 'scan.php') ? 'active' : '' ?>">
                            <i class="bi bi-qr-code-scan me-2"></i> Scan QR
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>

    <!-- BAGIAN BAWAH (LOGIN / LOGOUT) -->
    <div class="p-3 border-top">
        <?php if (!$isLogin): ?>
            <a href="login.php" class="btn btn-outline-primary w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i> Login Admin
            </a>
        <?php else: ?>
            <div class="text-center mb-2">
                <small class="text-muted">Login sebagai</small><br>
                <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></strong>
            </div>
            <a href="logout.php" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        <?php endif; ?>
    </div>
</div>
