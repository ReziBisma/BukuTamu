<?php
require __DIR__ . '/../src/config/koneksi.php';
require __DIR__ . '/../src/lib/cipher.php'; // diperlukan oleh view_tabel
session_start();

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total data & statistik
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tamu");
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $limit);

$total_hadir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tamu WHERE kehadiran='Hadir'"))['jml'];
$total_tidak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tamu WHERE kehadiran='Tidak Hadir'"))['jml'];
$total_vip = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tamu WHERE role='VIP'"))['jml'];
$total_reg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM tamu WHERE role='Reguler'"))['jml'];

// Ambil data sesuai halaman
$result = mysqli_query($conn, "SELECT * FROM tamu ORDER BY id DESC LIMIT $limit OFFSET $offset");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Buku Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- Load CSS -->
</head>
<body>
    <?php include __DIR__ . '/../src/views/partials/_header.php'; ?>
    <?php include __DIR__ . '/../src/views/partials/_sidebar.php'; ?>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <div class="main-content">
        <?php include __DIR__ . '/../src/views/view_tabel.php'; // Include konten tabel ?>
    </div> <!-- Akhir Main Content -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
