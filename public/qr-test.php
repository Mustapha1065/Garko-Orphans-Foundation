<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

$output = __DIR__ . '/uploads/qrcodes/test-qr.png';

$directory = dirname($output);

if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}

try {

    $result = Builder::create()
        ->writer(new PngWriter())
        ->data('GOF3-light QR TEST')
        ->size(300)
        ->margin(10)
        ->build();

    $result->saveToFile($output);

    echo '<h2>QR TEST</h2>';

    if (file_exists($output)) {

        echo '<p style="color:green;">QR generated successfully.</p>';

        echo '<p>File: ' . htmlspecialchars($output) . '</p>';

        echo '<p>Size: ' . filesize($output) . ' bytes</p>';

        echo '<img src="uploads/qrcodes/test-qr.png" width="300">';

    } else {

        echo '<p style="color:red;">QR file was not created.</p>';
    }

} catch (\Throwable $e) {

    echo '<h2 style="color:red;">QR ERROR</h2>';

    echo '<pre>';
    echo htmlspecialchars($e->getMessage());
    echo "\n\n";
    echo htmlspecialchars($e->getFile());
    echo ':';
    echo $e->getLine();
    echo '</pre>';
}