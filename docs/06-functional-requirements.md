# GOF Functional Requirements Specification

This document defines the canonical functional requirements for the GOF platform.

The goal is to turn the current `GOF2` codebase into a production-ready NGO management system with clear scope, secure workflows, and role-appropriate access.

---

## 1. Authentication and Authorization Requirements

### FR-001 Secure Login
Users shall be able to authenticate with email and password.

**Inputs**
- email
- password

**Rules**
- passwords must be verified using `password_verify()`
- only active accounts may log in
- locked or rate-limited users must be denied access
- successful login must regenerate session ID
- failed attempts must be recorded

**Outputs**
- authenticated session on success
- generic error message on failure

---

### FR-002 Logout
Authenticated users shall be able to log out securely.

**Rules**
- session data must be cleared
- session should be regenerated after logout

---

### FR-003 Password Reset
Users shall be able to reset forgotten passwords using time-limited tokens.

**Inputs**
- email address
- reset token
- new password
- password confirmation

**Rules**
- token must expire after a configured period
- token must become unusable after successful reset
- password must meet minimum strength requirements
- system must avoid email enumeration

---

### FR-004 Session Protection
The platform shall enforce secure session handling.

**Rules**
- inactivity timeout must be enforced
- secure session cookie settings must be used in production
- session fixation protection must be applied on login

---

### FR-005 Role-Based Access Control
The platform shall enforce permission-based access for staff and administrative functions.

**Rules**
- every protected route must validate permission server-side
- UI hiding alone is not sufficient authorization
- super admin bypass may be supported only for the `super_admin` account type
- user permission overrides must take precedence over inherited role permissions where designed

---

## 2. User and Staff Requirements

### FR-006 Staff Account Creation
Super Admin shall be able to create staff accounts.

**Inputs**
- staff profile data
- user account email
- temporary or chosen password
- role assignments
- optional permission overrides

**Rules**
- email must be unique
- staff GOF ID must be unique
- account creation must be auditable

---

### FR-007 Staff Lifecycle Management
Authorized users shall be able to activate, deactivate, suspend, and update staff accounts.

**Rules**
- lifecycle changes must be auditable
- staff access must reflect current account status immediately

---

### FR-008 Staff Activity Visibility
Authorized users shall be able to view staff activity summaries derived from audit logs.

---

## 3. Beneficiary Management Requirements

### FR-009 Beneficiary Registration
Authorized staff shall be able to register new beneficiaries.

**Inputs**
- personal information
- date of birth
- gender
- contact details where applicable
- address, state, and LGA
- photograph
- guardian details
- emergency contact details
- initial education details
- supporting documents

**Rules**
- new registrations must begin in `pending` status
- GOF beneficiary ID should be generated only after approval
- files must be validated by size, type, and MIME
- registration must create an audit log

---

### FR-010 Beneficiary Profile View
Authorized staff shall be able to view complete internal beneficiary profiles.

**Rules**
- profile must include linked guardian, education, support, document, and ID card sections where available
- beneficiary accounts may view only their own restricted self-service profile
- private data must not leak into public pages

---

### FR-011 Beneficiary Search and Filtering
Authorized users shall be able to search and filter beneficiaries by:
- GOF ID
- name
- gender
- date of birth
- age group
- status
- institution
- registration date

**Rules**
- results must be paginated
- search input must be validated and sanitized

---

### FR-012 Beneficiary Approval Workflow
Authorized reviewers shall be able to approve or reject beneficiary registrations.

**Approval rules**
- record `approved_by` and `approved_at`
- set status to `active`
- generate unique GOF beneficiary ID
- optionally create or activate linked beneficiary account
- mark beneficiary eligible for ID card generation

**Rejection rules**
- record `rejected_by` and `rejected_at`
- rejection reason should be captured
- status must reflect rejection or inactive review outcome per final implementation decision

---

### FR-013 Beneficiary Status Management
Authorized users shall be able to update beneficiary status.

**Supported statuses**
- pending
- active
- inactive
- suspended
- transferred
- graduated
- archived
- optional rejected

**Rules**
- destructive deletion of important beneficiary records should be avoided
- status changes must be audited

---

### FR-014 Guardian Management
Authorized staff shall be able to add and update guardian information.

