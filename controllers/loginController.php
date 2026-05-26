<?php
declare(strict_types = 1);

session_start();
require_once __DIR__ . '/../utils/csrf.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [
    'empty'   => 'Please enter your username and password.',
    'invalid' => 'Invalid username or password.',
];

$errorMessage = $errors[$_GET['error'] ?? ''] ?? null;
