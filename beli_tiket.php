<?php
session_start();
require 'koneksi.php';
require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Ambil data produk (VIP / Reguler)
$produk = mysqli_query($conn, "SELECT * FROM produk");
$qrImage = null;
$msg = "";

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk = $_POST['produk'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $kota = trim($_POST['kota']); // nama kota

    // Ambil data produk
    $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id_produk'"));
    $role = $p['nama_produk'] ?? 'Reguler';
    $harga = $p['harga'] ?? 0;

    // Buat ID tamu dan ID transaksi unik
    $id_tamu = 'TAMU' . time() . rand(100, 999);
    $id_transaksi = 'TRX' . time() . rand(100, 999);

    // Simpan ke tabel tamu
    $stmt = $conn->prepare("INSERT INTO tamu (id_tamu, nama, lokasi_acara, email, role, kehadiran) VALUES (?, ?, ?, ?, ?, 'Tidak Hadir')");
    $stmt->bind_param("sssss", $id_tamu, $nama, $kota, $email, $role);
    $stmt->execute();

    // Simpan transaksi (jika tabel transaksi tersedia)
    mysqli_query($conn, "INSERT INTO transaksi (id_transaksi, id_produk, nama_pembeli, email, status_pembayaran) 
                         VALUES ('$id_transaksi', '$id_produk', '$nama', '$email', 'Lunas')");

    // Generate QR Code
    $qr = new QrCode($id_tamu);
    $writer = new PngWriter();
    $result = $writer->write($qr);
    $qrImage = base64_encode($result->getString());

    $msg = "Transaksi berhasil dibuat! Berikut QR ID Anda.";

    // Simpan ke session supaya tidak regenerate di refresh
    $_SESSION['qr_image'] = $qrImage;
    $_SESSION['id_tamu'] = $id_tamu;
    $_SESSION['msg'] = $msg;

    // Redirect ke halaman yang sama agar mencegah resubmission & bisa hapus QR di refresh
    header("Location: beli_tiket.php?success=1");
    exit;
}

// Saat pertama kali halaman dibuka (bukan POST)
if (isset($_GET['success']) && isset($_SESSION['qr_image'])) {
    $qrImage = $_SESSION['qr_image'];
    $msg = $_SESSION['msg'] ?? '';
    $id_tamu = $_SESSION['id_tamu'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Beli Tiket - Buku Tamu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
  <h3>Beli Tiket Acara</h3>
  <p>Pilih jenis tiket dan lengkapi data Anda untuk mendapatkan QR Ticket Masuk.</p>

  <?php if ($msg): ?>
    <div class="alert alert-success"><?= $msg ?></div>
  <?php endif; ?>

  <form method="post" class="mb-4">
    <div class="mb-3">
      <label>Nama</label>
      <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Kota (Lokasi Acara)</label>
      <input type="text" name="kota" class="form-control" placeholder="Contoh: Jakarta" required>
    </div>
    <div class="mb-3">
      <label>Pilih Tiket</label>
      <select name="produk" class="form-select" required>
        <option value="">-- Pilih Tiket --</option>
        <?php while ($p = mysqli_fetch_assoc($produk)): ?>
          <option value="<?= $p['id_produk'] ?>">
            <?= $p['nama_produk'] ?> - Rp<?= number_format($p['harga'], 0, ',', '.') ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Buat Transaksi</button>
  </form>

  <?php if ($qrImage): ?>
    <hr>
    <h5>ID Tamu Anda: <span class="text-primary"><?= htmlspecialchars($id_tamu) ?></span></h5>
    <div class="text-center">
      <img src="data:image/png;base64,<?= $qrImage ?>" class="img-thumbnail" width="200" height="200"
           style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#qrModal" alt="QR Code">
      <p class="mt-2">📌 Tap QR Code untuk memperbesar atau scan langsung dengan HP.</p>
    </div>

    <a href="download_qr.php?id=<?= urlencode($id_tamu) ?>" class="btn btn-primary mt-2">📥 Download QR</a>


    <!-- Modal QR besar -->
    <div class="modal fade" id="qrModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
          <div class="modal-body text-center">
            <img src="data:image/png;base64,<?= $qrImage ?>" class="img-fluid" alt="QR Code Besar">
          </div>
        </div>
      </div>
    </div>

    <?php 
      // Hapus data QR dari session supaya hilang saat refresh
      unset($_SESSION['qr_image'], $_SESSION['msg'], $_SESSION['id_tamu']); 
    ?>
  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
