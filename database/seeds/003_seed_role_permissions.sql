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