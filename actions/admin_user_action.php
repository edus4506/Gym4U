<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: ../profile.php');
  exit;
}

$db = getDatabaseConnection();

$mode = $_POST['mode'] ?? '';
$userId = (int)($_POST['user_id'] ?? 0);

try {
  if ($mode === 'create') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'member';

    if ($name === '' || $username === '' || $email === '' || $password === '' || !in_array($role, ['member', 'trainer', 'admin'], true)) {
      throw new RuntimeException('Invalid user');
    }

    $db->beginTransaction();
    $stmt = $db->prepare('INSERT INTO users (name, username, email, password, role, active) VALUES (?, ?, ?, ?, ?, 1)');
    $stmt->execute([$name, $username, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
    $newUserId = (int)$db->lastInsertId();

    if ($role === 'trainer') {
      $trainerStmt = $db->prepare('INSERT INTO trainers (user_id, bio, experience_years, certifications, phone, title) VALUES (?, "", 0, "", "", "Trainer")');
      $trainerStmt->execute([$newUserId]);
    } elseif ($role === 'admin') {
      $adminStmt = $db->prepare('INSERT INTO admins (user_id) VALUES (?)');
      $adminStmt->execute([$newUserId]);
    }

    $db->commit();
  } elseif ($mode === 'update' && $userId > 0) {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'member';
    $active = isset($_POST['active']) ? 1 : 0;

    if ($name === '' || $username === '' || $email === '' || !in_array($role, ['member', 'trainer', 'admin'], true)) {
      throw new RuntimeException('Invalid user');
    }

    $db->beginTransaction();
    $stmt = $db->prepare('UPDATE users SET name = ?, username = ?, email = ?, role = ?, active = ? WHERE id = ?');
    $stmt->execute([$name, $username, $email, $role, $active, $userId]);

    if ($role === 'trainer') {
      $db->prepare('INSERT OR IGNORE INTO trainers (user_id, bio, experience_years, certifications, phone, title) VALUES (?, "", 0, "", "", "Trainer")')->execute([$userId]);
    } elseif ($role === 'admin') {
      $db->prepare('INSERT OR IGNORE INTO admins (user_id) VALUES (?)')->execute([$userId]);
    }

    $db->commit();
  } else {
    throw new RuntimeException('Invalid mode');
  }
} catch (Throwable $exception) {
  if ($db->inTransaction()) {
    $db->rollBack();
  }

  header('Location: ../profile.php?error=admin');
  exit;
}

header('Location: ../profile.php?success=admin');
exit;
