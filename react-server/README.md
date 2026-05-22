# React Server

This folder contains the Node.js Express backend for the React portal.

## Installation

```bash
cd react-server
npm install
```

## Run

```bash
node index.js
```

## Default configuration

The server uses the MySQL connection defined in `index.js`:

- host: `localhost`
- user: `root`
- password: `` (empty)
- database: `air_attendance_portal_byted`

Update these values before running in a different environment.

## Available endpoints

- `GET /` - simple health check.
- `POST /register` - register a new user.
- `POST /login` - login a user.
- `GET /dashboard/getStudents` - fetch student attendance data.
- `GET /dashboard/getTeacherRegisteredCourses` - fetch teacher courses by date.

## Notes

This server is intended for development and educational use. Do not deploy it to production without adding security, validation, and environment configuration.
