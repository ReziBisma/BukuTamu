<?php
require 'koneksi.php';
require 'cipher.php';
session_start();

$action = $_POST['action'] ?? '';

// ========== TAMBAH DATA ==========
if ($action === 'add') {
    $nama = $_POST['Nama'];
    $lokasi_acara = $_POST['Lokasi'];
    $email = $_POST['Email'];
    $role = $_POST['Role'];

    $id_tamu = 'TAMU' . time();

    $stmt = $conn->prepare("INSERT INTO tamu (id_tamu, nama, lokasi_acara, email, role, kehadiran) VALUES (?, ?, ?, ?, ?, 'Tidak Hadir')");
    $stmt->bind_param("sssss", $id_tamu, $nama, $lokasi_acara, $email, $role);
    $stmt->execute();

    $_SESSION['msg'] = "Data berhasil ditambahkan dengan ID: $id_tamu";
    header("Location: export_import.php");
    exit;
}


// ========== UPDATE DATA ==========
if ($action === 'update') {
    $id = $_POST['id'];
    $nama = $_POST['Nama'];
    $lokasi_acara = $_POST['Lokasi'];
    $email = $_POST['Email'];
    $role = $_POST['Role'];

    $stmt = $conn->prepare("UPDATE tamu SET nama=?, lokasi_acara=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("ssssi", $nama, $lokasi_acara, $email, $role, $id);
    $stmt->execute();

    $_SESSION['msg'] = "Data berhasil diupdate.";
    header("Location: index.php");
    exit;
}


// ========== HAPUS DATA ==========
if ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM tamu WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['msg'] = "Data berhasil dihapus.";
    header("Location: index.php");
    exit;
}


// ========== IMPORT EXCEL / CSV ==========
if ($action === 'import_excel') {
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['msg'] = "Gagal upload file.";
        header("Location: index.php");
        exit;
    }

    $tmpPath = $_FILES['excel_file']['tmp_name'];
    $origName = $_FILES['excel_file']['name'];
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $rows = [];

    if (in_array($ext, ['xlsx', 'xls']) && file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmpPath);
        $spreadsheet = $reader->load($tmpPath);
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($sheet->getRowIterator(1) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = trim((string)$cell->getValue());
            }
            $rows[] = $cells;
        }
    } else {
        if (($handle = fopen($tmpPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $rows[] = array_map("trim", $data);
            }
            fclose($handle);
        } else {
            $_SESSION['msg'] = "Gagal membaca file.";
            header("Location: index.php");
            exit;
        }
    }

    if (count($rows) == 0) {
        $_SESSION['msg'] = "File kosong.";
        header("Location: index.php");
        exit;
    }

    $firstRowLower = array_map('strtolower', $rows[0]);
    $hasHeader = in_array('nama', $firstRowLower) 
              || in_array('lokasi_acara', $firstRowLower)
              || in_array('email', $firstRowLower)
              || in_array('role', $firstRowLower);

    // Query insert sesuai struktur baru
    $stmt = $conn->prepare("INSERT INTO tamu (id_tamu, nama, lokasi_acara, email, role, kehadiran) VALUES (?, ?, ?, ?, ?, 'Tidak Hadir')");
    $countInserted = 0;

    foreach ($rows as $idx => $cols) {
        if ($hasHeader && $idx === 0) continue;

        $nama = $cols[0] ?? '';
        $lokasi_acara = $cols[1] ?? '';
        $email = $cols[2] ?? '';
        $role = $cols[3] ?? 'Reguler';

        if ($nama === '' && $lokasi_acara === '' && $email === '') continue;

        // id tamu unik
        $id_tamu = 'TAMU' . time() . rand(1000, 9999);

        $stmt->bind_param("sssss", $id_tamu, $nama, $lokasi_acara, $email, $role);
        if ($stmt->execute()) $countInserted++;
    }

    $_SESSION['msg'] = "Import selesai. Berhasil menambah $countInserted data.";
    header("Location: index.php");
    exit;
}


// ========== EXPORT CSV ==========
if ($action === 'export_csv') {
    $filterKota = $_POST['filter_kota'] ?? 'semua';
    $filename = "buku_tamu_export_" . date("Y-m-d_H-i-s") . ".csv";

    if ($filterKota !== 'semua') {
        $query = "SELECT * FROM tamu WHERE lokasi_acara = '$filterKota'";
    } else {
        $query = "SELECT * FROM tamu";
    }

    $result = mysqli_query($conn, $query);

    // Hitung total hadir / tidak hadir di hasil filter
    $hadir = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT COUNT(*) as jml FROM tamu WHERE kehadiran='Hadir' " . 
        ($filterKota !== 'semua' ? "AND lokasi_acara='$filterKota'" : "")
    ))['jml'] ?? 0;

    $tidak = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT COUNT(*) as jml FROM tamu WHERE kehadiran='Tidak Hadir' " . 
        ($filterKota !== 'semua' ? "AND lokasi_acara='$filterKota'" : "")
    ))['jml'] ?? 0;

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');

    // Header file
    fputcsv($output, ['Filter Kota:', $filterKota === 'semua' ? 'Semua Kota' : $filterKota]);
    fputcsv($output, ['Total Hadir:', $hadir]);
    fputcsv($output, ['Total Tidak Hadir:', $tidak]);
    fputcsv($output, []); // baris kosong
    fputcsv($output, ['ID Tamu', 'Nama', 'Lokasi Acara', 'Email', 'Role', 'Kehadiran']);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id_tamu'],
            $row['nama'],
            $row['lokasi_acara'],
            $row['email'],
            $row['role'],
            $row['kehadiran']
        ]);
    }

    fclose($output);
    exit;
}
?>
