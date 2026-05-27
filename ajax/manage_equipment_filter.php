<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'method-not-allowed']);
    exit;
}

$db = getDatabaseConnection();

$search       = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$query  = 'SELECT id, name, quantity, status FROM equipment WHERE 1=1';
$params = [];

if ($search !== '') {
    $query   .= ' AND name LIKE ?';
    $params[] = "%$search%";
}

$allowedStatuses = ['available', 'maintenance', 'unavailable'];
if (in_array($statusFilter, $allowedStatuses, true)) {
    $query   .= ' AND status = ?';
    $params[] = $statusFilter;
}

$query .= ' ORDER BY name';

$stmt = $db->prepare($query);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