**Rules**
- one beneficiary may have multiple guardian records
- emergency and primary guardian flags may be supported
- guardian information is private

---

## 4. Support and Education Requirements

### FR-015 Support Record Creation
Authorized staff shall be able to record support provided to a beneficiary.

**Fields**
- beneficiary
- support type
- description
- quantity where applicable
- amount where applicable
- currency
- support date
- staff responsible
- program or project
- notes
- supporting documents

**Rules**
- all support records must be tied to a beneficiary
- support history must remain preserved
- support must feed reporting and analytics

---

### FR-016 Support History View
Authorized users shall be able to view complete support history per beneficiary.

**Rules**
- results should support filtering by date and support type
- records should be shown chronologically

---

### FR-017 Education Record Management
Authorized staff shall be able to create and update education records for beneficiaries.

**Fields**
- institution name
- institution type
- class, course, or level
- academic session
- enrollment status
- performance or remarks
- supporting documents

**Rules**
- educational history should support multiple records over time
- records should preserve history rather than overwrite unrelated past data

---

## 5. Donor and Donation Requirements

### FR-018 Donor Registration
Authorized users shall be able to register donors.

**Supported donor types**
- individual
- organization
- expandable later for other categories

**Rules**
- donor GOF ID must be unique
- donor may optionally have a linked user account
- donor records should support activation and deactivation

---

### FR-019 Donor Profile View
Authorized users shall be able to view donor profiles and donation history.

**Rules**
- staff view depends on permission
- donors may view only their own profile and own donation history

---

### FR-020 Donation Recording
Authorized users shall be able to record donations.

**Fields**
- donor
- amount
- currency
- donation date
- payment method
- transaction reference
- purpose
- program or project association
- status
- notes

**Rules**
- one donor can have many donations
- amount must be positive
- transaction reference should be stored where applicable

---

### FR-021 Donation Confirmation
Authorized users shall be able to confirm pending donations.

**Rules**
- only trusted confirmation sources should move a donation to confirmed
- system must record who confirmed the donation and when
- confirmed donations should be eligible for receipt generation

---

### FR-022 Donation Cancellation and Refund States
Authorized users shall be able to cancel donations or mark refunded status where appropriate.

**Rules**
- changes must be audited
- refund logic must not silently overwrite confirmation history

---

### FR-023 Receipt Management
Authorized staff and eligible donors shall be able to view and download donation receipts.

**Rules**
- receipts should only be generated for appropriate donation states, normally confirmed donations
- donors may access only their own receipts

---

## 6. Program and Project Requirements

### FR-024 Program Management
Authorized users shall be able to manage programs.

**Fields**
- program code
- name
- description
- objectives
- dates
- status
- budget
- manager
- public visibility

---

### FR-025 Project Management
Authorized users shall be able to manage projects.

**Rules**
- projects remain a separate first-class entity from programs
- projects may optionally belong to programs

---

### FR-026 Program Beneficiary Assignment
Authorized users shall be able to assign beneficiaries to programs.

**Rules**
- duplicate assignment should be prevented
- assignment history should be retained

---

### FR-027 Project Beneficiary Assignment
Authorized users shall be able to assign beneficiaries to projects.

---

## 7. Content and Public Engagement Requirements

### FR-028 Public Website Pages
The platform shall provide a public-facing website with pages for:
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

---

### FR-029 Home Page Content
The home page shall support:
- hero section
- mission statement
- donation CTA
- volunteer CTA
- impact statistics
- recent programs
- recent activities
- recent news
- event preview
- gallery preview
- contact and footer content

---

### FR-030 Event Management
Authorized users shall be able to create, update, publish, and archive events.

---

### FR-031 News Management
Authorized users shall be able to create, edit, publish, and archive news articles.

**Rules**
- public users must see only published content
- slug-based routing should be supported

---

### FR-032 Gallery Management
Authorized users shall be able to manage albums and gallery images.

**Rules**
- uploaded image files must be validated
- public visibility must be controlled

---

### FR-033 Contact Messages
Public users shall be able to send contact messages.

**Stored fields**
- name
- email
- phone where applicable
- subject
- message
- date
- status

**Administrative actions**
- view
- mark read
- mark replied
- archive

---

### FR-034 Volunteer Applications
Public users shall be able to submit volunteer applications.

