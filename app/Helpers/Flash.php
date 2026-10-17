<?php
namespace GOF\Helpers;

class Flash {
    public static function set(string $type, string $message): void {
        $_SESSION['_flash'][$type][] = $message;
    }

    public static function get(string $type): ?string {
        if (isset($_SESSION['_flash'][$type]) && !empty($_SESSION['_flash'][$type])) {
            $message = array_shift($_SESSION['_flash'][$type]);
            return $message;
        }
        return null;
    }

    public static function has(string $type): bool {
        return !empty($_SESSION['_flash'][$type]);
    }

    public static function all(): array {
        $flashes = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flashes;
    }

    public static function clear(): void {
        unset($_SESSION['_flash']);
    }
}
