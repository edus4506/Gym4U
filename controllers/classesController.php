<?php
declare(strict_types = 1);
require_once __DIR__ . '/../database/db.php';

$db = getDatabaseConnection();

$classes = $db->query("
    SELECT c.id, c.name, c.description, cat.name AS division, u.name AS trainer_name
    FROM classes c
    JOIN trainers t ON c.trainer_id = t.id
    JOIN users u ON t.user_id = u.id
    JOIN categories cat ON c.category_id = cat.id
    ORDER BY cat.name, c.name")->fetchAll();

$schedule = $db->query("
    SELECT cs.id, c.name AS class_name, u.name AS trainer_name,
           cs.day, cs.time, cs.capacity, cs.enrolled
    FROM class_schedule cs
    JOIN classes c ON cs.class_id = c.id
    JOIN trainers t ON c.trainer_id = t.id
    JOIN users u ON t.user_id = u.id
    ORDER BY CASE cs.day
        WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3
        WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7
    END, cs.time
")->fetchAll();

$reviews = $db->query("
    SELECT r.rating, r.comment, u.name, c.name AS class_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN class_schedule cs ON r.schedule_id = cs.id
    JOIN classes c ON cs.class_id = c.id
    ORDER BY r.id DESC
    LIMIT 3
")->fetchAll();

$divisions = [];
foreach ($classes as $class) {
    $divisions[$class['division']][] = $class;
}

$divisionImages = [
    'Yoga & Mobility'     => ['src' => 'images/classes/yoga-class.jpeg',    'alt' => 'Yoga and mobility class area'],
    'Cycling & Cardio'    => ['src' => 'images/classes/cycling-img.png',     'alt' => 'Cycling and cardio training zone'],
    'Personal Training'   => ['src' => 'images/classes/personal-img.jpeg',   'alt' => 'Personal training studio area'],
    'HIIT & Conditioning' => ['src' => 'images/classes/HIIT-img.jpeg',       'alt' => 'HIIT and conditioning class floor'],
    'Advanced Pilates'    => ['src' => 'images/classes/advance_pilates.png', 'alt' => 'Pilates session'],
    'Functional Training' => ['src' => 'images/classes/funct-img.jpeg',      'alt' => 'Functional training equipment section'],
    'Pilates & Core'      => ['src' => 'images/classes/pilates-img.jpeg',    'alt' => 'Pilates and core stability space'],
    'Body Toning'         => ['src' => 'images/classes/toning.png',          'alt' => 'Personal toning training'],
];
?>