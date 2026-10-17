-- Create login_attempts table
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    successful TINYINT(1) NOT NULL DEFAULT 0,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_login_attempts_email (email),
    INDEX idx_login_attempts_ip_address (ip_address),
    INDEX idx_login_attempts_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;