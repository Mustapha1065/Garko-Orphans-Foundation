# GOF Complete Module List

This document defines the complete functional module inventory for the GOF platform.

---

## 1. Foundation Modules

### 1.1 Authentication
- secure login
- logout
- password reset
- account status checks
- session timeout handling
- failed login tracking
- account lockout and rate limiting

### 1.2 Authorization and RBAC
- roles
- permissions
- role assignment
- per-user permission overrides
- route middleware protection
- policy-based server-side checks

### 1.3 User Management
- user account creation
- account activation and deactivation
- password updates
- profile updates
- user search and filtering

### 1.4 Settings and Configuration
- organization profile settings
- logo and branding
- contact details
- public website settings
- ID card settings
- mail settings placeholders
- currency and general defaults

### 1.5 Notifications
- in-app notifications
- unread and read state
- mark single notification read
- mark all read
- module-based notification linking

### 1.6 Audit Logging
- activity tracking
- security-sensitive action tracking
- filterable audit history
- admin-only access

### 1.7 Backup and Maintenance
- database backup initiation
- backup metadata tracking
- secure backup storage
- backup download restrictions

---

## 2. Staff and Internal Operations Modules

### 2.1 Staff Management
- create staff profiles
- create linked staff user accounts
- assign roles
- assign permission overrides
- activate and deactivate staff
- view staff details
- view staff activity
- generate staff GOF IDs

### 2.2 Dashboard and Analytics
- summary cards
- pending task indicators
- charts and trends
- recent activities
- recent registrations
- recent donations
- quick actions

---

## 3. Beneficiary Modules

### 3.1 Beneficiary Registration
- beneficiary intake form
- photograph upload
- guardian information capture
- emergency contact capture
- initial education data
- supporting document upload
- pending review status

### 3.2 Beneficiary Profile Management
- profile view
- profile edit
- search and filter
- complete internal profile page
- status display
- archive handling

### 3.3 Beneficiary Approval Workflow
- pending queue
- approve or reject actions
- rejection reason capture
- approver tracking
- approval timestamps
- account activation workflow
- GOF ID generation

### 3.4 Beneficiary Status Management
- active
- inactive
- suspended
- transferred
- graduated
- archived
- optional rejected state

### 3.5 Guardian Management
- add guardian
- edit guardian
- emergency flag
- relationship tracking
- guardian contact records

### 3.6 Support and Assistance Management
- education support
- food support
- clothing support
- medical and welfare support
- financial assistance
- school materials
- other support categories
- support history timeline

### 3.7 Education Management
- institution records
- institution type
- class, course, or level
- academic session
- enrollment status
- remarks and performance notes
- school-related documents

### 3.8 Beneficiary Portal
- own profile view
- own approved information only
- own ID card view where enabled
- own notifications
- no access to administrative data

---

## 4. Donor and Donation Modules

### 4.1 Donor Management
- donor registration
- individual donor profiles
- organization donor profiles
- donor search and filters
- donor activation and deactivation
- donor history view
- donor GOF ID generation

### 4.2 Donation Management
- manual donation entry
- donation status management
- donor linking
- program and project linking
- payment method capture
- transaction reference storage
- confirmation workflow
- cancellation workflow
- refund state support

### 4.3 Receipt Management
- receipt generation
- receipt view
- receipt print
- donor access to own receipts

### 4.4 Donor Portal
- own profile
- own donation history
- own receipts
- supported programs visibility
- own notifications

---

## 5. Program and Project Modules

### 5.1 Program Management
- create program
- edit program
- assign manager
- define objectives
- define dates
- define status
- define budget
- public visibility control

### 5.2 Project Management
- create project
- link project to program optionally
- assign manager
- define milestones and status
- define budget and timeline
- public visibility control

### 5.3 Program Beneficiary Assignment
- assign beneficiaries to programs
- view beneficiary participation by program
- track join history

### 5.4 Project Beneficiary Assignment
- assign beneficiaries to projects
- track participation

---

## 6. Content and Public Engagement Modules

### 6.1 Public Website Pages
- home
- about
- mission
- vision
- objectives
- programs
- projects
- beneficiary support
- events
- news
- gallery
- donate
- volunteer
- contact
- FAQ
- privacy policy
- terms

### 6.2 Event Management
- event creation
- event editing
- event scheduling
- event visibility
- event publication state
- event completion tracking

### 6.3 News Management
- article drafting
- article editing
- article publishing
- article archiving
- category tagging
- featured image support

### 6.4 Gallery Management
- album creation
- image uploads
- captions
- event association
- publish and unpublish states
- sorting

### 6.5 Contact Management
- public message capture
- message status tracking
- read, replied, archived states
- internal review queue

### 6.6 Volunteer Application Management
- public volunteer application form
- skill capture
- availability capture
- motivation capture
- review workflow
- approve and reject flow
- future conversion to account support

---

## 7. Documents and Identity Modules

### 7.1 Document Management
- secure document upload
- document metadata
- owner and entity association
- permission-controlled download
- document deletion and archive rules
- private storage defaults

### 7.2 ID Card Management
- generate cards for beneficiaries
- generate cards for staff
- generate cards for donors
- card status handling
- expiry handling
- revoke and replace actions
- print and PDF generation

### 7.3 ID Card History
- issuance history
- reprint history
- replacement history
- revocation history
- action logging

### 7.4 QR Verification
- secure verification token resolution
- privacy-safe public verification page
- verification attempt logging
- token revocation
- suspicious access monitoring

---

## 8. Reporting Modules

### 8.1 Operational Reports
- beneficiary reports
- staff reports
- donor reports
- donation reports
- support reports
- education reports
- program reports
- project reports
- event reports
- ID card reports
- volunteer reports

### 8.2 Analytics
- beneficiaries by gender
- beneficiaries by age group
- beneficiaries by status
- donations over time
- donations by program
- support distribution
- pending approvals
- event counts

### 8.3 Export and Print
- print-friendly reports
- PDF export where implemented

---

## 9. Future-Ready Modules

These are not core launch requirements but must be supported by the architecture.

- volunteer accounts
- payment gateway integration
- SMS notifications
- email campaign tools
- public fundraising campaigns
- mobile app integration
- multi-branch support
- API integrations
- multilingual support
