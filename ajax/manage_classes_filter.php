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

$search         = trim($_GET['search'] ?? '');
$categoryFilter = $_GET['category'] ?? '';
$trainerFilter  = $_GET['trainer'] ?? '';

$query = "
    SELECT c.id, c.name, c.description, c.category_id, c.trainer_id,
           cat.name AS category_name, u.name AS trainer_name
    FROM classes c
    JOIN categories cat ON cat.id = c.category_id
    LEFT JOIN trainers t ON t.id = c.trainer_id
    LEFT JOIN users u ON u.id = t.user_id
    WHERE 1=1
";

$params = [];

if ($search !== '') {
    $query   .= ' AND c.name LIKE ?';
    $params[] = "%$search%";
}

if ($categoryFilter !== '') {
    $query   .= ' AND c.category_id = ?';
    $params[] = (int)$categoryFilter;
}

if ($trainerFilter !== '') {
    $query   .= ' AND c.trainer_id = ?';
    $params[] = (int)$trainerFilter;
}

$query .= ' ORDER BY c.name';

$stmt = $db->prepare($query);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
