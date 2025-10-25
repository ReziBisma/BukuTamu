<nav class="navbar shadow-sm mb-4" style="background-color: #FFD700;">
  <div class="container-fluid">
    <button class="btn btn-outline-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
      ☰
    </button>
    <a class="navbar-brand fw-bold ms-3" href="index.php" style="color: black;">
      PEMBUKUAN LAWAKFEST
    </a>
  </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" style="background-color: #FFD700; color: black;">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" style="color: black;">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="list-unstyled">
      <li class="mb-2">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" 
           href="index.php" style="color: black;">
          🏠 Home
        </a>
      </li>
      <li class="mb-2">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'export_import.php' ? 'active' : '' ?>" 
           href="export_import.php" style="color: black;">
          📤 Import / Export
        </a>
      </li>
      <li class="mb-2">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active fw-bold text-primary' : '' ?>" 
           href="beli_tiket.php" style="color: black;">
          ✍️ Registrasi Tamu
        </a>
      </li>
      <li class="mb-2">
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'scan.php' ? 'active' : '' ?>" 
           href="scan.php" style="color: black;">
          📷 Scan QR / Barcode
        </a>
      </li>
    </ul>
  </div>
</div>