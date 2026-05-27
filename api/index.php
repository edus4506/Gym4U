<?php
declare(strict_types = 1);

require_once __DIR__ . '/api_helpers.php';

api_require_get();

api_send_json([
  'name' => 'Gym4U Public API',
  'version' => '1.0',
  'endpoints' => [
    [
      'method' => 'GET',
      'path' => '/api/schedules.php',
      'filters' => ['id', 'class_id', 'trainer_id', 'category_id', 'day', 'available_only'],
    ],
    [
      'method' => 'GET',
      'path' => '/api/trainers.php',
      'filters' => ['id', 'category_id', 'search'],
    ],
    [
      'method' => 'GET',
      'path' => '/api/equipment.php',
      'filters' => ['id', 'status', 'search'],
    ],
  ],
]);
