<?php
require 'koneksi.php';
require 'cipher.php';
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

include 'view_tabel.php';

