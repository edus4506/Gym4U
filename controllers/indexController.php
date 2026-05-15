<?php
declare(strict_types = 1);
require_once __DIR__ . '/../database/db.php';

$db = getDatabaseConnection();

$reviews = $db->query("
    SELECT r.rating, r.comment, u.name, c.name AS class_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN class_schedule cs ON r.schedule_id = cs.id
    JOIN classes c ON cs.class_id = c.id
    ORDER BY r.id DESC
    LIMIT 3
")->fetchAll();
?>