<?php
require_once "path.php";
require_once "app/controllers/topics.php";
require_once "app/helps/site-info.php";

$blogSummary = buildBlogSummary($topics);
$services = getBlogServices();
$siteAuthor = getSiteAuthor();
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
   <title>Services | My blog</title>
</head>

<body>
   <?php include "app/include/header.php"; ?>

   <div class="container">
      <div class="content row">
         <div class="main-content col-md-9 col-12">
            <h2>Services</h2>
            <p>
               <?= htmlspecialchars($siteAuthor['project']); ?> offers these server-side features.
               The list is stored in a PHP array and printed with a loop.
            </p>
            <?php include "app/include/blog-summary.php"; ?>

            <?php foreach ($services as $service) : ?>
               <article class="service-card">
                  <h3><?= htmlspecialchars($service['title']); ?></h3>
                  <p><?= htmlspecialchars($service['description']); ?></p>
                  <p><strong>Audience:</strong> <?= htmlspecialchars($service['audience']); ?></p>
               </article>
            <?php endforeach; ?>
         </div>
         <div class="sidebar col-md-3 col-12">
            <div class="section">
               <h3>Search</h3>
               <form action="search.php" method="post">
                  <input type="text" name="search-term" class="text-input" placeholder="Enter a search term...">
               </form>
            </div>
            <div class="section topics">
               <h3>Categories</h3>
               <ul>
                  <?php foreach ($topics as $topic) : ?>
                     <li><a href="<?= BASE_URL . 'category.php?id=' . $topic['id']; ?>"><?= htmlspecialchars($topic['name']); ?></a></li>
                  <?php endforeach; ?>
               </ul>
            </div>
         </div>
      </div>
   </div>

   <?php include "app/include/footer.php"; ?>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
