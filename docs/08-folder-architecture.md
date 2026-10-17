# GOF Folder and Project Architecture

This document defines the recommended project structure for the `GOF2` application.

The goal is to keep the existing codebase recognizable while guiding it toward a cleaner modular architecture.

---

## 1. Architectural Direction

The current project already follows a basic MVC-style structure. That foundation should be kept, but organized more deliberately by domain and responsibility.

The folder strategy should support:
- modular development
- easier maintenance
- secure file handling
- future API support
- future portal separation for donor and beneficiary users

---

## 2. Recommended Top-Level Structure

```text
GOF2/
├── app/
│   ├── controllers/
│   ├── models/
│   ├── services/
│   ├── helpers/
│   ├── middleware/
│   ├── validators/
│   ├── policies/
│   └── views/
├── config/
├── database/
│   ├── migrations/
│   ├── seeds/
│   └── schema/
├── docs/
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── vendor/
│   └── index.php
├── routes/
├── scripts/
├── storage/
│   ├── logs/
│   ├── documents/
│   ├── idcards/
│   ├── qr/
│   ├── backups/
│   └── tmp/
├── tests/
├── vendor/
├── composer.json
└── README.md
```

---

## 3. Application Layer Structure

## 3.1 `app/controllers/`
Controllers handle HTTP requests and responses.

### Current-safe direction
The current flat controller layout can remain temporarily.

Examples already present:
- `AdminController.php`
- `AuthController.php`
- `BeneficiariesController.php`
- `DonorsController.php`
- `DonationsController.php`
- `ProgramsController.php`
- `EventsController.php`
- `NewsController.php`
- `GalleryController.php`
- `IdCardsController.php`
- `VerifyController.php`
- `ReportsController.php`

### Target organization
As the codebase grows, controllers can be grouped by area:

```text
app/controllers/
├── admin/
├── auth/
├── public/
├── donor/
├── beneficiary/
└── api/
```

### Recommended controller groups
- `admin/` for dashboard and privileged operations
- `auth/` for login, logout, reset, session actions
- `public/` for public website pages and engagement forms
- `donor/` for donor portal actions
- `beneficiary/` for beneficiary portal actions
- `api/` for future JSON endpoints

---

## 3.2 `app/models/`
Models encapsulate data access and table-level behavior.

### Existing examples
- `User.php`
- `Beneficiary.php`
- `Donation.php`
- `Donor.php`
- `Program.php`
- `Staff.php`
- `Role.php`
- `Permission.php`

### Missing or likely future models
- `Guardian.php`
- `EducationRecord.php`
- `BeneficiarySupport.php`
- `Project.php`
- `Document.php`
- `IdCard.php`
- `IdCardToken.php`
- `Notification.php`
- `AuditLog.php`
- `VolunteerApplication.php`
- `ContactMessage.php`
- `Setting.php`

---

## 3.3 `app/services/`
Services contain business logic and multi-step workflows.

### Existing service direction in current codebase
- `AuthService.php`
- `RBACService.php`
- `AuditService.php`
- `NotificationService.php`
- `DocumentService.php`
- `PdfService.php`
- `QrService.php`
- `TokenService.php`
- `TokenStore.php`
- `SettingsService.php`

### Service design rule
Controllers should call services for workflows such as:
- beneficiary approval
- donation confirmation
- ID card issuance
- QR verification
- document access
- notification creation
- audit logging

---

## 3.4 `app/helpers/`
Helpers provide reusable technical utilities.

Existing helpers include:
- `Router.php`
- `Database.php`
- `Csrf.php`
- `Flash.php`
- `Paginator.php`
- `FileUpload.php`

Helpers should remain small and generic. Domain logic should stay in services.

---

## 3.5 `app/middleware/`
Middleware enforces request rules before controller actions execute.

Existing middleware includes:
- `AuthMiddleware.php`
- `PermissionMiddleware.php`

Likely future additions:
- `GuestMiddleware.php`
- `PortalScopeMiddleware.php`
- `RateLimitMiddleware.php`

---

## 3.6 `app/validators/`
Recommended new directory for input validation rules.

Examples:
- `BeneficiaryValidator.php`
- `DonorValidator.php`
- `DonationValidator.php`
- `StaffValidator.php`
- `ContactValidator.php`
- `VolunteerValidator.php`

This helps avoid bloated controllers.

---

