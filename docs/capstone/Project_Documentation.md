# Barangay Estanza Community Services Portal

## Project Documentation

**Document version:** 1.0  
**Prepared:** 29 October 2026  
**System type:** Role-based web application for barangay community services

## 1. Executive summary

The Barangay Estanza Community Services Portal provides a shared online workspace for residents and barangay staff. Residents can create an account, submit their personal details for administrator review, request certificates, report local problems, and follow request status. Staff can manage resident records, process certificate requests, update problem reports, publish announcements, and view reports. Administrators can also manage user accounts, verify resident registrations, and archive or restore requests.

The application uses Laravel 12 with PHP, MySQL, Blade views, Vite, Tailwind CSS 4, and browser-side JavaScript. The application follows Laravel's model-view-controller structure: routes direct requests to controllers, controllers validate and coordinate operations, Eloquent models persist records, and Blade templates render role-specific pages.

## 2. Problem context

Residents need a clear way to ask for barangay services and report community concerns. Staff need a consistent place to review resident details, update submissions, and find past service activity. The portal addresses these needs by bringing account verification, service requests, problem reports, announcements, and reporting into one role-aware system.

This description states the system's intended use. Any claims about measured time savings, user satisfaction, or operational impact require evidence from an actual evaluation and are not asserted here.

## 3. Objectives

### General objective

Develop a web portal that supports resident registration and verification, certificate requests, community problem reporting, and barangay staff workflows.

### Specific objectives

1. Let residents submit a complete account profile for administrator verification.
2. Let verified residents submit certificate requests and problem reports.
3. Give staff a workspace to review, update, and track submissions.
4. Give administrators tools for role management, account verification, and request archiving.
5. Provide announcements and operational reports in the same portal.
6. Protect access with authentication, role checks, server-side validation, and request throttling.

## 4. Scope and boundaries

### Included

- Sign in, account registration, password reset flow, and account settings.
- Resident profile fields: name, birth date, gender, civil status, purok, address, and phone.
- Pending administrator verification for public resident sign-ups.
- Roles: user (resident), staff, and admin.
- Certificate request types: Barangay Clearance, Certificate of Residency, Certificate of Indigency, and Business Clearance.
- Resident-submitted problem reports with category, description, and location.
- Staff review and status updates for certificate requests and problem reports.
- Resident record management and soft deletion.
- Announcements, dashboard summaries, and reports.
- Soft deletion and restoration for certificate requests and problem reports.

### Boundaries and deployment dependencies

- Real email delivery depends on mail transport configured for the target environment. The local prototype's mail configuration should be checked before demonstrating password reset through a real inbox.
- The application records a certificate request and its status. Do not describe this as automated certificate PDF generation unless that feature is added and demonstrated.
- Production hosting, TLS, backups, retention policy, privacy notice, and authorized access to real resident data require operational setup by the deploying organization.
- Evaluation findings must come from actual user testing. No test results or participant feedback are claimed in this document.

## 5. Users and permissions

| Role | Main capabilities |
| --- | --- |
| User / resident | Manage own account, view community announcements, submit certificate requests and problem reports, and view own submission history after verification. |
| Staff | User capabilities plus resident records, certificate/service request review, problem report handling, announcements, and reports. |
| Admin | Staff capabilities plus account and role management, resident account verification, archive and restore actions for requests and problem reports. |

The role middleware restricts staff areas to staff and administrators. Resident routes use the `user` role and require a verified account. New self-registered users start with role `user` and an unverified state. Administrator review associates the account with a resident record.

## 6. Functional requirements

### Account and verification

- The system accepts sign-up details and validates required values on the server.
- Names accept letters plus permitted spaces, apostrophes, and hyphens. Phone validation accepts 7 to 15 digits.
- New accounts are not allowed into protected resident workflows until verified by an administrator.
- Password confirmation, minimum password length, password reset, sign out, and account settings are available through the authentication and settings flows.

### Certificate requests

