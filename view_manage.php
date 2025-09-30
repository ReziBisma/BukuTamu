<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Buku Tamu - Manajemen</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
  <h3 class="mb-3">Kelola Data Tamu</h3>

  <?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-info"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
  <?php endif; ?>

  <!-- Form tambah -->
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

  <!-- Import Excel/CSV -->
  <form action="controll.php" method="post" enctype="multipart/form-data" class="mb-3">
    <div class="row g-2">
      <div class="col-md-6"><input type="file" name="excel_file" accept=".xlsx,.xls,.csv" class="form-control" required></div>
      <div class="col-md-2"><button type="submit" name="action" value="import_excel" class="btn btn-primary w-100">Import</button></div>
    </div>
  </form>

  <!-- Export -->
  <form action="controll.php" method="post" style="display:inline-block; margin-bottom: 15px;">
    <button type="submit" name="action" value="export_csv" class="btn btn-info">Export CSV</button>
  </form>

  <!-- Tabel -->
  <table class="table table-bordered table-striped mt-3">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Telepon</th>
        <th>Kehadiran</th>
        <th>QR Code</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $row['id_tamu'] ?></td>
          <td><?= htmlspecialchars($row['nama']) ?></td>
          <td><?= htmlspecialchars(encryptCaesar($row['alamat'], 3)) ?></td>
          <td><?= htmlspecialchars(encryptCaesar($row['telepon'], 3)) ?></td>
          <td><?= $row['kehadiran']?></td>
          <td>
            <?php
              if (!empty($row['id_tamu'])) {
                  $qr = new \Endroid\QrCode\QrCode($row['id_tamu']);
                  $writer = new \Endroid\QrCode\Writer\PngWriter();
                  $qrResult = $writer->write($qr);

                  echo '<img src="data:image/png;base64,' . base64_encode($qrResult->getString()) . '" width="80" height="80" /><br>';

                  // tombol download
                  echo '<a href="download_qr.php?id=' . urlencode($row['id_tamu']) . '" class="btn btn-sm btn-primary mt-1">Download</a>';
              } else {
                  echo 'Data QR tidak tersedia';
              }
            ?>
          </td>
          <td>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
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
                  <div class="mb-3"><label>Nama</label><input type="text" name="Nama" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>"></div>
                  <div class="mb-3"><label>Alamat</label><input type="text" name="Alamat" class="form-control" value="<?= htmlspecialchars($row['alamat']) ?>"></div>
                  <div class="mb-3"><label>Telepon</label><input type="text" name="Telp" class="form-control" value="<?= htmlspecialchars($row['telepon']) ?>"></div>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
