<?php

session_start();

require_once __DIR__ . '/../utils/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

csrf_verify();

session_destroy();
header('Location: ../index.php');
exit;
?>
