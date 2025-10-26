<?php
require __DIR__ . '/../src/config/koneksi.php';
require __DIR__ . '/../src/lib/cipher.php';
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
    header("Location: export_import.php");
    exit;
}


// ========== HAPUS DATA ==========
if ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM tamu WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['msg'] = "Data berhasil dihapus.";
    header("Location: export_import.php");
    exit;
}


// ========== IMPORT EXCEL / CSV ==========
if ($action === 'import_excel') {
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['msg'] = "Gagal upload file.";
        header("Location: export_import.php");
        exit;
    }

    $tmpPath = $_FILES['excel_file']['tmp_name'];
    $origName = $_FILES['excel_file']['name'];
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $rows = [];

    if (in_array($ext, ['xlsx', 'xls']) && file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
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


if ($action === 'export_csv') {
    $filterKota = $_POST['filter_kota'] ?? 'semua';
    $filename = "buku_tamu_export_" . date("Y-m-d_H-i-s") . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'w');

    // Header kolom CSV utama
    fputcsv($output, ['ID Tamu', 'Nama', 'Lokasi Acara', 'Email', 'Role', 'Kehadiran']);

    // Ambil harga tiket dari tabel produk
    $hargaVIP = 0;
    $hargaReg = 0;
    $resHarga = mysqli_query($conn, "SELECT * FROM produk");
    while ($prod = mysqli_fetch_assoc($resHarga)) {
        if (strtolower($prod['nama_produk']) == 'vip') {
            $hargaVIP = (int)$prod['harga'];
        } elseif (strtolower($prod['nama_produk']) == 'reguler') {
            $hargaReg = (int)$prod['harga'];
        }
    }

    if ($filterKota !== 'semua') {
        // FILTER berdasarkan satu kota
        $stmt = $conn->prepare("SELECT id_tamu, nama, lokasi_acara, email, role, kehadiran FROM tamu WHERE lokasi_acara=?");
        $stmt->bind_param("s", $filterKota);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id_tamu'],
                $row['nama'],
                $row['lokasi_acara'],
                $row['email'],
                $row['role'],
                $row['kehadiran']
            ]);
        }

        // Hitung rekap kehadiran + role + total pendapatan kota
        $rekap = mysqli_query($conn, "
            SELECT 
              SUM(CASE WHEN kehadiran='Hadir' THEN 1 ELSE 0 END) AS total_hadir,
              SUM(CASE WHEN kehadiran='Tidak Hadir' THEN 1 ELSE 0 END) AS total_tidak,
              SUM(CASE WHEN role='VIP' THEN 1 ELSE 0 END) AS total_vip,
              SUM(CASE WHEN role='Reguler' THEN 1 ELSE 0 END) AS total_reguler
            FROM tamu WHERE lokasi_acara='$filterKota'
        ");
        $rekapData = mysqli_fetch_assoc($rekap);

        $totalPeserta = $rekapData['total_hadir'] + $rekapData['total_tidak'];
        $totalPendapatan = ($rekapData['total_vip'] * $hargaVIP) + ($rekapData['total_reguler'] * $hargaReg);

        fputcsv($output, []);
        fputcsv($output, ['Rekap Kota', 'Total Peserta', 'Hadir', 'Tidak Hadir', 'VIP', 'Reguler', 'Pendapatan (Rp)']);
        fputcsv($output, [
            $filterKota,
            $totalPeserta,
            $rekapData['total_hadir'],
            $rekapData['total_tidak'],
            $rekapData['total_vip'],
            $rekapData['total_reguler'],
            number_format($totalPendapatan, 0, ',', '.')
        ]);

    } else {
        // SEMUA KOTA
        $result = mysqli_query($conn, "SELECT id_tamu, nama, lokasi_acara, email, role, kehadiran FROM tamu");
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

        fputcsv($output, []);
        fputcsv($output, ['Rekap Per Kota']);
        fputcsv($output, ['Kota', 'Total Hadir', 'Total Tidak Hadir', 'Total Pengunjung', 'VIP', 'Reguler', 'Pendapatan (Rp)']);

        $rekapAll = mysqli_query($conn, "
            SELECT lokasi_acara,
              SUM(CASE WHEN kehadiran='Hadir' THEN 1 ELSE 0 END) AS total_hadir,
              SUM(CASE WHEN kehadiran='Tidak Hadir' THEN 1 ELSE 0 END) AS total_tidak,
              SUM(CASE WHEN role='VIP' THEN 1 ELSE 0 END) AS total_vip,
              SUM(CASE WHEN role='Reguler' THEN 1 ELSE 0 END) AS total_reguler
            FROM tamu
            GROUP BY lokasi_acara
        ");

        $grandTotalPeserta = 0;
        $grandTotalPendapatan = 0;

        while ($rekap = mysqli_fetch_assoc($rekapAll)) {
            $totalPeserta = $rekap['total_hadir'] + $rekap['total_tidak'];
            $pendapatan = ($rekap['total_vip'] * $hargaVIP) + ($rekap['total_reguler'] * $hargaReg);

            $grandTotalPeserta += $totalPeserta;
            $grandTotalPendapatan += $pendapatan;

            fputcsv($output, [
                $rekap['lokasi_acara'],
                $rekap['total_hadir'],
                $rekap['total_tidak'],
                $totalPeserta, // 🔹 kolom baru: total pengunjung
                $rekap['total_vip'],
                $rekap['total_reguler'],
                number_format($pendapatan, 0, ',', '.')
            ]);
        }

        // Tambahkan total keseluruhan
        fputcsv($output, []);
        fputcsv($output, ['Total Keseluruhan', '', '', $grandTotalPeserta, '', '', number_format($grandTotalPendapatan, 0, ',', '.')]);
    }

    fclose($output);
    exit;
}
?>