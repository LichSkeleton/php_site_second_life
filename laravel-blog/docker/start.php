<?php

declare(strict_types=1);

$root = dirname(__DIR__);

function envValue(string $key, string $default = ''): string
{
    $fromProcess = getenv($key);
    if (is_string($fromProcess) && $fromProcess !== '') {
        return $fromProcess;
    }

    $path = dirname(__DIR__).'/.env';
    if (! is_file($path)) {
        return $default;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (! str_starts_with($line, $key.'=')) {
            continue;
        }

        return trim(substr($line, strlen($key) + 1), " \t\"'");
    }

    return $default;
}

$host = envValue('DB_HOST', 'db');
$port = envValue('DB_PORT', '3306');
$user = envValue('DB_USERNAME', 'root');
$pass = envValue('DB_PASSWORD');
$database = envValue('DB_DATABASE', 'laravel_blog');

if (! preg_match('/^[A-Za-z0-9_]+$/', $database)) {
    fwrite(STDERR, "Invalid database name.\n");
    exit(1);
}

if (! is_file($root.'/.env') && is_file($root.'/.env.example')) {
    copy($root.'/.env.example', $root.'/.env');
}

if (! is_file($root.'/vendor/autoload.php')) {
    passthru('composer install --no-interaction --prefer-dist', $installCode);
    if ($installCode !== 0) {
        exit($installCode);
    }
}

$env = is_file($root.'/.env') ? (string) file_get_contents($root.'/.env') : '';
if (! preg_match('/^APP_KEY=base64:.+/m', $env)) {
    passthru('php artisan key:generate --force', $keyCode);
    if ($keyCode !== 0) {
        exit($keyCode);
    }
}

$pdo = null;
$lastError = '';
for ($attempt = 1; $attempt <= 30; $attempt++) {
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port}",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        break;
    } catch (PDOException $exception) {
        $lastError = $exception->getMessage();
        fwrite(STDERR, "Waiting for MySQL ({$attempt})...\n");
        sleep(2);
    }
}

if (! $pdo instanceof PDO) {
    fwrite(STDERR, "MySQL is not reachable: {$lastError}\n");
    exit(1);
}

$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

foreach (['php artisan migrate --force', 'php artisan db:seed --force'] as $command) {
    passthru($command, $code);
    if ($code !== 0) {
        exit($code);
    }
}

if (! is_dir($root.'/public/images/posts')) {
    mkdir($root.'/public/images/posts', 0777, true);
}

passthru('php artisan serve --host=0.0.0.0 --port=8000');
