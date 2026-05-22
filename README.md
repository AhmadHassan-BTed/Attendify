# Attendance and Leave Management Portal

A university attendance portal with both modern and legacy implementations.

- **React/Vite frontend** in `react-app/`
- **Node/Express backend** in `react-server/`
- **Legacy PHP version** in `php-app/`
- **Documentation** in `docs/`
- **Database assets** in `database/`
- **Demo media** in `assets/`

---

## ✨ What this project contains

This repo includes the complete academic project for attendance management, including:

- A React-based portal UI
- A Node.js backend for API and MySQL integration
- A legacy PHP portal for comparison
- Database scripts and an Access model
- Project report and supporting documentation

---

## 📁 Repository structure

| Folder | Purpose |
|---|---|
| `react-app/` | React + Vite frontend application |
| `react-server/` | Node.js backend and API server |
| `php-app/` | Legacy PHP attendance portal |
| `docs/` | Project reports and documentation |
| `database/` | SQL scripts, database model, and archives |
| `assets/` | Demo media and support files |

---

## 🚀 Quick start

### Run the React frontend

```bash
cd react-app
npm install
npm run dev
```

Open the browser at `http://localhost:5173`.

### Run the Node backend

```bash
cd react-server
npm install
node index.js
```

The backend runs on `http://localhost:3000` by default.

### Run the PHP version

Place `php-app/` inside your PHP server root (for example, XAMPP `htdocs/`).

Update `php-app/db_connect.php` with your database credentials, then open `php-app/index.php` in your browser.

---

## 🗂️ Database files

The project includes:

- `database/221775_221847_222679_DBProject_Attendance_Portal.sql`
- `database/Attendance_Database_Model.accdb`
- `database/xampp sql data.rar`
- `database/xampp.rar`

If you use MySQL, import the SQL script to recreate the database schema.

---

## 📘 Documentation

See `docs/README.md` for the project report and academic documentation.

---

## ⚠️ Notes

- This project is intended for educational use.
- Update database credentials in `react-server/index.js` before running the backend.
- The PHP version is legacy and is provided for reference.

---

## 📄 License

This project is released under the MIT License. See `LICENSE` for details.
