<?php
require_once SITE_ROOT . "/app/database/db.php";
if (empty($_SESSION['id'])) {
   header('location: ' . BASE_URL . 'log.php');
   exit();
}

$errMsg = [];
$id = '';
$title = '';
$content = '';
$topic = '';
$img = '';

$topics = selectAll('topics');
$posts = selectAll('posts');
$postsAdm = selectAllFromPostsWithUsers('posts', 'users');

// Post creation form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])) {

   //tt($_FILES);
   if (!empty($_FILES['img']['name'])) {
      $imgName = time() . "_" . $_FILES['img']['name'];
      $fileTmpName = $_FILES['img']['tmp_name'];
      $fileType = $_FILES['img']['type'];
      $postsDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'posts';
      if (!is_dir($postsDir)) {
         mkdir($postsDir, 0777, true);
      }
      $destination = $postsDir . DIRECTORY_SEPARATOR . $imgName;

      //tt($_FILES);

      if (strpos($fileType, 'image') === false) {
         array_push($errMsg, "The uploaded file is not an image!");
      } else {
         $result = move_uploaded_file($fileTmpName, $destination);

         if ($result) {
            $_POST['img'] = $imgName;
         } else {
            array_push($errMsg, "Failed to upload the image to the server");
         }
      }
   } else {
      array_push($errMsg, "Failed to receive the image");
   }

   $title = trim($_POST['title']);
   $content = trim($_POST['content']);
   $topic = trim($_POST['topic']);
   $publish = isset($_POST['publish']) ? 1 : 0;
   //tt($_POST);

   if ($title === '' || $content === '' || $topic === 'Post category:') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (mb_strlen($title, 'UTF8') < 7) {
      array_push($errMsg, "The post title must be longer than 7 characters");
   } else {
      $add_post = [
         'id_user' => $_SESSION['id'],
         'title' => $title,
         'content' => $content,
         'img' => $_POST['img'],
         'status' => $publish,
         'id_topic' => $topic
      ];
      //tt($add_post);
      $post = insert('posts', $add_post);
      $post = selectOne('posts', ['id' => $id]);
      header('location: ' . BASE_URL . 'admin/posts/index.php');
      exit();
   }
} else {
   $id = '';
   $title = '';
   $content = '';
   $publish = 'Post category:';
   $topic = '';
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
   }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {

   $id = $_POST['id'];
   $title = trim($_POST['title']);
   $content = trim($_POST['content']);
   $topic = trim($_POST['topic']);
   $publish = isset($_POST['publish']) ? 1 : 0;

   if (!empty($_FILES['img']['name'])) {
      $imgName = time() . "_" . $_FILES['img']['name'];
      $fileTmpName = $_FILES['img']['tmp_name'];
      $fileType = $_FILES['img']['type'];
      $postsDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'posts';
      if (!is_dir($postsDir)) {
         mkdir($postsDir, 0777, true);
      }
      $destination = $postsDir . DIRECTORY_SEPARATOR . $imgName;

      //tt($_FILES);

      if (strpos($fileType, 'image') === false) {
         array_push($errMsg, "The uploaded file is not an image!");
      } else {
         $result = move_uploaded_file($fileTmpName, $destination);

         if ($result) {
            $_POST['img'] = $imgName;
         } else {
            array_push($errMsg, "Failed to upload the image to the server");
         }
      }
   } else {
      array_push($errMsg, "Failed to receive the image");
   }


   if ($title === '' || $content === '' || $topic === 'Post category:') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (mb_strlen($title, 'UTF8') < 7) {
      array_push($errMsg, "The post title must be longer than 7 characters");
   } else {
      $add_post = [
         'id_user' => $_SESSION['id'],
         'title' => $title,
         'content' => $content,
         'img' => $_POST['img'],
         'status' => $publish,
         'id_topic' => $topic
      ];

      $post = update('posts', $id, $add_post);
      header('location: ' . BASE_URL . 'admin/posts/index.php');
      exit();
   }
} else {
   $title = $_POST['title'] ?? $title;
   $content = $_POST['content'] ?? $content;
   $publish = isset($_POST['publish']) ? 1 : 0;
   $topic = $_POST['id_topic'] ?? $topic;
}

// Update publish status
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])) {
   $id = $_GET['pub_id'];
   $publish = $_GET['publish'];

   $postId = update('posts', $id, ['status' => $publish]);

   header('location: ' . BASE_URL . 'admin/posts/index.php');
   exit();
}


// Delete post
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
   $id = $_GET['delete_id'];
   delete('posts', $id);
   header('location: ' . BASE_URL . 'admin/posts/index.php');
   exit();
}