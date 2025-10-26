<?php
session_start();
require __DIR__ . '/../src/config/koneksi.php';
require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Ambil data produk (VIP / Reguler)
$produk_query = mysqli_query($conn, "SELECT * FROM produk"); // Ubah nama variabel agar tidak konflik
$qrImage = null;
$msg = "";
$id_tamu_generated = ''; // Untuk menyimpan ID Tamu yang baru dibuat

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk = $_POST['produk'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $kota = trim($_POST['kota']); // nama kota

    // Ambil data produk
    $p_query = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id_produk'");
    $p = mysqli_fetch_assoc($p_query);
    $role = $p['nama_produk'] ?? 'Reguler';
    $harga = $p['harga'] ?? 0;

    // Buat ID tamu dan ID transaksi unik
    $id_tamu_new = 'TAMU' . time() . rand(100, 999);
    $id_transaksi = 'TRX' . time() . rand(100, 999);

    // Simpan ke tabel tamu
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
        <div class="container mt-4">
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
                        // Pastikan $produk_query ada dan valid
                        if (isset($produk_query) && $produk_query) {
                            mysqli_data_seek($produk_query, 0); // Reset pointer
                            while ($p = mysqli_fetch_assoc($produk_query)):
                        ?>
                                <option value="<?= $p['id_produk'] ?>">
                                    <?= htmlspecialchars($p['nama_produk']) ?> - Rp<?= number_format($p['harga'], 0, ',', '.') ?>
                                </option>
                        <?php
                            endwhile;
                        } // Akhir if
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success" id="tombolSubmit">
                    <span id="submitText">Buat Transaksi</span>
                    <span id="submitSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                </button>
            </form>

            <?php if ($qrImage): ?>
            <?php endif; ?>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Ambil elemen form dan tombol
        const formBeliTiket = document.getElementById('formBeliTiket');
        const tombolSubmit = document.getElementById('tombolSubmit');
        const submitText = document.getElementById('submitText');
        const submitSpinner = document.getElementById('submitSpinner');

        if (formBeliTiket && tombolSubmit) {
            // Tambahkan event listener saat form di-submit
            formBeliTiket.addEventListener('submit', function(e) {
                // Cek apakah form valid (jika menggunakan validasi HTML5)
                if (formBeliTiket.checkValidity()) {
                    // Nonaktifkan tombol
                    tombolSubmit.disabled = true;

                    // Ubah teks tombol dan tampilkan spinner
                    submitText.textContent = 'Memproses...';
                    submitSpinner.style.display = 'inline-block';
                }
                // Jika form tidak valid, browser akan otomatis menghentikan submit
                // dan tombol tidak akan dinonaktifkan, sehingga pengguna bisa memperbaiki input
            });
        }
    </script>
</body>

</html>