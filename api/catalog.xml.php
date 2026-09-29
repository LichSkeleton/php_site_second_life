<?php

require_once dirname(__DIR__) . '/path.php';
require_once SITE_ROOT . '/app/database/db.php';
require_once SITE_ROOT . '/app/include/structured.php';

header('Content-Type: application/xml; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    echo postsFeedXml(publishedPostsFeed());
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    echo importResultXml(['ok' => false, 'errors' => ['Use GET to read the catalog or POST to add a post.'], 'post' => null]);
    exit;
}

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo importResultXml(['ok' => false, 'errors' => ['Sign in as an administrator.'], 'post' => null]);
    exit;
}

if ((int) $_SESSION['admin'] !== 1) {
    http_response_code(403);
    echo importResultXml(['ok' => false, 'errors' => ['Only an administrator can add a post from XML.'], 'post' => null]);
    exit;
}

$result = importPostFromXml((string) file_get_contents('php://input'), (int) $_SESSION['id']);
http_response_code($result['ok'] ? 201 : 422);
echo importResultXml($result);
