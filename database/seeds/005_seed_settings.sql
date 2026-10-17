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