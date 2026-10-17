CREATE TABLE IF NOT EXISTS id_card_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_card_id INT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    revoked TINYINT(1) NOT NULL DEFAULT 0,
    revoked_by INT UNSIGNED NULL,
    revoked_at DATETIME NULL,
    revoke_reason TEXT NULL,
    expires_at DATETIME NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_id_card (id_card_id),
    CONSTRAINT fk_id_card_tokens_id_card FOREIGN KEY (id_card_id) REFERENCES id_cards(id) ON DELETE CASCADE,
    CONSTRAINT fk_id_card_tokens_revoked_by FOREIGN KEY (revoked_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
