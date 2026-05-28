<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = getDatabaseConnection();

$stmt = $db->prepare('
    SELECT id, name, role, subscription_plan
    FROM users
    WHERE id = ?
');

$stmt->execute([(int)$_SESSION['user_id']]);

$user = $stmt->fetch() ?: null;

if ($user === null || $user['role'] !== 'member' || $user['subscription_plan'] !== 'none') {
    header('Location: index.php');
    exit;
}

$selectedPlan = $_GET['plan'] ?? null;

$planNames = [
    'offpeak' => 'Off-Peak Plan',
    'classic' => 'Classic Plan',
    'premium' => 'Premium Plan',
];

$messages = [
    'registered' => 'Account created successfully. Welcome to Gym4U!',
];

$successMessage = $messages[$_GET['success'] ?? ''] ?? null;

$errorMessage = null;

if (isset($_GET['error']) && $_GET['error'] === 'password') {
    $errorMessage = 'Incorrect password. Please try again.';
}
