-- DROP ALL TABLES

PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS class_schedule;
DROP TABLE IF EXISTS classes;
DROP TABLE IF EXISTS trainer_categories;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS equipment;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS trainers;
DROP TABLE IF EXISTS users;

-- CREATE TABLES
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('member', 'trainer', 'admin')) DEFAULT 'member'
);

CREATE TABLE IF NOT EXISTS trainers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL UNIQUE,
    bio TEXT,
    experience_years INTEGER,
    certifications TEXT,
    phone TEXT,
    title TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
  );

  CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id)
  );

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS trainer_categories (
    trainer_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    PRIMARY KEY (trainer_id, category_id),
    FOREIGN KEY (trainer_id) REFERENCES trainers(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS classes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    category_id INTEGER NOT NULL,
    trainer_id INTEGER,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id),
    FOREIGN KEY(category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS class_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    class_id INTEGER NOT NULL,
    day TEXT NOT NULL,
    time TEXT NOT NULL,
    duration_minutes INTEGER NOT NULL,
    capacity INTEGER NOT NULL,
    enrolled INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE IF NOT EXISTS enrollments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    schedule_id INTEGER NOT NULL,
    enrolled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, schedule_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (schedule_id) REFERENCES class_schedule(id)
);

CREATE TABLE IF NOT EXISTS equipment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    quantity INTEGER NOT NULL,
    status TEXT NOT NULL CHECK(status in ('available', 'maintenance', 'unavailable'))
);

CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    schedule_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
    comment TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (schedule_id) REFERENCES class_schedule(id)
);

-- INSERT DATA
-- Users (members) — password: 1234
INSERT INTO users (name, username, email, password, role) VALUES
    ('Maria Silva', 'member', 'maria@email.com', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'member'),
    ('Joao Pereira', 'joao.pereira', 'joao@email.com', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'member'),
    ('Daniel Rocha', 'daniel.rocha', 'daniel@email.com', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'member'),
    ('Ana Costa', 'ana.costa', 'ana@email.com', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'member'),
    ('Pedro Matos', 'pedro.matos', 'pedro@email.com', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'member');

-- Users (trainers) — ids 6 to 13 — password: 1234
INSERT INTO users (name, username, email, password, role) VALUES
    ('Tiago Santos', 'trainer', 'tiago.santos@gym4u.pt', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'trainer'),
    ('Dario Ribeiro', 'dario.ribeiro', 'dario.ribeiro@gym4u.pt', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'trainer'),
    ('Eduardo Amaral', 'eduardo.amaral', 'eduardo.amaral@gym4u.pt', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'trainer'),
    ('Adriana Mendes', 'adriana.mendes', 'adriana.mendes@gym4u.pt', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'trainer'),
    ('Goncalo Pinto', 'goncalo.pinto', 'goncalo.pinto@gym4u.pt', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'trainer'),
    ('David Gomes', 'david.gomes', 'david.gomes@gym4u.pt', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'trainer'),
    ('Joana Rodrigues', 'joana.rodrigues', 'joana.rodrigues@gym4u.pt', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'trainer'),
    ('Tiago Rocha', 'tiago.rocha', 'tiago.rocha@gym4u.pt', '$2y$12$fjBAAB0sRN4GCrVuwBX4HejD3yKzOn9XQqDl7vKyGAD9LhmtPsqvC', 'trainer');

-- Users (admin) — id 14 — password: p4s5w0rd
INSERT INTO users (name, username, email, password, role) VALUES
    ('Admin', 'admin', 'admin@gym4u.pt', '$2y$12$qKqks0PvoeIfaMxgmZ6UlO5xnvLGcsw4Ui/ZH1YKFE7Cw2X/Yw9lK', 'admin');

-- Trainers (extra data, user_id matches users above)
INSERT INTO trainers (user_id, bio, experience_years, certifications, phone, title) VALUES
    (6, 'Tiago focuses on functional strength, endurance, and building sustainable workout routines for all experience levels.', 8, 'NASM CPT, Mobility Specialist', '+351 911 203 145', 'Strength & Conditioning Coach'),
    (7, 'Dario helps members improve posture, flexibility, and recovery through mindful movement and breath-focused practice.', 6, 'RYT 500, Breathwork Coach', '+351 912 118 406', 'Yoga & Mobility Trainer'),
    (8, 'Eduardo leads energetic sessions designed to improve cardiovascular endurance and keep members motivated.', 5, 'Indoor Cycling Instructor', '+351 913 442 781', 'Cycling & Cardio Coach'),
    (9, 'Adriana specializes in pilates and body alignment, helping members improve posture, flexibility, and core strength.', 5, 'Pilates Instructor Certification', '+351 917 000 111', 'Pilates Instructor'),
    (10, 'Goncalo specializes in one-on-one coaching, helping members achieve specific goals with structured progressive plans.', 9, 'Level 4 Personal Trainer', '+351 914 330 267', 'Personal Trainer'),
    (11, 'David works with members on core control, posture, flexibility, and body awareness through pilates sessions.', 7, 'Clinical Pilates Instructor', '+351 915 227 904', 'Pilates Instructor'),
    (12, 'Joana focuses on personalized training plans aimed at toning, fat loss, and overall fitness improvement.', 6, 'Certified Personal Trainer', '+351 918 000 222', 'Personal Trainer'),
    (13, 'Tiago delivers high-intensity classes focused on power, conditioning, coordination, and training efficiency.', 4, 'Group Fitness Coach', '+351 916 541 382', 'HIIT & Functional Coach');

