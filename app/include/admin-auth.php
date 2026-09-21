<?php
if (empty($_SESSION['id'])) {
    header('location: ' . BASE_URL . 'log.php');
    exit();
}
