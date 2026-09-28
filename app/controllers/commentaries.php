<?php

require_once SITE_ROOT . "/app/database/db.php";

$errMsg = [];
$email = '';
$commentText = '';
$id = '';
$text1 = '';
$pub = 0;
$comments = [];
$commentNotice = '';

if (!isset($page)) {
   $page = $_GET['post'] ?? ($_POST['page'] ?? 0);
}
$page = (int) $page;

$isCommentAdmin = isset($_SERVER['SCRIPT_NAME']) && str_contains($_SERVER['SCRIPT_NAME'], '/admin/comments/');
$commentsForAdm = [];

function commentShouldPublishNow()
{
   if (empty($_SESSION['id'])) {
      return false;
   }
   $user = selectOne('users', ['id' => $_SESSION['id']]);
   return (bool) $user;
}

function requireCommentAdmin()
{
   if (empty($_SESSION['id']) || (int) ($_SESSION['admin'] ?? 0) !== 1) {
      header('location: ' . BASE_URL);
      exit();
   }
}

if ($isCommentAdmin) {
   requireCommentAdmin();
   $commentsForAdm = selectAll('comments');
   usort($commentsForAdm, function ($a, $b) {
      return (int) $b['id'] <=> (int) $a['id'];
   });
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
   requireCommentAdmin();
   delete('comments', $_GET['delete_id']);
   header('location: ' . BASE_URL . 'admin/comments/index.php');
   exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])) {
   requireCommentAdmin();
   $publish = ((int) ($_GET['publish'] ?? 0) === 1) ? 1 : 0;
   update('comments', $_GET['pub_id'], ['status' => $publish]);
   header('location: ' . BASE_URL . 'admin/comments/index.php');
   exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && $isCommentAdmin) {
   $oneComment = selectOne('comments', ['id' => $_GET['id']]);
   if ($oneComment) {
      $id = $oneComment['id'];
      $email = $oneComment['email'];
      $text1 = $oneComment['comment'];
      $pub = $oneComment['status'];
   }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
   requireCommentAdmin();

   $id = (int) ($_POST['id'] ?? 0);
   $text = trim($_POST['content'] ?? '');
   $publish = isset($_POST['publish']) ? 1 : 0;
   $existing = $id > 0 ? selectOne('comments', ['id' => $id]) : false;
   $email = $existing['email'] ?? '';
   $text1 = $text;
   $pub = $publish;

   if (!$existing) {
      array_push($errMsg, "Comment not found.");
   } elseif ($text === '') {
      array_push($errMsg, "The comment has no text!");
   } elseif (mb_strlen($text, 'UTF-8') < 25) {
      array_push($errMsg, "The comment must be at least 25 characters");
   } else {
      update('comments', $id, [
         'comment' => $text,
         'status' => $publish
      ]);
      header('location: ' . BASE_URL . 'admin/comments/index.php');
      exit();
   }
}

if (!empty($_SESSION['comment_flash']) && (int) ($_SESSION['comment_flash']['page'] ?? 0) === $page) {
   $commentNotice = (string) ($_SESSION['comment_flash']['text'] ?? '');
   unset($_SESSION['comment_flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goComment'])) {
   $email = trim($_POST['email'] ?? '');
   $commentText = trim($_POST['comment'] ?? '');

   if ($page < 1 || !selectOne('posts', ['id' => $page])) {
      array_push($errMsg, "This post does not exist.");
   } elseif ($email === '' || $commentText === '') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      array_push($errMsg, "Enter a valid email address.");
   } elseif (mb_strlen($commentText, 'UTF-8') < 25) {
      array_push($errMsg, "The comment must be at least 25 characters");
   } else {
      $status = commentShouldPublishNow() ? 1 : 0;
      $newId = insert('comments', [
         'status' => $status,
         'page' => $page,
         'email' => $email,
         'comment' => $commentText
      ]);
      if ($status !== 1) {
         if (!isset($_SESSION['pending_comments']) || !is_array($_SESSION['pending_comments'])) {
            $_SESSION['pending_comments'] = [];
         }
         $_SESSION['pending_comments'][(int) $newId] = $page;
      }
      $_SESSION['comment_flash'] = [
         'page' => $page,
         'text' => $status === 1
            ? 'Your comment has been published.'
            : 'Your comment was saved. It stays visible to you here and appears for everyone after an administrator publishes it.'
      ];
      header('location: ' . BASE_URL . 'single.php?post=' . $page);
      exit();
   }
}

if ($page > 0) {
   $comments = selectAll('comments', ['page' => $page, 'status' => 1]);
   usort($comments, function ($a, $b) {
      return (int) $a['id'] <=> (int) $b['id'];
   });
}

$visibleIds = [];
foreach ($comments as $existingComment) {
   $visibleIds[(int) $existingComment['id']] = true;
}
if (!empty($_SESSION['pending_comments']) && is_array($_SESSION['pending_comments'])) {
   foreach ($_SESSION['pending_comments'] as $pendingId => $pendingPage) {
      if ((int) $pendingPage !== $page) {
         continue;
      }
      $pendingComment = selectOne('comments', ['id' => (int) $pendingId]);
      if (!$pendingComment || (int) $pendingComment['status'] === 1 || (int) $pendingComment['page'] !== $page) {
         unset($_SESSION['pending_comments'][$pendingId]);
         continue;
      }
      if (!isset($visibleIds[(int) $pendingComment['id']])) {
         $pendingComment['pending'] = true;
         $comments[] = $pendingComment;
         $visibleIds[(int) $pendingComment['id']] = true;
      }
   }
}

if ($email === '' && !empty($_SESSION['id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
   $sessionUser = selectOne('users', ['id' => $_SESSION['id']]);
   if ($sessionUser) {
      $email = $sessionUser['email'];
   }
}
