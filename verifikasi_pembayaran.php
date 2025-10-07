<?php
require 'koneksi.php';
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data transaksi
    $trx = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id_transaksi='$id'"));
    if ($trx) {
        // Dapatkan produk untuk tahu role
        $produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id_produk={$trx['id_produk']}"));
        $role = $produk['nama_produk'];

        // Setelah verifikasi → buat data tamu
        $id_tamu = 'TAMU' . time() . rand(1000, 9999);
        $lokasi = 'Lokasi Acara Besar'; // bisa ditentukan tetap

        $stmt = $conn->prepare("INSERT INTO tamu (id_tamu, nama, lokasi_acara, email, role, kehadiran) VALUES (?, ?, ?, ?, ?, 'Tidak Hadir')");
        $stmt->bind_param("sssss", $id_tamu, $trx['nama_pembeli'], $lokasi, $trx['email'], $role);
        $stmt->execute();

        mysqli_query($conn, "UPDATE transaksi SET status_pembayaran='Lunas' WHERE id_transaksi='$id'");
        $_SESSION['msg'] = "Transaksi diverifikasi dan tamu ditambahkan.";
    }
}

header("Location: daftar_transaksi.php");
exit;
