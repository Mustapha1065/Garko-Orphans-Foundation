# Garko Orphans Foundation (GOF)

Professional NGO management platform for managing orphans, beneficiaries, staff, donors, donations, programs, events, and more.

## Overview

- **PHP 8+**, **MySQL 8+**
- MVC architecture with Service Layer
- PDO with prepared statements for secure database operations
- Role-Based Access Control (RBAC)
- Secure authentication with password hashing, login throttling, and session management
- ID card generation with QR code verification
- Comprehensive audit logging
- Responsive public-facing website
- Full admin dashboard

## Features

### Authentication & Security
- Secure login with password hashing (bcrypt)
- Account lockout after 5 failed login attempts
- Session timeout and regeneration
- CSRF protection on all forms
- Security headers (X-Frame-Options, CSP, XSS Protection)
- Input sanitization and validation

### Public Website
- Homepage with statistics and program highlights
- About page with mission, vision, and objectives
- Programs page showcasing core initiatives
- Events page for upcoming and past activities
- News page with articles and updates
- Gallery page with filterable images
- Donate page with multiple payment options
- Volunteer page with application form
- Contact page with inquiry form

### Admin Dashboard
- Overview with key statistics and charts
- Staff management with role assignments
- Beneficiary management with approval workflow
- Guardian information tracking
- Education records management
- Support history tracking
- Donor management
- Donation recording and tracking
- Program and project management
- Event management
- ID card generation and QR verification
- News and gallery management
- Notifications system
- Reports generation
- Audit log viewer

### Database
- 25+ tables covering all aspects of NGO management
- Proper indexing for performance
- Foreign key relationships where applicable
- Audit trail for all critical operations

## Getting Started (Development)

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Composer
- Web server (Apache/Nginx) or PHP built-in server

### Installation

1. **Install dependencies:**
```bash
composer install
```

2. **Configure environment:**
```bash
cp .env.example .env
```
Edit `.env` with your database credentials:
```
DB_HOST=localhost
DB_NAME=gof
DB_USER=root
DB_PASS=
APP_ENV=development
APP_URL=http://localhost:8000
APP_SECRET=your-secret-key-here
```

3. **Create database:**
```sql
CREATE DATABASE gof CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. **Run migrations:**
```bash
php scripts/migrate.php
```

5. **Seed initial data:**
```bash
php scripts/seed.php
```

This creates:
- Default roles (Super Admin, Staff, Beneficiary, Donor)
- Default permissions
- Super Admin user (email: `admin@gof.org.ng`, password: `Admin@123`)

6. **Start development server:**
```bash
php -S localhost:8000 -t public
```

7. **Access the application:**
- Public website: `http://localhost:8000`
- Admin login: `http://localhost:8000/login`

**Important:** Change the default super-admin password immediately after first login.

## Project Structure

```
GOF2/
├── app/
│   ├── controllers/       # Request handlers
│   ├── helpers/           # Utility classes (Database, Csrf, FileUpload, etc.)
│   ├── middleware/        # Auth and permission middleware
│   ├── models/            # Database models
│   ├── services/          # Business logic (AuthService, RBACService, etc.)
│   └── views/             # View templates
│       ├── admin/         # Admin panel views
│       ├── auth/          # Authentication views
│       ├── errors/        # Error pages
│       └── public/        # Public website views
├── config/               # Configuration files
├── database/
│   ├── migrations/        # Database migration files
│   └── seeds/             # Database seed files
├── public/                # Web root
│   ├── assets/            # CSS, JS, images
│   └── index.php          # Entry point
├── routes/               # Route definitions
├── scripts/              # Utility scripts (migrate, seed)
├── storage/              # Logs, uploads
└── vendor/               # Composer dependencies
```

## Default Credentials

**Super Admin:**
- Email: `admin@gof.org.ng`
- Password: `Admin@123`

## Security Features

- **Password Hashing:** All passwords hashed using bcrypt
- **CSRF Protection:** All forms include CSRF tokens
- **SQL Injection Prevention:** PDO prepared statements used throughout
- **XSS Prevention:** Output escaping and input sanitization
- **Session Security:** Secure cookies, session regeneration, timeout
- **Rate Limiting:** Login attempt throttling
- **Security Headers:** CSP, X-Frame-Options, XSS Protection
- **Audit Logging:** All critical actions logged

## Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Change `APP_SECRET` to a strong random value
3. Use HTTPS with SSL certificate
4. Configure proper file permissions
5. Set up cron jobs for:
   - Cleanup old login attempts
   - Cleanup old password reset tokens
   - Backup database
6. Configure error logging
7. Disable display of errors
8. Use a production web server (Apache/Nginx)
9. Set up database backups
10. Configure email settings for notifications

## Support

For issues or questions, please contact the development team.

## License

Copyright © 2026 Garko Orphans Foundation. All rights reserved.
