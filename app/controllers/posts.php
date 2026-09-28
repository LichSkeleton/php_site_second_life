<?php
require_once SITE_ROOT . "/app/database/db.php";
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

$topics = selectAll('topics');
$posts = selectAll('posts');
$postsAdm = selectAllFromPostsWithUsers('posts', 'users');

function storeUploadedPostImage($file, &$errMsg)
{
   if (empty($file['name'])) {
      return null;
   }
   if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
      array_push($errMsg, "Failed to upload the image to the server");
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
      array_push($errMsg, "The uploaded file is not an image!");
      return false;
   }

   $imgName = time() . "_" . bin2hex(random_bytes(4)) . "." . $types[$info[2]];
   $postsDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'posts';
   if (!is_dir($postsDir)) {
      mkdir($postsDir, 0777, true);
   }
   $destination = $postsDir . DIRECTORY_SEPARATOR . $imgName;
   if (!move_uploaded_file($file['tmp_name'], $destination)) {
      array_push($errMsg, "Failed to upload the image to the server");
      return false;
   }
   return $imgName;
}

function postFieldsAreValid($title, $content, $topic, &$errMsg)
{
   if ($title === '' || $content === '' || $topic === '' || $topic === 'Post category:') {
      array_push($errMsg, "Please fill in all fields!");
      return false;
   }
   if (mb_strlen($title, 'UTF-8') < 7) {
      array_push($errMsg, "The post title must be longer than 7 characters");
      return false;
   }
   $topicRow = selectOne('topics', ['id' => (int) $topic]);
   if (!$topicRow) {
      array_push($errMsg, "Choose a category.");
      return false;
   }
   return true;
}

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
