-- Users
INSERT INTO users (name, email, password) VALUES
    ('Maria Silva', 'maria@email.com', 'hashed_pw_1'),
    ('Joao Pereira', 'joao@email.com', 'hashed_pw_2'),
    ('Daniel Rocha', 'daniel@email.com', 'hashed_pw_3'),
    ('Ana Costa', 'ana@email.com', 'hashed_pw_4'),
    ('Pedro Matos', 'pedro@email.com', 'hashed_pw_5');

-- Trainers
INSERT INTO trainers (name, role, bio, experience_years, certifications, specializations, phone, email) VALUES
    ('Tiago Santos', 'Strength & Conditioning Coach', 'Tiago focuses on functional strength, endurance, and building sustainable workout routines for all experience levels.', 8, 'NASM CPT, Mobility Specialist', 'Strength, HIIT, Functional Fitness', '+351 911 203 145', 'tiago.santos@gym4u.pt'),
    ('Dario Ribeiro', 'Yoga & Mobility Trainer', 'Dario helps members improve posture, flexibility, and recovery through mindful movement and breath-focused practice.', 6, 'RYT 500, Breathwork Coach', 'Yoga, Mobility, Recovery', '+351 912 118 406', 'dario.ribeiro@gym4u.pt'),
    ('Eduardo Amaral', 'Cycling & Cardio Coach', 'Eduardo leads energetic sessions designed to improve cardiovascular endurance and keep members motivated from start to finish.', 5, 'Indoor Cycling Instructor', 'Cycling, Cardio, Endurance', '+351 913 442 781', 'eduardo.amaral@gym4u.pt'),
    ('Adriana Mendes', 'Pilates Instructor', 'Adriana specializes in pilates and body alignment, helping members improve posture, flexibility, and core strength.', 5, 'Pilates Instructor Certification', 'Pilates, Core, Flexibility', '+351 917 000 111', 'adriana.mendes@gym4u.pt'),
    ('Goncalo Pinto', 'Personal Trainer', 'Goncalo specializes in one-on-one coaching, helping members achieve specific goals with structured and progressive plans.', 9, 'Level 4 Personal Trainer', 'Muscle Gain, Fat Loss, Coaching', '+351 914 330 267', 'goncalo.pinto@gym4u.pt'),
    ('David Gomes', 'Pilates Instructor', 'David works with members on core control, posture, flexibility, and body awareness through technique-driven pilates sessions.', 7, 'Clinical Pilates Instructor', 'Pilates, Core, Posture', '+351 915 227 904', 'david.gomes@gym4u.pt'),
    ('Joana Rodrigues', 'Personal Trainer', 'Joana focuses on personalized training plans aimed at toning, fat loss, and overall fitness improvement.', 6, 'Certified Personal Trainer', 'Toning, Fat Loss, Personal Training', '+351 918 000 222', 'joana.rodrigues@gym4u.pt'),
    ('Tiago Rocha', 'HIIT & Functional Coach', 'Tiago delivers high-intensity classes focused on power, conditioning, coordination, and training efficiency.', 4, 'Group Fitness Coach', 'HIIT, Conditioning, Agility', '+351 916 541 382', 'tiago.rocha@gym4u.pt');

-- Classes
INSERT INTO classes (name, description, division, trainer_id) VALUES
    ('Morning Yoga', 'Calm sessions focused on flexibility, breathing, and recovery.', 'Yoga & Mobility', 2),
    ('Deep Stretch', 'Deep stretching for improved range of motion and recovery.', 'Yoga & Mobility', 2),
    ('Spin Power', 'High-energy rides and cardio work to build endurance.', 'Cycling & Cardio', 3),
    ('Cardio Burn', 'Intense cardio session to burn calories and boost fitness.', 'Cycling & Cardio', 3),
    ('Personal Training Sessions', 'Individualized sessions tailored to your exact fitness goals.', 'Personal Training', 5),
    ('HIIT Blast', 'Fast-paced interval blocks to improve stamina and power.', 'HIIT & Conditioning', 1),
    ('Core Strength', 'Core-focused conditioning to improve stability and power.', 'HIIT & Conditioning', 1),
    ('Core Stability+', 'Focused sessions for deeper core strength, control, and flexibility.', 'Advanced Pilates', 4),
    ('Pilates Advanced', 'Advanced pilates for experienced practitioners.', 'Advanced Pilates', 4),
    ('HIIT Express', 'Circuit-based sessions to improve movement and coordination.', 'Functional Training', 8),
    ('Functional Circuit', 'Functional training circuits for real-world strength.', 'Functional Training', 8),
    ('Pilates Flow', 'Technique-focused classes for posture and core control.', 'Pilates & Core', 6),
    ('Core Control', 'Controlled core sessions for balance and posture improvement.', 'Pilates & Core', 6),
    ('Body Toning', 'Goal-focused sessions for toning, fat loss, and body definition.', 'Body Toning', 7),
    ('Fit Shape', 'Shape and tone your body with structured fitness sessions.', 'Body Toning', 7);

-- Class Schedule
INSERT INTO class_schedule (class_id, day, time, duration_minutes, capacity, enrolled) VALUES
    (8,  'Monday',    '17:00', 50, 12, 10),
    (6,  'Monday',    '18:00', 45, 20, 15),
    (1,  'Tuesday',   '08:00', 60, 12,  8),
    (3,  'Tuesday',   '19:00', 50, 15, 12),
    (7,  'Wednesday', '18:00', 45, 20, 18),
    (11, 'Wednesday', '18:30', 50, 18, 10),
    (14, 'Wednesday', '19:00', 45, 14,  8),
    (12, 'Thursday',  '17:30', 55, 12,  6),
    (9,  'Thursday',  '18:30', 55, 12,  7),
    (13, 'Friday',    '17:30', 50, 12, 12),
    (15, 'Friday',    '18:00', 45, 14,  9),
    (5,  'Saturday',  '10:00', 60,  5,  3),
    (10, 'Saturday',  '18:30', 45, 16, 14);

-- Reviews
INSERT INTO reviews (user_id, schedule_id, rating, comment) VALUES
    (1, 3, 4, 'Yoga classes are very relaxing and well structured. Perfect for stress relief after work.'),
    (2, 4, 5, 'Spin Power has amazing energy. The trainer keeps everyone motivated throughout the session.'),
    (3, 8, 5, 'Pilates sessions are excellent for posture and core strength. Very clear instruction from the trainer.'),
    (4, 2, 4, 'HIIT Blast was intense but super fun. Tiago really knows how to push you to your limits!'),
    (5, 12, 5, 'Personal training with Goncalo was exactly what I needed. Very professional and motivating.');