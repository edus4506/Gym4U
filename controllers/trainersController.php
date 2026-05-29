<?php
declare(strict_types = 1);

require_once __DIR__ . '/../database/db.php';

$db = getDatabaseConnection();

$categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

$search     = trim($_GET['trainer-search'] ?? '');
$categoryId = (int)($_GET['specialization'] ?? 0);
$experience = $_GET['experience'] ?? '';
$classDay   = $_GET['class-day'] ?? '';

$query = "
    SELECT t.id, t.bio, t.experience_years, t.certifications, t.phone, t.title,
           u.name, u.email,
           GROUP_CONCAT(cat.name, ', ') AS specializations
    FROM trainers t
    JOIN users u ON u.id = t.user_id
    LEFT JOIN trainer_categories tc ON tc.trainer_id = t.id
    LEFT JOIN categories cat ON cat.id = tc.category_id
    WHERE 1=1
";

$params = [];

if ($search !== '') {
    $query   .= ' AND u.name LIKE ?';
    $params[] = "%$search%";
}

if ($categoryId > 0) {
    $query   .= ' AND t.id IN (SELECT trainer_id FROM trainer_categories WHERE category_id = ?)';
    $params[] = $categoryId;
}

if ($experience === '1-3') {
    $query .= ' AND t.experience_years BETWEEN 1 AND 3';
} elseif ($experience === '4-6') {
    $query .= ' AND t.experience_years BETWEEN 4 AND 6';
} elseif ($experience === '7-plus') {
    $query .= ' AND t.experience_years >= 7';
}

if ($classDay !== '') {
    $query   .= ' AND t.id IN (
        SELECT tr.id FROM trainers tr
        JOIN classes c ON c.trainer_id = tr.id
        JOIN class_schedule cs ON cs.class_id = c.id
        WHERE LOWER(cs.day) = ?
    )';
    $params[] = strtolower($classDay);
}

$query .= ' GROUP BY t.id ORDER BY u.name';

$stmt = $db->prepare($query);
$stmt->execute($params);
$trainers = $stmt->fetchAll();

$classesPerTrainer = [];
foreach ($db->query("
    SELECT c.trainer_id, GROUP_CONCAT(c.name, ', ') AS class_list
    FROM classes c
    GROUP BY c.trainer_id
")->fetchAll() as $row) {
    $classesPerTrainer[$row['trainer_id']] = $row['class_list'];
}
