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
  header('Location: ../cancelations.php');
  exit;
}

csrf_verify();

$db = getDatabaseConnection();

$stmt = $db->prepare('
  SELECT
    id,
    password,
    role,
    subscription_plan
  FROM users
  WHERE id = ?
');

$stmt->execute([(int)$_SESSION['user_id']]);

$user = $stmt->fetch();

if (!$user || $user['role'] !== 'member') {
  header('Location: ../cancelations.php');
  exit;
}

if ($user['subscription_plan'] === 'none') {
  header('Location: ../cancelations.php');
  exit;
}

$password = $_POST['password'] ?? '';

if (
  $password === '' ||
  !password_verify($password, $user['password'])
) {
  header('Location: ../cancellations.php?cancel-membership=1&error=password');
  exit;
}

$stmt = $db->prepare('
  UPDATE users
  SET subscription_plan = ?
  WHERE id = ?
');

$stmt->execute([
  'none',
  (int)$user['id']
]);

$stmt = $db->prepare('DELETE FROM enrollments WHERE user_id = ?');
$stmt->execute([(int)$user['id']]);

header('Location: ../profile.php?success=membership-cancelled');
exit;
