<?php

if (!function_exists('env_value')) {
    require_once __DIR__ . '/../config/env.php';
    load_env_file(dirname(__DIR__, 2) . '/.env');
}

$driver = env_value('DB_DRIVER', 'mysql');
$host = env_value('DB_HOST', 'db');
$port = env_value('DB_PORT', '3306');
$db_name = env_value('DB_NAME', 'dinamic_site');
$db_user = env_value('DB_USER', 'blog_user');
$db_pass = env_value('DB_PASSWORD', 'blog_pass');
$charset = env_value('DB_CHARSET', 'utf8mb4');
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO(
        "{$driver}:host={$host};port={$port};dbname={$db_name};charset={$charset}",
        $db_user,
        $db_pass,
        $options
    );
} catch (PDOException $i) {
    die("Database connection error");
}
