<?php

require_once 'path.php';

$theme = (($_POST['theme'] ?? '') === 'dark') ? 'dark' : 'light';
setcookie('site_theme', $theme, [
    'expires' => time() + 180 * 24 * 60 * 60,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

$back = BASE_URL;
$referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
if ($referer !== '' && str_starts_with($referer, BASE_URL)) {
    $back = $referer;
}

header('location: ' . $back);
exit();
