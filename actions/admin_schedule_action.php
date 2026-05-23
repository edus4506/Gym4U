<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: ../manage_schedules.php');
  exit;
}

csrf_verify();

$db = getDatabaseConnection();
$mode = $_POST['mode'] ?? '';
$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$classId = (int)($_POST['class_id'] ?? 0);
$day = $_POST['day'] ?? '';
$time = $_POST['time'] ?? '';
$duration = max(15, (int)($_POST['duration_minutes'] ?? 45));
$capacity = max(1, (int)($_POST['capacity'] ?? 1));
$validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

try {
  if ($mode === 'create' || $mode === 'update') {
    if ($classId <= 0 || !in_array($day, $validDays, true) || !preg_match('/^\d{2}:\d{2}$/', $time)) {
      throw new RuntimeException('Invalid schedule');
    }

    if ($mode === 'create') {
      $stmt = $db->prepare('INSERT INTO class_schedule (class_id, day, time, duration_minutes, capacity, enrolled) VALUES (?, ?, ?, ?, ?, 0)');
      $stmt->execute([$classId, $day, $time, $duration, $capacity]);
    } else {
      $stmt = $db->prepare('UPDATE class_schedule SET class_id = ?, day = ?, time = ?, duration_minutes = ?, capacity = MAX(?, enrolled) WHERE id = ?');
      $stmt->execute([$classId, $day, $time, $duration, $capacity, $scheduleId]);
    }
  } elseif ($mode === 'delete' && $scheduleId > 0) {
    $db->beginTransaction();
    $db->prepare('DELETE FROM reviews WHERE schedule_id = ?')->execute([$scheduleId]);
    $db->prepare('DELETE FROM enrollments WHERE schedule_id = ?')->execute([$scheduleId]);
    $db->prepare('DELETE FROM class_schedule WHERE id = ?')->execute([$scheduleId]);
    $db->commit();
  } else {
    throw new RuntimeException('Invalid schedule action');
  }
} catch (Throwable $exception) {
  if ($db->inTransaction()) {
    $db->rollBack();
  }

  header('Location: ../manage_schedules.php?error=class');
  exit;
}

header('Location: ../manage_schedules.php?success=class');
exit;
