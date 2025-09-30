<?php
session_start();
require 'koneksi.php';

if (isset($_POST['code'])) {
    $code = trim($_POST['code']);
    $code = mysqli_real_escape_string($conn, $code);

    $query = mysqli_query($conn, "SELECT * FROM tamu WHERE id_tamu='$code'");
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        // update kehadiran jadi "Hadir"
        mysqli_query($conn, "UPDATE tamu SET kehadiran='Hadir' WHERE id_tamu='$code'");

        echo "<div class='alert alert-success'>
                ✅ Selamat datang, <strong>" . htmlspecialchars($row['nama']) . "</strong><br>
                ID Anda: " . htmlspecialchars($row['id_tamu']) . "<br>
                Status Kehadiran: Hadir
              </div>";
    } else {
        echo "<div class='alert alert-danger'>❌ ID tidak ditemukan!</div>";
    }
}
?>
