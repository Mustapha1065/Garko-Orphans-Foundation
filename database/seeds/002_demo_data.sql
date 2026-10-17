-- Admin user will be created via scripts/seed.php to properly hash password and generate UUID

-- Demo Beneficiaries
INSERT INTO `beneficiaries` (`gof_id`, `full_name`, `dob`, `gender`, `status`) VALUES
('GOF-BEN-0001', 'Aliyu Musa', '2010-05-12', 'male', 'active'),
('GOF-BEN-0002', 'Fatima Bello', '2012-08-20', 'female', 'active'),
('GOF-BEN-0003', 'Ibrahim Sani', '2015-02-14', 'male', 'pending'),
('GOF-BEN-0004', 'Zainab Umar', '2016-11-30', 'female', 'pending'),
('GOF-BEN-0005', 'Hassan Kabir', '2005-09-05', 'male', 'graduated');

-- Update sequences for beneficiaries
UPDATE `sequences` SET `last_val` = 5 WHERE `name` = 'beneficiary';

-- Demo Donors
INSERT INTO `donors` (`donor_code`, `name`, `donor_type`, `email`, `status`) VALUES
('GOF-DON-0001', 'Abubakar Danladi', 'individual', 'abubakar@example.com', 'active'),
('GOF-DON-0002', 'Kano Relief Foundation', 'foundation', 'contact@krf.org', 'active');

-- Update sequences for donors
UPDATE `sequences` SET `last_val` = 2 WHERE `name` = 'donor';

-- Demo Programs
INSERT INTO `programs` (`program_code`, `name`, `status`, `budget`) VALUES
('PROG-EDU-01', 'Orphan Education Support 2026', 'active', 5000000.00),
('PROG-MED-01', 'Free Medical Outreach', 'planned', 2000000.00);

-- Demo Donations
INSERT INTO `donations` (`donation_ref`, `donor_id`, `amount`, `donation_date`, `payment_method`, `status`, `program_id`) VALUES
('TXN-10001', 1, 50000.00, '2026-08-01', 'bank_transfer', 'confirmed', 1),
('TXN-10002', 1, 10000.00, '2026-08-15', 'cash', 'pending', NULL),
('TXN-10003', 2, 1000000.00, '2026-08-10', 'bank_transfer', 'confirmed', 1);

-- Map donation to program in many-to-many
INSERT INTO `program_donations` (`program_id`, `donation_id`) VALUES
(1, 1),
(1, 3);
