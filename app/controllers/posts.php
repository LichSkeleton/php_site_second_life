<?php
require_once SITE_ROOT . "/app/database/db.php";
require_once SITE_ROOT . "/app/include/post-rules.php";
if (empty($_SESSION['id']) || (int) ($_SESSION['admin'] ?? 0) !== 1) {
   header('location: ' . (empty($_SESSION['id']) ? BASE_URL . 'log.php' : BASE_URL));
   exit();
}

$errMsg = [];
$id = '';
$title = '';
$content = '';
$topic = '';
$img = '';
$publish = 1;
$createdDate = '';
$updatedDate = '';

$topics = selectAll('topics');
$posts = selectAll('posts');
$postsAdm = selectAllFromPostsWithUsers('posts', 'users');

// Post creation form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])) {
   $title = trim($_POST['title'] ?? '');
   $content = trim($_POST['content'] ?? '');
   $topic = trim($_POST['topic'] ?? '');
   $publish = isset($_POST['publish']) ? 1 : 0;
   $storedImage = storeUploadedPostImage($_FILES['img'] ?? [], $errMsg);

   if ($storedImage === null) {
      array_push($errMsg, "Failed to receive the image");
   }

   if (postFieldsAreValid($title, $content, $topic, $errMsg) && $storedImage && empty($errMsg)) {
      insert('posts', [
         'id_user' => $_SESSION['id'],
         'title' => $title,
         'content' => $content,
         'img' => $storedImage,
         'status' => $publish,
         'id_topic' => (int) $topic
      ]);
      header('location: ' . BASE_URL . 'admin/posts/index.php');
      exit();
   }
}

// Update post
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
   $post = selectOne('posts', ['id' => $_GET['id']]);
   if ($post) {
      $id = $post['id'];
      $title = $post['title'];
      $content = $post['content'];
      $topic = $post['id_topic'];
      $publish = $post['status'];
      $img = $post['img'];
      $createdDate = $post['created_date'];
      $updatedDate = $post['updated_date'];
   }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {
   $id = (int) ($_POST['id'] ?? 0);
   $title = trim($_POST['title'] ?? '');
   $content = trim($_POST['content'] ?? '');
   $topic = trim($_POST['topic'] ?? '');
   $publish = isset($_POST['publish']) ? 1 : 0;
   $existing = $id > 0 ? selectOne('posts', ['id' => $id]) : false;
   $img = $existing['img'] ?? '';
   $createdDate = $existing['created_date'] ?? '';
   $updatedDate = $existing['updated_date'] ?? '';
   $storedImage = storeUploadedPostImage($_FILES['img'] ?? [], $errMsg);

   if (!$existing) {
      array_push($errMsg, "Post not found.");
   } elseif ($storedImage === false) {
      // The upload helper already recorded the reason.
   } elseif (postFieldsAreValid($title, $content, $topic, $errMsg) && empty($errMsg)) {
      if (is_string($storedImage)) {
         $img = $storedImage;
      }
      update('posts', $id, [
         'title' => $title,
         'content' => $content,
         'img' => $img,
         'status' => $publish,
         'id_topic' => (int) $topic
      ]);
      header('location: ' . BASE_URL . 'admin/posts/index.php');
      exit();
   }
}

// Update publish status
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])) {
   $publish = ((int) ($_GET['publish'] ?? 0) === 1) ? 1 : 0;
   update('posts', $_GET['pub_id'], ['status' => $publish]);
   header('location: ' . BASE_URL . 'admin/posts/index.php');
   exit();
}

// Delete post
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
   delete('posts', $_GET['delete_id']);
   header('location: ' . BASE_URL . 'admin/posts/index.php');
   exit();
}
