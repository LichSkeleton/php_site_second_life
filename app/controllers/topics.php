<?php
require_once SITE_ROOT . "/app/database/db.php";


$errMsg = '';
$id = '';
$name = '';
$description = '';

$topics = selectAll('topics');

// Category creation form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic-create'])) {

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
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
   $id = $_GET['id'];
   $topic = selectOne('topics', ['id' => $id]);
   if ($topic) {
      $id = $topic['id'];
      $name = $topic['name'];
      $description = $topic['description'];
   }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic-edit'])) {

   $name = trim($_POST['name']);
   $description = trim($_POST['description']);


   if ($name === '' || $description === '') {
      $errMsg = "Please fill in all fields!";
   } elseif (mb_strlen($name, 'UTF8') < 2) {
      $errMsg = "The category name must be longer than 2 characters";
   } else {
      $add_topic = [
         'name' => $name,
         'description' => $description
      ];
      $id = $_POST['id'];
      $topic_id = update('topics', $id, $add_topic);
      header('location: ' . BASE_URL . 'admin/topics/index.php');
      exit();
   }
}


// Delete category
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['del-id'])) {
   $id = $_GET['del-id'];
   delete('topics', $id);
   header('location: ' . BASE_URL . 'admin/topics/index.php');
   exit();
}
