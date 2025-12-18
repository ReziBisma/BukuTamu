<?php
session_start();
require '../src/config/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();

$result = $query->get_result();
$admin = $result->fetch_assoc();

if ($admin && password_verify($password, $admin['password'])) {

    $_SESSION['admin_login'] = true;
    $_SESSION['admin_id'] = $admin['id_admin'];
    $_SESSION['admin_nama'] = $admin['nama_admin'];

    header("Location: ../public/index.php");
    exit;
}

header("Location: login.php?error=1");
