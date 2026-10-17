# GOF Roles and Permissions Matrix

This document defines the canonical access model for the GOF platform.

It separates:
- account types
- staff roles
- permissions

This distinction is important because donors and beneficiaries may have accounts without being treated like staff users.

---

## 1. Account Types

The platform supports these primary account types:

1. `super_admin`
2. `staff`
3. `beneficiary`
4. `donor`

Future-ready:
- `volunteer`

### Notes
- `super_admin` is both an account type and a privileged system identity.
- `staff` users receive access based on assigned roles and permission overrides.
- `beneficiary` and `donor` accounts are limited self-service identities.
- a donation is not an account type.

---

## 2. Access Model

### 2.1 Super Admin
Has unrestricted access to the system.

Can:
- manage users
- manage staff
- manage roles and permissions
- manage beneficiaries, donors, donations, programs, projects, and content
- generate and revoke ID cards
- view reports
- view audit logs
- manage settings and backups

### 2.2 Staff
Access is permission-based.

Typical staff roles:
- Beneficiary Officer
- Welfare Officer
- Education Officer
- Finance Officer
- Program Manager
- Content Manager
- ID Card Officer
- Report Viewer
- Viewer

### 2.3 Beneficiary
Restricted to approved self-service features only.

Can never access:
- other beneficiaries
- donor private information
- audit logs
- internal reports
- admin settings

### 2.4 Donor
Restricted to own data.

Can view:
- own profile
- own donation history
- own receipts
- supported programs visibility where relevant

Can never access:
- private beneficiary records
- staff/admin modules
- audit logs

---

## 3. Canonical Permission Vocabulary

This is the recommended canonical permission list for implementation.

### Core and Security
- `access_admin`
- `manage_users`
- `manage_roles`
- `manage_permissions`
- `manage_settings`
- `view_audit_logs`
- `manage_backups`

### Staff Management
- `view_staff`
- `create_staff`
- `edit_staff`
- `activate_staff`
- `view_staff_activity`

### Beneficiary Management
- `view_beneficiaries`
- `create_beneficiary`
- `edit_beneficiary`
- `approve_beneficiary`
- `reject_beneficiary`
- `archive_beneficiary`
- `change_beneficiary_status`
- `view_beneficiary_history`

### Guardian Management
- `view_guardians`
- `create_guardian`
- `edit_guardian`

### Support and Welfare
- `view_support`
- `create_support`
- `edit_support`
- `approve_support`

### Education
- `view_education`
- `create_education`
- `edit_education`
- `update_education_status`

### Donor Management
- `view_donors`
- `create_donor`
- `edit_donor`
- `activate_donor`
- `view_donor_history`

### Donation Management
- `view_donations`
- `create_donation`
- `confirm_donation`
- `cancel_donation`
- `refund_donation`
- `generate_receipts`

### Programs and Projects
- `view_programs`
- `create_programs`
- `edit_programs`
- `manage_projects`
- `assign_beneficiaries_to_programs`

### Events, News, Gallery, Website Content
- `manage_events`
- `publish_events`
- `manage_news`
- `publish_news`
- `manage_gallery`
- `publish_gallery`
- `manage_website_content`

### Documents
- `upload_documents`
- `download_documents`
- `delete_documents`
- `manage_document_access`

### ID Cards and Verification
- `view_id_cards`
- `generate_id_cards`
- `reissue_id_cards`
- `revoke_id_cards`
- `view_verification_logs`

### Reports
- `view_reports`
- `export_reports`

### Contact, Volunteers, Notifications
- `manage_contacts`
- `manage_volunteers`
- `view_notifications`
- `send_notifications`

### Self-Service
- `view_own_profile`
- `edit_own_profile`
- `view_own_donations`
- `view_own_receipts`
- `view_own_id_card`
- `view_own_notifications`

---

## 4. High-Level Permission Matrix

