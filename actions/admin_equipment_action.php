<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: ../profile.php');
  exit;
}

csrf_verify();

$db = getDatabaseConnection();
$mode = $_POST['mode'] ?? '';
$id = (int)($_POST['equipment_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$quantity = max(0, (int)($_POST['quantity'] ?? 0));
$status = $_POST['status'] ?? 'available';

try {
  if (!in_array($status, ['available', 'maintenance', 'unavailable'], true)) {
    throw new RuntimeException('Invalid status');
  }

  if ($mode === 'create' && $name !== '') {
    $stmt = $db->prepare('INSERT INTO equipment (name, quantity, status) VALUES (?, ?, ?)');
    $stmt->execute([$name, $quantity, $status]);
  } elseif ($mode === 'update' && $id > 0 && $name !== '') {
    $stmt = $db->prepare('UPDATE equipment SET name = ?, quantity = ?, status = ? WHERE id = ?');
    $stmt->execute([$name, $quantity, $status, $id]);
  } elseif ($mode === 'delete' && $id > 0) {
    $stmt = $db->prepare('DELETE FROM equipment WHERE id = ?');
    $stmt->execute([$id]);
  } else {
    throw new RuntimeException('Invalid equipment action');
  }
} catch (Throwable $exception) {
  header('Location: ../profile.php?error=equipment');
  exit;
}

header('Location: ../profile.php?success=equipment');
exit;
