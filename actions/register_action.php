<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

csrf_verify();

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
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

if (strlen($password) < 4) {
    header('Location: ../register.php?error=weakpassword');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../register.php?error=email');
    exit;
}

$db = getDatabaseConnection();

try {
    $stmt = $db->prepare('INSERT INTO users (name, username, email, phone, password, role, subscription_plan) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $name,
        $username,
        $email,
        $phone !== '' ? $phone : null,
        password_hash($password, PASSWORD_DEFAULT),
        'member',
        'none',
    ]);
} catch (PDOException $exception) {
    header('Location: ../register.php?error=exists');
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = (int)$db->lastInsertId();
$_SESSION['user_name'] = $name;
$_SESSION['user_role'] = 'member';

header('Location: ../subscriptions.php?success=registered');
exit;
