# PHP App

This folder contains the legacy PHP implementation of the Attendance and Leave Management Portal.

## Usage

1. Copy the `php-app/` folder into your PHP web server document root (for example, `htdocs` in XAMPP).
2. Configure the database connection in `db_connect.php`.
3. Open `index.php` in your browser.

## Contents

- `api.php` - API endpoint for form submit and data operations.
- `db_connect.php` - MySQL database connection helper.
- `index.php` - Redirects to the login page.
- `login.php` - Login screen and authentication.
- `register.php` - Registration screen.
- `portal.php` - Main student portal page.
- `logout.php` - Logout handler.
- `css/` - Styling for the PHP pages.
- `images/` - Static assets used by the PHP pages.

## Notes

This implementation is kept for legacy reference and university comparison. It uses plain PHP with form-based authentication and should be run only in a local or development environment.
