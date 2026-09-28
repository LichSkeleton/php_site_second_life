<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['id'])) {
    header('location: ' . BASE_URL . 'log.php');
    exit();
}

if ((int) $_SESSION['admin'] !== 1) {
    header('location: ' . BASE_URL);
    exit();
}
