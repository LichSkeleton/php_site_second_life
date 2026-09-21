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
               <h2>Add post</h2>
            </div>
            <div class="row add-post">
               <div class="mb-12 col-12 col-md-12 err">
                  <!-- Display the errors array -->
                  <?php include "../../app/helps/errorInfo.php"; ?>
               </div>
               <!--enctype="multipart/form-data"-->
               <form action="create.php" method="post" enctype="multipart/form-data">
                  <div class="col mb-4">
                     <input value="<?= $title; ?>" name="title" type="text" class="form-control" placeholder="Title" aria-label="Post title">
                  </div>
                  <div class="col">
                     <label for="editor" class="form-label">Post content</label>
                     <textarea name="content" id="editor" class="form-control" rows="6"><?= $content; ?></textarea>
                  </div>
                  <div class="input-group col mb-4 mt-4">
                     <input name="img" type="file" class="form-control" id="inputGroupFile02">
                     <label class="input-group-text" for="inputGroupFile02">Upload</label>
                  </div>
                  <select name="topic" class="form-select mb-2" aria-label="Default select example">
                     <option selected>Post category:</option>
                     <?php foreach ($topics as $key => $topic) : ?>
                        <option value="<?= $topic['id'] ?>"><?= $topic['name'] ?></option>
                     <?php endforeach; ?>
                  </select>
                  <div class="form-check">
                     <input name="publish" class="form-check-input" type="checkbox" value="1" id="flexCheckChecked" checked>
                     <label class="form-check-label" for="flexCheckChecked">
                        Publish
                     </label>
                  </div>
                  <div class="col col-6">
                     <button name="add_post" class="btn btn-primary" type="submit">Add post</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>

   <?php include("../../app/include/footer.php"); ?>

   <!-- Optional JavaScript; choose one of the two! -->

   <!-- Option 1: Bootstrap Bundle with Popper -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

   <!-- Attach the visual editor to the admin textarea -->
   <script src="https://cdn.ckeditor.com/ckeditor5/31.1.0/classic/ckeditor.js"></script>

   <!-- Option 2: Separate Popper and Bootstrap JS -->
   <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
   <script src="../../assets/js/scripts.js"></script>
</body>

</html>