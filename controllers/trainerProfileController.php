<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';

$db = getDatabaseConnection();

$trainerId = (int)($_GET['id'] ?? 0);

if($trainerId <= 0) {
    header('Location: ../trainers.php');
    exit;
}

$stmt = $db->prepare("
    SELECT t.id, t.bio, t.experience_years, t.certifications, t.phone, t.title, u.name, u.email, GROUP_CONCAT(DISTINCT cat.name) AS specializations
    FROM trainers t
    JOIN users u ON u.id = t.user_id
    LEFT JOIN trainer_categories tc ON tc.trainer_id = t.id
    LEFT JOIN categories cat ON cat.id = tc.category_id
    WHERE t.id = ?
    GROUP BY t.id
");

$stmt->execute([$trainerId]);

$trainer = $stmt->fetch();

if(!$trainer) {
    header('Location: ../trainers.php');
    exit;
}

$classesStmt = $db->prepare("
    SELECT c.id, c.name, c.description, cat.name AS category
    FROM classes c
    JOIN categories cat ON cat.id = c.category_id
    WHERE c.trainer_id = ?
    ORDER BY cat.name, c.name
");

$classesStmt->execute([$trainerId]);
$trainerClasses = $classesStmt->fetchAll();

$scheduleStmt = $db->prepare("
    SELECT cs.id, c.name AS class_name, cs.day, cs.time, cs.duration_minutes, cs.capacity, cs.enrolled
    FROM class_schedule AS cs
    JOIN classes c ON cs.class_id = c.id
    WHERE c.trainer_id = ?
    ORDER BY CASE cs.day
        WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3
        WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7
    END, cs.time

");

$scheduleStmt->execute([$trainerId]);
$trainerSchedule = $scheduleStmt->fetchAll();

?>