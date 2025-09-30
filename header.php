<!-- Navbar -->
<nav class="navbar navbar-light bg-white shadow-sm mb-4">
  <div class="container-fluid">
    <!-- Tombol untuk membuka sidebar -->
    <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
      ☰
    </button>
    <a class="navbar-brand fw-bold text-primary ms-3" href="index.php">
      📖 Buku Tamu Digital
    </a>
  </div>
</nav>

<!-- Sidebar (Offcanvas) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Menu</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="list-unstyled">
      <li class="mb-2">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
          🏠 Home
        </a>
      </li>
      <li class="mb-2">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'export_import.php' ? 'active' : '' ?>" href="export_import.php">
          📤 Import / Export
        </a>
      </li>
      <li class="mb-2">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active fw-bold text-primary' : '' ?>" href="register.php">
          ✍️ Registrasi Tamu
        </a>
      </li>
      <li class="mb-2">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'scan.php' ? 'active' : '' ?>" href="scan.php">
          📷 Scan QR / Barcode
        </a>
      </li>
    </ul>
  </div>
</div>
