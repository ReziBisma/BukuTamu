<?php
session_start();
require __DIR__ . '/../src/config/koneksi.php';
require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

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

    $config = Configuration::getDefaultConfiguration()->setApiKey(
    'api-key',
    'apikey kalian'
);

$apiInstance = new TransactionalEmailsApi(
    new GuzzleHttp\Client(),
    $config
);

$emailData = new SendSmtpEmail([
    'sender' => [
        'name' => 'Panitia Lawakfest',
        'email' => 'lawaktick@gmail.com'
    ],
    'to' => [
        ['email' => $email, 'name' => $nama]
    ],
    'subject' => "Tiket Lawakfest – ID Tamu: $id_tamu_new",
    'htmlContent' => "
        <p>Halo <strong>$nama</strong>,</p>
        <p>Terima kasih telah membeli tiket <strong>$role</strong> Lawakfest!</p>
        <p>
            Berikut detail tiket Anda:<br>
            ID Tamu: <strong>$id_tamu_new</strong><br>
            Email: $email<br>
            Kota Acara: $kota<br>
            Harga: Rp " . number_format($harga, 0, ',', '.') . "
        </p>
        <p>QR tiket Anda terlampir di email ini.</p>
        <p>Salam,<br><strong>Panitia Lawakfest</strong></p>
    ",
    'attachment' => [
        [
            'content' => base64_encode($result->getString()),
            'name' => "QR_Ticket_$id_tamu_new.png"
        ]
    ]
]);

try {
    $response = $apiInstance->sendTransacEmail($emailData);
    error_log("Email terkirim: " . print_r($response, true));
} catch (Exception $e) {
    error_log("Gagal kirim email: " . $e->getMessage());
}

    $msg = "Transaksi berhasil dibuat! silahkan cek Email, atau download QR langsung.";

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
        <?php include __DIR__ . '/../src/views/view_beli.php'; // Include konten manajemen ?>
    </div> <!-- Akhir Main Content -->

    
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