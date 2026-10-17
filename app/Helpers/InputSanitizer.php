<?php
namespace GOF\Helpers;

class InputSanitizer {
    /**
     * Sanitize a string by removing HTML tags and special characters
     */
    public static function sanitize(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize an array of inputs recursively
     */
    public static function sanitizeArray(array $data): array {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = self::sanitize($value);
            }
        }
        return $sanitized;
    }

    /**
     * Validate email format
     */
    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL format
     */
    public static function validateUrl(string $url): bool {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate integer
     */
    public static function validateInt(mixed $value): bool {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Clean SQL injection attempts (additional layer on top of PDO)
     */
    public static function cleanSql(string $input): string {
        return preg_replace('/[^\w\s\-@.]/', '', $input);
    }
}
