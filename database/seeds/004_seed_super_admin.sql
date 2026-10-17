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