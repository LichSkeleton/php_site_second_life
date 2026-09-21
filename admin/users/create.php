<?php
require_once "../../path.php";
require_once "../../app/controllers/users.php";
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
               <a href="<?php echo BASE_URL . "admin/users/create.php"; ?>" class="col-2 btn btn-success">Create</a>
               <span class="col-1"></span>
               <a href="<?php echo BASE_URL . "admin/users/index.php"; ?>" class="col-3 btn btn-warning">Edit</a>
            </div>
            <div class="row title-table">
               <h2>Create user</h2>
            </div>
            <div class="row add-post">
               <div class="mb-12 col-12 col-md-12 err">
                  <!-- Display the errors array -->
                  <?php include "../../app/helps/errorInfo.php"; ?>
               </div>
               <form action="create.php" method="post">
                  <div class="col">
                     <label for="formGroupExampleInput" class="form-label">Username</label>
                     <input name="login" value="<?= $login ?>" type="text" class="form-control" id="formGroupExampleInput" placeholder="enter username...">
                  </div>
                  <div class="col">
                     <label for="exampleInputEmail1" class="form-label">Email</label>
                     <input name="mail" value="<?= $email ?>" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="enter email...">
                  </div>
                  <div class="col">
                     <label for="exampleInputPassword1" class="form-label">Password</label>
                     <input name="pass-first" type="password" class="form-control" id="exampleInputPassword1" placeholder="enter password...">
                  </div>
                  <div class="col">
                     <label for="exampleInputPassword2" class="form-label">Repeat password</label>
                     <input name="pass-second" type="password" class="form-control" id="exampleInputPassword2" placeholder="repeat password...">
                  </div>
                  <input name="admin-pub" class="form-check-input" value="1" type="checkbox" id="flexCheckChecked">
                  <label class="form-check-label" for="flexCheckChecked">
                     Admin
                  </label>
                  <div class="col">
                     <button name="create-user" class="btn btn-primary" type="submit">Create</button>
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

   <!-- Option 2: Separate Popper and Bootstrap JS -->
   <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>