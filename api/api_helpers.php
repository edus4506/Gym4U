<?php
declare(strict_types = 1);

require_once __DIR__ . '/../database/db.php';

function api_send_json(mixed $data, int $statusCode = 200): never {
  http_response_code($statusCode);
  header('Content-Type: application/json; charset=utf-8');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
  echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  exit;
}

function api_handle_options(): void {
  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
  }
}

function api_require_get(): void {
  api_handle_options();

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_send_json(['error' => 'Method not allowed. Use GET.'], 405);
  }
}

function api_positive_int_param(string $name): ?int {
  if (!isset($_GET[$name]) || trim((string)$_GET[$name]) === '') {
    return null;
  }

  $value = filter_var($_GET[$name], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

  if ($value === false) {
    api_send_json(['error' => "Invalid $name. Use a positive integer."], 400);
  }

  return (int)$value;
}

function api_string_param(string $name): string {
  return trim((string)($_GET[$name] ?? ''));
}
