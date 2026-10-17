CREATE TABLE IF NOT EXISTS education_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    beneficiary_id INT NOT NULL,
    institution_name VARCHAR(200),
    institution_type VARCHAR(50),
    course_level VARCHAR(100),
    academic_session VARCHAR(50),
    enrollment_status VARCHAR(50),
    performance VARCHAR(100),
    remarks TEXT,
    start_date DATE,
    end_date DATE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_beneficiary_id (beneficiary_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
