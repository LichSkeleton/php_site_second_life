<?php
require_once "path.php";
require_once "app/controllers/topics.php";
$postId = isset($_GET['post']) ? (int) $_GET['post'] : 0;
$post = $postId > 0 ? selectPostFromPostsWithUserOnSingle('posts', 'users', $postId) : false;
if (!$post) {
   header('location: ' . BASE_URL);
   exit();
}
//tt($post);
?>
<!doctype html>
<html lang="en">

<head>
   <!-- Required meta tags -->
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <!--Font Awesome-->
   <script src="https://kit.fontawesome.com/1e72fe6500.js" crossorigin="anonymous"></script>
   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

   <link rel="stylesheet" href="assets/css/style1.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;700&display=swap" rel="stylesheet">

   <title>Hello, world!</title>
</head>

<body>
   <?php include("app/include/header.php"); ?>

   <!-- Main block -->
   <div class="container">
      <div class="content row">
         <!-- Main content -->
         <div class="main-content col-md-9 col-12">

            <h2><?php echo $post['title']; ?></h2>

            <div class="single_post row">
               <div class="img col-12">
                  <img src="<?= BASE_URL . 'assets/img/posts/' . $post['img'] ?>" alt="<?= $post['title'] ?>" class="img-thumbnail" style="height: 560px; width: 1080px;">
               </div>
               <div class="info">
                  <i><?= $post['username']; ?></i>
                  <i><?= $post['created_date']; ?></i>
               </div>
               <div class="single_post_text col-12">
                  <?= $post['content']; ?>
               </div>
               <!-- Include the comments HTML block -->
               <?php include("app/include/comments.php"); ?>
            </div>

         </div>
         <!-- Sidebar content -->
         <div class="sidebar col-md-3 col-12">

            <div class="section search">
               <h3>Search</h3>
               <form action="search.php" method="post">
                  <input type="text" name="search-term" class="text-input" placeholder="Enter a search term...">
               </form>
            </div>


            <div class="section topics">
               <h3>Categories</h3>
               <ul>
                  <?php foreach ($topics as $key => $topic) : ?>
                     <li><a href="<?= BASE_URL . 'category.php?id=' . $topic['id']; ?>"><?= $topic['name']; ?></a></li>
                  <?php endforeach; ?>
               </ul>
            </div>

         </div>
      </div>
   </div>
   <!-- End of main block -->

   <?php include("app/include/footer.php"); ?>

   <!-- Optional JavaScript; choose one of the two! -->

   <!-- Option 1: Bootstrap Bundle with Popper -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

   <!-- Option 2: Separate Popper and Bootstrap JS -->
   <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>