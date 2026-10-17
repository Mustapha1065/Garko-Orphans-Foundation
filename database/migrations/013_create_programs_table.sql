CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_code VARCHAR(20) UNIQUE,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    objectives TEXT,
    start_date DATE,
    end_date DATE,
    status ENUM('planned', 'active', 'completed', 'suspended', 'archived') DEFAULT 'planned',
    budget DECIMAL(15,2),
    currency VARCHAR(10) DEFAULT 'NGN',
    manager_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_program_code (program_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
