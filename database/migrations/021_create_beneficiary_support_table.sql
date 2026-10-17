CREATE TABLE IF NOT EXISTS beneficiary_support (
    id INT AUTO_INCREMENT PRIMARY KEY,
    beneficiary_id INT NOT NULL,
    support_type VARCHAR(50) NOT NULL,
    description TEXT,
    quantity DECIMAL(10,2),
    amount DECIMAL(15,2),
    currency VARCHAR(10) DEFAULT 'NGN',
    support_date DATE,
    recorded_by_user_id INT,
    program_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_beneficiary_id (beneficiary_id),
    INDEX idx_support_date (support_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
