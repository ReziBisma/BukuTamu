<?php
require 'koneksi.php';
require 'cipher.php';
session_start();

// --- Pagination setup ---
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tamu");
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data sesuai halaman
$result = mysqli_query($conn, "SELECT * FROM tamu LIMIT $limit OFFSET $offset");

// Include view
include 'view.php';
