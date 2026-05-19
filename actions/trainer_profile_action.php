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

$db = getDatabaseConnection();
$stmt = $db->prepare('UPDATE trainers SET bio = ?, title = ?, certifications = ?, phone = ?, experience_years = ? WHERE user_id = ?');
$stmt->execute([$bio, $title, $certifications, $phone, $experience, (int)$_SESSION['user_id']]);

header('Location: ../profile.php?success=trainer');
exit;
