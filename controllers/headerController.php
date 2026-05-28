<?php
declare(strict_types = 1);
session_start();

require_once __DIR__ . '/../database/db.php';

$subscriptionPlan = 'none';
$role = 'none';

if (isset($_SESSION['user_id'])) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare('SELECT subscription_plan, role FROM users WHERE id = ?');
    $stmt->execute([(int)$_SESSION['user_id']]);
    $row = $stmt->fetch();
    $subscriptionPlan = (string)($row['subscription_plan'] ?? 'none');
    $role = (string)($row['role'] ?? 'none');
}