- A verified resident chooses a certificate type, supplies a purpose and contact/address details, and submits a request.
- Staff review the applicant and request details and update its status.
- The request lifecycle supports pending, approved, rejected, and issued statuses. Issuing requires approval and a resident link.
- An administrator can archive a request and restore it from the archive view.

### Problem reports

- A verified resident submits a category, subject, description, and optional location and phone.
- Staff review the report and update it to open, in progress, resolved, or rejected, with an optional staff note.
- An administrator can archive a report and restore it from the archive view.

### Other staff functions

- Maintain resident records and active status.
- Draft or publish announcements.
- View dashboard summaries and operational reports.
- Manage account roles and verify new resident accounts as an administrator.

## 7. System architecture

The portal uses a browser-to-server web architecture. The browser loads Blade-rendered pages and Vite-built CSS/JavaScript. Nginx serves public assets and forwards PHP application requests to the Laravel runtime. Laravel routes apply authentication, verification, role, validation, and throttling rules. Controllers use Eloquent models to read and write MySQL records. Mail delivery is an environment-configured service used by the password recovery flow.

See [System_Architecture.svg](diagrams/System_Architecture.svg) for the rendered diagram and [System_Architecture.mmd](diagrams/System_Architecture.mmd) for its editable Mermaid source.

### Main request path

1. A browser sends an HTTPS or local HTTP request to Nginx.
2. Nginx serves built assets directly and forwards application requests to Laravel/PHP.
3. Laravel routes and middleware check sign-in state, role, and account verification.
4. Controllers validate input and apply the requested operation.
5. Eloquent models read or write MySQL data.
6. Laravel returns a Blade page or redirect, which the browser renders.

## 8. Database design

The domain schema centers on users, residents, service requests, problem reports, and announcements. Foreign keys connect submitters, processors, resident records, and announcement authors. Nullable foreign keys allow an account or staff member to be removed without erasing historical submissions; soft-deleted request records remain recoverable.

See [Database_ERD.svg](diagrams/Database_ERD.svg) and the editable [Database_ERD.mmd](diagrams/Database_ERD.mmd). Framework tables such as sessions, cache, jobs, and password reset tokens are infrastructure tables and are omitted from the domain ERD.

### Core tables

| Table | Purpose | Selected fields / relationships |
| --- | --- | --- |
| `users` | Authentication, role, verification state, and submitted resident profile | `email` unique; role; profile details; nullable `resident_id` to `residents.id`; verification timestamps. |
| `residents` | Barangay resident record used by staff and linked service requests | Name, birth date, gender, civil status, purok, address, phone, active flag, soft-delete timestamp. |
| `service_requests` | Certificate/service request and its review history | Nullable `resident_id`, `requester_id`, `processed_by`; type, purpose, status, notes, processing timestamp, soft-delete timestamp. |
| `problem_reports` | Resident-submitted community issue and staff handling details | Nullable `user_id`, `processed_by`; reporter snapshot, category, subject, description, location, status, notes, soft-delete timestamp. |
| `announcements` | Community notice authored by a staff account | Title, body, nullable `created_by`, publication timestamp. |

### Relationships

- A user can be linked to one resident record; a resident record may be linked to multiple users over time if the application workflow allows it.
- A resident can have many service requests.
- A user can submit many service requests and problem reports.
- A staff user can process many requests and problem reports.
- A staff user can author many announcements.

## 9. Security and data handling

- Authentication is required for portal workflows.
- Route middleware checks role and, for resident routes, account verification.
- Laravel request validation runs server-side; client-side input helpers improve feedback but do not replace server validation.
- Login, registration, and password-reset POST routes have request throttles.
- Passwords use Laravel's hashed cast. Password fields are hidden from model serialization.
- Laravel Blade escapes displayed values by default. Forms use CSRF tokens.
- Soft deletes retain user-requested archived items and allow restoration.
- The system stores personal information. Production use should follow the barangay's approved privacy notice, data access policy, retention schedule, and backup procedure.

