<?php
declare(strict_types = 1);

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


if(($_SESSION['user_role'] ?? '') !== 'member') {
    jsonError('members-only');
}


$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$mode = $_POST['mode'] ?? '';

if ($scheduleId <= 0 || !in_array($mode, ['enroll', 'cancel'], true)) {
    jsonError('invalid');
}

$db = getDatabaseConnection();
$userId = (int)$_SESSION['user_id'];

try {
    $db->beginTransaction();

    if($mode === 'enroll') {
        $slotStmt = $db->prepare('SELECT capacity, enrolled FROM class_schedule WHERE id = ?');
        $slotStmt->execute([$scheduleId]);
        $slot = $slotStmt->fetch();

        if(!$slot || (int)$slot['enrolled'] >= (int)$slot['capacity']) {
            $db->rollBack();
            jsonError('full');
        }

        $insert = $db->prepare('INSERT INTO enrollments (user_id, schedule_id) VALUES (?,?)');
        $insert->execute([$userId, $scheduleId]);

        $update = $db->prepare('UPDATE class_schedule SET enrolled = enrolled + 1 WHERE id = ?');
        $update->execute([$scheduleId]);
    } else {
        $delete = $db->prepare('DELETE FROM enrollments WHERE user_id = ? AND schedule_id = ?');
        $delete->execute([$userId, $scheduleId]);

        if($delete->rowCount() > 0) {
            $update = $db->prepare('UPDATE class_schedule SET enrolled = MAX(enrolled - 1, 0) WHERE id = ?');
            $update->execute([$scheduleId]);
        }
    }
    $db->commit();

    $slotStmt = $db->prepare('SELECT enrolled, capacity FROM class_schedule WHERE id = ?');
    $slotStmt->execute([$scheduleId]);
    $updated = $slotStmt->fetch();

    echo json_encode([
        'success' => true,
        'mode' => $mode,
        'enrolled' => (int)$updated['enrolled'],
        'capacity' => (int)$updated['capacity'],
    ]);

} catch (PDOException) {
    if($db->inTransaction()) {
        $db->rollBack();
    }
    jsonError('already-enrolled');
}

