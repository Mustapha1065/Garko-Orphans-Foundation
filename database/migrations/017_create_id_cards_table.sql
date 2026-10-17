CREATE TABLE IF NOT EXISTS id_cards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    card_no VARCHAR(30) NOT NULL UNIQUE,
    person_type ENUM('beneficiary', 'staff', 'donor') NOT NULL,
    person_id INT UNSIGNED NOT NULL,
    qr_token VARCHAR(64) NOT NULL UNIQUE,
    issue_date DATE NOT NULL,
    expiry_date DATE NULL,
    status ENUM('active', 'expired', 'revoked', 'replaced') NOT NULL DEFAULT 'active',
    generated_by INT UNSIGNED NULL,
    reprint_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_card_no (card_no),
    INDEX idx_person (person_type, person_id),
    CONSTRAINT fk_id_cards_generated_by FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
