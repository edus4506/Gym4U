<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

$user = null;
$errorMessage = null;

if (isset($_SESSION['user_id'])) {
  $db = getDatabaseConnection();

  $stmt = $db->prepare('
    SELECT
      id,
      name,
      email,
      role,
      subscription_plan
    FROM users
    WHERE id = ?
  ');

  $stmt->execute([(int)$_SESSION['user_id']]);

  $user = $stmt->fetch() ?: null;
}
