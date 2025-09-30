<?php
require 'koneksi.php';
require 'cipher.php';
session_start();

// Ambil semua data
$result = mysqli_query($conn, "SELECT * FROM tamu");

// Panggil view manajemen
include 'view_manage.php';

