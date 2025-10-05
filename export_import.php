<?php
require __DIR__ . '/vendor/autoload.php';
require 'koneksi.php';
require 'cipher.php';
session_start();

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$result = mysqli_query($conn, "SELECT * FROM tamu");

// hitung hadir dan tidak hadir
$totalHadir = 0;
$totalTidakHadir = 0;

$res = mysqli_query($conn, "SELECT kehadiran, COUNT(*) AS jml FROM tamu GROUP BY kehadiran");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        if (strtolower($r['kehadiran']) === 'hadir') {
            $totalHadir = (int) $r['jml'];
        } else {
            $totalTidakHadir = (int) $r['jml'];
        }
    }
}


// Panggil view manajemen
include 'view_manage.php';

