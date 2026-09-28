<?php
require_once SITE_ROOT . "/app/database/db.php";


$errMsg = '';
$id = '';
$name = '';
$description = '';

$topics = selectAll('topics');
$isTopicAdmin = isset($_SERVER['SCRIPT_NAME']) && str_contains($_SERVER['SCRIPT_NAME'], '/admin/topics/');

function topicActorIsAdmin()
{
   return !empty($_SESSION['id']) && (int) ($_SESSION['admin'] ?? 0) === 1;
}

// Category creation form
if ($isTopicAdmin && topicActorIsAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic-create'])) {

   $name = trim($_POST['name']);
   $description = trim($_POST['description']);


   if ($name === '' || $description === '') {
      $errMsg = "Please fill in all fields!";
   } elseif (mb_strlen($name, 'UTF8') < 2) {
      $errMsg = "The category name must be longer than 2 characters";
   } else {
      $existennce = selectOne('topics', ['name' => $name]);
      if ($existennce && $existennce['name'] === $name) {
         $errMsg = "This category already exists";
      } else {
         $add_topic = [
            'name' => $name,
            'description' => $description
         ];
         $id = insert('topics', $add_topic);
         $add_topic = selectOne('topics', ['id' => $id]);
         header('location: ' . BASE_URL . 'admin/topics/index.php');
         exit();
      }
   }
} else {
   $name = '';
   $description = '';
}

// Update category
if ($isTopicAdmin && topicActorIsAdmin() && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
   $id = $_GET['id'];
   $topic = selectOne('topics', ['id' => $id]);
   if ($topic) {
      $id = $topic['id'];
      $name = $topic['name'];
      $description = $topic['description'];
   }
}
if ($isTopicAdmin && topicActorIsAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic-edit'])) {

   $name = trim($_POST['name']);
   $description = trim($_POST['description']);


   if ($name === '' || $description === '') {
      $errMsg = "Please fill in all fields!";
   } elseif (mb_strlen($name, 'UTF8') < 2) {
      $errMsg = "The category name must be longer than 2 characters";
   } else {
      $id = $_POST['id'];
      $existennce = selectOne('topics', ['name' => $name]);
      if ($existennce && (int) $existennce['id'] !== (int) $id) {
         $errMsg = "This category already exists";
      } else {
         $add_topic = [
            'name' => $name,
            'description' => $description
         ];
         update('topics', $id, $add_topic);
         header('location: ' . BASE_URL . 'admin/topics/index.php');
         exit();
      }
   }
}


// Delete category
if ($isTopicAdmin && topicActorIsAdmin() && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['del-id'])) {
   $id = $_GET['del-id'];
   delete('topics', $id);
   header('location: ' . BASE_URL . 'admin/topics/index.php');
   exit();
}
