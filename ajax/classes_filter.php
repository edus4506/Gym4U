<?php
declare(strict_types=1);
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';
require_once __DIR__ . '/../utils/membership_limits.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

$db = getDatabaseConnection();

$filters = [
    'type'    => trim($_GET['type']    ?? ''),
    'trainer' => trim($_GET['trainer'] ?? ''),
    'day'     => trim($_GET['day']     ?? ''),
    'time'    => trim($_GET['time']    ?? ''),
];

$sql = "
    SELECT cs.id, c.name AS class_name, c.category_id, c.trainer_id, u.name AS trainer_name,
           cs.day, cs.time, cs.capacity, cs.enrolled
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

$timeFilter = ($filters['time'] === 'Any time') ? '' : $filters['time'];

$stmt = $db->prepare($sql);
$stmt->execute([
    ':type_filter'    => $filters['type'],
    ':type_value'     => $filters['type'],
    ':trainer_filter' => $filters['trainer'],
    ':trainer_value'  => $filters['trainer'],
    ':day_filter'     => strtolower($filters['day']),
    ':day_value'      => strtolower($filters['day']),
    ':time_filter'    => $timeFilter,
    ':time_value'     => $timeFilter,
]);
$schedule = $stmt->fetchAll();

$memberEnrollments = [];
$isLoggedIn = isset($_SESSION['user_id']);
$isMember   = ($isLoggedIn && ($_SESSION['user_role'] ?? '') === 'member');

if ($isLoggedIn) {
    $enrollStmt = $db->prepare('SELECT schedule_id FROM enrollments WHERE user_id = ?');
    $enrollStmt->execute([(int)$_SESSION['user_id']]);
    $memberEnrollments = array_flip(array_column($enrollStmt->fetchAll(), 'schedule_id'));

    $waitlistStmt = $db->prepare('SELECT schedule_id FROM waitlist WHERE user_id = ?');
    $waitlistStmt->execute([(int)$_SESSION['user_id']]);
    $memberWaitlist = array_flip(array_column($waitlistStmt->fetchAll(), 'schedule_id'));

    if ($isMember) {
        $planStmt = $db->prepare('SELECT subscription_plan FROM users WHERE id = ?');
        $planStmt->execute([(int)$_SESSION['user_id']]);
        $userPlan      = (string)($planStmt->fetchColumn() ?: 'none');
        $limitReached  = !membership_can_enroll($db, (int)$_SESSION['user_id'], $userPlan);
    } else {
        $limitReached = false;
        $memberWaitlist = [];
    }
}

$result = [];
foreach ($schedule as $slot) {
    $full     = (int)$slot['enrolled'] >= (int)$slot['capacity'];
    $enrolled = isset($memberEnrollments[$slot['id']]);

    $onWaitlist = isset($memberWaitlist[$slot['id']]);

    if (!$isLoggedIn) {
        $action = 'login';
    } elseif (!$isMember) {
        $action = 'members-only';
    } elseif ($enrolled) {
        $action = 'cancel';
    } elseif ($full && $onWaitlist) {
        $action = 'cancel-waitlist';
    } elseif ($full && ($userPlan ?? 'none') === 'premium') {
        $action = 'waitlist';
    } elseif ($full) {
        $action = 'full';
    } elseif ($limitReached) {
        $action = 'limit-reached';
    } else {
        $action = 'enroll';
    }

    $row = [
        'id'           => (int)$slot['id'],
        'class_name'   => $slot['class_name'],
        'trainer_id'   => (int)$slot['trainer_id'],
        'trainer_name' => $slot['trainer_name'],
        'day'          => $slot['day'],
        'time'         => $slot['time'],
        'enrolled'     => (int)$slot['enrolled'],
        'capacity'     => (int)$slot['capacity'],
        'action'       => $action,
    ];

    if (in_array($action, ['enroll', 'cancel', 'waitlist', 'cancel-waitlist'], true)) {
        $row['csrf_token'] = csrf_token();
    }

    $result[] = $row;
}

echo json_encode($result);
