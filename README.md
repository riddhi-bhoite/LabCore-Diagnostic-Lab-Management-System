# Lab Management System

A PHP-based lab management web application for managing patients, doctors, lab technicians, and administrative tasks.

## Project Overview

This application provides a complete workflow for a medical diagnostic lab:
- Patient registration and login
- Appointment booking and payment
- Lab sample collection and result entry
- Doctor report approval
- Admin management for users, doctors, technicians, tests, bookings, and reports

## Project Structure

- `index.php` - redirects to login page
- `pages/` - HTML UI pages for login, registration, dashboards, and profile features
- `api/` - PHP endpoints for authentication, CRUD operations, booking flow, report handling, payment, and dashboards
- `includes/` - shared database connection and helper utilities
- `assets/` - front-end assets (stylesheets, scripts, images)

## Setup

1. Copy the project to a PHP-enabled web root.
2. Configure MySQL connection in `includes/db.php`:
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
3. Create the database `lab_management` and import the required schema.
4. Open the app in a browser and access `index.php`.

## Authentication

- Login: `api/login.php`
  - POST JSON: `{ "email": "...", "password": "..." }`
  - Returns session data and dashboard redirect URL depending on role.
- Register: `api/register.php`
  - POST JSON for patient registration.
- Logout: `api/logout.php`
  - Ends the session and returns a redirect to the login page.

## Roles and Dashboard Flows

### Admin

Admin users can access the admin dashboard and manage the following:
- `pages/admin-dashboard.html`
  - Overview stats: total bookings, today bookings, revenue, pending reports, active patients
  - Recent bookings
- `pages/admin-doctors.html`
  - Add or edit doctor records via `api/admin-save-doctor.php`
- `pages/admin-technicians.html`
  - Add lab technicians via `api/admin-save-technician.php`
- `pages/admin-tests.html`
  - Add or update tests via `api/admin-save-test.php`
- `pages/admin-patients.html`
  - List patients via `api/admin-get-patients.php`
- `pages/admin-bookings.html`
  - View bookings via `api/admin-get-bookings.php`
  - Update booking status using `api/admin-update-booking.php`
- `pages/admin-reports.html`
  - View reports via `api/admin-get-reports.php`
  - Approve reports via `api/admin-approve-report.php`
- `pages/admin-dashboard.html` data endpoint: `api/admin-dashboard.php`
- Additional admin endpoints:
  - `api/admin-get-doctors.php`
  - `api/admin-get-technicians.php`
  - `api/admin-get-tests.php`
  - `api/admin-delete-person.php`
  - `api/admin-delete-test.php`

### Doctor

Doctor users can access the doctor dashboard and manage reports:
- `pages/doctor-dashboard.html`
  - View doctor stats, bookings, and pending report approvals via `api/doctor-dashboard.php`
- Approve ready lab reports via `api/approve-report.php`
- The doctor role can view report details and approve them from the dashboard.

### Lab Technician

Lab technician users can handle sample collection and result entry:
- `pages/lab-dashboard.html`
  - View pending collections and collected bookings via `api/lab-dashboard.php`
- Record sample collection via `api/collect-sample.php`
- Enter test results and generate reports via `api/enter-results.php`

### Patient

Patient users can register, book appointments, pay, and view reports:
- `pages/register.html`
  - New patient registration via `api/register.php`
- `pages/login.html`
  - Login to any role
- `pages/patient-dashboard.html`
  - View patient stats, bookings, and reports via `api/patient-dashboard.php`
- `pages/book-appointment.html`
  - Book new lab appointments using `api/book-appointment.php`
  - Uses `api/get-doctors.php` and `api/get-tests.php` for selectable doctors/tests
- `pages/payment.html`
  - Pay for completed bookings via `api/make-payment.php`
- `pages/my-bookings.html`
  - List patient bookings via `api/my-bookings.php`
- `pages/my-reports.html`
  - View full report details via `api/my-reports.php`
- `api/cancel-booking.php` allows patients to cancel pending bookings.

## Shared API Endpoints

- `api/get-doctors.php` - returns list of doctors for appointment booking
- `api/get-tests.php` - returns list of available lab tests
- `api/get-booking.php` - fetch booking details for a patient

## Database Notes

The app uses a normalized schema with the following entity patterns:
- `Person` table stores users and roles
- `Patient`, `Doctor`, and `Lab_Technician` tables extend `Person`
- `Test` stores available lab tests
- `Booking` stores appointment reservations
- `Booking_Test` links bookings to tests
- `Payment` stores payment records
- `Sample_Collection` stores sample collection events
- `Test_Report` and `Report_Details` store generated reports and results

## Project Flow

1. User visits `index.php` and is redirected to `pages/login.html`.
2. Patients register using `pages/register.html`, then log in.
3. Authenticated users are redirected to their role-specific dashboard.
4. Patients book appointments, view bookings, and pay when tests complete.
5. Lab technicians collect samples and enter test results.
6. Doctors review reports and approve them.
7. Admins manage doctors, technicians, tests, bookings, patients, and reports.

## Notes

- Sessions are used for role-based access control.
- API responses are JSON and use `respond()` from `includes/helpers.php`.
- The app assumes a local MySQL server and `lab_management` database.
- Passwords are stored securely with bcrypt hashing.

## Recommended Improvements

- Add server-side form validation to pages
- Improve front-end error handling and user feedback
- Harden `Access-Control-Allow-Origin` for production
- Add role-based UI navigation and access restrictions in HTML pages
- Add database schema SQL or migration file for easier setup
