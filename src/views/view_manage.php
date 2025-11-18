<?php
if (!isset($kotaQuery)) {
  $kotaQuery = mysqli_query($conn, "SELECT DISTINCT lokasi_acara FROM tamu ORDER BY lokasi_acara ASC");
}
?>

<div class="container">
  <h3 class="mb-3 fw-bold pt-4">Kelola Data Tamu</h3>

  <?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <?= $_SESSION['msg'];
      unset($_SESSION['msg']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Form tambah -->
  <div class="card card-body shadow-sm mb-4 ">
    <h5 class="card-title mb-3">Tambah Tamu Baru</h5>
    <form action="controll.php" method="post">
      <div class="row g-3">
        <div class="col-md-6 col-lg-3"><input type="text" name="Nama" class="form-control" placeholder="Nama" required></div>
        <div class="col-md-6 col-lg-3"><input type="text" name="Lokasi" class="form-control" placeholder="Lokasi Acara (Kota)" required></div>
        <div class="col-md-6 col-lg-3"><input type="email" name="Email" class="form-control" placeholder="Email" required></div>
        <div class="col-md-6 col-lg-2">
          <select name="Role" class="form-select" required>
            <option value="Reguler" selected>Reguler</option>
            <option value="VIP">VIP</option>
          </select>
        </div>
        <div class="col-lg-1 d-grid">
          <button type="submit" name="action" value="add" class="btn btn-success">Tambah</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Import/Export -->
  <div class="card card-body shadow-sm mb-4">
    <h5 class="card-title mb-3">Import / Export Data</h5>
    <div class="row">
      <!-- Import -->
      <div class="col">
        <small class="text-muted">Format file: .xlsx, .xls, .csv</small>

        <form action="controll.php" method="post" enctype="multipart/form-data" class="d-flex gap-2">
          <div class="input-group">

            <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" class="form-control" required>
            <button type="submit" name="action" value="import_excel" class="btn btn-primary text-nowrap">
              <i class="bi bi-file-earmark-arrow-up me-1"></i> Import
          </div>
          </button>
        </form>
      </div>
      <!-- Export -->
      <div class="col">
        <small class="text-muted">Filter kota/lokasi sebelum di export</small>
        <form action="controll.php" method="post" class="d-flex flex-wrap gap-2 align-items-center">
          <div class="input-group">
            <label for="filter_kota" class="input-group-text">Filter Kota:</label>
            <select name="filter_kota" id="filter_kota" class="form-select flex-grow-1" style="min-width: 150px;">
              <option value="semua">-- Semua Kota --</option>
              <?php mysqli_data_seek($kotaQuery, 0);
              while ($kota = mysqli_fetch_assoc($kotaQuery)): ?>
                <option value="<?= htmlspecialchars($kota['lokasi_acara']) ?>">
                  <?= htmlspecialchars($kota['lokasi_acara']) ?>
                </option>
              <?php endwhile; ?>
            </select>
            <button type="submit" name="action" value="export_csv" class="btn btn-info text-nowrap">
              <i class="bi bi-file-earmark-arrow-down me-1"></i> Export CSV
            </button>
          </div>
        </form>
      </div>
      <div class="row">
        <div class="col"></div>
        <div class="col"></div>
      </div>
    </div>
  </div>

  <!-- Statistik Kehadiran -->
  <div class="row mb-3">
    <div class="col-md-6">
      <div class="alert alert-success d-flex align-items-center mb-0">
        <i class="bi bi-check-circle-fill me-2 fs-4"></i>
        Total Hadir: <strong><?= $totalHadir ?? 0 ?></strong>
      </div>
    </div>
    <div class="col-md-6">
      <div class="alert alert-danger d-flex align-items-center mb-0">
        <i class="bi bi-clock-history me-2 fs-4"></i>
        Total Tidak Hadir: <strong><?= $totalTidakHadir ?? 0 ?></strong>
      </div>
    </div>
  </div>

  <!-- Tabel Data Tamu -->
  <div class="card shadow-sm">
    <div class="card-header">
      <h5 class="mb-0">Daftar Semua Tamu</h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>ID Tamu</th>
            <th>Nama</th>
            <th>Lokasi</th>
            <th>Email (Encrypted)</th>
            <th>Role</th>
            <th>Kehadiran</th>
            <th>QR Code</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($result) && mysqli_num_rows($result) > 0):
            mysqli_data_seek($result, 0);
            while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td class="text-center"><small><?= htmlspecialchars($row['id_tamu']) ?></small></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['lokasi_acara']) ?></td>
                <td><small><?= htmlspecialchars(encryptCaesar($row['email'], 3)) ?></small></td>
                <td class="text-center">
                  <span class="badge p-2 bg-<?= $row['role'] == 'VIP' ? 'primary' : 'secondary' ?>">
                    <?= htmlspecialchars($row['role']) ?>
                  </span>
                </td>
                <td class="text-center">
                  <?php if ($row['kehadiran'] == 'Hadir'): ?>
                    <span class="badge p-2 bg-success">Hadir</span>
                  <?php else: ?>
                    <span class="badge p-2 bg-danger">Tidak Hadir</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <?php
                  if (!empty($row['id_tamu'])) {

                    if (class_exists('\Endroid\QrCode\QrCode') && class_exists('\Endroid\QrCode\Writer\PngWriter')) {
                      try {
                        $qr = new \Endroid\QrCode\QrCode($row['id_tamu']);
                        $writer = new \Endroid\QrCode\Writer\PngWriter();
                        $qrResult = $writer->write($qr);
                        echo '<img src="' . $qrResult->getDataUri() . '" width="60" height="60" alt="QR Code" loading="lazy" /><br>';
                        echo '<a href="download_qr.php?id=' . urlencode($row['id_tamu']) . '" class="btn btn-sm btn-outline-primary mt-1 px-1 py-0"><i class="bi bi-download"></i></a>';
                      } catch (Exception $e) {
                        echo '<small class="text-danger">Error QR</small>';
                      }
                    } else {
                      echo '<small class="text-muted">Lib QR?</small>';
                    }
                  } else {
                    echo '-';
                  }
                  ?>
                </td>
                <td class="text-center">
                  <button class="btn btn-warning btn-sm px-1 py-0" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <form action="controll.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" name="action" value="delete" class="btn  btn-danger btn-sm px-1 py-0">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>

              <!-- Modal Edit -->
              <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="controll.php" method="post">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Tamu: <?= htmlspecialchars($row['nama']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <div class="mb-3">
                          <label for="editNama<?= $row['id'] ?>" class="form-label">Nama</label>
                          <input id="editNama<?= $row['id'] ?>" type="text" name="Nama" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="editLokasi<?= $row['id'] ?>" class="form-label">Lokasi Acara</label>
                          <input id="editLokasi<?= $row['id'] ?>" type="text" name="Lokasi" class="form-control" value="<?= htmlspecialchars($row['lokasi_acara']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="editEmail<?= $row['id'] ?>" class="form-label">Email</label>
                          <input id="editEmail<?= $row['id'] ?>" type="email" name="Email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label for="editRole<?= $row['id'] ?>" class="form-label">Role</label>
                          <select id="editRole<?= $row['id'] ?>" name="Role" class="form-select" required>
                            <option value="Reguler" <?= ($row['role'] == 'Reguler') ? 'selected' : '' ?>>Reguler</option>
                            <option value="VIP" <?= ($row['role'] == 'VIP') ? 'selected' : '' ?>>VIP</option>
                          </select>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="action" value="update" class="btn btn-primary">Simpan Perubahan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center text-muted py-3">Belum ada data tamu yang ditambahkan.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div> <!-- Akhir card-body -->
  </div> <!-- Akhir card -->

</div> <!-- Akhir .container -->