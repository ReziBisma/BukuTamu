<?php
require __DIR__ . '/../src/middleware/auth_admin.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/config/koneksi.php';
require __DIR__ . '/../src/lib/cipher.php'; // diperlukan oleh view_manage

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Query ini mungkin diperlukan oleh view_manage.php
$result = mysqli_query($conn, "SELECT * FROM tamu ORDER BY id DESC"); // Ambil semua data tamu atau sesuaikan

// Statistik mungkin juga diperlukan
$totalHadir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tamu WHERE kehadiran='Hadir'"))['jml'] ?? 0;
$totalTidakHadir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tamu WHERE kehadiran!='Hadir'"))['jml'] ?? 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import / Export - Buku Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../src/views/partials/_header.php'; ?>
    <?php include __DIR__ . '/../src/views/partials/_sidebar.php'; ?>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <div class="main-content">
        <?php include __DIR__ . '/../src/views/view_manage.php'; // Include konten manajemen ?>
    </div> <!-- Akhir Main Content -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
