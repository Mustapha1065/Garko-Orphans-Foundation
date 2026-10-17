<?php
namespace GOF\Services;

class TokenService
{
    protected string $secret;

    public function __construct()
    {
        $cfg = require __DIR__ . '/../../config/config.php';
        $this->secret = $cfg['app']['key'] ?? 'change-me-please';
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) $data .= str_repeat('=', 4 - $remainder);
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function sign(array $payload, int $ttlSeconds = 86400): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + $ttlSeconds;
        $json = json_encode($payload);
        $b = $this->base64UrlEncode($json);
        $sig = hash_hmac('sha256', $b, $this->secret, true);
        $s = $this->base64UrlEncode($sig);
        return $b . '.' . $s;
    }

    public function verify(string $token): ?array
    {
        if (strpos($token, '.') === false) return null;
        [$b, $s] = explode('.', $token, 2);
        $expected = $this->base64UrlEncode(hash_hmac('sha256', $b, $this->secret, true));
        if (!hash_equals($expected, $s)) return null;
        $json = $this->base64UrlDecode($b);
        $payload = json_decode($json, true);
        if (!is_array($payload)) return null;
        if (isset($payload['exp']) && time() > (int)$payload['exp']) return null;
        return $payload;
    }
}
