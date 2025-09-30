<?php
session_start();
require 'koneksi.php';
require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$qrImage = null;
$msg = "";

// Jika submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');

    if ($nama !== "" && $alamat !== "") {
        $id_tamu = 'TAMU' . time() . rand(1000, 9999);

        $stmt = $conn->prepare("INSERT INTO tamu (id_tamu, nama, alamat, telepon, kehadiran) VALUES (?, ?, ?, ?, 'Tidak Hadir')");
        $stmt->bind_param("ssss", $id_tamu, $nama, $alamat, $telepon);

        if ($stmt->execute()) {
            $msg = "Pendaftaran berhasil! Simpan QR Code berikut sebagai tanda masuk.";

            // simpan id_tamu di session
            $_SESSION['last_id_tamu'] = $id_tamu;
        } else {
            $msg = "Gagal menyimpan data.";
        }
    } else {
        $msg = "Nama dan Alamat wajib diisi!";
    }
}

// Jika ada session id_tamu → generate QR ulang
if (isset($_SESSION['last_id_tamu'])) {
    $qr = new QrCode($_SESSION['last_id_tamu']);
    $writer = new PngWriter();
    $result = $writer->write($qr);
    $qrImage = base64_encode($result->getString());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Registrasi Tamu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
  <h3>Registrasi Tamu Baru</h3>
  <p>Silakan isi form di bawah ini untuk mendapatkan QR Code Anda.</p>

  <?php if ($msg): ?>
    <div class="alert alert-info"><?= $msg ?></div>
  <?php endif; ?>

  <form method="post" class="mb-4">
    <div class="mb-3">
      <label>Nama</label>
      <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Alamat</label>
      <input type="text" name="alamat" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Telepon</label>
      <input type="text" name="telepon" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Daftar</button>
  </form>

  <?php if ($qrImage): ?>
    <h5>QR Code Anda:</h5>
    <img src="data:image/png;base64,<?= $qrImage ?>"
        width="200" height="200"
        alt="QR Code"
        class="img-thumbnail"
        style="cursor:pointer"
        data-bs-toggle="modal"
        data-bs-target="#qrModal">

    <p class="mt-2">📌 Tap QR Code untuk memperbesar atau scan langsung dengan HP.</p>

    <!-- Tombol download -->
    <a href="download_qr.php?id=<?= urlencode($_SESSION['last_id_tamu']) ?>"
        class="btn btn-primary mt-2" download>
        📥 Download QR Code
    </a>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="qrModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body text-center">
            <img src="data:image/png;base64,<?= $qrImage ?>" class="img-fluid" alt="QR Code Besar">
            </div>
        </div>
        </div>
    </div>

    <?php unset($_SESSION['last_id_tamu']); // QR hilang setelah refresh ?>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
