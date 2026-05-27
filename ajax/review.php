<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'method-not-allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'not-logged-in']);
    exit;
}

if (($_SESSION['user_role'] ?? '') !== 'member') {
    http_response_code(403);
    echo json_encode(['error' => 'members-only']);
    exit;
}

csrf_verify();

$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$rating     = (int)($_POST['rating'] ?? 0);
$comment    = trim($_POST['comment'] ?? '');

if ($scheduleId <= 0 || $rating < 1 || $rating > 5 || $comment === '') {
    http_response_code(400);
    echo json_encode(['error' => 'invalid-input']);
    exit;
}

$db = getDatabaseConnection();
$gymReviewScheduleId = 14;

if ($scheduleId !== $gymReviewScheduleId) {
    $stmt = $db->prepare('
        SELECT cs.day, cs.time FROM enrollments e
        JOIN class_schedule cs ON cs.id = e.schedule_id
        WHERE e.user_id = ? AND e.schedule_id = ?
    ');
    $stmt->execute([(int)$_SESSION['user_id'], $scheduleId]);
    $slot = $stmt->fetch();

    if (!$slot) {
        http_response_code(403);
        echo json_encode(['error' => 'not-enrolled']);
        exit;
    }

    $dayOrder = ['Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>7];
    $now = new DateTimeImmutable('now');
    $slotDayNumber = $dayOrder[$slot['day']] ?? 0;
    $finished = $slotDayNumber < (int)$now->format('N') ||
                ($slotDayNumber === (int)$now->format('N') && $slot['time'] < $now->format('H:i'));

    if (!$finished) {
        http_response_code(403);
        echo json_encode(['error' => 'not-attended']);
        exit;
    }
}

$stmt = $db->prepare('INSERT INTO reviews (user_id, schedule_id, rating, comment) VALUES (?, ?, ?, ?)');
$stmt->execute([(int)$_SESSION['user_id'], $scheduleId, $rating, $comment]);

echo json_encode(['success' => true]);
