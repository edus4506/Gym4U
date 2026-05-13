<?php
declare(strict_types = 1);

require_once __DIR__ . '/database/db.php';

$db = getDatabaseConnection();

// Read and execute tables.sql
$tablesSql = file_get_contents(__DIR__ . '/database/tables.sql');
$db->exec($tablesSql);

// Read and execute data.sql
$dataSql = file_get_contents(__DIR__ . '/database/data.sql');
$db->exec($dataSql);

echo "Database setup completed successfully.\n";
?>