<?php
require __DIR__ . '/vendor/autoload.php';
require 'koneksi.php';
require 'cipher.php';
session_start();

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$result = mysqli_query($conn, "SELECT * FROM tamu");


// Panggil view manajemen
include 'view_manage.php';

