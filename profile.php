<?php
require_once 'path.php';
require_once SITE_ROOT . '/app/database/db.php';

if (empty($_SESSION['id'])) {
    header('location: ' . BASE_URL . 'log.php');
    exit();
}

$user = selectOne('users', ['id' => (int) $_SESSION['id']]);
if (!$user) {
    header('location: ' . BASE_URL . 'logout.php');
    exit();
}

$ownPosts = selectAll('posts', ['id_user' => (int) $user['id']]);
usort($ownPosts, function ($a, $b) {
    $byDate = strcmp((string) ($b['created_date'] ?? ''), (string) ($a['created_date'] ?? ''));
    return $byDate !== 0 ? $byDate : ((int) $b['id'] <=> (int) $a['id']);
});
$role = ((int) $user['admin'] === 1) ? 'Administrator' : 'Reader';
$savedNotice = isset($_GET['saved']);
$theme = siteTheme();
?>
<!doctype html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
   <link rel="stylesheet" href="assets/css/style1.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;700&display=swap" rel="stylesheet">
   <title>Profile | My blog</title>
</head>

<body>
   <?php include 'app/include/header.php'; ?>

   <div class="container">
      <div class="content row">
         <div class="main-content col-12">
            <h2>Profile</h2>
            <p>This page is available only after sign-in. The password is stored as a hash and is not shown here.</p>
            <div class="author-card">
               <p><strong>Username:</strong> <?= htmlspecialchars((string) $user['username']); ?></p>
               <p><strong>Email:</strong> <?= htmlspecialchars((string) $user['email']); ?></p>
               <p><strong>Role:</strong> <?= htmlspecialchars($role); ?></p>
               <p><strong>Last sign-in:</strong>
                  <?php if (!empty($user['last_login'])) : ?>
                     <?= htmlspecialchars(formatAppDate($user['last_login'])); ?> (Europe/Kyiv)
                  <?php else : ?>
                     No sign-in has been recorded yet.
                  <?php endif; ?>
               </p>
               <p><strong>Theme cookie:</strong> <?= htmlspecialchars($theme); ?>. The cookie stores only this preference, not the password or the session.</p>
               <form method="post" action="<?= BASE_URL; ?>theme.php">
                  <button class="btn btn-secondary" type="submit" name="theme" value="<?= $theme === 'dark' ? 'light' : 'dark'; ?>">
                     Switch to <?= $theme === 'dark' ? 'light' : 'dark'; ?> theme
                  </button>
               </form>
            </div>

            <h3>Your posts</h3>
            <p><a class="btn btn-primary" href="<?= BASE_URL; ?>write.php">Write a post</a></p>
            <?php if ($savedNotice) : ?>
               <p class="info-ok">Your post was saved as unpublished. An administrator will review the queue, oldest first, and publish it.</p>
            <?php endif; ?>
            <?php if (count($ownPosts) === 0) : ?>
               <p>You have not submitted a post yet. A new post stays unpublished until an administrator publishes it.</p>
            <?php else : ?>
               <?php foreach ($ownPosts as $post) : ?>
                  <div class="post row">
                     <div class="post_text col-12">
                        <h3>
                           <?php if ((int) $post['status'] === 1) : ?>
                              <a href="<?= BASE_URL . 'single.php?post=' . (int) $post['id']; ?>"><?= htmlspecialchars((string) $post['title']); ?></a>
                           <?php else : ?>
                              <?= htmlspecialchars((string) $post['title']); ?>
                           <?php endif; ?>
                        </h3>
                        <i><?= ((int) $post['status'] === 1) ? 'Published' : 'Awaiting review'; ?></i>
                        <i>Created: <?= htmlspecialchars(formatAppDate($post['created_date'])); ?></i>
                        <i>Updated: <?= htmlspecialchars(formatAppDate($post['updated_date'] ?? '')); ?></i>
                     </div>
                  </div>
               <?php endforeach; ?>
            <?php endif; ?>
            <p><a href="<?= BASE_URL; ?>logout.php">Log out</a></p>
         </div>
      </div>
   </div>

   <?php include 'app/include/footer.php'; ?>
</body>

</html>
