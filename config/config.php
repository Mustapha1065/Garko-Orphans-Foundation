<?php

return [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'Garko Orphans Foundation',
        'url' => getenv('APP_URL') ?: 'http://localhost/GOF3-light/public',
        'env' => getenv('APP_ENV') ?: 'production',
        'secret' => getenv('APP_SECRET') ?: 'default_secret',
        'version' => '1.0.0',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'gof3light',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
    ],
    'session' => [
        'lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 7200),
        'name' => getenv('SESSION_NAME') ?: 'gof_session',
        'cookie_samesite' => 'Strict',
        'cookie_secure' => getenv('APP_ENV') === 'production',
        'cookie_httponly' => true,
    ],
    'upload' => [
        'max_size' => (int)(getenv('UPLOAD_MAX_SIZE') ?: 10485760),
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'photos_path' => realpath(__DIR__ . '/../storage/photos') ?: __DIR__ . '/../storage/photos',
        'docs_path' => realpath(__DIR__ . '/../storage/documents') ?: __DIR__ . '/../storage/documents',
    ],
    'idcard' => [
        'expiry_years' => 5,
        'sequence_table' => 'sequences',
    ],
    'mail' => [
        'enabled' => filter_var(getenv('MAIL_ENABLED'), FILTER_VALIDATE_BOOLEAN),
        'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
        'port' => (int)(getenv('MAIL_PORT') ?: 587),
        'username' => getenv('MAIL_USERNAME') ?: '',
        'password' => getenv('MAIL_PASSWORD') ?: '',
        'from' => getenv('MAIL_FROM') ?: 'noreply@gof.org.ng',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Garko Orphans Foundation',
    ]
];
