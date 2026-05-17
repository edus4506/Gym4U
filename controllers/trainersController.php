<?php
declare(strict_types = 1);
require_once __DIR__ . '/../database/db.php';

session_start();

$db = getDatabaseConnection();

$trainers = $db->query("SELECT t.id, t.bio, t.experience_years, t.certifications, t.phone, t.title, u.name, u.email, GROUP_CONCAT(cat.name, ', ') AS specializations
  FROM trainers t 
  JOIN users u ON u.id = t.user_id 
  LEFT JOIN trainer_categories tc ON tc.trainer_id = t.id
  LEFT JOIN categories cat ON cat.id = tc.category_id
  GROUP BY t.id
  ORDER BY u.name")->fetchAll();

$classesPerTrainer = [];
foreach ($db->query("
    SELECT c.trainer_id, GROUP_CONCAT(c.name, ', ') AS class_list
    FROM classes c
    GROUP BY c.trainer_id
")->fetchAll() as $row) {
    $classesPerTrainer[$row['trainer_id']] = $row['class_list'];
}
?>
