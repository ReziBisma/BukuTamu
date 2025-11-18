<?php
session_start();
require __DIR__ . '/../src/config/koneksi.php';
require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$produk_query = mysqli_query($conn, "SELECT * FROM produk"); // Ubah nama variabel agar tidak konflik
$qrImage = null;
$msg = "";
$id_tamu_generated = ''; // ID TAMU Output QR

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk = $_POST['produk'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $kota = trim($_POST['kota']); //Lokasi

    $p_query = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id_produk'");
    $p = mysqli_fetch_assoc($p_query);
    $role = $p['nama_produk'] ?? 'Reguler';
    $harga = $p['harga'] ?? 0;

    $id_tamu_new = 'TAMU' . time() . rand(100, 999);
    $id_transaksi = 'TRX' . time() . rand(100, 999);

    $stmt = $conn->prepare("INSERT INTO tamu (id_tamu, nama, lokasi_acara, email, role, kehadiran) VALUES (?, ?, ?, ?, ?, 'Tidak Hadir')");
    $stmt->bind_param("sssss", $id_tamu_new, $nama, $kota, $email, $role);
    $stmt->execute();

    // Simpan transaksi
    mysqli_query($conn, "INSERT INTO transaksi (id_transaksi, id_produk, nama_pembeli, email, status_pembayaran) 
                         VALUES ('$id_transaksi', '$id_produk', '$nama', '$email', 'Lunas')");

    // Generate QR Code
    $qr = new QrCode($id_tamu_new);
    $writer = new PngWriter();
    $result = $writer->write($qr);
    $qrImage = base64_encode($result->getString());

    $msg = "Transaksi berhasil dibuat! Berikut QR ID Anda.";

    // Simpan ke session
    $_SESSION['qr_image'] = $qrImage;
    $_SESSION['id_tamu'] = $id_tamu_new;
    $_SESSION['msg'] = $msg;

    header("Location: beli_tiket.php?success=1");
    exit;
}

// Jika redirect success
if (isset($_GET['success']) && isset($_SESSION['qr_image'])) {
    $qrImage = $_SESSION['qr_image'];
    $msg = $_SESSION['msg'] ?? '';
    $id_tamu_generated = $_SESSION['id_tamu'] ?? ''; // Ambil ID Tamu dari session
    // Hapus session setelah digunakan
    unset($_SESSION['qr_image'], $_SESSION['msg'], $_SESSION['id_tamu']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Tiket - Buku Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <?php include __DIR__ . '/../src/views/partials/_header.php'; ?>
    <?php include __DIR__ . '/../src/views/partials/_sidebar.php'; ?>

    <div class="main-content">
        <div class="container mt-4 mb-5">
            <h3 class="fw-bold">Beli Tiket Acara</h3>
            <p>Pilih jenis tiket dan lengkapi data Anda untuk mendapatkan QR Ticket Masuk.</p>

            <?php if ($msg): ?>
                <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <form method="post" class="mb-4 card card-body shadow-sm" id="formBeliTiket">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kota (Lokasi Acara)</label>
                    <input type="text" name="kota" class="form-control" placeholder="Contoh: Jakarta" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Tiket</label>
                    <select name="produk" class="form-select" required>
                        <option value="">-- Pilih Tiket --</option>
                        <?php
                        if (isset($produk_query) && $produk_query) {
                            mysqli_data_seek($produk_query, 0); 
                            while ($p = mysqli_fetch_assoc($produk_query)):
                        ?>
                                <option value="<?= $p['id_produk'] ?>">
                                    <?= htmlspecialchars($p['nama_produk']) ?> - Rp<?= number_format($p['harga'], 0, ',', '.') ?>
                                </option>
                        <?php
                            endwhile;
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success" id="tombolSubmit">
                    <span id="submitText">Buat Transaksi</span>
                    <span id="submitSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                </button>
            </form>

            <?php if ($qrImage): ?>
                <hr>
                <h5>ID Tamu Anda: <span class="text-primary"><?= htmlspecialchars($id_tamu_generated) ?></span></h5>
                <div class="text-center">
                    <img src="data:image/png;base64,<?= $qrImage ?>" class="img-thumbnail" width="200" height="200"
                        style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#qrModal" alt="QR Code">
                    <p class="mt-2">📌 Tap QR Code untuk memperbesar</p>
                    <!-- <p class="mt-2">📌 Tap QR Code untuk memperbesar atau scan langsung dengan HP.</p> -->
                </div>

                <a href="download_qr.php?id=<?= urlencode($id_tamu_generated) ?>" class="btn btn-primary mt-2 d-flex justify-content-center">📥 Download QR</a>

                <!-- <div class="text-center mt-4">
                    <h6>Simulasi Pembayaran:</h6>
                    <img src="assets/qris.jpg" alt="QRIS" style="max-width:250px" class="img-fluid">
                    <p class="text-muted">Bayar melalui QRIS ini (simulasi, tidak perlu benar-benar membayar).</p>
                </div> -->

                <!-- Modal QR  -->
                <div class="modal fade" id="qrModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-body text-center">
                                <img src="data:image/png;base64,<?= $qrImage ?>" class="img-fluid" alt="QR Code Besar">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Prosee loading setelah submit -->
    <script>
        const formBeliTiket = document.getElementById('formBeliTiket');
        const tombolSubmit = document.getElementById('tombolSubmit');
        const submitText = document.getElementById('submitText');
        const submitSpinner = document.getElementById('submitSpinner');

        if (formBeliTiket && tombolSubmit) {
            formBeliTiket.addEventListener('submit', function(e) {
                if (formBeliTiket.checkValidity()) {
                    tombolSubmit.disabled = true;

                    submitText.textContent = 'Memproses...';
                    submitSpinner.style.display = 'inline-block';
                }
            });
        }
    </script>
</body>

</html>