<?php
require_once "path.php";
require_once "app/controllers/topics.php";
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
   <title>About</title>
</head>

<body>
   <?php include "app/include/header.php"; ?>

   <div class="container">
      <div class="content row">
         <div class="main-content col-md-9 col-12">
            <h2>About My blog</h2>
            <p>
               My blog is a learning project built to teach web development
               and to practice PHP, MySQL, and Docker.
            </p>
            <p>
               You can read posts, browse categories, search the archive, and leave comments.
               Administrators can manage posts, categories, users, and comments from the admin panel.
            </p>
         </div>
         <div class="sidebar col-md-3 col-12">
            <div class="section topics">
               <h3>Categories</h3>
               <ul>
                  <?php foreach ($topics as $topic) : ?>
                     <li><a href="<?= BASE_URL . 'category.php?id=' . $topic['id']; ?>"><?= $topic['name']; ?></a></li>
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
