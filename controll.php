<?php
require 'koneksi.php';
require 'cipher.php';
session_start();

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $nama = $_POST['Nama'];
    $alamat = $_POST['Alamat'];
    $telepon = $_POST['Telp'];

    $id_tamu = 'TAMU' . time();

    $stmt = $conn->prepare("INSERT INTO tamu (id_tamu, nama, alamat, telepon, kehadiran) VALUES (?, ?, ?, ?, 'Tidak Hadir')");
    $stmt->bind_param("ssss", $id_tamu, $nama, $alamat, $telepon);
    $stmt->execute();

    $_SESSION['msg'] = "Data berhasil ditambahkan dengan ID: $id_tamu";
    header("Location: export_import.php");
    exit;
}


if ($action === 'update') {
    $id = $_POST['id'];
    $nama = $_POST['Nama'];
    $alamat = $_POST['Alamat'];
    $telepon = $_POST['Telp'];

    if ($telepon !== '' && !ctype_digit($telepon)) {
        $_SESSION['msg'] = "Nomor telepon hanya boleh berisi angka.";
    } else {
        $stmt = $conn->prepare("UPDATE tamu SET nama=?, alamat=?, telepon=? WHERE id=?");
        $stmt->bind_param("sssi", $nama, $alamat, $telepon, $id);
        $stmt->execute();
        $_SESSION['msg'] = "Data berhasil diupdate.";
    }
    header("Location: index.php");
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM tamu WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['msg'] = "Data berhasil dihapus.";
    header("Location: index.php");
    exit;
}

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
    $hasHeader = in_array('nama', $firstRowLower) || in_array('alamat', $firstRowLower) || in_array('telepon', $firstRowLower);

    // sekarang tambahkan kolom kehadiran default 'Tidak Hadir'
    $stmt = $conn->prepare("INSERT INTO tamu (id_tamu, nama, alamat, telepon, kehadiran) VALUES (?, ?, ?, ?, 'Tidak Hadir')");
    $countInserted = 0;

    foreach ($rows as $idx => $cols) {
        if ($hasHeader && $idx === 0) continue;

        $nama = $cols[0] ?? '';
        $alamat = $cols[1] ?? '';
        $telepon = $cols[2] ?? '';

        if ($nama === '' && $alamat === '' && $telepon === '') continue;

        if ($telepon !== '' && !ctype_digit(preg_replace('/\D+/', '', $telepon))) {
            continue;
        }
        $telepon = preg_replace('/\D+/', '', $telepon);

        // Buat id_tamu unik seperti di add
        $id_tamu = 'TAMU' . time() . rand(1000, 9999);

        $stmt->bind_param("ssss", $id_tamu, $nama, $alamat, $telepon);
        if ($stmt->execute()) $countInserted++;
    }

    $_SESSION['msg'] = "Import selesai. Berhasil menambah $countInserted data.";
    header("Location: index.php");
    exit;
}


if ($action === 'export_csv') {
    $filename = "buku_tamu_export_" . date("Y-m-d_H-i-s") . ".csv";

    // Query semua data, sekarang termasuk kehadiran
    $result = mysqli_query($conn, "SELECT id_tamu, nama, alamat, telepon, kehadiran FROM tamu");

    // Set header agar browser download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    // Output buffer ke "php://output"
    $output = fopen('php://output', 'w');

    // Tulis header kolom
    fputcsv($output, ['ID Tamu', 'Nama', 'Alamat', 'Telepon', 'Kehadiran']);

    // Loop isi database
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id_tamu'],
            $row['nama'],
            $row['alamat'],
            $row['telepon'],
            $row['kehadiran']
        ]);
    }

    fclose($output);
    exit;
}
