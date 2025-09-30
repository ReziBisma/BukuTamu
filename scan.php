<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Scan QR / Barcode Tamu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
  <h3>Scan QR / Barcode Tamu</h3>
  <p>Gunakan kamera untuk memindai QR Code tamu.</p>

  <div id="reader" style="width:300px;"></div>
  <div id="scan-result" class="mt-3"></div>

  <a href="index.php" class="btn btn-secondary mt-3">Kembali ke halaman utama</a>
</div>

<script>
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
        document.getElementById('scan-result').innerHTML = data;
    })
    .catch(err => console.error(err));
}

function onScanSuccess(decodedText, decodedResult) {
    // hentikan scanner setelah berhasil scan
    html5QrcodeScanner.stop().then(ignore => {
        sendCodeToServer(decodedText.trim());
    }).catch(error => console.error(error));
}

// Mulai scanner
var html5QrcodeScanner = new Html5Qrcode("reader");
html5QrcodeScanner.start(
    { facingMode: "environment" }, // gunakan kamera belakang jika ada
    {
        fps: 15,
        qrbox: 300
    },
    onScanSuccess
);
</script>
</body>
</html>
