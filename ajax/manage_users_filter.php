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
$roleFilter   = $_GET['role'] ?? '';
$activeFilter = $_GET['active'] ?? '';

$query  = 'SELECT id, name, username, email, role, active FROM users WHERE 1=1';
$params = [];

if ($search !== '') {
    $query   .= ' AND name LIKE ?';
    $params[] = "%$search%";
}

$allowedRoles = ['member', 'trainer', 'admin'];
if (in_array($roleFilter, $allowedRoles, true)) {
    $query   .= ' AND role = ?';
    $params[] = $roleFilter;
}

if ($activeFilter === '0' || $activeFilter === '1') {
    $query   .= ' AND active = ?';
    $params[] = (int)$activeFilter;
}

$query .= ' ORDER BY role, name';

$stmt = $db->prepare($query);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
