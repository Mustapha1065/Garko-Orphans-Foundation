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