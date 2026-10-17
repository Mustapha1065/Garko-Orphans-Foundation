<?php
namespace GOF\Helpers;

use InvalidArgumentException;
use RuntimeException;

class FileUpload {
    private array $allowedExtensions;
    private array $allowedMimes;
    private int $maxSize;

    public function __construct() {
        $config = require __DIR__ . '/../../config/config.php';
        $this->allowedExtensions = $config['upload']['allowed_extensions'];
        $this->allowedMimes = $config['upload']['allowed_mimes'];
        $this->maxSize = $config['upload']['max_size'];
    }

    public function upload(array $file, string $destination, string $prefix = ''): string {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new InvalidArgumentException("Invalid parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException("No file sent.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException("Exceeded filesize limit.");
            default:
                throw new RuntimeException("Unknown errors.");
        }

        if ($file['size'] > $this->maxSize) {
            throw new RuntimeException("Exceeded filesize limit.");
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $ext = array_search(
            $finfo->file($file['tmp_name']),
            $this->allowedMimes,
            true
        );

        if (false === $ext) {
            throw new RuntimeException("Invalid file format.");
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions, true)) {
            throw new RuntimeException("Invalid file extension.");
        }

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename = $prefix . bin2hex(random_bytes(16)) . '.' . $extension;
        $target = rtrim($destination, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new RuntimeException("Failed to move uploaded file.");
        }

        return $filename;
    }

    public function uploadPhoto(array $file): string {
        $config = require __DIR__ . '/../../config/config.php';
        return $this->upload($file, $config['upload']['photos_path'], 'photo_');
    }

    public function uploadDocument(array $file): string {
        $config = require __DIR__ . '/../../config/config.php';
        return $this->upload($file, $config['upload']['docs_path'], 'doc_');
    }

    public function deleteFile(string $storedName, string $dir): bool {
        $path = rtrim($dir, '/') . '/' . basename($storedName);
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}
