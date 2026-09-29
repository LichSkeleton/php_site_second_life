<?php

require_once SITE_ROOT . '/app/include/post-rules.php';

function feedRecord(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'title' => (string) $row['title'],
        'content' => (string) $row['content'],
        'topic' => (string) ($row['topic_name'] ?? ''),
        'author' => (string) $row['username'],
        'image' => BASE_URL . 'assets/img/posts/' . rawurlencode((string) $row['img']),
        'created_at' => formatAppDateIso($row['created_date']),
        'updated_at' => formatAppDateIso($row['updated_date']),
        'url' => BASE_URL . 'single.php?post=' . (int) $row['id'],
    ];
}

function publishedPostsFeed(): array
{
    global $pdo;
    $sql = 'SELECT p.id, p.title, p.content, p.img, p.created_date, p.updated_date,
            t.name AS topic_name, u.username
            FROM posts AS p
            JOIN users AS u ON p.id_user = u.id
            LEFT JOIN topics AS t ON p.id_topic = t.id
            WHERE p.status = 1
            ORDER BY p.created_date DESC, p.id DESC';
    $query = $pdo->prepare($sql);
    $query->execute();
    dbCheckError($query);

    $posts = [];
    foreach ($query->fetchAll() as $row) {
        $posts[] = feedRecord($row);
    }
    return $posts;
}

function feedPostById(int $id): ?array
{
    global $pdo;
    $sql = 'SELECT p.id, p.title, p.content, p.img, p.created_date, p.updated_date,
            t.name AS topic_name, u.username
            FROM posts AS p
            JOIN users AS u ON p.id_user = u.id
            LEFT JOIN topics AS t ON p.id_topic = t.id
            WHERE p.id = ?';
    $query = $pdo->prepare($sql);
    $query->execute([$id]);
    dbCheckError($query);
    $row = $query->fetch();
    return $row ? feedRecord($row) : null;
}

function postsFeedDocument(array $posts): array
{
    return [
        'generated_at' => (new DateTimeImmutable('now', appTimeZone()))->format('c'),
        'timezone' => 'Europe/Kyiv',
        'count' => count($posts),
        'posts' => $posts,
    ];
}

function postsFeedJson(array $posts): string
{
    return json_encode(
        postsFeedDocument($posts),
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    );
}

function appendXmlText(DOMDocument $dom, DOMElement $parent, string $name, string $value): void
{
    $node = $dom->createElement($name);
    $node->appendChild($dom->createTextNode($value));
    $parent->appendChild($node);
}

function postElement(DOMDocument $dom, array $post): DOMElement
{
    $node = $dom->createElement('post');
    $node->setAttribute('id', (string) $post['id']);
    foreach (['title', 'content', 'topic', 'author', 'image', 'created_at', 'updated_at', 'url'] as $field) {
        appendXmlText($dom, $node, $field, (string) ($post[$field] ?? ''));
    }
    return $node;
}

function postsFeedXml(array $posts): string
{
    $document = postsFeedDocument($posts);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    $root = $dom->createElement('catalog');
    $dom->appendChild($root);
    appendXmlText($dom, $root, 'generated_at', (string) $document['generated_at']);
    appendXmlText($dom, $root, 'timezone', (string) $document['timezone']);
    appendXmlText($dom, $root, 'count', (string) $document['count']);
    $list = $dom->createElement('posts');
    $root->appendChild($list);
    foreach ($posts as $post) {
        $list->appendChild(postElement($dom, $post));
    }
    return $dom->saveXML();
}

function importResultXml(array $result): string
{
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    $root = $dom->createElement('result');
    $dom->appendChild($root);
    appendXmlText($dom, $root, 'ok', $result['ok'] ? 'true' : 'false');
    $errors = $dom->createElement('errors');
    $root->appendChild($errors);
    foreach ($result['errors'] as $error) {
        appendXmlText($dom, $errors, 'error', (string) $error);
    }
    if (!empty($result['post']) && is_array($result['post'])) {
        $root->appendChild(postElement($dom, $result['post']));
    }
    return $dom->saveXML();
}

function importStructuredPost(array $data, int $userId): array
{
    $title = trim((string) ($data['title'] ?? ''));
    $content = trim((string) ($data['content'] ?? ''));
    $topic = trim((string) ($data['topic_id'] ?? ''));
    $statusRaw = $data['status'] ?? 0;
    $status = ((string) $statusRaw === '1' || $statusRaw === 1 || $statusRaw === true) ? 1 : 0;
    $errors = [];

    if (!postFieldsAreValid($title, $content, $topic, $errors)) {
        return ['ok' => false, 'errors' => $errors, 'post' => null];
    }

    $id = (int) insert('posts', [
        'id_user' => $userId,
        'title' => $title,
        'content' => $content,
        'img' => 'post_php.jpg',
        'status' => $status,
        'id_topic' => (int) $topic,
    ]);

    return [
        'ok' => true,
        'errors' => [],
        'post' => feedPostById($id),
    ];
}

function importPostFromXml(string $raw, int $userId): array
{
    $raw = trim($raw);
    if ($raw === '') {
        return ['ok' => false, 'errors' => ['Paste an XML document.'], 'post' => null];
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NONET);
    if ($xml === false || strtolower($xml->getName()) !== 'post') {
        return ['ok' => false, 'errors' => ['The XML root element must be post.'], 'post' => null];
    }

    return importStructuredPost([
        'title' => (string) $xml->title,
        'content' => (string) $xml->content,
        'topic_id' => (string) $xml->topic_id,
        'status' => (string) $xml->status,
    ], $userId);
}