-- Admins
INSERT INTO admins (user_id) VALUES (14);

-- Categories
INSERT INTO categories (name) VALUES
    ('Yoga & Mobility'),
    ('Cycling & Cardio'),
    ('Personal Training'),
    ('HIIT & Conditioning'),
    ('Advanced Pilates'),
    ('Functional Training'),
    ('Pilates & Core'),
    ('Body Toning');

-- Trainer categories
-- Tiago Santos (user_id = 6)
INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 4 FROM trainers WHERE user_id = 6;

INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 6 FROM trainers WHERE user_id = 6;


-- Dario Ribeiro (user_id = 7)
INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 1 FROM trainers WHERE user_id = 7;


-- Eduardo Amaral (user_id = 8)
INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 2 FROM trainers WHERE user_id = 8;


-- Adriana Mendes (user_id = 9)
INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 5 FROM trainers WHERE user_id = 9;


-- Gonçalo Pinto (user_id = 10)
INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 3 FROM trainers WHERE user_id = 10;


-- David Gomes (user_id = 11)
INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 7 FROM trainers WHERE user_id = 11;


-- Joana Rodrigues (user_id = 12)
INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 8 FROM trainers WHERE user_id = 12;


-- Tiago Rocha (user_id = 13)
INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 4 FROM trainers WHERE user_id = 13;

INSERT INTO trainer_categories (trainer_id, category_id)
SELECT id, 6 FROM trainers WHERE user_id = 13;

-- Classes
INSERT INTO classes (name, description, category_id, trainer_id) VALUES
    ('Morning Yoga', 'Calm sessions focused on flexibility, breathing, and recovery.', 1, 2),
    ('Deep Stretch', 'Deep stretching for improved range of motion and recovery.', 1, 2),
    ('Spin Power', 'High-energy rides and cardio work to build endurance.', 2, 3),
    ('Cardio Burn', 'Intense cardio session to burn calories and boost fitness.', 2, 3),
    ('Personal Training Sessions', 'Individualized sessions tailored to your exact fitness goals.', 3, 5),
    ('HIIT Blast', 'Fast-paced interval blocks to improve stamina and power.', 4, 1),
    ('Core Strength', 'Core-focused conditioning to improve stability and power.', 4, 1),
    ('Core Stability+', 'Focused sessions for deeper core strength, control, and flexibility.', 5, 4),
    ('Pilates Advanced', 'Advanced pilates for experienced practitioners.', 5, 4),
    ('HIIT Express', 'Circuit-based sessions to improve movement and coordination.', 6, 8),
    ('Functional Circuit', 'Functional training circuits for real-world strength.', 6, 8),
    ('Pilates Flow', 'Technique-focused classes for posture and core control.', 7, 6),
    ('Core Control', 'Controlled core sessions for balance and posture improvement.', 7, 6),
    ('Body Toning', 'Goal-focused sessions for toning, fat loss, and body definition.', 8, 7),
    ('Fit Shape', 'Shape and tone your body with structured fitness sessions.', 8, 7);

-- Class Schedule
INSERT INTO class_schedule (class_id, day, time, duration_minutes, capacity, enrolled) VALUES
    (8, 'Monday', '17:00', 50, 12, 10),
    (6, 'Monday', '18:00', 45, 20, 15),
    (1, 'Tuesday', '08:00', 60, 12, 8),
    (3, 'Tuesday', '19:00', 50, 15, 12),
    (7, 'Wednesday', '18:00', 45, 20, 18),
    (11, 'Wednesday', '18:30', 50, 18, 10),
    (14, 'Wednesday', '19:00', 45, 14, 8),
    (12, 'Thursday', '17:30', 55, 12, 6),
    (9, 'Thursday', '18:30', 55, 12, 7),
    (13, 'Friday', '17:30', 50, 12, 12),
    (15, 'Friday', '18:00', 45, 14, 9),
    (5, 'Saturday', '10:00', 60, 5, 3),
    (10, 'Saturday', '18:30', 45, 16, 14);

-- Equipment
INSERT INTO equipment (name, quantity, status) VALUES
    ('Treadmill', 8, 'available'),
    ('Stationary Bike', 6, 'available'),
    ('Rowing Machine', 4, 'available'),
    ('Weight Bench', 10, 'available'),
    ('Dumbbells Set', 12, 'available'),
    ('Barbell Rack', 5, 'available'),
    ('Elliptical', 4, 'maintenance'),
    ('Cable Machine', 3, 'available'),
    ('Leg Press', 2, 'available'),
    ('Pull-up Bar', 4, 'available');

-- Reviews
INSERT INTO reviews (user_id, schedule_id, rating, comment) VALUES
    (1, 3, 4, 'Yoga classes are very relaxing and well structured. Perfect for stress relief after work.'),
    (2, 4, 5, 'Spin Power has amazing energy. The trainer keeps everyone motivated throughout the session.'),
    (3, 8, 5, 'Pilates sessions are excellent for posture and core strength. Very clear instruction from the trainer.'),
    (4, 2, 4, 'HIIT Blast was intense but super fun. Tiago really knows how to push you to your limits!'),
    (5, 12, 5, 'Personal training with Goncalo was exactly what I needed. Very professional and motivating.');

PRAGMA foreign_keys = ON;
