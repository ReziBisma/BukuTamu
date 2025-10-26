<div class="container my-4">

  <div class="mb-4">
    <h3 class="fw-bold">Dashboard</h3>
    <p class="text-muted">Menampilkan data tamu yang telah terdaftar dan status kehadirannya.</p>

    <!-- <a href="beli_tiket.php" class="btn btn-ticket mt-2">
      🎟️ Beli Tiket Sekarang
    </a> -->
  </div>

  <div class="row mb-4 text-center">
    <div class="col-md-3 mb-2">
      <div class="card card-stat bg-success text-white">
        <div class="card-body">
          <i class="bi bi-person-check display-6"></i>
          <h5><?= $total_hadir ?> Hadir</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="card card-stat bg-danger text-white">
        <div class="card-body">
          <i class="bi bi-person-x display-6"></i>
          <h5><?= $total_tidak ?> Tidak Hadir</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="card card-stat bg-primary text-white">
        <div class="card-body">
          <i class="bi bi-star-fill display-6"></i>
          <h5><?= $total_vip ?> VIP</h5>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="card card-stat bg-secondary text-white">
        <div class="card-body">
          <i class="bi bi-people-fill display-6"></i>
          <h5><?= $total_reg ?> Reguler</h5>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Tamu (<?= $total_data ?> total)</h5>
      <a href="export_import.php" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-box-arrow-up-right"></i> Kelola Data
      </a>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Kota</th>
            <th>Email</th>
            <th>Role</th>
            <th>Kehadiran</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['lokasi_acara']) ?></td>
                <td><?= htmlspecialchars(encryptCaesar($row['email'], 3)) ?></td>
                <td>
                  <span class="badge p-2 bg-<?= $row['role']=='VIP'?'primary':'secondary' ?>">
                    <?= $row['role'] ?>
                  </span>
                </td>
                <td>
                  <?php if ($row['kehadiran'] == 'Hadir'): ?>
                    <span class="badge bg-success p-2">Hadir</span>
                  <?php else: ?>
                    <span class="badge bg-danger p-2">Tidak Hadir</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted">Tidak ada data tamu.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <nav class="mt-3">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>"><b><<</b></a>
      </li>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>"><b>>></b></a>
      </li>
    </ul>
  </nav>

</div>