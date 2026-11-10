<?php
$host = getenv('SPACENEST_DB_HOST') ?: '127.0.0.1';
$port = getenv('SPACENEST_DB_PORT') ?: '3306';
$db   = getenv('SPACENEST_DB_NAME') ?: 'spacenest_db';
$user = getenv('SPACENEST_DB_USER') ?: 'root';
$pass = getenv('SPACENEST_DB_PASS') ?: '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log('SPACENEST database connection failed: ' . $e->getMessage());
    throw $e;
}