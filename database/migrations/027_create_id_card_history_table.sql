CREATE TABLE IF NOT EXISTS id_card_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_card_id INT UNSIGNED NOT NULL,
    action ENUM('issued', 'reprinted', 'revoked', 'expired', 'replaced', 'reactivated', 'token_revoked') NOT NULL,
    user_id INT UNSIGNED NULL,
    reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_id_card (id_card_id),
    CONSTRAINT fk_id_card_history_id_card FOREIGN KEY (id_card_id) REFERENCES id_cards(id) ON DELETE CASCADE,
    CONSTRAINT fk_id_card_history_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
