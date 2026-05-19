# ltw05g02

# Gym4U

## Project Overview

Gym4U is a gym management website built for the LTW course at FEUP (2026). It allows members to browse and enrol in fitness classes, check equipment availability, and leave reviews. Trainers manage their profile and schedule. Admins control the whole platform.

## Team

- Dário Amaral - up202405681
- Eduardo Santos - up202406938
- Tiago Ribeiro - up202404941

## Features

**All users:**
- [x] Register a new account.
- [x] Log in and out.
- [x] Edit their profile, including name, username, password, and profile photo.

**Members:**
- [x] Browse the schedule of available fitness classes, filtering by type, trainer, day, or time.
- [x] Enroll in and cancel enrollment from upcoming classes, subject to capacity limits.
- [x] View trainer profiles, including their specializations and the classes they teach.
- [x] Check the current availability of equipment in the main training area.
- [x] Leave ratings and reviews for classes they have attended.

**Trainers:**
- [x] Manage their public profile, including bio, specializations, and certifications.
- [x] View the roster of members enrolled in their classes.
- [x] Track and manage their assigned class schedule.

**Admins:**
- [x] Manage members and trainers (create, update, and deactivate accounts).
- [x] Manage the class catalog (create, edit, and remove classes) and assign trainers to them.
- [x] Manage equipment in the main training area (add, update availability status, and remove items).
- [x] Elevate a user to admin status.
- [x] Oversee and ensure the smooth operation of the entire system.

**Extra:**
- [ ] Something extra (e.g., personal training bookings, membership plans, waitlist, ...).

## Running

    sqlite3 database/database.db < database/database.sql
    php -S localhost:9000

## Credentials

- admin/p4s5w0rd
- member/1234
- trainer/1234

## Website Screenshots

### Home Page
![Home](images/readme/home_readme.png)

### Classes Page
![Classes](images/readme/classes_readme.png)

### Trainers Page
![Trainers](images/readme/trainers_readme.png)

## Project Structure

```
├── index.php              # Home page
├── classes.php            # Classes page
├── trainers.php           # Trainers page
├── equipment.php          # Equipment availability page
├── profile.php            # User profile (member/trainer/admin)
├── login.php              # Login page
├── register.php           # Registration page
├── controllers/           # Data loaders (no HTML output)
├── actions/               # POST handlers (validate, update DB, redirect)
├── utils/
│   └── csrf.php           # CSRF token helpers
├── css/
│   ├── base.css           # CSS reset, typography, and base styles
│   ├── components.css     # Reusable component styles
│   ├── index.css          # Home page styles
│   ├── classes.css        # Classes page styles
│   ├── trainers.css       # Trainers page styles
│   ├── equipment.css      # Equipment page styles
│   └── profile.css        # Profile page styles
├── database/
│   ├── database.sql       # Schema and seed data
│   └── db.php             # PDO connection helper
├── images/                # Image assets
└── README.md              # Project documentation
```
