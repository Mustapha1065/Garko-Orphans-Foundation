CREATE TABLE IF NOT EXISTS verification_attempts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_card_id INT UNSIGNED NULL,
    token_id INT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    result VARCHAR(80) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_verification_attempts_id_card FOREIGN KEY (id_card_id) REFERENCES id_cards(id) ON DELETE SET NULL,
    CONSTRAINT fk_verification_attempts_token FOREIGN KEY (token_id) REFERENCES id_card_tokens(id) ON DELETE SET NULL,
    INDEX idx_verification_attempts_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
