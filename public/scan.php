<?php
session_start();
?>
<?php include __DIR__ . '/../src/views/partials/_header.php'; ?>
<?php include __DIR__ . '/../src/views/partials/_sidebar.php'; ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR / Barcode - Buku Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- Load CSS -->
    <!-- Library QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body>
    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <div class="main-content">
        <div class="container mt-4"> <!-- Container di dalam main content -->
            <h3 class="fw-bold">Scan QR / Barcode Tamu</h3>
            <p>Arahkan kamera ke QR Code tamu. Pemindai akan aktif kembali setelah beberapa detik.</p>

            <div class="card card-body shadow-sm mx-auto" style="max-width: 400px;">
                <div id="reader" style="width: 100%;"></div>
                <div id="scan-result" class="mt-3">
                    <!-- Pesan awal -->
                    <div class="alert alert-info">Arahkan kamera ke QR Code...</div>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Dashboard</a>
            </div>

        </div> <!-- Akhir .container -->
    </div> <!-- Akhir Main Content -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variabel global untuk instance scanner
        let html5QrcodeScanner;
        // Konstanta untuk delay restart (dalam milidetik)
        const restartDelay = 3000; // 3 detik

        // Fungsi untuk mengirim kode ke server
        function sendCodeToServer(scannedCode) {
            fetch('check_code.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'code=' + encodeURIComponent(scannedCode)
                })
                .then(response => response.text())
                .then(data => {
                    // Tampilkan hasil dari PHP
                    document.getElementById('scan-result').innerHTML = data;

                    // Jadwalkan restart scanner setelah delay
                    console.log(`Scan berhasil. Merestart scanner dalam ${restartDelay / 1000} detik...`);
                    setTimeout(() => {
                        startScanner(); // Panggil fungsi untuk memulai scan lagi
                    }, restartDelay);
                })
                .catch(err => {
                    console.error("Error saat fetch:", err);
                    document.getElementById('scan-result').innerHTML = "<div class='alert alert-danger'>Terjadi kesalahan saat memeriksa kode. Coba lagi.</div>";
                    // Coba restart scanner juga jika fetch gagal
                    setTimeout(() => {
                        startScanner();
                    }, restartDelay);
                });
        }

        // Fungsi callback ketika scan berhasil
        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Kode terdeteksi = ${decodedText}`, decodedResult);

            // Hentikan pemindaian sementara
            if (html5QrcodeScanner && html5QrcodeScanner.getState && html5QrcodeScanner.getState() === 2 /* SCANNING */ ) {
                html5QrcodeScanner.pause(true); // Jeda pemindaian (true = hentikan video feed juga)
                console.log("Scanner dijeda.");
            }

            // Tampilkan pesan sedang memproses
            document.getElementById('scan-result').innerHTML = '<div class="alert alert-secondary">Memproses kode...</div>';

            // Kirim kode ke server
            sendCodeToServer(decodedText.trim());
        }

        // Fungsi callback ketika scan gagal (biasanya diabaikan)
        function onScanFailure(error) {
            // console.warn(`Code scan error = ${error}`);
            // Tidak melakukan apa-apa agar scan berlanjut
        }

        // Fungsi untuk memulai atau merestart scanner
        function startScanner() {
            const scanResultDiv = document.getElementById('scan-result');

            // Cek apakah instance sudah ada dan sedang tidak scan/jeda
            if (!html5QrcodeScanner || !html5QrcodeScanner.getState || html5QrcodeScanner.getState() !== 2 /* SCANNING */ ) {

                // Jika instance belum ada, buat baru
                if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader", // ID element div
                        {
                            fps: 10, // Frame per second
                            qrbox: {
                                width: 250,
                                height: 250
                            }, // Ukuran kotak scan
                            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA], // Hanya gunakan kamera
                            rememberLastUsedCamera: true // Ingat kamera terakhir
                        },
                        /* verbose= */
                        false // Jangan tampilkan log detail dari library
                    );
                }

                // Hapus pesan sebelumnya dan tampilkan pesan siap scan
                scanResultDiv.innerHTML = '<div class="alert alert-info">Arahkan kamera ke QR Code...</div>';

                // Render dan mulai/lanjutkan scan
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                console.log("Scanner dimulai/dilanjutkan.");

            } else {
                // Jika sudah ada dan sedang scan, mungkin tidak perlu di-render ulang
                console.log("Scanner sudah berjalan.");
                scanResultDiv.innerHTML = '<div class="alert alert-info">Arahkan kamera ke QR Code...</div>';
                // Jika dijeda, panggil resume
                if (html5QrcodeScanner.getState() === 3 /* PAUSED */ ) {
                    html5QrcodeScanner.resume();
                    console.log("Scanner dilanjutkan dari jeda.");
                }
            }
        }

        // Mulai scanner saat halaman dimuat
        document.addEventListener('DOMContentLoaded', (event) => {
            // Konfigurasi library agar lebih jelas
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    // Gunakan kamera belakang jika tersedia (biasanya 'environment')
                    const cameraId = devices.find(device => device.label.toLowerCase().includes('back'))?.id || devices[0].id;
                    startScanner(); // Mulai scanner setelah memilih kamera (implisit oleh library)
                } else {
                    document.getElementById('scan-result').innerHTML = '<div class="alert alert-danger">Tidak ada kamera ditemukan.</div>';
                }
            }).catch(err => {
                console.error("Error mendapatkan kamera:", err);
                document.getElementById('scan-result').innerHTML = '<div class="alert alert-danger">Gagal mengakses kamera. Pastikan izin telah diberikan.</div>';
            });
        });

        // Hentikan scanner saat halaman ditutup/pindah (opsional tapi baik)
        window.addEventListener('beforeunload', () => {
            if (html5QrcodeScanner && html5QrcodeScanner.getState && html5QrcodeScanner.getState() === 2 /* SCANNING */ ) {
                html5QrcodeScanner.clear().catch(error => {
                    console.error("Gagal menghentikan scanner saat unload:", error);
                });
            }
        });
    </script>
</body>

</html>