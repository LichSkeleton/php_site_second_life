<?php
require_once SITE_ROOT . "/app/database/db.php";

$errMsg = [];
$id = '';
$username = '';
$admin = 0;
$login = '';
$email = '';

if (!empty($_SESSION['flash_error'])) {
   $errMsg[] = $_SESSION['flash_error'];
   unset($_SESSION['flash_error']);
}

if (!function_exists('userAuth')) {
function userAuth($user)
{
   session_regenerate_id(true);
   $now = date('Y-m-d H:i:s');
   update('users', (int) $user['id'], ['last_login' => $now]);
   $_SESSION['id'] = $user['id'];
   $_SESSION['login'] = $user['username'];
   $_SESSION['admin'] = $user['admin'];
   $_SESSION['last_login'] = $now;

   if ((int) $_SESSION['admin'] === 1) {
      header('location: ' . BASE_URL . "admin/posts/index.php");
   } else {
      header('location: ' . BASE_URL . 'profile.php');
   }
   exit();
}
}

function textLength($value)
{
   return mb_strlen($value, 'UTF-8');
}

$users = selectAll('users');
$isUserAdmin = isset($_SERVER['SCRIPT_NAME']) && str_contains($_SERVER['SCRIPT_NAME'], '/admin/users/');

function userActorIsAdmin()
{
   return !empty($_SESSION['id']) && (int) ($_SESSION['admin'] ?? 0) === 1;
}

// Registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-reg'])) {
   $login = trim($_POST['login'] ?? '');
   $email = trim($_POST['mail'] ?? '');
   $passF = trim($_POST['pass-first'] ?? '');
   $passS = trim($_POST['pass-second'] ?? '');

   if ($login === '' || $email === '' || $passF === '' || $passS === '') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (textLength($login) < 2) {
      array_push($errMsg, "The username must be longer than 2 characters");
   } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      array_push($errMsg, "Enter a valid email address.");
   } elseif ($passF !== $passS) {
      array_push($errMsg, "The passwords in both fields must match!");
   } else {
      $existennce = selectOne('users', ['email' => $email]);
      if ($existennce && $existennce['email'] === $email) {
         array_push($errMsg, "A user with this email is already registered!");
      } else {
         $id = insert('users', [
            'admin' => 0,
            'username' => $login,
            'email' => $email,
            'password' => password_hash($passF, PASSWORD_DEFAULT)
         ]);
         $user = selectOne('users', ['id' => $id]);
         userAuth($user);
      }
   }
}

// Login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-log'])) {
   $email = trim($_POST['mail'] ?? '');
   $pass = trim($_POST['password'] ?? '');

   if ($email === '' || $pass === '') {
      array_push($errMsg, "Please fill in all fields!");
   } else {
      $existennce = selectOne('users', ['email' => $email]);
      if ($existennce && password_verify($pass, $existennce['password'])) {
         userAuth($existennce);
      } else {
         array_push($errMsg, "Email or password is incorrect!");
      }
   }
}

// Add user from the admin panel
if ($isUserAdmin && userActorIsAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create-user'])) {
   $login = trim($_POST['login'] ?? '');
   $email = trim($_POST['mail'] ?? '');
   $passF = trim($_POST['pass-first'] ?? '');
   $passS = trim($_POST['pass-second'] ?? '');
   $admin = isset($_POST['admin-pub']) ? 1 : 0;

   if ($login === '' || $email === '' || $passF === '' || $passS === '') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (textLength($login) < 2) {
      array_push($errMsg, "The username must be longer than 2 characters");
   } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      array_push($errMsg, "Enter a valid email address.");
   } elseif ($passF !== $passS) {
      array_push($errMsg, "The passwords in both fields must match!");
   } else {
      $existennce = selectOne('users', ['email' => $email]);
      if ($existennce && $existennce['email'] === $email) {
         array_push($errMsg, "A user with this email is already registered!");
      } else {
         insert('users', [
            'admin' => $admin,
            'username' => $login,
            'email' => $email,
            'password' => password_hash($passF, PASSWORD_DEFAULT)
         ]);
         header('location: ' . BASE_URL . 'admin/users/index.php');
         exit();
      }
   }
}

// Delete user from the admin panel
if ($isUserAdmin && userActorIsAdmin() && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
   $id = (int) $_GET['delete_id'];
   $target = selectOne('users', ['id' => $id]);
   $adminCount = 0;
   foreach ($users as $existingUser) {
      if ((int) $existingUser['admin'] === 1) {
         $adminCount++;
      }
   }

   if (!$target) {
      $_SESSION['flash_error'] = "User not found.";
   } elseif ((int) $target['id'] === (int) ($_SESSION['id'] ?? 0)) {
      $_SESSION['flash_error'] = "You cannot delete your own account.";
   } elseif ((int) $target['admin'] === 1 && $adminCount <= 1) {
      $_SESSION['flash_error'] = "You cannot delete the only administrator.";
   } else {
      delete('users', $id);
   }
   header('location: ' . BASE_URL . 'admin/users/index.php');
   exit();
}

// Edit user from the admin panel
if ($isUserAdmin && userActorIsAdmin() && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_id'])) {
   $user = selectOne('users', ['id' => $_GET['edit_id']]);
   if ($user) {
      $id = $user['id'];
      $admin = $user['admin'];
      $username = $user['username'];
      $email = $user['email'];
   }
}

if ($isUserAdmin && userActorIsAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-user'])) {
   $id = (int) ($_POST['id'] ?? 0);
   $email = trim($_POST['mail'] ?? '');
   $login = trim($_POST['login'] ?? '');
   $username = $login;
   $passF = trim($_POST['pass-first'] ?? '');
   $passS = trim($_POST['pass-second'] ?? '');
   $admin = isset($_POST['admin-pub']) ? 1 : 0;
   $isSelf = (int) $id === (int) ($_SESSION['id'] ?? 0);

   if ($isSelf) {
      $admin = 1;
   }

   if ($login === '' || $email === '') {
      array_push($errMsg, "Please fill in all fields!");
   } elseif (textLength($login) < 2) {
      array_push($errMsg, "The username must be longer than 2 characters");
   } elseif ($passF !== '' || $passS !== '') {
      if ($passF !== $passS) {
         array_push($errMsg, "The passwords in both fields must match!");
      } else {
         $updated = [
            'admin' => $admin,
            'username' => $login,
            'password' => password_hash($passF, PASSWORD_DEFAULT)
         ];
         update('users', $id, $updated);
         if ($isSelf) {
            $_SESSION['login'] = $login;
            $_SESSION['admin'] = 1;
         }
         header('location: ' . BASE_URL . 'admin/users/index.php');
         exit();
      }
   } else {
      update('users', $id, [
         'admin' => $admin,
         'username' => $login
      ]);
      if ($isSelf) {
         $_SESSION['login'] = $login;
         $_SESSION['admin'] = 1;
      }
      header('location: ' . BASE_URL . 'admin/users/index.php');
      exit();
   }
}
