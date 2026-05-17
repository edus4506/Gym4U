<?php
session_start();
require_once __DIR__ . '/../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: ../login.php?error=empty');
    exit;
}

$db = getDatabaseConnection();

$stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || (int)$user['active'] !== 1 || !password_verify($password, $user['password'])) {
    header('Location: ../login.php?error=invalid');
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];

header('Location: ../index.php');
exit;
