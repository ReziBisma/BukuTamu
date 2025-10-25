<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Buku Tamu - Manajemen</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<div class="container">

  <h3 class="mb-3">Kelola Data Tamu</h3>

  <?php
    // hitung total hadir & tidak hadir
    $totalHadir = 0;
    $totalTidakHadir = 0;

    $resHadir = mysqli_query($conn, "SELECT COUNT(*) as jml FROM tamu WHERE kehadiran='Hadir'");
    if ($resHadir) {
        $rowHadir = mysqli_fetch_assoc($resHadir);
        $totalHadir = $rowHadir['jml'];
    }

    $resTidak = mysqli_query($conn, "SELECT COUNT(*) as jml FROM tamu WHERE kehadiran='Tidak Hadir'");
    if ($resTidak) {
        $rowTidak = mysqli_fetch_assoc($resTidak);
        $totalTidakHadir = $rowTidak['jml'];
    }

    // ambil daftar kota unik dari database
    $kotaQuery = mysqli_query($conn, "SELECT DISTINCT lokasi_acara FROM tamu ORDER BY lokasi_acara ASC");
  ?>

  <?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-info"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
  <?php endif; ?>

  <form action="controll.php" method="post" class="mb-3">
    <div class="row g-2">
      <div class="col-md-3"><input type="text" name="Nama" class="form-control" placeholder="Nama" required></div>
      <div class="col-md-3"><input type="text" name="Lokasi" class="form-control" placeholder="Lokasi Acara (Kota)" required></div>
      <div class="col-md-3"><input type="email" name="Email" class="form-control" placeholder="Email" required></div>
      <div class="col-md-2">
        <select name="Role" class="form-select" required>
          <option value="Reguler">Reguler</option>
          <option value="VIP">VIP</option>
        </select>
      </div>
      <div class="col-md-1">
        <button type="submit" name="action" value="add" class="btn btn-success w-100">Tambah</button>
      </div>
    </div>
  </form>

  <form action="controll.php" method="post" enctype="multipart/form-data" class="mb-3">
    <div class="row g-2">
      <div class="col-md-6">
        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" class="form-control" required>
      </div>
      <div class="col-md-2">
        <button type="submit" name="action" value="import_excel" class="btn btn-primary w-100">Import</button>
      </div>
    </div>
  </form>

 <form action="controll.php" method="post" class="d-flex align-items-center justify-content-start gap-2 mb-3 flex-wrap">
  <label for="filter_kota" class="form-label mb-0 me-2 fw-semibold">Filter Kota:</label>

  <select name="filter_kota" id="filter_kota" class="form-select" style="width: 220px;">
    <option value="semua">-- Semua Kota --</option>
    <?php while ($kota = mysqli_fetch_assoc($kotaQuery)): ?>
      <option value="<?= htmlspecialchars($kota['lokasi_acara']) ?>">
        <?= htmlspecialchars($kota['lokasi_acara']) ?>
      </option>
    <?php endwhile; ?>
  </select>

  <button type="submit" name="action" value="export_csv" class="btn btn-info" style="min-width:150px;">
    Export CSV
  </button>
</form>


  <div class="row mb-3 mt-2">
    <div class="col-md-6">
      <div class="alert alert-success mb-0">
        ✅ Total Hadir: <strong><?= $totalHadir ?></strong>
      </div>
    </div>
    <div class="col-md-6">
      <div class="alert alert-warning mb-0">
        ⏳ Total Tidak Hadir: <strong><?= $totalTidakHadir ?></strong>
      </div>
    </div>
  </div>

  <table class="table table-bordered table-striped mt-3">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Lokasi Acara</th>
        <th>Email</th>
        <th>Role</th>
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
          <td><?= htmlspecialchars($row['lokasi_acara']) ?></td>
          <td><?= htmlspecialchars(encryptCaesar($row['email'], 3)) ?></td>
          <td><?= htmlspecialchars($row['role']) ?></td>
          <td><?= htmlspecialchars($row['kehadiran']) ?></td>
          <td>
            <?php
              if (!empty($row['id_tamu'])) {
                  $qr = new \Endroid\QrCode\QrCode($row['id_tamu']);
                  $writer = new \Endroid\QrCode\Writer\PngWriter();
                  $qrResult = $writer->write($qr);

                  echo '<img src="data:image/png;base64,' . base64_encode($qrResult->getString()) . '" width="80" height="80" /><br>';
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
                  <div class="mb-3"><label>Lokasi Acara</label><input type="text" name="Lokasi" class="form-control" value="<?= htmlspecialchars($row['lokasi_acara']) ?>"></div>
                  <div class="mb-3"><label>Email</label><input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>"></div>
                  <div class="mb-3">
                    <label>Role</label>
                    <select name="Role" class="form-select">
                      <option value="Reguler" <?= ($row['role'] == 'Reguler') ? 'selected' : '' ?>>Reguler</option>
                      <option value="VIP" <?= ($row['role'] == 'VIP') ? 'selected' : '' ?>>VIP</option>
                    </select>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>