<?php
declare(strict_types = 1);

require_once __DIR__ . '/database/db.php';

$db = getDatabaseConnection();

// Read and execute database.sql
$databaseSql = file_get_contents(__DIR__ . '/database/database.sql');
$db->exec($databaseSql);

echo "Database setup completed successfully.\n";
?>
