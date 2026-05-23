<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: index.php');
  exit;
}

$db = getDatabaseConnection();

$messages = [
  'success' => [
    'admin' => 'Admin changes saved.',
  ],
  'error' => [
    'admin' => 'Admin action could not be completed.',
  ],
];

$successMessage = $messages['success'][$_GET['success'] ?? ''] ?? null;
$errorMessage = $messages['error'][$_GET['error'] ?? ''] ?? null;

$search = trim($_GET['search'] ?? '');
$roleFilter = $_GET['role'] ?? '';
$activeFilter = $_GET['active'] ?? '';

$query = 'SELECT id, name, username, email, role, active FROM users WHERE 1=1';

$params = [];

if ($search !== '') {
  $query .= ' AND name LIKE ?';
  $params[] = "%$search%";
}

if ($roleFilter !== '') {
  $query .= ' AND role = ?';
  $params[] = $roleFilter;
}

if ($activeFilter !== '') {
  $query .= ' AND active = ?';
  $params[] = (int)$activeFilter;
}

$query .= ' ORDER BY role, name';

$stmt = $db->prepare($query);

$stmt->execute($params);

$adminUsers = $stmt->fetchAll();
