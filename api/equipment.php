<?php
declare(strict_types = 1);

require_once __DIR__ . '/api_helpers.php';

api_require_get();

$db = getDatabaseConnection();

$equipmentId = api_positive_int_param('id');
$status = strtolower(api_string_param('status'));
$search = api_string_param('search');
$allowedStatuses = ['available', 'maintenance', 'unavailable'];

if ($status !== '' && !in_array($status, $allowedStatuses, true)) {
  api_send_json([
    'error' => 'Invalid status. Use available, maintenance, or unavailable.',
  ], 400);
}

$sql = '
  SELECT id, name, quantity, status
  FROM equipment
  WHERE 1 = 1
';

$params = [];

if ($equipmentId !== null) {
  $sql .= ' AND id = :equipment_id';
  $params[':equipment_id'] = $equipmentId;
}

if ($status !== '') {
  $sql .= ' AND status = :status';
  $params[':status'] = $status;
}

if ($search !== '') {
  $sql .= ' AND name LIKE :search';
  $params[':search'] = '%' . $search . '%';
}

$sql .= ' ORDER BY name';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$equipment = $stmt->fetchAll();

if ($equipmentId !== null && $equipment === []) {
  api_send_json(['error' => 'Equipment not found.'], 404);
}

api_send_json(['data' => $equipment]);
