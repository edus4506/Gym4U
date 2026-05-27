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

$classFilter = $_GET['class'] ?? '';
$dayFilter   = $_GET['day'] ?? '';

$query = "
    SELECT cs.id, cs.class_id, c.name AS class_name, cs.day, cs.time,
           cs.duration_minutes, cs.capacity, cs.enrolled
    FROM class_schedule cs
    JOIN classes c ON c.id = cs.class_id
    WHERE 1=1
";

$params = [];

if ($classFilter !== '') {
    $query   .= ' AND cs.class_id = ?';
    $params[] = (int)$classFilter;
}

$allowedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
if (in_array($dayFilter, $allowedDays, true)) {
    $query   .= ' AND cs.day = ?';
    $params[] = $dayFilter;
}

$query .= "
    ORDER BY CASE cs.day
        WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3
        WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7
    END, cs.time
";

$stmt = $db->prepare($query);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
