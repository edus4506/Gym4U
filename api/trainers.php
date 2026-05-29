<?php
declare(strict_types = 1);

require_once __DIR__ . '/api_helpers.php';

api_require_get();

$db = getDatabaseConnection();

$trainerId = api_positive_int_param('id');
$categoryId = api_positive_int_param('category_id');
$search = api_string_param('search');

$sql = "
  SELECT
    t.id,
    u.name,
    t.title,
    t.bio,
    t.experience_years,
    t.certifications,
    GROUP_CONCAT(DISTINCT cat.name) AS specializations,
    GROUP_CONCAT(DISTINCT c.name) AS classes
  FROM trainers t
  JOIN users u ON u.id = t.user_id
  LEFT JOIN trainer_categories tc ON tc.trainer_id = t.id
  LEFT JOIN categories cat ON cat.id = tc.category_id
  LEFT JOIN classes c ON c.trainer_id = t.id
  WHERE u.active = 1
";

$params = [];

if ($trainerId !== null) {
  $sql .= ' AND t.id = :trainer_id';
  $params[':trainer_id'] = $trainerId;
}

if ($categoryId !== null) {
  $sql .= ' AND t.id IN (SELECT trainer_id FROM trainer_categories WHERE category_id = :category_id)';
  $params[':category_id'] = $categoryId;
}

if ($search !== '') {
  $sql .= ' AND u.name LIKE :search';
  $params[':search'] = '%' . $search . '%';
}

$sql .= ' GROUP BY t.id ORDER BY u.name';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$trainers = $stmt->fetchAll();

foreach ($trainers as &$trainer) {
  $trainer['specializations'] = $trainer['specializations'] === null ? [] : explode(',', $trainer['specializations']);
  $trainer['classes'] = $trainer['classes'] === null ? [] : explode(',', $trainer['classes']);
}
unset($trainer);

if ($trainerId !== null && $trainers === []) {
  api_send_json(['error' => 'Trainer not found.'], 404);
}

api_send_json(['data' => $trainers]);
