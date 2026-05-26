<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';

$db = getDatabaseConnection();

$statusFilter = $_GET['status'] ?? 'all';

$allowed = ['available', 'maintenance', 'unavailable'];

if(in_array($statusFilter, $allowed)){
    $stmt = $db->prepare("SELECT id, name, quantity, status FROM equipment WHERE status = ?
    ORDER BY name");
    $stmt->execute([$statusFilter]);
    $equipment = $stmt->fetchAll();
} else {
    $equipment = $db->query("SELECT id, name, quantity, status FROM equipment ORDER BY name")->fetchAll();
}
?>