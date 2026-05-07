# Hospital San José Appointment Scheduling System

Demo app in pure PHP to schedule appointments from a public view and manage them from a protected panel.

## What includes

- Public page to select date, time and register an appointment.
- Calendar made with HTML, CSS and pure JavaScript.
- Simple PHP API to check availability and reserve.
- Administrative panel with PHP session login.
- Registration of new administrators.
- Cancellation of appointments to free time slots.
- MySQL database with tables `admins` and `appointments`.

## Requirements

- UwAmp open with Apache/MySQL started.
- Project located in `C:\UwAmp\www\my-app`.
- Web browser.

## Installation from scratch

1. Open UwAmp and confirm that Apache and MySQL are running.
2. Open in the browser:

   `http://localhost/my-app/install.php`

3. The installer shows default data:

   - Username: `admin`
   - Password: `admin123`

4. Click **Install demo app**.
5. Access the public app:

   `http://localhost/my-app/`

6. Access the administrative panel:

   `http://localhost/my-app/admin/`

## Database configuration

The connection is in:

`config/database.php`

Default values for UwAmp:

- Host: `localhost`
- Database: `hospital_sanjose_demo`
- User: `root`
- Password: `root`

If your installation uses another password, change `DB_PASS`.

## Demo usage

Public view:

1. Select a date in the calendar.
2. Choose an available time.
3. Enter name, phone and optional email.
4. Confirm the appointment.

Administrative panel:

1. Log in with the admin created in `install.php`.
2. Review registered appointments.
3. Cancel appointments when needed.
4. Create other administrators from **New admin**.

## Note

This app is only a demo. It does not implement advanced security, JWT, complex roles nor production-ready controls.
