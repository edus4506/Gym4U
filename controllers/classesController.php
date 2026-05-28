<?php
declare(strict_types = 1);

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';
require_once __DIR__ . '/../utils/membership_limits.php';

ini_set('display_errors', '1');
error_reporting(E_ALL);

$db = getDatabaseConnection();

$filters = [
    'type'    => trim($_GET['type'] ?? ''),
    'trainer' => trim($_GET['trainer'] ?? ''),
    'day'     => trim($_GET['day'] ?? ''),
    'time'    => trim($_GET['time'] ?? ''),
];

$classes = $db->query("
    SELECT c.id, c.name, c.description, cat.name AS division, u.name AS trainer_name
    FROM classes c
    JOIN trainers t ON c.trainer_id = t.id
    JOIN users u ON t.user_id = u.id
    JOIN categories cat ON c.category_id = cat.id
    ORDER BY cat.name, c.name")->fetchAll();

$scheduleSql = "
    SELECT cs.id, c.name AS class_name, c.category_id, c.trainer_id, u.name AS trainer_name,
           cs.day, cs.time, cs.capacity, cs.enrolled,
           cat.name AS division
    FROM class_schedule cs
    JOIN classes c ON cs.class_id = c.id
    JOIN trainers t ON c.trainer_id = t.id
    JOIN users u ON t.user_id = u.id
    JOIN categories cat ON c.category_id = cat.id
    WHERE (:type_filter = '' OR c.category_id = :type_value)
      AND (:trainer_filter = '' OR c.trainer_id = :trainer_value)
      AND (:day_filter = '' OR LOWER(cs.day) = :day_value)
      AND (:time_filter = '' OR cs.time = :time_value)
    ORDER BY CASE cs.day
        WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3
        WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7
    END, cs.time
";
$scheduleStmt = $db->prepare($scheduleSql);
$timeFilter = $filters['time'] === 'Any time' ? '' : $filters['time'];
$scheduleStmt->execute([
    ':type_filter'    => $filters['type'],
    ':type_value'     => $filters['type'],
    ':trainer_filter' => $filters['trainer'],
    ':trainer_value'  => $filters['trainer'],
    ':day_filter'     => strtolower($filters['day']),
    ':day_value'      => strtolower($filters['day']),
    ':time_filter'    => $timeFilter,
    ':time_value'     => $timeFilter,
]);
$schedule = $scheduleStmt->fetchAll();

$categories         = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$trainersForFilter  = $db->query('SELECT t.id AS id, u.name AS name FROM trainers t JOIN users u ON t.user_id = u.id ORDER BY u.name')->fetchAll();
$equipment          = $db->query('SELECT id, name, quantity, status FROM equipment ORDER BY name')->fetchAll();

$memberEnrollments     = [];
$reviewableScheduleIds = [];
$userPlan              = 'none';
$userEnrolledCount     = 0;
$userMaxClasses        = 0;

if (isset($_SESSION['user_id'])) {
    $enrollmentStmt = $db->prepare('SELECT schedule_id FROM enrollments WHERE user_id = ?');
    $enrollmentStmt->execute([(int)$_SESSION['user_id']]);
    $memberEnrollments = array_flip(array_column($enrollmentStmt->fetchAll(), 'schedule_id'));

    if (($_SESSION['user_role'] ?? '') === 'member') {
        $planStmt = $db->prepare('SELECT subscription_plan FROM users WHERE id = ?');
        $planStmt->execute([(int)$_SESSION['user_id']]);
        $userPlan          = (string)($planStmt->fetchColumn() ?: 'none');
        $userEnrolledCount = count($memberEnrollments);
        $userMaxClasses    = membership_max_classes($userPlan); // null = unlimited
    }

    $dayOrder = [
        'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3,
        'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7,
    ];
    $now              = new DateTimeImmutable('now');
    $currentDayNumber = (int)$now->format('N');
    $currentTime      = $now->format('H:i');

    foreach ($schedule as $slot) {
        if (!isset($memberEnrollments[$slot['id']])) {
            continue;
        }
        $slotDayNumber = $dayOrder[$slot['day']] ?? 0;
        $slotTime      = (string)$slot['time'];
        $isPast        = $slotDayNumber < $currentDayNumber
            || ($slotDayNumber === $currentDayNumber && $slotTime < $currentTime);

        if ($isPast) {
            $reviewableScheduleIds[(int)$slot['id']] = true;
        }
    }
}

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

$successMessages = [
    'enrolled'  => 'Enrollment confirmed.',
    'cancelled' => 'Enrollment cancelled.',
    'reviewed'  => 'Review submitted.',
];
$errorMessages = [
    'full'             => 'That class is already full.',
    'already-enrolled' => 'You are already enrolled in that class.',
    'members-only'     => 'Only members can enroll in classes or leave reviews.',
    'not-enrolled'     => 'You can only review classes you are enrolled in.',
    'not-attended'     => 'You can only review classes you have already attended.',
    'review'           => 'Please choose a class, rating, and comment.',
    'invalid'          => 'That class action could not be completed.',
    'limit-reached'    => 'You have reached the maximum number of classes for your plan.',
];
$successMessage = $successMessages[$_GET['success'] ?? ''] ?? null;
$errorMessage   = $errorMessages[$_GET['error'] ?? ''] ?? null;
