<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

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
$user = null;

if (isset($_SESSION['user_id'])) {
    $db = getDatabaseConnection();

    $stmt = $db->prepare('
        SELECT id, name, role, subscription_plan
        FROM users
        WHERE id = ?
    ');
    $stmt->execute([(int)$_SESSION['user_id']]);
    $user = $stmt->fetch() ?: null;

    if ($user === null) {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    if ($selectedPlan && $user['subscription_plan'] !== 'none') {
        $errorMessage = 'You already have an active membership.';
        $selectedPlan = null;
    }

    if ($selectedPlan && $user['role'] !== 'member') {
        $errorMessage = 'Only members can subscribe to a plan.';
        $selectedPlan = null;
    }
}
