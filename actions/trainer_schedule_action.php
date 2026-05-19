<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['user_role'] ?? '') !== 'trainer') {
  header('Location: ../profile.php');
  exit;
}

csrf_verify();

$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$day = $_POST['day'] ?? '';
$time = $_POST['time'] ?? '';
$duration = max(15, (int)($_POST['duration_minutes'] ?? 45));
$capacity = max(1, (int)($_POST['capacity'] ?? 1));
$validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

if ($scheduleId <= 0 || !in_array($day, $validDays, true) || !preg_match('/^\d{2}:\d{2}$/', $time)) {
  header('Location: ../profile.php?error=class');
  exit;
}

$db = getDatabaseConnection();
$stmt = $db->prepare("
  UPDATE class_schedule
  SET day = ?, time = ?, duration_minutes = ?, capacity = MAX(?, enrolled)
  WHERE id = ?
    AND class_id IN (
      SELECT c.id
      FROM classes c
      JOIN trainers t ON t.id = c.trainer_id
      WHERE t.user_id = ?
    )
");
$stmt->execute([$day, $time, $duration, $capacity, $scheduleId, (int)$_SESSION['user_id']]);

header('Location: ../profile.php?success=class');
exit;
