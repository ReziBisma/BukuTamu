<?php
session_start();
require 'koneksi.php';

if(isset($_POST['code'])){
    $code = trim($_POST['code']); // hapus spasi / newline
    $code = mysqli_real_escape_string($conn, $code); // aman dari karakter khusus

    $query = mysqli_query($conn, "SELECT * FROM tamu WHERE id_tamu='$code'");
    if(mysqli_num_rows($query) > 0){
        $row = mysqli_fetch_assoc($query);
        echo "<div class='alert alert-success'>Selamat datang, " . htmlspecialchars($row['nama']) . "!</div>";
    } else {
        echo "<div class='alert alert-danger'>ID tidak ditemukan!</div>";
    }
}
?>
