-- GOF Canonical Database Schema Blueprint
-- This file is a reference blueprint for the target schema.
-- It should guide migration consolidation for the GOF2 project.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS backups;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS volunteer_applications;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS verification_attempts;
DROP TABLE IF EXISTS blocked_ips;
DROP TABLE IF EXISTS id_card_tokens;
DROP TABLE IF EXISTS id_card_history;
DROP TABLE IF EXISTS id_cards;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS gallery_images;
DROP TABLE IF EXISTS gallery_albums;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS project_beneficiaries;
DROP TABLE IF EXISTS program_beneficiaries;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS programs;
DROP TABLE IF EXISTS donations;
DROP TABLE IF EXISTS donors;
DROP TABLE IF EXISTS beneficiary_support;
DROP TABLE IF EXISTS education_records;
DROP TABLE IF EXISTS guardians;
DROP TABLE IF EXISTS beneficiaries;
DROP TABLE IF EXISTS staff;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS user_permissions;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS sequences;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    account_type ENUM('super_admin','staff','beneficiary','donor') NOT NULL,
    status ENUM('pending','active','inactive','suspended','archived') NOT NULL DEFAULT 'pending',
    first_name VARCHAR(120) NULL,
    last_name VARCHAR(120) NULL,
    phone VARCHAR(30) NULL,
    photo VARCHAR(255) NULL,
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    failed_login_attempts INT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    remember_token VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_users_email (email),
    INDEX idx_users_account_type (account_type),
    INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    is_system TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE permissions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    module VARCHAR(100) NULL,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    assigned_by INT UNSIGNED NULL,
    assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_assigned_by FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_permissions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    granted TINYINT(1) NOT NULL DEFAULT 1,
    granted_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_permissions (user_id, permission_id),
    CONSTRAINT fk_user_permissions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_permissions_granted_by FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    successful TINYINT(1) NOT NULL DEFAULT 0,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_login_attempts_email (email),
    INDEX idx_login_attempts_ip_address (ip_address),
    INDEX idx_login_attempts_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    PRIMARY KEY (id),
    INDEX idx_password_resets_email (email),
    INDEX idx_password_resets_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sequences (
    name VARCHAR(80) NOT NULL,
    last_val BIGINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE staff (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NULL,
    gof_id VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    gender ENUM('male','female','other') NULL,
    date_of_birth DATE NULL,
    phone VARCHAR(30) NULL,
    email VARCHAR(255) NULL,
    address TEXT NULL,
    state VARCHAR(100) NULL,
    lga VARCHAR(100) NULL,
    designation VARCHAR(120) NULL,
    department VARCHAR(120) NULL,
    employment_date DATE NULL,
    photo VARCHAR(255) NULL,
    status ENUM('active','inactive','suspended','archived') NOT NULL DEFAULT 'active',
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_staff_user_id (user_id),
    CONSTRAINT fk_staff_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_staff_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_staff_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE beneficiaries (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NULL,
    gof_id VARCHAR(30) NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    photo VARCHAR(255) NULL,
    date_of_birth DATE NULL,
    gender ENUM('male','female','other') NULL,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    state VARCHAR(100) NULL,
    lga VARCHAR(100) NULL,
    registration_date DATE NOT NULL,
    status ENUM('pending','active','inactive','graduated','transferred','suspended','archived','rejected') NOT NULL DEFAULT 'pending',
    created_by INT UNSIGNED NULL,
    approved_by INT UNSIGNED NULL,
    approved_at DATETIME NULL,
    rejected_by INT UNSIGNED NULL,
    rejected_at DATETIME NULL,
    rejection_reason TEXT NULL,
    archived_by INT UNSIGNED NULL,
    archived_at DATETIME NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_beneficiaries_user_id (user_id),
    CONSTRAINT fk_beneficiaries_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_beneficiaries_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_beneficiaries_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_beneficiaries_rejected_by FOREIGN KEY (rejected_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_beneficiaries_archived_by FOREIGN KEY (archived_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_beneficiaries_gof_id (gof_id),
    INDEX idx_beneficiaries_status (status),
    INDEX idx_beneficiaries_registration_date (registration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE guardians (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    beneficiary_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    relationship VARCHAR(80) NULL,
    phone VARCHAR(30) NULL,
    email VARCHAR(255) NULL,
    address TEXT NULL,
    occupation VARCHAR(120) NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    is_emergency TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_guardians_beneficiary FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE education_records (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    beneficiary_id INT UNSIGNED NOT NULL,
    institution_name VARCHAR(255) NULL,
    institution_type ENUM('primary','secondary','tertiary','vocational','quranic','none','other') NULL,
    course_level VARCHAR(120) NULL,
    academic_session VARCHAR(50) NULL,
    enrollment_status ENUM('enrolled','graduated','dropped_out','transferred','on_break') NULL,
    performance TEXT NULL,
    remarks TEXT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_education_records_beneficiary FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE,
    CONSTRAINT fk_education_records_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE programs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    program_code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    objectives TEXT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    status ENUM('planned','active','completed','suspended','archived') NOT NULL DEFAULT 'planned',
    budget DECIMAL(14,2) NULL,
    currency VARCHAR(8) NOT NULL DEFAULT 'NGN',
    manager_id INT UNSIGNED NULL,
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_programs_manager FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_programs_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_programs_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projects (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    program_id INT UNSIGNED NULL,
    project_code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    objectives TEXT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    status ENUM('planned','active','completed','suspended','archived') NOT NULL DEFAULT 'planned',
    budget DECIMAL(14,2) NULL,
    currency VARCHAR(8) NOT NULL DEFAULT 'NGN',
    manager_id INT UNSIGNED NULL,
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_projects_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL,
    CONSTRAINT fk_projects_manager FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_projects_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_projects_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE beneficiary_support (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    beneficiary_id INT UNSIGNED NOT NULL,
    program_id INT UNSIGNED NULL,
    project_id INT UNSIGNED NULL,
    support_type ENUM('education','food','clothing','medical','financial','materials','shelter','other') NULL,
    description TEXT NULL,
    quantity INT UNSIGNED NULL,
    amount DECIMAL(12,2) NULL,
    currency VARCHAR(8) NOT NULL DEFAULT 'NGN',
    support_date DATE NULL,
    recorded_by_user_id INT UNSIGNED NULL,
    status ENUM('recorded','approved','cancelled') NOT NULL DEFAULT 'recorded',
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_beneficiary_support_beneficiary FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE,
    CONSTRAINT fk_beneficiary_support_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL,
    CONSTRAINT fk_beneficiary_support_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    CONSTRAINT fk_beneficiary_support_recorded_by FOREIGN KEY (recorded_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE donors (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NULL,
    gof_id VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    organization_name VARCHAR(255) NULL,
    donor_type ENUM('individual','organization','government','foundation','ngo','other') NOT NULL DEFAULT 'individual',
    email VARCHAR(255) NULL,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'Nigeria',
    status ENUM('active','inactive','archived') NOT NULL DEFAULT 'active',
    notes TEXT NULL,
    registered_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_donors_user_id (user_id),
    CONSTRAINT fk_donors_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_donors_registered_by FOREIGN KEY (registered_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_donors_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE donations (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    donation_ref VARCHAR(60) NOT NULL UNIQUE,
    donor_id INT NOT NULL,
    program_id INT UNSIGNED NULL,
    project_id INT UNSIGNED NULL,
    amount DECIMAL(14,2) NOT NULL,
    currency VARCHAR(8) NOT NULL DEFAULT 'NGN',
    donation_date DATE NOT NULL,
    payment_method ENUM('bank_transfer','cash','cheque','online','mobile_money','other') NULL,
    transaction_ref VARCHAR(255) NULL,
    purpose VARCHAR(255) NULL,
    status ENUM('pending','confirmed','cancelled','refunded') NOT NULL DEFAULT 'pending',
    notes TEXT NULL,
    receipt_number VARCHAR(60) NULL UNIQUE,
    recorded_by INT UNSIGNED NULL,
    confirmed_by INT UNSIGNED NULL,
    confirmed_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_donations_donor FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE RESTRICT,
    CONSTRAINT fk_donations_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL,
    CONSTRAINT fk_donations_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    CONSTRAINT fk_donations_recorded_by FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_donations_confirmed_by FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_donations_donation_date (donation_date),
    INDEX idx_donations_status (status),
    INDEX idx_donations_transaction_ref (transaction_ref)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE program_beneficiaries (
    program_id INT UNSIGNED NOT NULL,
    beneficiary_id INT UNSIGNED NOT NULL,
    joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (program_id, beneficiary_id),
    CONSTRAINT fk_program_beneficiaries_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    CONSTRAINT fk_program_beneficiaries_beneficiary FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_beneficiaries (
    project_id INT UNSIGNED NOT NULL,
    beneficiary_id INT UNSIGNED NOT NULL,
    joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (project_id, beneficiary_id),
    CONSTRAINT fk_project_beneficiaries_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_project_beneficiaries_beneficiary FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE events (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NULL UNIQUE,
    description TEXT NULL,
    location VARCHAR(255) NULL,
    event_date DATE NOT NULL,
    end_date DATE NULL,
    status ENUM('upcoming','ongoing','completed','cancelled','archived') NOT NULL DEFAULT 'upcoming',
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    featured_image VARCHAR(255) NULL,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_events_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE news (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NULL,
    content LONGTEXT NULL,
    featured_image VARCHAR(255) NULL,
    category VARCHAR(120) NOT NULL DEFAULT 'General',
    status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    published_at DATETIME NULL,
    author_id INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_news_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gallery_albums (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    event_id INT UNSIGNED NULL,
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_gallery_albums_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    CONSTRAINT fk_gallery_albums_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gallery_images (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    album_id INT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    caption TEXT NULL,
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    uploaded_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_gallery_images_album FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE CASCADE,
    CONSTRAINT fk_gallery_images_uploaded_by FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documents (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    entity_type ENUM('beneficiary','donor','program','project','staff','donation','general') NULL,
    entity_id INT UNSIGNED NOT NULL,
    document_type VARCHAR(80) NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(120) NULL,
    file_size INT UNSIGNED NULL,
    storage_path VARCHAR(500) NOT NULL,
    visibility ENUM('private','internal','owner_only','public') NOT NULL DEFAULT 'private',
    status ENUM('active','archived','deleted') NOT NULL DEFAULT 'active',
    description TEXT NULL,
    uploaded_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_documents_uploaded_by FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE id_cards (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    card_no VARCHAR(30) NOT NULL UNIQUE,
    person_type ENUM('beneficiary','staff','donor','volunteer','other') NULL,
    person_id INT UNSIGNED NOT NULL,
    gof_id VARCHAR(30) NOT NULL,
    issue_date DATE NOT NULL,
    expiry_date DATE NULL,
    status ENUM('active','expired','revoked','replaced') NOT NULL DEFAULT 'active',
    generated_by INT UNSIGNED NULL,
    reprint_count INT UNSIGNED NOT NULL DEFAULT 0,
    current_token_id INT UNSIGNED NULL,
    pdf_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_id_cards_generated_by FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_id_cards_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE id_card_tokens (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_card_id INT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    issued_by INT UNSIGNED NULL,
    issued_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL,
    revoked TINYINT(1) NOT NULL DEFAULT 0,
    revoked_by INT UNSIGNED NULL,
    revoked_at DATETIME NULL,
    revoke_reason TEXT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_id_card_tokens_token (token),
    CONSTRAINT fk_id_card_tokens_id_card FOREIGN KEY (id_card_id) REFERENCES id_cards(id) ON DELETE CASCADE,
    CONSTRAINT fk_id_card_tokens_issued_by FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_id_card_tokens_revoked_by FOREIGN KEY (revoked_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE id_card_history (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_card_id INT UNSIGNED NOT NULL,
    action ENUM('issued','reprinted','revoked','expired','replaced','reactivated','token_revoked') NULL,
    by_user_id INT UNSIGNED NULL,
    reason TEXT NULL,
    action_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_id_card_history_id_card FOREIGN KEY (id_card_id) REFERENCES id_cards(id) ON DELETE CASCADE,
    CONSTRAINT fk_id_card_history_by_user FOREIGN KEY (by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE id_cards
    ADD CONSTRAINT fk_id_cards_current_token FOREIGN KEY (current_token_id) REFERENCES id_card_tokens(id) ON DELETE SET NULL;

CREATE TABLE blocked_ips (
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

CREATE TABLE verification_attempts (
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

CREATE TABLE notifications (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    type VARCHAR(80) NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NULL,
    link VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user_read (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_messages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    status ENUM('unread','read','replied','archived') NOT NULL DEFAULT 'unread',
    assigned_to INT UNSIGNED NULL,
    replied_at DATETIME NULL,
    archived_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_contact_messages_assigned_to FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE volunteer_applications (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    skills TEXT NULL,
    area_of_interest VARCHAR(255) NULL,
    availability VARCHAR(255) NULL,
    motivation TEXT NULL,
    status ENUM('pending','reviewed','approved','rejected','contacted') NOT NULL DEFAULT 'pending',
    reviewed_by INT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    notes TEXT NULL,
    contacted_at DATETIME NULL,
    converted_user_id INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_volunteer_applications_reviewed_by FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_volunteer_applications_converted_user FOREIGN KEY (converted_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    module VARCHAR(80) NULL,
    entity_type VARCHAR(80) NULL,
    entity_id VARCHAR(80) NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_logs_module (module),
    INDEX idx_audit_logs_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    group_name VARCHAR(80) NOT NULL,
    key_name VARCHAR(120) NOT NULL,
    value TEXT NULL,
    type VARCHAR(40) NOT NULL DEFAULT 'string',
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    updated_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_settings_group_key (group_name, key_name),
    CONSTRAINT fk_settings_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE backups (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    storage_path VARCHAR(500) NOT NULL,
    size_bytes BIGINT UNSIGNED NULL,
    status ENUM('created','failed','deleted') NOT NULL DEFAULT 'created',
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_backups_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