## 10. Technology and deployment

| Layer | Technology evidenced in project |
| --- | --- |
| Application framework | Laravel 12 |
| Server language | PHP (project requirement `^8.2`; local setup has been reported as PHP 8.5.9) |
| Database | MySQL in the established local environment; Laravel migrations define the schema |
| Web server | Nginx in the established local environment |
| Views | Laravel Blade |
| Frontend build | Vite 7, Tailwind CSS 4, JavaScript |
| Dependencies | Composer and npm |

The project root includes `.env` for local configuration. Never include `.env` values, database passwords, application keys, or real resident data in submitted documentation, screenshots, source control, or demonstration recordings. Set deployment secrets in the target environment and confirm database backups independently.

## 11. Development methodology

The project follows an incremental, feature-based development process:

1. **Requirements and scope:** identify resident, staff, and administrator tasks and define role boundaries.
2. **Design:** map workflows, routes, views, models, relationships, and validation rules before changing the database.
3. **Implementation:** add one workflow at a time using Laravel routes, middleware, controllers, models, migrations, Blade forms, and Vite assets.
4. **Verification:** inspect route access and input validation, review migrations, compile frontend assets, and perform role-based manual walkthroughs. Automated tests exist in the repository; their current pass status must be reported only after they are run.
5. **Review and improvement:** use feedback to fix usability issues and preserve working checkpoints before schema changes.
6. **Deployment preparation:** configure Nginx, PHP, MySQL, mail, TLS, backups, and production access controls for the target environment.

This methodology describes the development approach. It does not claim a completed formal user study or successful production deployment.

## 12. Verification plan

| Area | Verification activity | Evidence to record |
| --- | --- | --- |
| Registration | Submit valid and invalid profiles; confirm pending accounts cannot access protected pages | Screenshots with synthetic data and observed messages |
| Verification | Admin reviews a pending account and links/creates its resident record | Review action and resulting account state |
| Role access | Visit staff/admin pages as each role | Expected access or denial for each route |
| Certificate requests | Submit as verified user, review as staff, transition statuses, archive and restore as admin | Request list states and archive restore result |
| Problem reports | Submit as verified user, update status and notes, archive and restore as admin | Report status and archive restore result |
| Announcements | Save a draft and publish an announcement | Draft/published states and resident visibility |
| Password recovery | Request reset and follow a valid reset link using configured email transport | Reset flow result; verify mail configuration first |
| Data and migrations | Apply migrations to a disposable or backed-up database and inspect schema | Migration output and schema review |
| Browser usability | Check login, signup, dashboard, tables, and forms at desktop and mobile widths | Browser/version, viewport, issues found |

Record actual outcomes, date, environment, and tester. Do not mark a row as passed until someone performs and records the check.

## 13. Known limitations and next steps

- Confirm mail delivery in the deployment environment; local logging of reset notifications does not send external email.
- Add formal test evidence and a real-user evaluation before making performance or satisfaction claims.
- Agree on resident data retention, backups, restore testing, and access audit practices before production use.
- Add certificate document generation or printing only if required and after defining approved templates and authorization.
- Confirm the final hosting environment, TLS, domain, and account provisioning process.

## 14. Demonstration

Use the role-based sequence in [Demo_Guide.md](Demo_Guide.md). Demonstrate with synthetic accounts and records. The guide includes a pre-demo checklist, flow order, expected behavior, and recovery steps.

## 15. Project handover checklist

- [ ] Replace institution, presenter, and adviser fields if required by the school.
- [ ] Confirm the database migration status and independently verify a current database backup.
- [ ] Prepare synthetic resident, staff, and admin accounts.
- [ ] Run the verification plan and record actual results.
- [ ] Confirm password recovery email configuration.
- [ ] Remove personal or secret data from screenshots and presentation materials.
- [ ] Rehearse the demo and keep the application running before the presentation.
