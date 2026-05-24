<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

csrf_verify();

$gymReviewScheduleId = 14;
$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$redirectPage = $scheduleId === $gymReviewScheduleId ? '../index.php' : '../classes.php';

if (($_SESSION['user_role'] ?? '') !== 'member') {
  header("Location: $redirectPage?error=members-only");
  exit;
}

$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($scheduleId <= 0 || $rating < 1 || $rating > 5 || $comment === '') {
  header("Location: $redirectPage?error=review");
  exit;
}

$db = getDatabaseConnection();

if ($scheduleId !== $gymReviewScheduleId) {
  $enrolled = $db->prepare('
    SELECT cs.day, cs.time
    FROM enrollments e
    JOIN class_schedule cs ON cs.id = e.schedule_id
    WHERE e.user_id = ? AND e.schedule_id = ?
  ');

  $enrolled->execute([(int)$_SESSION['user_id'], $scheduleId]);

  $enrollmentSlot = $enrolled->fetch();

  if (!$enrollmentSlot) {
    header("Location: $redirectPage?error=not-enrolled");
    exit;
  }

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
  $slotDayNumber = $dayOrder[(string)$enrollmentSlot['day']] ?? 0;
  $slotTime = (string)$enrollmentSlot['time'];

  $finished_class = $slotDayNumber < $currentDayNumber || ($slotDayNumber === $currentDayNumber && $slotTime < $currentTime);

  if (!$finished_class) {
    header("Location: $redirectPage?error=not-attended");
    exit;
  }
}

$stmt = $db->prepare('INSERT INTO reviews (user_id, schedule_id, rating, comment) VALUES (?, ?, ?, ?)');
$stmt->execute([(int)$_SESSION['user_id'], $scheduleId, $rating, $comment]);

header("Location: $redirectPage?success=reviewed");
exit;
