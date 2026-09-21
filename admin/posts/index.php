<?php
require_once "../../path.php";
require_once "../../app/controllers/posts.php";
require_once SITE_ROOT . "/app/include/admin-auth.php";
?>
<!doctype html>
<html lang="en">

<head>
   <!-- Required meta tags -->
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

   <link rel="stylesheet" href="../../assets/css/admin.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;700&display=swap" rel="stylesheet">

   <title>Hello, world!</title>
</head>

<body>

   <?php include("../../app/include/header-admin.php"); ?>

   <div class="container">
      <div class="row">
         <?php include("../../app/include/sidebar-admin.php"); ?>
         <div class="posts col-9">
            <div class="botton row">
               <a href="<?php echo BASE_URL . "admin/posts/create.php"; ?>" class="col-2 btn btn-success">Create</a>
               <span class="col-1"></span>
               <a href="<?php echo BASE_URL . "admin/posts/index.php"; ?>" class="col-3 btn btn-warning">Edit</a>
            </div>
            <div class="row title-table">
               <h2>Manage posts</h2>
               <div class="col-1">ID</div>
               <div class="col-5">Title</div>
               <div class="col-2">Author</div>
               <div class="col-4">Actions</div>
            </div>
            <?php foreach ($postsAdm as $key => $post) : ?>
               <div class="row post">
                  <div class="id col-1"><?= $key + 1; ?></div>
                  <div class="title col-5"><?= mb_substr($post['title'], 0, 50, 'UTF-8') . "..." ?></div>
                  <div class="author col-2"><?= $post['username']; ?></div>
                  <div class="red col-1"><a href="edit.php?id=<?= $post['id']; ?>">edit</a></div>
                  <div class="del col-1"><a href="edit.php?delete_id=<?= $post['id']; ?>">delete</a></div>
                  <?php if ($post['status']) : ?>
                     <div class="status col-2"><a href="edit.php?publish=0&pub_id=<?= $post['id']; ?>">unpublish</a></div>
                  <?php else : ?>
                     <div class="status col-2"><a href="edit.php?publish=1&pub_id=<?= $post['id']; ?>">publish</a></div>
                  <?php endif; ?>
               </div>
            <?php endforeach; ?>
         </div>
      </div>
   </div>

   <?php include("../../app/include/footer.php"); ?>

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