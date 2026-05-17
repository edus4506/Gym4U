<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/users.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../register.php');
  exit;
}

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($name === '' || $username === '' || $email === '' || $password === '') {
  header('Location: ../register.php?error=empty');
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: ../register.php?error=email');
  exit;
}

$db = getDatabaseConnection();
ensureUserProfilePhotoColumn($db);

try {
  $stmt = $db->prepare('INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)');
  $stmt->execute([
    $name,
    $username,
    $email,
    password_hash($password, PASSWORD_DEFAULT),
    'member',
  ]);
} catch (PDOException $exception) {
  header('Location: ../register.php?error=exists');
  exit;
}

$_SESSION['user_id'] = (int)$db->lastInsertId();
$_SESSION['user_name'] = $name;
$_SESSION['user_role'] = 'member';

header('Location: ../profile.php?success=registered');
exit;
