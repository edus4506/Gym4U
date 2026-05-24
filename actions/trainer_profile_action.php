<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../utils/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['user_role'] ?? '') !== 'trainer') {
  header('Location: ../profile.php');
  exit;
}

csrf_verify();

$bio = trim($_POST['bio'] ?? '');
$title = trim($_POST['title'] ?? '');
$certifications = trim($_POST['certifications'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$experience = max(0, (int)($_POST['experience_years'] ?? 0));
$specializationIdsRaw = $_POST['specialization_ids'] ?? [];
$specializationIds = [];
if (is_array($specializationIdsRaw)) {
  foreach ($specializationIdsRaw as $id) {
    $value = (int)$id;
    if ($value > 0) {
      $specializationIds[] = $value;
    }
  }
  $specializationIds = array_values(array_unique($specializationIds));
}

$db = getDatabaseConnection();

try {
  $db->beginTransaction();

  $stmt = $db->prepare('UPDATE trainers SET bio = ?, title = ?, certifications = ?, phone = ?, experience_years = ? WHERE user_id = ?');
  $stmt->execute([$bio, $title, $certifications, $phone, $experience, (int)$_SESSION['user_id']]);

  $trainerIdStmt = $db->prepare('SELECT id FROM trainers WHERE user_id = ?');
  $trainerIdStmt->execute([(int)$_SESSION['user_id']]);
  $trainerId = (int)$trainerIdStmt->fetchColumn();

  if ($trainerId > 0) {
    $validCategoryIds = [];
    if ($specializationIds !== []) {
      $placeholders = implode(',', array_fill(0, count($specializationIds), '?'));
      $validCategoryStmt = $db->prepare("SELECT id FROM categories WHERE id IN ($placeholders)");
      $validCategoryStmt->execute($specializationIds);
      $validCategoryIds = array_map('intval', array_column($validCategoryStmt->fetchAll(), 'id'));
    }

    $db->prepare('DELETE FROM trainer_categories WHERE trainer_id = ?')->execute([$trainerId]);

    if ($validCategoryIds !== []) {
      $insertSpecialization = $db->prepare('INSERT INTO trainer_categories (trainer_id, category_id) VALUES (?, ?)');
      foreach ($validCategoryIds as $categoryId) {
        $insertSpecialization->execute([$trainerId, $categoryId]);
      }
    }
  }

  $db->commit();
} catch (Throwable $exception) {
  if ($db->inTransaction()) {
    $db->rollBack();
  }
  header('Location: ../profile.php?error=class');
  exit;
}

header('Location: ../profile.php?success=trainer');
exit;
