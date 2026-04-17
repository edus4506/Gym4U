# ltw05g02

# Gym4U

## Project Overview

This project is a gym management website for LTW delivery 1. It presents the core static pages and demonstrates semantic HTML, responsive structure, and organized CSS architecture for a fitness center platform.

## Team

- Dário Amaral
- Eduardo Santos
- Tiago Ribeiro

## First Delivery

This first delivery includes the tree selected pages required for evaluation:

1. Home Page (`index.html`) - Landing page with clear navigation, hero section, value propositions, featured classes, and a call-to-action.
2. Classes Page (`classes.html`) - Classes overview with schedule details, trainer information, and enrollment links.
3. Trainers Page (`trainers.html`) - Trainer profiles with certifications, specializations, experience, and contact options.

### Evaluation Criteria

- Semantic HTML: correct use of structural and semantic elements.
- CSS organization: base/reset rules first, then reusable component styles, then specific page or variant rules.
- File and layer organization: logical separation of concerns across files or CSS layers.
- Responsive design: layout adapts correctly to different screen sizes.

## Features

**All users:**
- [ ] Register a new account.
- [ ] Log in and out.
- [ ] Edit their profile, including name, username, password, and profile photo.

**Members:**
- [ ] Browse the schedule of available fitness classes, filtering by type, trainer, day, or time.
- [ ] Enroll in and cancel enrollment from upcoming classes, subject to capacity limits.
- [ ] View trainer profiles, including their specializations and the classes they teach.
- [ ] Check the current availability of equipment in the main training area.
- [ ] Leave ratings and reviews for classes they have attended.

**Trainers:**
- [ ] Manage their public profile, including bio, specializations, and certifications.
- [ ] View the roster of members enrolled in their classes.
- [ ] Track and manage their assigned class schedule.

**Admins:**
- [ ] Manage members and trainers (create, update, and deactivate accounts).
- [ ] Manage the class catalog (create, edit, and remove classes) and assign trainers to them.
- [ ] Manage equipment in the main training area (add, update availability status, and remove items).
- [ ] Elevate a user to admin status.
- [ ] Oversee and ensure the smooth operation of the entire system.

**Extra:**
- [ ] Something extra (e.g., personal training bookings, membership plans, waitlist, ...).

## Technologies

- HTML5
- CSS3
- Responsive design

## Project Structure

```
├── index.html           # Home page
├── classes.html         # Classes page
├── trainers.html        # Trainers page
├── css/                 # CSS files
│   ├── base.css         # CSS reset, typography, and base styles
│   ├── components.css   # Reusable component styles
│   ├── layout.css       # Layout utilities and spacing
│   ├── responsive.css   # Media queries for mobile/tablet/desktop
│   └── style.css        # Page-specific styles and enhancements
├── images/              # Image assets directory
└── README.md            # Project documentation
```

## Running

    sqlite3 database/database.db < database/database.sql
    php -S localhost:9000

## Credentials

- admin/p4s5w0rd
- member/1234
- trainer/1234
