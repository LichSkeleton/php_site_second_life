<?php
require_once "path.php";
require_once SITE_ROOT . "/app/database/db.php";

$posts = [];
$searchTerm = '';
$searchError = '';
$searchDone = false;
$resultCount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search-term'])) {
   $searchDone = true;
   $searchTerm = trim((string) $_POST['search-term']);

   if ($searchTerm === '') {
      $searchError = 'Please enter a search term.';
   } elseif (mb_strlen($searchTerm, 'UTF-8') < 2) {
      $searchError = 'The search term must be at least 2 characters.';
   } else {
      $posts = searchInTitleAndContent($searchTerm, 'posts', 'users');
      if (!is_array($posts)) {
         $posts = [];
      }
      $resultCount = count($posts);
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
   <title>Search | My blog</title>
</head>

<body>
   <?php include("app/include/header.php"); ?>

   <div class="container">
      <div class="content row">
         <div class="main-content col-12">
            <h2>Search results</h2>

            <?php if (!$searchDone) : ?>
               <p class="info-empty">Use the search form to find published posts.</p>
            <?php elseif ($searchError !== '') : ?>
               <p class="info-error"><?= htmlspecialchars($searchError); ?></p>
            <?php else : ?>
               <p>
                  Query: <strong><?= htmlspecialchars($searchTerm); ?></strong>.
                  Found <strong><?= (int) $resultCount; ?></strong>
                  <?= $resultCount === 1 ? 'post' : 'posts'; ?>.
               </p>
               <?php if ($resultCount === 0) : ?>
                  <p class="info-empty">No published posts match this term.</p>
               <?php endif; ?>
            <?php endif; ?>

            <?php foreach ($posts as $post) : ?>
               <div class="post row">
                  <div class="img col-12 col-md-4">
                     <img src="<?= BASE_URL . 'assets/img/posts/' . $post['img'] ?>" alt="<?= $post['title'] ?>" class="img-thumbnail">
                  </div>
                  <div class="post_text col-12 col-md-8">
                     <h3>
                        <a href="<?= BASE_URL . 'single.php?post=' . $post['id']; ?>"><?= substr($post['title'], 0, 80) . "..." ?></a>
                     </h3>
                     <i><?= $post['username']; ?></i>
                     <i><?= $post['created_date']; ?></i>
                     <p class="preview-text">
                        <?= mb_substr($post['content'], 0, 55, 'UTF-8') . "..." ?>
                     </p>
                  </div>
               </div>
            <?php endforeach; ?>

         </div>
      </div>
   </div>

   <?php include("app/include/footer.php"); ?>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
