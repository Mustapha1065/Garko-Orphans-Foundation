-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    account_type ENUM('super_admin', 'staff', 'beneficiary', 'donor') NOT NULL,
    status ENUM('pending', 'active', 'inactive', 'suspended', 'archived') NOT NULL DEFAULT 'pending',
    first_name VARCHAR(120) NULL,
    last_name VARCHAR(120) NULL,
    phone VARCHAR(30) NULL,
    photo VARCHAR(255) NULL,
    email_verified_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login_at DATETIME NULL,
    failed_login_attempts INT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    remember_token VARCHAR(255) NULL,
    INDEX idx_users_email (email),
    INDEX idx_users_account_type (account_type),
    INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;