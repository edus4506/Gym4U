<?php
declare(strict_types  = 1);

require_once __DIR__ . '/../database/db.php';

if($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$db = getDatabaseConnection();

$allowed = ['available', 'maintenance', 'unavailable'];
$status = $_GET['status'] ?? 'all';

if(in_array($status, $allowed)) {
    $stmt = $db->prepare("SELECT id, name, quantity, status FROM equipment WHERE status = ? ORDER BY name");
    $stmt->execute([$status]);
} else {
    $stmt = $db->query("SELECT id, name, quantity, status FROM equipment ORDER BY name");
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($stmt->fetchAll());

?>