<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../database/db.php';

header('Content-Type: application/json; charset=utf-8');

$role   = $_SESSION['user_role'] ?? '';
$userId = (int)($_SESSION['user_id'] ?? 0);

if ($role !== 'trainer' && $role !== 'admin') {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$db = getDatabaseConnection();

if ($role === 'admin') {
    $targetTrainerId = (int)($_GET['trainer_id'] ?? 0);
    if ($targetTrainerId === 0) {
        $first = $db->query('SELECT id FROM trainers ORDER BY id LIMIT 1')->fetch();
        $targetTrainerId = $first ? (int)$first['id'] : 0;
    }
} else {
    $stmt = $db->prepare('SELECT id FROM trainers WHERE user_id = ?');
    $stmt->execute([$userId]);
    $targetTrainerId = (int)($stmt->fetchColumn() ?: 0);
}

if ($targetTrainerId === 0) {
    echo json_encode(['error' => 'no_trainer']);
    exit;
}

$trainerStmt = $db->prepare('
    SELECT t.id, u.name, t.title
    FROM trainers t
    JOIN users u ON u.id = t.user_id
    WHERE t.id = ?
');
$trainerStmt->execute([$targetTrainerId]);
$trainerInfo = $trainerStmt->fetch();

$classStatsStmt = $db->prepare('
    SELECT
        c.id AS class_id,
        c.name AS class_name,
        cs.id AS schedule_id,
        cs.day,
        cs.time,
        cs.capacity,
        cs.enrolled,
        ROUND(AVG(r.rating), 1) AS avg_rating,
        COUNT(r.id) AS review_count
    FROM class_schedule cs
    JOIN classes c ON c.id = cs.class_id
    LEFT JOIN reviews r ON r.schedule_id = cs.id
    WHERE c.trainer_id = ?
      AND c.name != "Gym Review"
    GROUP BY cs.id
    ORDER BY c.name, cs.day, cs.time
');
$classStatsStmt->execute([$targetTrainerId]);
$classStats = $classStatsStmt->fetchAll();

$reviewsStmt = $db->prepare('
    SELECT
        r.rating,
        r.comment,
        u.name AS member_name,
        c.name AS class_name
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    JOIN class_schedule cs ON cs.id = r.schedule_id
    JOIN classes c ON c.id = cs.class_id
    WHERE c.trainer_id = ?
      AND c.name != "Gym Review"
    ORDER BY r.id DESC
    LIMIT 10
');
$reviewsStmt->execute([$targetTrainerId]);
$recentReviews = $reviewsStmt->fetchAll();

$totalEnrolled = array_sum(array_column($classStats, 'enrolled'));
$totalCapacity = array_sum(array_column($classStats, 'capacity'));
$totalReviews  = array_sum(array_column($classStats, 'review_count'));
$ratingsOnly   = array_filter(array_column($classStats, 'avg_rating'), fn($r) => $r !== null);
$overallRating = count($ratingsOnly) > 0
    ? round(array_sum($ratingsOnly) / count($ratingsOnly), 1)
    : null;
$fillRate      = $totalCapacity > 0
    ? round(($totalEnrolled / $totalCapacity) * 100)
    : 0;

$topClass = null;
if (!empty($classStats)) {
    $topClass = array_reduce($classStats, fn($carry, $item) =>
        ($carry === null || $item['enrolled'] > $carry['enrolled']) ? $item : $carry
    );
}

echo json_encode([
    'trainer'       => $trainerInfo,
    'classStats'    => $classStats,
    'recentReviews' => $recentReviews,
    'totals'        => [
        'enrolled'      => $totalEnrolled,
        'capacity'      => $totalCapacity,
        'fillRate'      => $fillRate,
        'overallRating' => $overallRating,
        'totalReviews'  => $totalReviews,
        'slotCount'     => count($classStats),
    ],
    'topClass'      => $topClass,
]);
