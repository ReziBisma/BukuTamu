<?php
require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (!isset($_GET['id']) || $_GET['id'] === '') {
    die("ID tidak valid.");
}

$id_tamu = $_GET['id'];

// generate QR code dari ID
$qr = new QrCode($id_tamu);
$writer = new PngWriter();
$result = $writer->write($qr);

// atur header untuk download file PNG
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="QR_' . $id_tamu . '.png"');

echo $result->getString();
exit;
