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
    'equipment' => 'Equipment changes saved.',
  ],
  'error' => [
    'equipment' => 'Equipment action could not be completed.',
  ],
];

$successMessage = $messages['success'][$_GET['success'] ?? ''] ?? null;
$errorMessage = $messages['error'][$_GET['error'] ?? ''] ?? null;

$statuses = [
  'available',
  'maintenance',
];

$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$query = "
  SELECT
    id,
    name,
    quantity,
    status
  FROM equipment
  WHERE 1=1
";

$params = [];

if ($search !== '') {
  $query .= ' AND name LIKE ?';
  $params[] = "%$search%";
}

if ($statusFilter !== '') {
  $query .= ' AND status = ?';
  $params[] = $statusFilter;
}

$query .= ' ORDER BY name';

$stmt = $db->prepare($query);

$stmt->execute($params);

$equipment = $stmt->fetchAll();
