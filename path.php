<?php
if (defined('SITE_ROOT')) {
    return;
}

require_once __DIR__ . '/app/config/env.php';
load_env_file(__DIR__ . '/.env');

date_default_timezone_set('Europe/Kyiv');
require_once __DIR__ . '/app/include/datetime.php';
require_once __DIR__ . '/app/include/prefs.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

define('SITE_ROOT', __DIR__);
define('ROOT_PATH', realpath(__DIR__) ?: __DIR__);

$baseUrl = rtrim((string) env_value('APP_URL', 'http://localhost:8080'), '/') . '/';
define('BASE_URL', $baseUrl);
