<?php
declare(strict_types = 1);

require_once __DIR__ . '/api_helpers.php';

api_require_get();

$db = getDatabaseConnection();

$scheduleId = api_positive_int_param('id');
$classId = api_positive_int_param('class_id');
$trainerId = api_positive_int_param('trainer_id');
$categoryId = api_positive_int_param('category_id');
$day = api_string_param('day');
$availableOnly = api_string_param('available_only');

$sql = "
  SELECT
    cs.id,
    cs.day,
    cs.time,
    cs.duration_minutes,
    cs.capacity,
    cs.enrolled,
    MAX(cs.capacity - cs.enrolled, 0) AS spots_available,
    c.id AS class_id,
    c.name AS class_name,
    c.description AS class_description,
    cat.id AS category_id,
    cat.name AS category_name,
    t.id AS trainer_id,
    u.name AS trainer_name
  FROM class_schedule cs
  JOIN classes c ON c.id = cs.class_id
  LEFT JOIN categories cat ON cat.id = c.category_id
  LEFT JOIN trainers t ON t.id = c.trainer_id
  LEFT JOIN users u ON u.id = t.user_id
  WHERE 1 = 1
";

$params = [];

if ($scheduleId !== null) {
  $sql .= ' AND cs.id = :schedule_id';
  $params[':schedule_id'] = $scheduleId;
}

if ($classId !== null) {
  $sql .= ' AND c.id = :class_id';
  $params[':class_id'] = $classId;
}

if ($trainerId !== null) {
  $sql .= ' AND t.id = :trainer_id';
  $params[':trainer_id'] = $trainerId;
}

if ($categoryId !== null) {
  $sql .= ' AND cat.id = :category_id';
  $params[':category_id'] = $categoryId;
}

if ($day !== '') {
  $sql .= ' AND LOWER(cs.day) = :day';
  $params[':day'] = strtolower($day);
}

if (in_array(strtolower($availableOnly), ['1', 'true', 'yes'], true)) {
  $sql .= ' AND cs.enrolled < cs.capacity';
}

$sql .= "
  ORDER BY CASE cs.day
    WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3
    WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7
  END, cs.time
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$schedules = $stmt->fetchAll();

if ($scheduleId !== null && $schedules === []) {
  api_send_json(['error' => 'Schedule not found.'], 404);
}

api_send_json(['data' => $schedules]);