| Capability | Super Admin | Staff | Beneficiary | Donor |
|---|---|---|---|---|
| Access admin dashboard | Yes | By permission | No | No |
| Manage users and roles | Yes | Rare/limited | No | No |
| Manage staff | Yes | By permission | No | No |
| View beneficiaries | Yes | By permission | No | No |
| View own beneficiary profile | Yes | If relevant | Yes | No |
| Approve beneficiaries | Yes | By permission | No | No |
| Record beneficiary support | Yes | By permission | No | No |
| Manage education records | Yes | By permission | No | No |
| Manage donors | Yes | By permission | No | Own profile only |
| Manage donations | Yes | By permission | No | Own history only |
| View own receipts | Yes | If relevant | No | Yes |
| Manage programs and projects | Yes | By permission | No | No |
| Manage events, news, gallery | Yes | By permission | No | No |
| Download protected documents | Yes | By permission | Own allowed docs only | Own allowed docs only |
| Generate and revoke ID cards | Yes | By permission | No | No |
| View own ID card | Yes | Yes if own | Yes if enabled | Yes if enabled |
| View reports | Yes | By permission | No | No |
| View audit logs | Yes | Limited if assigned | No | No |
| Manage settings and backups | Yes | Very limited | No | No |
| View notifications | Yes | Yes | Yes | Yes |

---

## 5. Recommended Default Staff Roles

## 5.1 Beneficiary Officer
Permissions:
- `access_admin`
- `view_beneficiaries`
- `create_beneficiary`
- `edit_beneficiary`
- `approve_beneficiary`
- `reject_beneficiary`
- `change_beneficiary_status`
- `view_beneficiary_history`
- `view_guardians`
- `create_guardian`
- `edit_guardian`
- `view_support`
- `create_support`
- `view_education`
- `create_education`
- `edit_education`
- `view_notifications`

## 5.2 Welfare Officer
Permissions:
- `access_admin`
- `view_beneficiaries`
- `view_support`
- `create_support`
- `edit_support`
- `approve_support`
- `view_reports`
- `view_notifications`

## 5.3 Education Officer
Permissions:
- `access_admin`
- `view_beneficiaries`
- `view_education`
- `create_education`
- `edit_education`
- `update_education_status`
- `view_notifications`

## 5.4 Finance Officer
Permissions:
- `access_admin`
- `view_donors`
- `create_donor`
- `edit_donor`
- `view_donor_history`
- `view_donations`
- `create_donation`
- `confirm_donation`
- `cancel_donation`
- `refund_donation`
- `generate_receipts`
- `view_reports`
- `export_reports`
- `view_notifications`

## 5.5 Program Manager
Permissions:
- `access_admin`
- `view_programs`
- `create_programs`
- `edit_programs`
- `manage_projects`
- `assign_beneficiaries_to_programs`
- `view_reports`
- `view_notifications`

## 5.6 Content Manager
Permissions:
- `access_admin`
- `manage_events`
- `publish_events`
- `manage_news`
- `publish_news`
- `manage_gallery`
- `publish_gallery`
- `manage_website_content`
- `manage_contacts`
- `manage_volunteers`
- `view_notifications`

## 5.7 ID Card Officer
Permissions:
- `access_admin`
- `view_id_cards`
- `generate_id_cards`
- `reissue_id_cards`
- `revoke_id_cards`
- `view_verification_logs`
- `view_notifications`

## 5.8 Report Viewer
Permissions:
- `access_admin`
- `view_reports`
- `export_reports`
- `view_notifications`

## 5.9 Viewer
Permissions:
- `access_admin`
- `view_beneficiaries`
- `view_donors`
- `view_donations`
- `view_programs`
- `view_reports`
- `view_notifications`

---

## 6. Self-Service Permission Rules

### Beneficiary account defaults
- `view_own_profile`
- `edit_own_profile` only if limited editable fields are allowed
- `view_own_id_card`
- `view_own_notifications`

### Donor account defaults
- `view_own_profile`
- `edit_own_profile`
- `view_own_donations`
- `view_own_receipts`
- `view_own_id_card` where applicable
- `view_own_notifications`

---

## 7. Enforcement Rules

1. hiding UI elements is not sufficient authorization
2. every protected route must be checked server-side
3. every state-changing action must validate CSRF tokens
4. every document download must validate access scope
5. every admin action should be auditable

---

## 8. Notes for Current `GOF2` Implementation

The existing codebase already includes RBAC foundations, but the permission vocabulary is inconsistent across migrations, seeds, routes, and code.

Implementation should standardize on this document's permission names and align:
- route middleware
- seed data
- RBAC service checks
- UI navigation visibility