**Fields**
- name
- contact details
- skills
- area of interest
- availability
- motivation
- application date
- status

**Administrative actions**
- review
- approve
- reject
- contact
- future conversion to account if required

---

## 8. Document and Identity Requirements

### FR-035 Secure Document Management
Authorized users shall be able to upload and retrieve documents associated with approved modules.

**Rules**
- files must be validated
- storage must be access-controlled
- predictable public URLs must not expose private documents
- downloads must validate ownership or permission

---

### FR-036 GOF ID Generation
The system shall generate unique GOF identifiers for:
- beneficiaries
- staff
- donors
- future volunteers

**Formats**
- `GOF-ORP-00001`
- `GOF-STF-00001`
- `GOF-DON-00001`
- `GOF-VOL-00001`

**Rules**
- IDs must be unique
- generation must be centralized and transaction-safe

---

### FR-037 ID Card Generation
Authorized users shall be able to generate ID cards for staff, beneficiaries, and donors.

**Outputs**
- card record
- printable view
- PDF output
- QR code

**Rules**
- only eligible active records should receive active ID cards
- card generation must be audited

---

### FR-038 ID Card Lifecycle Management
Authorized users shall be able to:
- reprint
- replace
- expire
- revoke
- view card history

**Rules**
- every lifecycle action must be recorded in card history
- revocation reason should be captured

---

### FR-039 QR Verification
The platform shall provide a public verification page for card QR codes.

**Public output may include only**
- verification result
- full name
- category
- GOF ID
- current status

**Public output must not include**
- guardian data
- address
- welfare details
- education details
- documents
- internal notes
- donation internals

---

## 9. Notification, Reporting, and Audit Requirements

### FR-040 Internal Notifications
The platform shall provide internal notifications for relevant events.

**Examples**
- new pending beneficiary
- donation recorded
- new volunteer application
- ID card generated
- expiring ID card
- important announcement

---

### FR-041 Reports
Authorized users shall be able to view reports for:
- beneficiaries
- staff
- donors
- donations
- support
- education
- programs
- projects
- events
- ID cards
- volunteers
- activity summaries

**Rules**
- reports must support filtering
- reports should support print views
- PDF export may be provided where implemented

---

### FR-042 Dashboard Analytics
The admin dashboard shall provide statistics and charts such as:
- total beneficiaries
- active beneficiaries
- pending registrations
- total staff
- total donors
- total donations
- donation totals
- active programs
- upcoming events
- pending volunteer applications

---

### FR-043 Audit Logging
The platform shall maintain immutable audit logs for sensitive actions.

**Audit data should include**
- actor
- action
- module
- record type
- record id
- description
- IP address where available
- user agent where useful
- timestamp

---

## 10. Settings, Backup, and Operational Requirements

### FR-044 System Settings
Super Admin shall be able to manage core settings.

**Settings examples**
- organization name
- logo
- contact information
- email
- phone
- address
- social links
- currency
- ID card settings
- notification preferences
- selected website content settings

---

### FR-045 Backup Support
Where hosting and permissions allow, the system shall support secure backup generation.

**Rules**
- backup files must not be public
- backup actions must be restricted and auditable

---

## 11. Privacy and Security Requirements

### FR-046 Beneficiary Privacy
The platform shall treat beneficiary data as private by default.

### FR-047 Input and Output Safety
The platform shall validate all input and escape all rendered output.

### FR-048 Upload Security
The platform shall validate file extension, MIME type, and file size before accepting uploads.

### FR-049 Authorization Safety
Every protected server-side action shall validate permissions.

### FR-050 Production Error Handling
The platform shall avoid exposing stack traces, SQL details, credentials, or raw PHP errors to ordinary users.

---

## 12. Non-Functional Requirements

### FR-051 Responsiveness
The platform shall function on mobile, tablet, laptop, and desktop devices.

### FR-052 Maintainability
The codebase shall remain modular and avoid giant files, duplicate logic, and mixed concerns.

### FR-053 Accessibility
Forms, navigation, buttons, and tables should be designed with accessible labeling and clear feedback states.

### FR-054 Performance
Large data lists shall use pagination and avoid unnecessary full-table rendering.

### FR-055 Future Readiness
The architecture shall support future additions such as payment gateways, SMS, email services, volunteer accounts, mobile apps, and branch support without redesigning the entire platform.
