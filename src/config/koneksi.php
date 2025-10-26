<?php
$host = "localhost";  // Host XAMPP (default: localhost)
$user = "root";       // Username database (default: root)
$pass = "";           // Password database (kosong secara default)
$db   = "buku_tamu";  // Nama database yang Anda buat

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>
