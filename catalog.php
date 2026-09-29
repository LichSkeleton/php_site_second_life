<?php
require_once 'path.php';
require_once SITE_ROOT . '/app/database/db.php';
require_once SITE_ROOT . '/app/include/structured.php';

$feed = publishedPostsFeed();
$byTopic = [];
foreach ($feed as $post) {
    $topic = $post['topic'] !== '' ? $post['topic'] : 'No category';
    $byTopic[$topic][] = $post;
}
ksort($byTopic);
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
   <title>Catalog | My blog</title>
</head>

<body>
   <?php include 'app/include/header.php'; ?>

   <div class="container">
      <div class="content row">
         <div class="main-content col-12">
            <h2>Catalog</h2>
            <p>Published posts from the database. The same records are available as <a href="<?= BASE_URL; ?>api/posts.php">JSON</a> and <a href="<?= BASE_URL; ?>api/catalog.xml.php">XML</a>.</p>
            <?php if (count($feed) === 0) : ?>
               <p>The catalog is empty.</p>
            <?php endif; ?>
            <?php foreach ($byTopic as $topic => $posts) : ?>
               <h3><?= htmlspecialchars($topic); ?></h3>
               <?php foreach ($posts as $post) : ?>
                  <div class="post row">
                     <div class="post_text col-12">
                        <h3><a href="<?= htmlspecialchars($post['url']); ?>"><?= htmlspecialchars($post['title']); ?></a></h3>
                        <i><?= htmlspecialchars($post['author']); ?></i>
                        <i><?= htmlspecialchars(formatAppDate($post['created_at'])); ?></i>
                        <p class="preview-text"><?= htmlspecialchars(mb_substr(trim(strip_tags($post['content'])), 0, 140, 'UTF-8')); ?>...</p>
                     </div>
                  </div>
               <?php endforeach; ?>
            <?php endforeach; ?>
         </div>
      </div>
   </div>

   <?php include 'app/include/footer.php'; ?>
</body>

</html>
