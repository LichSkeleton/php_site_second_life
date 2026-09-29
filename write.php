<?php
require_once 'path.php';
require_once SITE_ROOT . '/app/database/db.php';
require_once SITE_ROOT . '/app/include/post-rules.php';

if (empty($_SESSION['id'])) {
    header('location: ' . BASE_URL . 'log.php');
    exit();
}

$errMsg = [];
$title = '';
$content = '';
$topic = '';
$topics = selectAll('topics');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $topic = trim($_POST['topic'] ?? '');
    $storedImage = storeUploadedPostImage($_FILES['img'] ?? [], $errMsg);

    if ($storedImage === false) {
        // The upload helper already recorded the reason.
    } elseif (postFieldsAreValid($title, $content, $topic, $errMsg) && empty($errMsg)) {
        $imageName = is_string($storedImage) ? $storedImage : 'post_php.jpg';
        saveUnpublishedPost((int) $_SESSION['id'], $title, $content, (int) $topic, $imageName);
        header('location: ' . BASE_URL . 'profile.php?saved=1');
        exit();
    }
}
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
   <title>Write a post | My blog</title>
</head>

<body>
   <?php include 'app/include/header.php'; ?>

   <div class="container">
      <div class="content row">
         <div class="main-content col-12 col-md-8">
            <h2>Write a post</h2>
            <p class="info-empty">The server saves this post as unpublished. It appears on the site only after an administrator publishes it. The review queue is oldest first.</p>
            <form method="post" action="write.php" enctype="multipart/form-data">
               <div class="mb-3 err">
                  <?php include 'app/helps/errorInfo.php'; ?>
               </div>
               <div class="mb-3">
                  <label for="post-title" class="form-label">Title</label>
                  <input id="post-title" name="title" value="<?= htmlspecialchars($title); ?>" type="text" class="form-control" placeholder="Title">
               </div>
               <div class="mb-3">
                  <label for="post-content" class="form-label">Text</label>
                  <textarea id="post-content" name="content" class="form-control" rows="8"><?= htmlspecialchars($content); ?></textarea>
               </div>
               <div class="mb-3">
                  <label for="post-topic" class="form-label">Category</label>
                  <select id="post-topic" name="topic" class="form-select">
                     <option value="" <?= ($topic === '') ? 'selected' : '' ?>>Post category:</option>
                     <?php foreach ($topics as $topicItem) : ?>
                        <option value="<?= (int) $topicItem['id']; ?>" <?= (string) $topic === (string) $topicItem['id'] ? 'selected' : '' ?>><?= htmlspecialchars($topicItem['name']); ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="mb-3">
                  <label for="post-image" class="form-label">Image (optional)</label>
                  <input id="post-image" name="img" type="file" class="form-control">
               </div>
               <button name="add_post" class="btn btn-primary" type="submit">Submit for review</button>
               <a class="btn btn-secondary" href="<?= BASE_URL; ?>profile.php">Back to profile</a>
            </form>
         </div>
      </div>
   </div>

   <?php include 'app/include/footer.php'; ?>
</body>

</html>
