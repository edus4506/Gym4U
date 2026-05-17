<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

if (($_SESSION['user_role'] ?? '') !== 'member') {
  header('Location: ../classes.php?error=members-only');
  exit;
}

$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($scheduleId <= 0 || $rating < 1 || $rating > 5 || $comment === '') {
  header('Location: ../classes.php?error=review');
  exit;
}

$db = getDatabaseConnection();
$enrolled = $db->prepare('SELECT 1 FROM enrollments WHERE user_id = ? AND schedule_id = ?');
$enrolled->execute([(int)$_SESSION['user_id'], $scheduleId]);

if (!$enrolled->fetchColumn()) {
  header('Location: ../classes.php?error=not-enrolled');
  exit;
}

$stmt = $db->prepare('INSERT INTO reviews (user_id, schedule_id, rating, comment) VALUES (?, ?, ?, ?)');
$stmt->execute([(int)$_SESSION['user_id'], $scheduleId, $rating, $comment]);

header('Location: ../classes.php?success=reviewed');
exit;
