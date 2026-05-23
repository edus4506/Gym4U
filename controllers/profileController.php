<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$db = getDatabaseConnection();

$stmt = $db->prepare('SELECT id, name, username, email, password, role, profile_photo, active FROM users WHERE id = ?');
$stmt->execute([(int)$_SESSION['user_id']]);
$user = $stmt->fetch() ?: null;

if ($user === null) {
  session_destroy();
  header('Location: login.php');
  exit;
}

$messages = [
  'success' => [
    'updated' => 'Profile updated successfully.',
    'registered' => 'Account created successfully. Welcome to Gym4U!',
    'trainer' => 'Trainer profile updated successfully.',
    'admin' => 'Admin changes saved.',
    'equipment' => 'Equipment changes saved.',
    'class' => 'Class catalog changes saved.',
  ],
  'error' => [
    'empty' => 'Name and username are required.',
    'exists' => 'That username is already taken.',
    'password' => 'Please check your current password and confirmation.',
    'photo' => 'Please upload a valid JPG, PNG, GIF, or WEBP image up to 2 MB.',
    'admin' => 'Admin action could not be completed.',
    'class' => 'Class action could not be completed.',
    'equipment' => 'Equipment action could not be completed.',
  ],
];

$successMessage = $messages['success'][$_GET['success'] ?? ''] ?? null;
$errorMessage = $messages['error'][$_GET['error'] ?? ''] ?? null;

$memberEnrollments = [];
$trainerProfile = null;
$trainerSchedule = [];
$trainerRoster = [];
$trainerCategoryIds = [];
$categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$trainers = $db->query('SELECT t.id, u.name FROM trainers t JOIN users u ON u.id = t.user_id ORDER BY u.name')->fetchAll();
$equipment = [];

if ($user['role'] === 'member') {
  $stmt = $db->prepare("
    SELECT e.id AS enrollment_id, cs.id AS schedule_id, c.name AS class_name, u.name AS trainer_name,
           cs.day, cs.time, cs.capacity, cs.enrolled
    FROM enrollments e
    JOIN class_schedule cs ON cs.id = e.schedule_id
    JOIN classes c ON c.id = cs.class_id
    JOIN trainers t ON t.id = c.trainer_id
    JOIN users u ON u.id = t.user_id
    WHERE e.user_id = ?
    ORDER BY CASE cs.day
      WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3
      WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7
    END, cs.time
  ");
  $stmt->execute([(int)$user['id']]);
  $memberEnrollments = $stmt->fetchAll();
}

if ($user['role'] === 'trainer') {
  $profileStmt = $db->prepare('SELECT * FROM trainers WHERE user_id = ?');
  $profileStmt->execute([(int)$user['id']]);
  $trainerProfile = $profileStmt->fetch();

  if ($trainerProfile) {
    $trainerCategoryStmt = $db->prepare('SELECT category_id FROM trainer_categories WHERE trainer_id = ?');
    $trainerCategoryStmt->execute([(int)$trainerProfile['id']]);
    $trainerCategoryIds = array_map('intval', array_column($trainerCategoryStmt->fetchAll(), 'category_id'));

    $scheduleStmt = $db->prepare("
      SELECT cs.id, c.name AS class_name, cs.day, cs.time, cs.duration_minutes, cs.capacity, cs.enrolled
      FROM class_schedule cs
      JOIN classes c ON c.id = cs.class_id
      WHERE c.trainer_id = ?
      ORDER BY CASE cs.day
        WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3
        WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7
      END, cs.time
    ");
    $scheduleStmt->execute([(int)$trainerProfile['id']]);
    $trainerSchedule = $scheduleStmt->fetchAll();

    $rosterStmt = $db->prepare("
      SELECT cs.id AS schedule_id, c.name AS class_name, cs.day, cs.time, u.name, u.email
      FROM enrollments e
      JOIN users u ON u.id = e.user_id
      JOIN class_schedule cs ON cs.id = e.schedule_id
      JOIN classes c ON c.id = cs.class_id
      WHERE c.trainer_id = ?
      ORDER BY cs.day, cs.time, u.name
    ");
    $rosterStmt->execute([(int)$trainerProfile['id']]);
    $trainerRoster = $rosterStmt->fetchAll();
  }
}

if ($user['role'] === 'admin') {
  $equipment = $db->query('SELECT id, name, quantity, status FROM equipment ORDER BY name')->fetchAll();
}
