<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../database/db.php';

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit;
}

$db=getDatabaseConnection();

$stmt=$db->prepare('SELECT id,name,username,email,role,membership FROM users WHERE id=?');
$stmt->execute([(int)$_SESSION['user_id']]);

$user=$stmt->fetch() ?: null;

if($user===null){
    session_destroy();
    header('Location: login.php');
    exit;
}

$userEmail=$user['email'];
$membership=$user['membership'];
?>
