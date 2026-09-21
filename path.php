<?php
if (defined('SITE_ROOT')) {
    return;
}

require_once __DIR__ . '/app/config/env.php';
load_env_file(__DIR__ . '/.env');

define('SITE_ROOT', __DIR__);
define('ROOT_PATH', realpath(__DIR__) ?: __DIR__);

$baseUrl = rtrim((string) env_value('APP_URL', 'http://localhost:8080'), '/') . '/';
define('BASE_URL', $baseUrl);
