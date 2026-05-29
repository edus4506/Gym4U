<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$db = getDatabaseConnection();

$messages = [
    'success' => [
        'schedule' => 'Schedule changes saved.',
    ],
    'error' => [
        'schedule' => 'Schedule action could not be completed.',
    ],
];

$successMessage = $messages['success'][$_GET['success'] ?? ''] ?? null;
$errorMessage   = $messages['error'][$_GET['error'] ?? ''] ?? null;

$days = [
    'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
];

$classFilter = $_GET['class'] ?? '';
$dayFilter   = $_GET['day'] ?? '';

$adminClasses = $db->query("
    SELECT
        c.id,
        c.name
    FROM classes c
    ORDER BY c.name
")->fetchAll();

$classesWithoutSlots = $db->query("
    SELECT c.id, c.name, u.name AS trainer_name
    FROM classes c
    LEFT JOIN trainers t ON t.id = c.trainer_id
    LEFT JOIN users u ON u.id = t.user_id
    WHERE c.name != 'Gym Review'
      AND c.id NOT IN (SELECT DISTINCT class_id FROM class_schedule)
    ORDER BY c.name
")->fetchAll();

$query = "
    SELECT
        cs.id,
        cs.class_id,
        c.name AS class_name,
        cs.day,
        cs.time,
        cs.duration_minutes,
        cs.capacity,
        cs.enrolled
    FROM class_schedule cs
    JOIN classes c ON c.id = cs.class_id
    WHERE 1=1
";

$params = [];

if ($classFilter !== '') {
    $query   .= ' AND cs.class_id = ?';
    $params[] = (int)$classFilter;
}

if ($dayFilter !== '') {
    $query   .= ' AND cs.day = ?';
    $params[] = $dayFilter;
}

$query .= "
    ORDER BY
        CASE cs.day
            WHEN 'Monday' THEN 1
            WHEN 'Tuesday' THEN 2
            WHEN 'Wednesday' THEN 3
            WHEN 'Thursday' THEN 4
            WHEN 'Friday' THEN 5
            WHEN 'Saturday' THEN 6
            WHEN 'Sunday' THEN 7
        END,
        cs.time
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$adminSchedules = $stmt->fetchAll();
