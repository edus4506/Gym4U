<?php
declare(strict_types = 1);
require_once __DIR__ . '/database/db.php';

$db = getDatabaseConnection();
$users = $db->query('SELECT COUNT(*) AS total FROM users')->fetch();
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test | Gym4U</title>
</head>
<body>
  <h1>Pagina de teste</h1>
  <p>Hora do servidor: <?= date('Y-m-d H:i:s') ?></p>
  <p>Versao do PHP: <?= phpversion() ?></p>
  <p>Numero de utilizadores na base de dados: <?= (int)$users['total'] ?></p>
</body>
</html>