## 3.7 `app/policies/`
Recommended new directory for complex authorization checks beyond simple permission slugs.

Examples:
- `DocumentPolicy.php`
- `BeneficiaryPolicy.php`
- `DonationPolicy.php`
- `IdCardPolicy.php`

Use policies when rules depend on both permission and record ownership or scope.

---

## 3.8 `app/views/`
Views contain the rendered HTML templates.

### Existing structure
The current project already uses:
- `app/views/admin/...`
- `app/views/public/...`
- `app/views/errors/...`
- `app/views/auth/...`

### Recommended target structure
```text
app/views/
├── admin/
├── auth/
├── public/
├── donor/
├── beneficiary/
├── components/
└── errors/
```

### Notes
- shared UI fragments should move into `components/`
- admin layout and public layout should remain separate
- PDF-specific templates may live under `admin/idcards/` or a future `pdf/` view area

---

## 4. Routing Structure

## 4.1 Current state
The project currently uses a single file:
- `routes/web.php`

That is acceptable for now but will become hard to maintain.

## 4.2 Recommended route structure
```text
routes/
├── web.php
├── public.php
├── admin.php
├── donor.php
├── beneficiary.php
└── api.php
```

### Recommended use
- `web.php` composes other route files
- `public.php` contains public pages, contact, volunteer, donate, verify
- `admin.php` contains admin and staff protected actions
- `donor.php` contains donor self-service routes
- `beneficiary.php` contains beneficiary self-service routes
- `api.php` contains future JSON endpoints

---

## 5. Database Structure

## 5.1 `database/migrations/`
This folder should contain only the canonical migration series.

### Important current issue
The project currently has duplicate numbered and dated migration tracks. These should be consolidated.

### Recommended canonical series
```text
database/migrations/
├── 001_create_users.sql
├── 002_create_rbac.sql
├── 003_create_auth_security.sql
├── 004_create_staff.sql
├── 005_create_beneficiaries.sql
├── 006_create_donors_donations.sql
├── 007_create_programs_projects.sql
├── 008_create_content.sql
├── 009_create_documents.sql
├── 010_create_id_cards.sql
├── 011_create_notifications_audit.sql
├── 012_create_contact_volunteers.sql
└── 013_create_settings_backups.sql
```

## 5.2 `database/seeds/`
Recommended canonical seed structure:

```text
database/seeds/
├── 001_seed_roles.sql
├── 002_seed_permissions.sql
├── 003_seed_role_permissions.sql
├── 004_seed_settings.sql
├── 005_seed_super_admin.sql
└── 006_seed_demo_data.sql
```

## 5.3 `database/schema/`
Recommended new folder for reference schema artifacts.

May contain:
- canonical SQL blueprint
- ERD exports
- migration consolidation notes

---

## 6. Public Web Root

## 6.1 `public/`
Contains only web-accessible assets and the front controller.

### Recommended structure
```text
public/
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── vendor/
└── index.php
```

### Important note
Sensitive files should not live in predictable public upload directories if they can be stored privately.

---

## 7. Storage Structure

The `storage/` folder should be used for non-public runtime and generated files.

```text
storage/
├── logs/
├── documents/
├── idcards/
├── qr/
├── backups/
└── tmp/
```

### Usage
- `logs/` for application and error logs
- `documents/` for private uploads
- `idcards/` for generated PDFs
- `qr/` for generated QR assets where needed
- `backups/` for protected backup files
- `tmp/` for temporary generation artifacts

---

## 8. Tests Structure

Recommended test categories:

```text
tests/
├── auth/
├── rbac/
├── beneficiaries/
├── donors/
├── donations/
├── idcards/
├── public/
└── integration/
```

This can start small and grow with the implementation.

---

## 9. Documentation Structure

The `docs/` folder should remain the home for:
- architecture
- modules
- workflows
- RBAC matrix
- database design
- schema blueprint
- implementation notes

This allows the project to be maintained professionally as features grow.

---

## 10. Minimal-Change Migration Path for Current `GOF2`

To avoid destabilizing the project too early:

### Phase 1
- keep current top-level app structure
- keep current controller locations
- keep current route loader
- add missing files and missing controllers

### Phase 2
- consolidate migrations and seeds
- introduce validators and policies
- split route files

### Phase 3
- gradually group controllers and services by domain if helpful
- improve asset and component organization

This phased approach respects the existing project while moving it toward a more maintainable structure.
