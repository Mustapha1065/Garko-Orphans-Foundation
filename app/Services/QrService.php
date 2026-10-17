<?php

namespace GOF\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QrService
{
    /**
     * Generate a QR code PNG.
     *
     * @param string $data
     * @param string $outputPath
     * @return string
     */
    public function generate(string $data, string $outputPath): string
    {
        $directory = dirname($outputPath);

        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(
                    'Unable to create QR directory: ' . $directory
                );
            }
        }

        if (!is_writable($directory)) {
            throw new \RuntimeException(
                'QR directory is not writable: ' . $directory
            );
        }

        try {
            $writer = new PngWriter();

            $result = Builder::create()
                ->writer($writer)
                ->data($data)
                ->size(300)
                ->margin(10)
                ->build();

            $result->saveToFile($outputPath);

        } catch (\Throwable $e) {

            throw new \RuntimeException(
                'QR generation failed: ' . $e->getMessage(),
                0,
                $e
            );
        }

        if (!file_exists($outputPath)) {
            throw new \RuntimeException(
                'QR file was not created: ' . $outputPath
            );
        }

        if (filesize($outputPath) === 0) {
            throw new \RuntimeException(
                'QR file was created but is empty: ' . $outputPath
            );
        }

        return $outputPath;
    }
}