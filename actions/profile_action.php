<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../profile.php');
  exit;
}

$db = getDatabaseConnection();

$stmt = $db->prepare('SELECT id, name, username, email, password, role, profile_photo, active FROM users WHERE id = ?');
$stmt->execute([(int)$_SESSION['user_id']]);
$user = $stmt->fetch() ?: null;

if ($user === null) {
  session_destroy();
  header('Location: ../login.php');
  exit;
}

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$profilePhoto = $user['profile_photo'];

if ($name === '' || $username === '') {
  header('Location: ../profile.php?error=empty');
  exit;
}

$passwordHash = $user['password'];
if ($newPassword !== '' || $confirmPassword !== '' || $currentPassword !== '') {
  if ($newPassword === '' || $newPassword !== $confirmPassword || !password_verify($currentPassword, $user['password'])) {
    header('Location: ../profile.php?error=password');
    exit;
  }

  $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
}

if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
  if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK || $_FILES['profile_photo']['size'] > 2 * 1024 * 1024) {
    header('Location: ../profile.php?error=photo');
    exit;
  }

  $tmpName = $_FILES['profile_photo']['tmp_name'];
  $mimeType = mime_content_type($tmpName);
  $extensions = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
  ];

  if (!isset($extensions[$mimeType])) {
    header('Location: ../profile.php?error=photo');
    exit;
  }

  $uploadDir = __DIR__ . '/../images/profiles';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  $filename = 'user-' . (int)$user['id'] . '-' . bin2hex(random_bytes(8)) . '.' . $extensions[$mimeType];
  $destination = $uploadDir . '/' . $filename;

  if (!move_uploaded_file($tmpName, $destination)) {
    header('Location: ../profile.php?error=photo');
    exit;
  }

  $profilePhoto = 'images/profiles/' . $filename;
}

try {
  $stmt = $db->prepare('UPDATE users SET name = ?, username = ?, password = ?, profile_photo = ? WHERE id = ?');
  $stmt->execute([$name, $username, $passwordHash, $profilePhoto, (int)$user['id']]);
} catch (PDOException $exception) {
  header('Location: ../profile.php?error=exists');
  exit;
}

$_SESSION['user_name'] = $name;

header('Location: ../profile.php?success=updated');
exit;
