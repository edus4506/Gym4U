<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$db = getDatabaseConnection();

$messages = [
    'success' => [
        'class' => 'Class changes saved.',
    ],
    'error' => [
        'class' => 'Class action could not be completed.',
    ],
];

$successMessage = $messages['success'][$_GET['success'] ?? ''] ?? null;
$errorMessage   = $messages['error'][$_GET['error'] ?? ''] ?? null;

$search         = trim($_GET['search'] ?? '');
$categoryFilter = $_GET['category'] ?? '';
$trainerFilter  = $_GET['trainer'] ?? '';

$categories = $db->query('
    SELECT id, name
    FROM categories
    ORDER BY name
')->fetchAll();

$trainers = $db->query('
    SELECT t.id, u.name
    FROM trainers t
    JOIN users u ON u.id = t.user_id
    ORDER BY u.name
')->fetchAll();

$query = "
    SELECT
        c.id,
        c.name,
        c.description,
        c.category_id,
        c.trainer_id,
        cat.name AS category_name,
        u.name AS trainer_name,
        COUNT(cs.id) AS slot_count
    FROM classes c
    JOIN categories cat ON cat.id = c.category_id
    LEFT JOIN trainers t ON t.id = c.trainer_id
    LEFT JOIN users u ON u.id = t.user_id
    LEFT JOIN class_schedule cs ON cs.class_id = c.id
    WHERE 1=1
";

$params = [];

if ($search !== '') {
    $query   .= ' AND c.name LIKE ?';
    $params[] = "%$search%";
}

if ($categoryFilter !== '') {
    $query   .= ' AND c.category_id = ?';
    $params[] = (int)$categoryFilter;
}

if ($trainerFilter !== '') {
    $query   .= ' AND c.trainer_id = ?';
    $params[] = (int)$trainerFilter;
}

$query .= ' GROUP BY c.id ORDER BY c.name';

$stmt = $db->prepare($query);
$stmt->execute($params);
$adminClasses = $stmt->fetchAll();
