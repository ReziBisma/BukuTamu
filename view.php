<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Buku Tamu</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

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
        <a class="nav-link" href="controll.php?action=export_csv">
          📤 Import / Export
        </a>
      </li>
    </ul>
  </div>
</div>


<div class="container">

  <!-- Notifikasi -->
  <?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-info"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
  <?php endif; ?>

  <!-- Form tambah tamu -->
  <form action="controll.php" method="post" class="mb-3">
    <div class="row g-2">
      <div class="col-md-3"><input type="text" name="Nama" class="form-control" placeholder="Nama" required></div>
      <div class="col-md-4"><input type="text" name="Alamat" class="form-control" placeholder="Alamat" required></div>
      <div class="col-md-3"><input type="text" name="Telp" class="form-control" placeholder="Telepon"></div>
      <div class="col-md-2">
        <button type="submit" name="action" value="add" class="btn btn-success w-100">Tambah</button>
      </div>
    </div>
  </form>

  <!-- Form import excel -->
  <form action="controll.php" method="post" enctype="multipart/form-data" class="mb-3">
    <div class="row g-2">
      <div class="col-md-6">
        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" class="form-control" required>
      </div>
      <div class="col-md-2">
        <button type="submit" name="action" value="import_excel" class="btn btn-primary w-100">Import CSV</button>
      </div>
    </div>
  </form>

    <!-- Tombol Export CSV -->
  <form action="controll.php" method="post" style="display:inline-block; margin-left:10px;">
    <button type="submit" name="action" value="export_csv" class="btn btn-info">Export CSV</button>
  </form>


  <!-- Tabel data -->
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Telepon</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><input type="text" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>" disabled></td>
          <td><input type="text" class="form-control" value="<?= htmlspecialchars(encryptCaesar($row['alamat'], 3)) ?>" disabled></td>
          <td><input type="text" class="form-control" value="<?= htmlspecialchars(encryptCaesar($row['telepon'], 3)) ?>" disabled></td>
          <td>
            <!-- Tombol edit -->
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
            <!-- Tombol hapus -->
            <form action="controll.php" method="post" style="display:inline-block;">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Hapus</button>
            </form>
          </td>
        </tr>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="controll.php" method="post">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Tamu</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="Nama" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>">
                  </div>
                  <div class="mb-3">
                    <label>Alamat</label>
                    <input type="text" name="Alamat" class="form-control" value="<?= htmlspecialchars($row['alamat']) ?>">
                  </div>
                  <div class="mb-3">
                    <label>Telepon</label>
                    <input type="text" name="Telp" class="form-control" value="<?= htmlspecialchars($row['telepon']) ?>">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" name="action" value="update" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      <?php endwhile; ?>
    </tbody>
  </table>

   <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $page-1 ?>">Previous</a>
          </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>