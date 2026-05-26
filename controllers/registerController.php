<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/../utils/csrf.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [
    'empty'        => 'Please fill in all required fields.',
    'email'        => 'Please enter a valid email address.',
    'exists'       => 'That username or email is already registered.',
    'password'     => 'Passwords do not match.',
    'weakpassword' => 'Password must contain at least 4 characters.',
];

$errorMessage = $errors[$_GET['error'] ?? ''] ?? null;
