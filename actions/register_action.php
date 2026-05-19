<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../register.php');
  exit;
}

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($name === '' || $username === '' || $email === '' || $password === '') {
  header('Location: ../register.php?error=empty');
  exit;
}

if ($password !== $confirmPassword) {
  header('Location: ../register.php?error=password');
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: ../register.php?error=email');
  exit;
}

$db = getDatabaseConnection();

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

session_regenerate_id(true);

$_SESSION['user_id'] = (int)$db->lastInsertId();
$_SESSION['user_name'] = $name;
$_SESSION['user_role'] = 'member';

header('Location: ../profile.php?success=registered');
exit;
