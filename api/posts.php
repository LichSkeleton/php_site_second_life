<?php

require_once dirname(__DIR__) . '/path.php';
require_once SITE_ROOT . '/app/database/db.php';
require_once SITE_ROOT . '/app/include/structured.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    echo postsFeedJson(publishedPostsFeed());
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    echo json_encode(['ok' => false, 'errors' => ['Use GET to read the catalog or POST to add a post.']], JSON_UNESCAPED_UNICODE);
    exit;
}

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'errors' => ['Sign in as an administrator.']], JSON_UNESCAPED_UNICODE);
    exit;
}

if ((int) $_SESSION['admin'] !== 1) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'errors' => ['Only an administrator can add a post from JSON.']], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'errors' => ['The request body must be a JSON object with title, content and topic_id.']], JSON_UNESCAPED_UNICODE);
    exit;
}

$result = importStructuredPost($data, (int) $_SESSION['id']);
http_response_code($result['ok'] ? 201 : 422);
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
