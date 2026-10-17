-- GOF3-light canonical database installer
CREATE DATABASE IF NOT EXISTS `gof3light` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gof3light`;

-- 001_create_users_table.sql
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

-- 002_create_roles_table.sql
-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    is_system TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 003_create_permissions_table.sql
-- Create permissions table
CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    module VARCHAR(100) NULL,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 004_create_role_permissions_table.sql
-- Create role_permissions table
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 005_create_user_roles_table.sql
-- Create user_roles table
CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT UNSIGNED NULL,
    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_assigned_by FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 006_create_user_permissions_table.sql
-- Create user_permissions table
CREATE TABLE IF NOT EXISTS user_permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    granted TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = grant, 0 = revoke',
    granted_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_permissions (user_id, permission_id),
    CONSTRAINT fk_user_permissions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_permissions_granted_by FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 007_create_login_attempts_table.sql
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

-- 008_create_password_resets_table.sql
-- Create password_resets table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    INDEX idx_password_resets_email (email),
    INDEX idx_password_resets_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 009_create_beneficiaries_table.sql
CREATE TABLE IF NOT EXISTS beneficiaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gof_id VARCHAR(20) UNIQUE,
    user_id INT,
    full_name VARCHAR(200) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female'),
    phone VARCHAR(20),
    address TEXT,
    state VARCHAR(100),
    lga VARCHAR(100),
    notes TEXT,
    status ENUM('pending', 'active', 'inactive', 'graduated', 'transferred', 'suspended', 'rejected', 'archived') DEFAULT 'pending',
    registration_date DATE,
    photo VARCHAR(255),
    created_by INT,
    approved_by INT,
    approved_at DATETIME NULL,
    rejected_by INT,
    rejected_at DATETIME NULL,
    rejection_reason TEXT,
    archived_by INT,
    archived_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_gof_id (gof_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 010_create_staff_table.sql
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    gof_id VARCHAR(20) UNIQUE,
    full_name VARCHAR(200) NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    position VARCHAR(100),
    employment_type VARCHAR(50),
    employment_date DATE,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    photo VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_gof_id (gof_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 011_create_donors_table.sql
CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gof_id VARCHAR(20) UNIQUE,
    user_id INT,
    name VARCHAR(200) NOT NULL,
    organization_name VARCHAR(200),
    donor_type ENUM('individual', 'organization') DEFAULT 'individual',
    email VARCHAR(150),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Nigeria',
    notes TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    registration_date DATE,
    registered_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_gof_id (gof_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 012_create_donations_table.sql
CREATE TABLE IF NOT EXISTS donations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donation_ref VARCHAR(60) NOT NULL UNIQUE,
    donor_id INT NOT NULL,
    program_id INT UNSIGNED NULL,
    amount DECIMAL(14,2) NOT NULL,
    currency VARCHAR(8) NOT NULL DEFAULT 'NGN',
    donation_date DATE NOT NULL,
    payment_method ENUM('bank_transfer', 'card', 'cash', 'cheque', 'online', 'mobile_money', 'other') DEFAULT 'bank_transfer',
    transaction_ref VARCHAR(255) NULL,
    purpose VARCHAR(255) NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'refunded') DEFAULT 'pending',
    notes TEXT NULL,
    receipt_number VARCHAR(60) NULL UNIQUE,
    recorded_by INT UNSIGNED NULL,
    confirmed_by INT UNSIGNED NULL,
    confirmed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_donor_id (donor_id),
    INDEX idx_donation_date (donation_date),
    CONSTRAINT fk_donations_donor FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE RESTRICT,
    CONSTRAINT fk_donations_recorded_by FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_donations_confirmed_by FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 013_create_programs_table.sql
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

-- 014_create_events_table.sql
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE,
    end_date DATE,
    event_time TIME,
    location VARCHAR(200),
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    is_public TINYINT(1) DEFAULT 1,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_event_date (event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 015_create_volunteer_applications_table.sql
CREATE TABLE IF NOT EXISTS volunteer_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    age INT,
    occupation VARCHAR(100),
    skills TEXT,
    interest VARCHAR(100),
    availability VARCHAR(50),
    motivation TEXT,
    experience TEXT,
    status ENUM('pending', 'reviewed', 'approved', 'rejected', 'contacted') DEFAULT 'pending',
    reviewed_by INT,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 016_create_contact_messages_table.sql
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 017_create_id_cards_table.sql
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

-- 018_create_audit_logs_table.sql
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50),
    entity_id VARCHAR(100),
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 019_create_guardians_table.sql
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

-- 020_create_education_records_table.sql
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

-- 021_create_beneficiary_support_table.sql
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

-- 022_create_news_table.sql
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(250) UNIQUE,
    category VARCHAR(100),
    excerpt TEXT,
    content TEXT,
    featured_image VARCHAR(255),
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_slug (slug),
    INDEX idx_published_at (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 023_create_gallery_albums_table.sql
CREATE TABLE IF NOT EXISTS gallery_albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    event_id INT,
    is_public TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_event_id (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 024_create_gallery_images_table.sql
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    caption VARCHAR(200),
    display_order INT DEFAULT 0,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_album_id (album_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 025_create_notifications_table.sql
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    type VARCHAR(50) DEFAULT 'info',
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 026_create_id_card_tokens_table.sql
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

-- 027_create_id_card_history_table.sql
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

-- 028_create_documents_table.sql
CREATE TABLE IF NOT EXISTS documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_type ENUM('beneficiary', 'donor', 'staff', 'program', 'volunteer', 'general') NOT NULL,
    entity_id INT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    uploaded_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_entity (entity_type, entity_id),
    CONSTRAINT fk_documents_uploaded_by FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 029_create_blocked_ips_table.sql
CREATE TABLE IF NOT EXISTS blocked_ips (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    reason TEXT NULL,
    blocked_by INT UNSIGNED NULL,
    expires_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_blocked_ips_ip_address (ip_address),
    CONSTRAINT fk_blocked_ips_blocked_by FOREIGN KEY (blocked_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 030_create_verification_attempts_table.sql
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

-- 031_create_sequences_table.sql
CREATE TABLE IF NOT EXISTS sequences (
    name VARCHAR(80) NOT NULL,
    last_val BIGINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 032_create_settings_table.sql
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(50) NOT NULL DEFAULT 'general',
    key_name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT,
    type VARCHAR(20) NOT NULL DEFAULT 'string',
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_group_name (group_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 001_seed_roles.sql
-- Canonical staff roles for GOF
INSERT INTO roles (name, slug, description, is_system) VALUES
('Super Admin', 'super_admin', 'Full system access with all permissions', 1),
('Beneficiary Officer', 'beneficiary_officer', 'Manages beneficiary registration, review, and case records', 1),
('Welfare Officer', 'welfare_officer', 'Manages beneficiary support and welfare assistance', 1),
('Education Officer', 'education_officer', 'Manages beneficiary education records and updates', 1),
('Finance Officer', 'finance_officer', 'Manages donors, donations, confirmations, and receipts', 1),
('Program Manager', 'program_manager', 'Manages programs, projects, and assignments', 1),
('Content Manager', 'content_manager', 'Manages public website content, events, news, and gallery', 1),
('ID Card Officer', 'id_card_officer', 'Manages ID card issuance, reissue, revocation, and verification logs', 1),
('Report Viewer', 'report_viewer', 'Views and exports operational reports', 1),
('Viewer', 'viewer', 'Read-only access to permitted operational data', 1)
ON DUPLICATE KEY UPDATE
name = VALUES(name),
description = VALUES(description),
is_system = VALUES(is_system);

-- 002_seed_permissions.sql
-- Canonical permissions for GOF
INSERT INTO permissions (slug, name, module, description) VALUES
('access_admin', 'Access Admin Dashboard', 'core', 'Access the protected administrative dashboard'),
('manage_users', 'Manage Users', 'core', 'Create, update, activate, and manage user accounts'),
('manage_roles', 'Manage Roles', 'core', 'Manage system roles'),
('manage_permissions', 'Manage Permissions', 'core', 'Manage permission assignments and overrides'),
('manage_settings', 'Manage Settings', 'core', 'Manage application and organization settings'),
('view_audit_logs', 'View Audit Logs', 'core', 'View audit logs and sensitive activity history'),
('manage_backups', 'Manage Backups', 'core', 'Create and manage database backups'),
('view_staff', 'View Staff', 'staff', 'View staff records'),
('create_staff', 'Create Staff', 'staff', 'Create staff profiles and accounts'),
('edit_staff', 'Edit Staff', 'staff', 'Update staff records'),
('activate_staff', 'Activate Staff', 'staff', 'Activate, deactivate, or suspend staff accounts'),
('view_staff_activity', 'View Staff Activity', 'staff', 'View staff activity summaries'),
('view_beneficiaries', 'View Beneficiaries', 'beneficiary', 'View beneficiary records'),
('create_beneficiary', 'Create Beneficiary', 'beneficiary', 'Register new beneficiaries'),
('edit_beneficiary', 'Edit Beneficiary', 'beneficiary', 'Update beneficiary records'),
('approve_beneficiary', 'Approve Beneficiary', 'beneficiary', 'Approve beneficiary registrations'),
('reject_beneficiary', 'Reject Beneficiary', 'beneficiary', 'Reject beneficiary registrations'),
('archive_beneficiary', 'Archive Beneficiary', 'beneficiary', 'Archive beneficiary records'),
('change_beneficiary_status', 'Change Beneficiary Status', 'beneficiary', 'Change beneficiary lifecycle status'),
('view_beneficiary_history', 'View Beneficiary History', 'beneficiary', 'View beneficiary activity and status history'),
('view_guardians', 'View Guardians', 'beneficiary', 'View guardian information'),
('create_guardian', 'Create Guardian', 'beneficiary', 'Create guardian records'),
('edit_guardian', 'Edit Guardian', 'beneficiary', 'Update guardian records'),
('view_support', 'View Support', 'support', 'View beneficiary support records'),
('create_support', 'Create Support', 'support', 'Create beneficiary support records'),
('edit_support', 'Edit Support', 'support', 'Update beneficiary support records'),
('approve_support', 'Approve Support', 'support', 'Approve support actions where approval is required'),
('view_education', 'View Education', 'education', 'View education records'),
('create_education', 'Create Education', 'education', 'Create education records'),
('edit_education', 'Edit Education', 'education', 'Update education records'),
('update_education_status', 'Update Education Status', 'education', 'Update beneficiary enrollment status'),
('view_donors', 'View Donors', 'donor', 'View donor records'),
('create_donor', 'Create Donor', 'donor', 'Create donor records'),
('edit_donor', 'Edit Donor', 'donor', 'Update donor records'),
('activate_donor', 'Activate Donor', 'donor', 'Activate or deactivate donor records'),
('view_donor_history', 'View Donor History', 'donor', 'View donor donation history'),
('view_donations', 'View Donations', 'donation', 'View donation records'),
('create_donation', 'Create Donation', 'donation', 'Record donations'),
('confirm_donation', 'Confirm Donation', 'donation', 'Confirm pending donations'),
('cancel_donation', 'Cancel Donation', 'donation', 'Cancel donation records where allowed'),
('refund_donation', 'Refund Donation', 'donation', 'Mark or process refunded donations'),
('generate_receipts', 'Generate Receipts', 'donation', 'Generate donation receipts'),
('view_programs', 'View Programs', 'program', 'View program records'),
('create_programs', 'Create Programs', 'program', 'Create program records'),
('edit_programs', 'Edit Programs', 'program', 'Update program records'),
('manage_projects', 'Manage Projects', 'program', 'Create and manage project records'),
('assign_beneficiaries_to_programs', 'Assign Beneficiaries To Programs', 'program', 'Assign beneficiaries to programs or projects'),
('manage_events', 'Manage Events', 'content', 'Create and manage events'),
('publish_events', 'Publish Events', 'content', 'Publish or unpublish events'),
('manage_news', 'Manage News', 'content', 'Create and manage news content'),
('publish_news', 'Publish News', 'content', 'Publish or unpublish news articles'),
('manage_gallery', 'Manage Gallery', 'content', 'Manage gallery albums and images'),
('publish_gallery', 'Publish Gallery', 'content', 'Publish or unpublish gallery content'),
('manage_website_content', 'Manage Website Content', 'content', 'Manage public site pages and content blocks'),
('upload_documents', 'Upload Documents', 'document', 'Upload protected documents'),
('download_documents', 'Download Documents', 'document', 'Download protected documents'),
('delete_documents', 'Delete Documents', 'document', 'Delete or archive documents'),
('manage_document_access', 'Manage Document Access', 'document', 'Manage document visibility and access rules'),
('view_id_cards', 'View ID Cards', 'id_card', 'View ID card records'),
('generate_id_cards', 'Generate ID Cards', 'id_card', 'Generate ID cards'),
('reissue_id_cards', 'Reissue ID Cards', 'id_card', 'Reissue or replace ID cards'),
('revoke_id_cards', 'Revoke ID Cards', 'id_card', 'Revoke ID cards'),
('view_verification_logs', 'View Verification Logs', 'id_card', 'View QR verification logs'),
('view_reports', 'View Reports', 'report', 'Access reporting dashboards and reports'),
('export_reports', 'Export Reports', 'report', 'Export reports to print or PDF outputs'),
('manage_contacts', 'Manage Contacts', 'communication', 'Manage public contact messages'),
('manage_volunteers', 'Manage Volunteers', 'communication', 'Manage volunteer applications'),
('view_notifications', 'View Notifications', 'notification', 'View internal notifications'),
('send_notifications', 'Send Notifications', 'notification', 'Create or broadcast internal notifications'),
('view_own_profile', 'View Own Profile', 'self_service', 'View own account profile'),
('edit_own_profile', 'Edit Own Profile', 'self_service', 'Edit allowed own profile fields'),
('view_own_donations', 'View Own Donations', 'self_service', 'View own donation history'),
('view_own_receipts', 'View Own Receipts', 'self_service', 'View own donation receipts'),
('view_own_id_card', 'View Own ID Card', 'self_service', 'View own ID card'),
('view_own_notifications', 'View Own Notifications', 'self_service', 'View own notifications')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
module = VALUES(module),
description = VALUES(description);

-- 003_seed_role_permissions.sql
-- Canonical role-permission assignments for GOF

-- Super Admin gets all permissions
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.slug = 'super_admin';

-- Beneficiary Officer
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'view_beneficiaries', 'create_beneficiary', 'edit_beneficiary',
    'approve_beneficiary', 'reject_beneficiary', 'archive_beneficiary',
    'change_beneficiary_status', 'view_beneficiary_history',
    'view_guardians', 'create_guardian', 'edit_guardian',
    'view_support', 'create_support',
    'view_education', 'create_education', 'edit_education',
    'view_notifications'
)
WHERE r.slug = 'beneficiary_officer';

-- Welfare Officer
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'view_beneficiaries',
    'view_support', 'create_support', 'edit_support', 'approve_support',
    'view_reports', 'view_notifications'
)
WHERE r.slug = 'welfare_officer';

-- Education Officer
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'view_beneficiaries',
    'view_education', 'create_education', 'edit_education', 'update_education_status',
    'view_notifications'
)
WHERE r.slug = 'education_officer';

-- Finance Officer
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'view_donors', 'create_donor', 'edit_donor', 'activate_donor', 'view_donor_history',
    'view_donations', 'create_donation', 'confirm_donation', 'cancel_donation', 'refund_donation', 'generate_receipts',
    'view_reports', 'export_reports', 'view_notifications'
)
WHERE r.slug = 'finance_officer';

-- Program Manager
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'view_programs', 'create_programs', 'edit_programs', 'manage_projects',
    'assign_beneficiaries_to_programs',
    'view_reports', 'view_notifications'
)
WHERE r.slug = 'program_manager';

-- Content Manager
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'manage_events', 'publish_events',
    'manage_news', 'publish_news',
    'manage_gallery', 'publish_gallery',
    'manage_website_content',
    'manage_contacts', 'manage_volunteers',
    'view_notifications'
)
WHERE r.slug = 'content_manager';

-- ID Card Officer
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'view_beneficiaries',
    'view_id_cards', 'generate_id_cards', 'reissue_id_cards', 'revoke_id_cards', 'view_verification_logs',
    'view_notifications'
)
WHERE r.slug = 'id_card_officer';

-- Report Viewer
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'view_reports', 'export_reports', 'view_notifications'
)
WHERE r.slug = 'report_viewer';

-- Viewer
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.slug IN (
    'access_admin',
    'view_staff', 'view_beneficiaries', 'view_donors', 'view_donations', 'view_programs',
    'view_reports', 'view_notifications'
)
WHERE r.slug = 'viewer';

-- 004_seed_super_admin.sql
-- Seed super admin user
-- Default password: Admin@1234
-- Change immediately after first login.
INSERT INTO users (email, password_hash, account_type, status, first_name, last_name, created_at, updated_at)
VALUES (
    'admin@gof.org.ng',
    '$2y$12$cQt5MwtmyBvt8yB1YetZh.1mik/uwxZDdpZ2dZEA3j7FRUorusg1e',
    'super_admin',
    'active',
    'Super',
    'Admin',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
password_hash = VALUES(password_hash),
account_type = VALUES(account_type),
status = VALUES(status),
first_name = VALUES(first_name),
last_name = VALUES(last_name),
updated_at = NOW();

INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by)
SELECT u.id, r.id, u.id
FROM users u
JOIN roles r ON r.slug = 'super_admin'
WHERE u.email = 'admin@gof.org.ng';

-- 005_seed_settings.sql
-- Seed baseline GOF settings
INSERT INTO settings (group_name, key_name, value, type, is_public, created_at, updated_at)
VALUES
('general', 'org_name', 'Garko Orphans Foundation', 'string', 1, NOW(), NOW()),
('general', 'org_email', 'info@gof.org.ng', 'string', 1, NOW(), NOW()),
('general', 'org_phone', '+234 800 000 0000', 'string', 1, NOW(), NOW()),
('general', 'org_address', 'Garko, Kano State, Nigeria', 'string', 1, NOW(), NOW()),
('general', 'currency', 'NGN', 'string', 0, NOW(), NOW()),
('id_card', 'id_card_expiry_years', '5', 'integer', 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE
value = VALUES(value),
type = VALUES(type),
is_public = VALUES(is_public),
updated_at = NOW();
