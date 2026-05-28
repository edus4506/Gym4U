<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

header('Content-Type: application/json');

function jsonError(string $message): never {
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    jsonError('not-logged-in');
}

csrf_verify();

if (($_SESSION['user_role'] ?? '') !== 'member') {
    jsonError('members-only');
}

$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$mode = $_POST['mode'] ?? '';

if ($scheduleId <= 0 || !in_array($mode, ['join', 'leave'], true)) {
    jsonError('invalid');
}

$db = getDatabaseConnection();
$userId = (int)$_SESSION['user_id'];

$planStmt = $db->prepare('SELECT subscription_plan FROM users WHERE id = ?');
$planStmt->execute([$userId]);
$plan = (string)($planStmt->fetchColumn() ?: 'none');

if ($plan !== 'premium') {
    jsonError('premium-only');
}

if ($mode === 'join') {
    $enrolledStmt = $db->prepare('SELECT 1 FROM enrollments WHERE user_id = ? AND schedule_id = ?');
    $enrolledStmt->execute([$userId, $scheduleId]);
    if ($enrolledStmt->fetchColumn()) {
        jsonError('already-enrolled');
    }

    $slotStmt = $db->prepare('SELECT id FROM class_schedule WHERE id = ?');
    $slotStmt->execute([$scheduleId]);
    if (!$slotStmt->fetchColumn()) {
        jsonError('invalid');
    }

    $insert = $db->prepare('INSERT OR IGNORE INTO waitlist (user_id, schedule_id) VALUES (?, ?)');
    $insert->execute([$userId, $scheduleId]);

    $posStmt = $db->prepare('
        SELECT COUNT(*) FROM waitlist
        WHERE schedule_id = ?
          AND added_at <= (SELECT added_at FROM waitlist WHERE user_id = ? AND schedule_id = ?)
    ');
    $posStmt->execute([$scheduleId, $userId, $scheduleId]);
    $position = (int)$posStmt->fetchColumn();

    echo json_encode(['success' => true, 'mode' => 'join', 'position' => $position]);
} else {
    $delete = $db->prepare('DELETE FROM waitlist WHERE user_id = ? AND schedule_id = ?');
    $delete->execute([$userId, $scheduleId]);

    echo json_encode(['success' => true, 'mode' => 'leave']);
}
