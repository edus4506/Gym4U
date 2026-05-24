<?php
declare(strict_types = 1);
require_once __DIR__ . '/../database/db.php';

session_start();

$db = getDatabaseConnection();

$reviews = $db->query("
    SELECT r.rating, r.comment, u.name, c.name AS class_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN class_schedule cs ON r.schedule_id = cs.id
    JOIN classes c ON cs.class_id = c.id
    ORDER BY r.id DESC
    LIMIT 3
")->fetchAll();

$memberEnrollments = [];
$reviewableScheduleIds = [];

if(isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'member') {
    $stmt= $db->prepare("
        SELECT cs.id, c.name AS class_name, cs.day, cs.time
        FROM enrollments e
        JOIN class_schedule cs ON e.schedule_id = cs.id
        JOIN classes c ON cs.class_id = c.id
        WHERE e.user_id = ?
    ");
    $stmt->execute([(int)$_SESSION['user_id']]);
    $memberEnrollments = $stmt->fetchAll();

    $dayOrder = [
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6,
        'Sunday' => 7,
    ];
    $now = new DateTimeImmutable('now');
    $currentDayNumber = (int)$now->format('N');
    $currentTime = $now->format('H:i');

    foreach ($memberEnrollments as $slot) {
        $slotDayNumber = $dayOrder[$slot['day']] ?? 0;
        $slotTime = (string)$slot['time'];
        $isPast = $slotDayNumber < $currentDayNumber
            || ($slotDayNumber === $currentDayNumber && $slotTime < $currentTime);

        if ($isPast) {
            $reviewableScheduleIds[(int)$slot['id']] = true;
        }
    }
}

?>
