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
    header('Location: ../subscriptions.php');
    exit;
}

csrf_verify();

$db = getDatabaseConnection();

$stmt = $db->prepare('SELECT id, password, role, subscription_plan FROM users WHERE id = ?');
$stmt->execute([(int)$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'member') {
    header('Location: ../subscriptions.php');
    exit;
}

if ($user['subscription_plan'] !== 'none') {
    header('Location: ../subscriptions.php');
    exit;
}

$password = $_POST['password'] ?? '';
if ($password === '' || !password_verify($password, $user['password'])) {
    header('Location: ../subscriptions.php?plan=' . urlencode($_POST['subscription_plan'] ?? '') . '&error=password');
    exit;
}

$allowedPlans = ['offpeak', 'classic', 'premium'];
$subscriptionPlan = $_POST['subscription_plan'] ?? '';
if (!in_array($subscriptionPlan, $allowedPlans, true)) {
    header('Location: ../subscriptions.php');
    exit;
}

$stmt = $db->prepare('UPDATE users SET subscription_plan = ? WHERE id = ?');
$stmt->execute([$subscriptionPlan, (int)$user['id']]);

header('Location: ../profile.php?success=membership-reactivated');
exit;
