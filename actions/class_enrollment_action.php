<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';
require_once __DIR__ . '/../utils/membership_limits.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit;
}

csrf_verify();

if (($_SESSION['user_role'] ?? '') !== 'member') {
  header('Location: ../classes.php?error=members-only');
  exit;
}

$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$mode = $_POST['mode'] ?? '';

if ($scheduleId <= 0 || !in_array($mode, ['enroll', 'cancel'], true)) {
  header('Location: ../classes.php?error=invalid');
  exit;
}

$db = getDatabaseConnection();
$userId = (int)$_SESSION['user_id'];

try {
  $db->beginTransaction();

  if ($mode === 'enroll') {
    $planStmt = $db->prepare('SELECT subscription_plan FROM users WHERE id = ?');
    $planStmt->execute([$userId]);
    $plan = (string)($planStmt->fetchColumn() ?: 'none');

    if (!membership_can_enroll($db, $userId, $plan)) {
      $db->rollBack();
      header('Location: ../classes.php?error=limit-reached');
      exit;
    }

    $slotStmt = $db->prepare('SELECT capacity, enrolled, time FROM class_schedule WHERE id = ?');
    $slotStmt->execute([$scheduleId]);
    $slot = $slotStmt->fetch();

    if ($plan === 'offpeak') {
        $hour = (int)substr($slot['time'], 0, 2);
        if ($hour >= 18 && $hour < 20) {
            $db->rollBack();
            header('Location: ../classes.php?error=offpeak-restricted');
            exit;
        }
    }

    if (!$slot || (int)$slot['enrolled'] >= (int)$slot['capacity']) {
      $db->rollBack();
      header('Location: ../classes.php?error=full');
      exit;
    }

    $insert = $db->prepare('INSERT INTO enrollments (user_id, schedule_id) VALUES (?, ?)');
    $insert->execute([$userId, $scheduleId]);

    $update = $db->prepare('UPDATE class_schedule SET enrolled = enrolled + 1 WHERE id = ?');
    $update->execute([$scheduleId]);
  } else {
    $delete = $db->prepare('DELETE FROM enrollments WHERE user_id = ? AND schedule_id = ?');
    $delete->execute([$userId, $scheduleId]);

    if ($delete->rowCount() > 0) {
      $update = $db->prepare('UPDATE class_schedule SET enrolled = MAX(enrolled - 1, 0) WHERE id = ?');
      $update->execute([$scheduleId]);

      $nextStmt = $db->prepare('SELECT user_id FROM waitlist WHERE schedule_id = ? ORDER BY added_at ASC LIMIT 1');
      $nextStmt->execute([$scheduleId]);
      $nextUserId = $nextStmt->fetchColumn();

      if ($nextUserId !== false) {
        $insertEnroll = $db->prepare('INSERT INTO enrollments (user_id, schedule_id) VALUES (?, ?)');
        $insertEnroll->execute([$nextUserId, $scheduleId]);

        $incrementEnrolled = $db->prepare('UPDATE class_schedule SET enrolled = enrolled + 1 WHERE id = ?');
        $incrementEnrolled->execute([$scheduleId]);

        $deleteWaitlist = $db->prepare('DELETE FROM waitlist WHERE user_id = ? AND schedule_id = ?');
        $deleteWaitlist->execute([$nextUserId, $scheduleId]);
      }
    }
  }

  $db->commit();
} catch (PDOException $exception) {
  if ($db->inTransaction()) {
    $db->rollBack();
  }

  header('Location: ../classes.php?error=already-enrolled');
  exit;
}

header('Location: ../classes.php?success=' . ($mode === 'enroll' ? 'enrolled' : 'cancelled'));
exit;
