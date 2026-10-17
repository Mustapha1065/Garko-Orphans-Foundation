CREATE TABLE IF NOT EXISTS guardians (
    id INT AUTO_INCREMENT PRIMARY KEY,
    beneficiary_id INT NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    relationship VARCHAR(50),
    phone VARCHAR(20),
    is_emergency TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_beneficiary_id (beneficiary_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
