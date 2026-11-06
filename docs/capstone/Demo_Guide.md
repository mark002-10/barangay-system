# Prototype demonstration guide

## Goal

Show how a resident uses the portal and how staff and administrators process the resulting work. Use synthetic accounts and data. Do not show real resident personal information or passwords.

## Before the presentation

1. Start Nginx, PHP, MySQL, and the Laravel application using the project's established local setup.
2. Confirm the database is reachable and the app loads at the intended local URL.
3. Prepare separate synthetic accounts for a verified resident, staff member, and administrator.
4. Ensure the resident account is verified and has a linked resident record if the certificate workflow requires it.
5. Add one sample announcement and one sample request/report, or create them live during the demo.
6. Confirm that role-specific pages open under the matching account.
7. Check password reset transport before demonstrating it. Local mail logging does not deliver to an inbox.
8. Close unrelated tabs and hide browser password suggestions or stored credentials.

## Suggested 8-10 minute flow

### 1. Introduce the system

Explain the three roles: resident, staff, and admin. State that the resident submits their own details, and an administrator reviews the new account before access to resident services.

### 2. Resident registration and verification (2 minutes)

- Open the sign-up page.
- Show required account and resident information fields.
- Point out name and phone input restrictions.
- Submit a synthetic resident profile.
- Show the pending-verification page.

### 3. Administrator review (1-2 minutes)

- Sign in as administrator.
- Open user accounts and review the pending resident details.
- Verify the account and connect it with a resident record.

### 4. Resident services (2 minutes)

- Sign in as the verified resident.
- Open the dashboard and show announcements and request history.
- Submit a certificate request with a clear purpose.
- Submit a problem report with a category and location.
- Show the resident's own request/report lists.

### 5. Staff processing (2 minutes)

- Sign in as staff.
- Open the certificate request list and update a request status with a note.
- Open problem reports, review the report, and update its status.
- Show the resident record, announcement, and reporting modules briefly.

### 6. Administrator archive and restore (1 minute)

- Open the request list as an administrator.
- Archive a synthetic request.
- Open the archive and restore it.
- If time permits, show the same archive flow for a problem report.

### 7. Close

Summarize the process from registration through staff handling. State any features that depend on deployment configuration, especially external email delivery and production hosting.

## Expected behavior

- Newly registered resident accounts wait for administrator verification.
- Unverified residents cannot open protected resident functions.
- Staff can process service requests and reports but do not manage user roles.
- Administrators can verify resident accounts and restore archived requests/reports.
- Invalid submissions display field validation errors and do not save.

## Recovery plan

- If the application becomes unavailable, show the architecture and database diagrams and explain the intended workflow while restarting services.
- If login fails, use a prepared synthetic account and verify its role before the presentation.
- If mail is not configured, skip inbox reset and describe the local reset behavior without claiming delivery.
- If a live record is missing, use a prepared synthetic record; avoid entering a real person's details.
