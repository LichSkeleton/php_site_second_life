<?php

function postFieldsAreValid($title, $content, $topic, &$errMsg)
{
    $ok = true;
    $plain = trim(strip_tags((string) $content));

    if ($title === '') {
        $errMsg[] = 'Enter a title.';
        $ok = false;
    } elseif (mb_strlen($title, 'UTF-8') < 7) {
        $errMsg[] = 'The post title must be at least 7 characters';
        $ok = false;
    } elseif (mb_strlen($title, 'UTF-8') > 255) {
        $errMsg[] = 'The post title must be at most 255 characters';
        $ok = false;
    }

    if ($plain === '') {
        $errMsg[] = 'Enter the post text.';
        $ok = false;
    } elseif (mb_strlen($plain, 'UTF-8') < 10) {
        $errMsg[] = 'The post text must be at least 10 characters';
        $ok = false;
    } elseif (mb_strlen($content, 'UTF-8') > 20000) {
        $errMsg[] = 'The post text must be at most 20000 characters';
        $ok = false;
    }

    if ($topic === '' || $topic === 'Post category:' || !ctype_digit((string) $topic)) {
        $errMsg[] = 'Choose a category.';
        $ok = false;
    } else {
        $topicRow = selectOne('topics', ['id' => (int) $topic]);
        if (!$topicRow) {
            $errMsg[] = 'Choose a category.';
            $ok = false;
        }
    }

    return $ok;
}

function storeUploadedPostImage($file, &$errMsg)
{
    if (empty($file['name'])) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $errMsg[] = 'Failed to upload the image to the server';
        return false;
    }

    $info = @getimagesize($file['tmp_name']);
    $types = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];
    if ($info === false || !isset($types[$info[2]])) {
        $errMsg[] = 'The uploaded file is not an image!';
        return false;
    }

    $imgName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $types[$info[2]];
    $postsDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'posts';
    if (!is_dir($postsDir)) {
        mkdir($postsDir, 0777, true);
    }
    $destination = $postsDir . DIRECTORY_SEPARATOR . $imgName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errMsg[] = 'Failed to upload the image to the server';
        return false;
    }
    return $imgName;
}

function saveUnpublishedPost($userId, $title, $content, $topicId, $imageName)
{
    $safeContent = nl2br(htmlspecialchars((string) $content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), false);

    return (int) insert('posts', [
        'id_user' => (int) $userId,
        'title' => $title,
        'content' => $safeContent,
        'img' => $imageName !== '' ? $imageName : 'post_php.jpg',
        'status' => 0,
        'id_topic' => (int) $topicId,
    ]);
}
