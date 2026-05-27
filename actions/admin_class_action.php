<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: ../manage_classes.php');
  exit;
}

csrf_verify();

$db = getDatabaseConnection();
$mode = $_POST['mode'] ?? '';
$classId = (int)($_POST['class_id'] ?? 0);

try {
  if ($mode === 'create' || $mode === 'update') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $trainerId = (int)($_POST['trainer_id'] ?? 0);

    if ($name === '' || $categoryId <= 0 || $trainerId <= 0) {
      throw new RuntimeException('Invalid class');
    }

    if ($mode === 'create') {
      $stmt = $db->prepare('INSERT INTO classes (name, description, category_id, trainer_id) VALUES (?, ?, ?, ?)');
      $stmt->execute([$name, $description, $categoryId, $trainerId]);
    } else {
      $stmt = $db->prepare('UPDATE classes SET name = ?, description = ?, category_id = ?, trainer_id = ? WHERE id = ?');
      $stmt->execute([$name, $description, $categoryId, $trainerId, $classId]);
    }
  } elseif ($mode === 'delete' && $classId > 0) {
    $db->beginTransaction();
    $scheduleIds = $db->prepare('SELECT id FROM class_schedule WHERE class_id = ?');
    $scheduleIds->execute([$classId]);
    $ids = array_column($scheduleIds->fetchAll(), 'id');

    foreach ($ids as $scheduleId) {
      $db->prepare('DELETE FROM reviews WHERE schedule_id = ?')->execute([(int)$scheduleId]);
      $db->prepare('DELETE FROM enrollments WHERE schedule_id = ?')->execute([(int)$scheduleId]);
    }

    $db->prepare('DELETE FROM class_schedule WHERE class_id = ?')->execute([$classId]);
    $db->prepare('DELETE FROM classes WHERE id = ?')->execute([$classId]);
    $db->commit();
  } else {
    throw new RuntimeException('Invalid class action');
  }
} catch (Throwable $exception) {
  if ($db->inTransaction()) {
    $db->rollBack();
  }

  header('Location: ../manage_classes.php?error=class');
  exit;
}

header('Location: ../manage_classes.php?success=class');
exit;
