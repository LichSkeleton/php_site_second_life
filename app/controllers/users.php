<?php
require_once SITE_ROOT . "/app/database/db.php";

$errMsg = [];
$id = '';
$username = '';
$admin = 0;

if (!function_exists('userAuth')) {
function userAuth($user)
{
   $_SESSION['id'] = $user['id'];
   $_SESSION['login'] = $user['username'];
   $_SESSION['admin'] = $user['admin'];

   if ($_SESSION['admin']) {
      header('location: ' . BASE_URL . "admin/posts/index.php");
   } else {
      header('location: ' . BASE_URL);
   }
   exit();
}
}

$users = selectAll('users');

// Registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-reg'])) {

   $admin = 0;
   $login = trim($_POST['login']);
   $email = trim($_POST['mail']);
   $passF = trim($_POST['pass-first']);
   $passS = trim($_POST['pass-second']);

   if ($login === '' || $email === '' || $passF === '' || $passS === '') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (mb_strlen($login, 'UTF8') < 2) {
      array_push($errMsg, "The username must be longer than 2 characters");
   } elseif ($passF !== $passS) {
      array_push($errMsg, "The passwords in both fields must match!");
   } else {
      $existennce = selectOne('users', ['email' => $email]);
      if ($existennce && $existennce['email'] === $email) {
         array_push($errMsg, "A user with this email is already registered!");
      } else {
         $pass = password_hash($passF, PASSWORD_DEFAULT);
         $post = [
            'admin' => $admin,
            'username' => $login,
            'email' => $email,
            'password' => $pass
         ];
         $id = insert('users', $post);
         $user = selectOne('users', ['id' => $id]);

         userAuth($user);
      }
   }
} else {
   $login = '';
   $email = '';
}

// Login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-log'])) {

   $email = trim($_POST['mail']);
   $pass = trim($_POST['password']);

   if ($email === '' || $pass === '') {
      array_push($errMsg, "Please fill in all fields!");
   } else {
      $existennce = selectOne('users', ['email' => $email]);
      if ($existennce && password_verify($pass, $existennce['password'])) {

         userAuth($existennce);
      } else {
         // Login error
         array_push($errMsg, "Email or password is incorrect!");
      }
   }
} else {
   $email = '';
}

// Add user from the admin panel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create-user'])) {


   $admin = 0;
   $login = trim($_POST['login']);
   $email = trim($_POST['mail']);
   $passF = trim($_POST['pass-first']);
   $passS = trim($_POST['pass-second']);

   if ($login === '' || $email === '' || $passF === '' || $passS === '') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (mb_strlen($login, 'UTF8') < 2) {
      array_push($errMsg, "The username must be longer than 2 characters");
   } elseif ($passF !== $passS) {
      array_push($errMsg, "The passwords in both fields must match!");
   } else {
      $existennce = selectOne('users', ['email' => $email]);
      if ($existennce && $existennce['email'] === $email) {
         array_push($errMsg, "A user with this email is already registered!");
      } else {
         $pass = password_hash($passF, PASSWORD_DEFAULT);
         if (isset($_POST['admin-pub'])) $admin = 1;
         $post = [
            'admin' => $admin,
            'username' => $login,
            'email' => $email,
            'password' => $pass
         ];
         $id = insert('users', $post);
         $user = selectOne('users', ['id' => $id]);

         userAuth($user);
      }
   }
} else {
   $login = '';
   $email = '';
}

// Delete user from the admin panel
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
   $id = $_GET['delete_id'];
   delete('users', $id);
   header('location: ' . BASE_URL . 'admin/users/index.php');
   exit();
}

// Edit user from the admin panel
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_id'])) {
   $user = selectOne('users', ['id' => $_GET['edit_id']]);
   if ($user) {
      $id = $user['id'];
      $admin = $user['admin'];
      $username = $user['username'];
      $email = $user['email'];
   }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-user'])) {

   $id = $_POST['id'];
   $mail = trim($_POST['mail']);
   $login = trim($_POST['login']);
   $passF = trim($_POST['pass-first']);
   $passS = trim($_POST['pass-second']);
   $admin = isset($_POST['admin-pub']) ? 1 : 0;

   if ($login === '') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (mb_strlen($login, 'UTF8') < 2) {
      array_push($errMsg, "The username must be longer than 2 characters");
   } elseif ($passF !== $passS) {
      array_push($errMsg, "The passwords in both fields must match!");
   } else {
      $pass = password_hash($passF, PASSWORD_DEFAULT);
      if (isset($_POST['admin-pub'])) $admin = 1;
      $add_post = [
         'admin' => $admin,
         'username' => $login,
         'password' => $pass
      ];

      $user = update('users', $id, $add_post);
      header('location: ' . BASE_URL . 'admin/users/index.php');
      exit();
   }
} elseif (isset($user) && is_array($user)) {
   $login = $user['id'];
   $admin = $user['admin'];
   $username = $user['username'];
   $email = $user['email'];
}
