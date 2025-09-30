<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Buku Tamu - Tabel</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'header.php'; ?>


<div class="container">
  <h3 class="mb-3">Daftar Tamu</h3>

  <table class="table table-bordered table-striped mt-3">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Telepon</th>
        <th>Kehadiran</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= htmlspecialchars($row['nama']) ?></td>
          <td><?= htmlspecialchars(encryptCaesar($row['alamat'], 3)) ?></td>
          <td><?= htmlspecialchars(encryptCaesar($row['telepon'], 3)) ?></td>
          <td><?= $row['kehadiran']?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>">Previous</a></li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>">Next</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
