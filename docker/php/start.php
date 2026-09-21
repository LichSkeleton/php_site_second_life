<?php
declare(strict_types=1);

function env_or(string $key, string $default): string
{
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

function wait_for_mysql(string $host, string $port, string $user, string $pass): PDO
{
    fwrite(STDOUT, "Waiting for MySQL at {$host}:{$port}...\n");
    $lastError = '';
    for ($i = 1; $i <= 60; $i++) {
        try {
            return new PDO(
                "mysql:host={$host};port={$port};charset=utf8mb4",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            $lastError = $e->getMessage();
            fwrite(STDOUT, "  attempt {$i}/60: {$lastError}\n");
            sleep(2);
        }
    }
    fwrite(STDERR, "MySQL is not available: {$lastError}\n");
    exit(1);
}

function exec_sql_file(PDO $pdo, string $path): void
{
    if (!is_readable($path)) {
        fwrite(STDERR, "Schema file not found: {$path}\n");
        exit(1);
    }
    $sql = (string) file_get_contents($path);
    $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }
}

$host = env_or('DB_HOST', 'db');
$port = env_or('DB_PORT', '3306');
$dbName = env_or('DB_NAME', 'dinamic_site');
$dbUser = env_or('DB_USER', 'blog_user');
$dbPass = env_or('DB_PASSWORD', 'blog_pass');

$pdo = wait_for_mysql($host, $port, $dbUser, $dbPass);
$safeDb = str_replace('`', '``', $dbName);
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$safeDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$safeDb}`");
exec_sql_file($pdo, '/var/www/html/docker/mysql/schema.sql');

passthru('php /var/www/html/app/database/seed.php', $seedCode);
if ($seedCode !== 0) {
    fwrite(STDERR, "Seeder failed with code {$seedCode}\n");
    exit($seedCode);
}

fwrite(STDOUT, "Starting Apache...\n");
passthru('apache2-foreground', $exitCode);
exit($exitCode);
