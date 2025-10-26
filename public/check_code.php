<?php
session_start();
require __DIR__ . '/../src/config/koneksi.php';

if (isset($_POST['code'])) {
    $code = trim($_POST['code']);
    $code = mysqli_real_escape_string($conn, $code);

    $query = mysqli_query($conn, "SELECT * FROM tamu WHERE id_tamu='$code'");

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        // Jika sudah hadir sebelumnya
        if (strtolower($row['kehadiran']) === 'hadir') {
            echo "<div class='alert alert-warning'>
                    ⚠️ Kode sudah pernah di-scan.<br>
                    <strong>" . htmlspecialchars($row['nama']) . "</strong> sudah tercatat hadir sebelumnya.
                  </div>";
        } else {
            // Jika belum hadir → update jadi hadir
            mysqli_query($conn, "UPDATE tamu SET kehadiran='Hadir' WHERE id_tamu='$code'");
            echo "<div class='alert alert-success'>
                    ✅ Selamat datang, <strong>" . htmlspecialchars($row['nama']) . "</strong>!<br>
                    ID Anda: " . htmlspecialchars($row['id_tamu']) . "<br>
                    Status Kehadiran: <strong>Hadir</strong>
                  </div>";
        }
    } else {
        echo "<div class='alert alert-danger'>❌ ID tidak ditemukan di database!</div>";
    }
}
?>