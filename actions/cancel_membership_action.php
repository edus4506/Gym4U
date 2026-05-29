<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../cancellations.php');
  exit;
}

csrf_verify();

$db = getDatabaseConnection();

$stmt = $db->prepare('SELECT id, password, role, subscription_plan FROM users WHERE id = ?');
$stmt->execute([(int)$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'member') {
  header('Location: ../cancellations.php');
  exit;
}

if ($user['subscription_plan'] === 'none') {
  header('Location: ../cancellations.php');
  exit;
}

$password = $_POST['password'] ?? '';

if ($password === '' || !password_verify($password, $user['password'])) {
  header('Location: ../cancellations.php?cancel-membership=1&error=password');
  exit;
}

$schedules = $db->prepare('SELECT schedule_id FROM enrollments WHERE user_id = ?');
$schedules->execute([(int)$user['id']]);
$scheduleIds = array_column($schedules->fetchAll(), 'schedule_id');

try {
  $db->beginTransaction();

  foreach ($scheduleIds as $scheduleId) {
    $db->prepare('UPDATE class_schedule SET enrolled = MAX(enrolled - 1, 0) WHERE id = ?')
      ->execute([(int)$scheduleId]);
  }

  $db->prepare('DELETE FROM enrollments WHERE user_id = ?')->execute([(int)$user['id']]);

  $db->prepare('DELETE FROM waitlist WHERE user_id = ?')->execute([(int)$user['id']]);

  $db->prepare('UPDATE users SET subscription_plan = ? WHERE id = ?')
    ->execute(['none', (int)$user['id']]);

  $db->commit();
  
} catch (Throwable $e) {
  $db->rollBack();
  header('Location: ../cancellations.php?error=cancel');
  exit;
}

header('Location: ../profile.php?success=membership-cancelled');
exit;
