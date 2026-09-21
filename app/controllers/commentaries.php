<?php


require_once SITE_ROOT . "/app/database/db.php";
$commentsForAdm = selectAll('comments');
//tt($_GET);
$page = $_GET['post'] ?? ($_POST['page'] ?? '');
$email = '';
$comment = '';
$id = '';
$text1 = '';
$pub = 0;
$errMsg = [];
$status = 0;
$comments = [];

// Comment creation form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goComment'])) {

   $email = trim($_POST['email']);
   $comment = trim($_POST['comment']);

   if ($email === '' || $comment === '') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (mb_strlen($comment, 'UTF8') < 25) {
      array_push($errMsg, "The comment must be longer than 25 characters");
   } else {

      $user = selectOne('users', ['email' => $email]);
      if ($user && $user['email'] == $email && $user['admin'] == 1) {
         $status = 1;
      }

      $comment = [
         'status' => $status,
         'page' => $page,
         'email' => $email,
         'comment' => $comment
      ];
      $comment = insert('comments', $comment);
      $comments = selectAll('comments', ['page' => $page, 'status' => 1]);
      header('location: ' . BASE_URL . 'single.php?post=' . $page);
      exit();
   }
} else {
   $email = '';
   $comment = '';
   $comments = selectAll('comments', ['page' => $page, 'status' => 1]);
}
// Delete comment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
   $id = $_GET['delete_id'];
   delete('comments', $id);
   header('location: ' . BASE_URL . 'admin/comments/index.php');
   exit();
}
// Update publish status
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])) {
   $id = $_GET['pub_id'];
   $publish = $_GET['publish'];

   $postId = update('comments', $id, ['status' => $publish]);

   header('location: ' . BASE_URL . 'admin/comments/index.php');
   exit();
}

// Update comment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {

   $oneComment = selectOne('comments', ['id' => $_GET['id']]);
   if ($oneComment) {
      $id = $oneComment['id'];
      $email = $oneComment['email'];
      $text1 = $oneComment['comment'];
      $pub = $oneComment['status'];
   }
   //tte($oneComment);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {

   $id = $_POST['id'];
   $text = trim($_POST['content']);
   $publish = isset($_POST['publish']) ? 1 : 0;

   if ($text === '') {
      array_push($errMsg, "The comment has no text!");
   } elseif (mb_strlen($text, 'UTF8') < 25) {
      array_push($errMsg, "The comment must be longer than 25 characters");
   } else {
      $edit_com = [
         'comment' => $text,
         'status' => $publish
      ];

      $comment = update('comments', $id, $edit_com);
      header('location: ' . BASE_URL . 'admin/comments/index.php');
      exit();
   }
} else {
   $text = trim($_POST['content'] ?? '');
   $publish = isset($_POST['publish']) ? 1 : 0;
}
