<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';

$selectedPlan = $_GET['plan'] ?? null;

$planNames = [
  'offpeak' => 'Off-Peak Plan',
  'classic' => 'Classic Plan',
  'premium' => 'Premium Plan',
];

$user = null;

if (isset($_SESSION['user_id'])) {
  $db = getDatabaseConnection();

  $stmt = $db->prepare('
    SELECT
      id,
      name,
      username,
      email,
      role,
      subscription_plan
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
}
