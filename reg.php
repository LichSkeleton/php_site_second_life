<?php
require_once "path.php";
require_once "app/controllers/users.php";
?>
<!doctype html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">

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

   <!-- Form -->
   <div class="container reg_form">
      <form class="row justify-content-md-center" method="post" action="reg.php">
         <h2>Registration form</h2>
         <div class="mb-3 col-12 col-md-4 err">
            <?php include("app/helps/errorInfo.php"); ?>
         </div>
         <div class="w-100"></div>

         <div class="mb-3 col-12 col-md-4">
            <label for="formGroupExampleInput" class="form-label">Your username</label>
            <input name="login" value="<?= $login ?>" type="text" class="form-control" id="formGroupExampleInput" placeholder="enter your username...">
         </div>
         <div class="w-100"></div>
         <div class="mb-3 col-12 col-md-4">
            <label for="exampleInputEmail1" class="form-label">Email</label>
            <input name="mail" value="<?= $email ?>" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="enter your email...">
            <div id="emailHelp" class="form-text">Your email address will not be used for spam!</div>
         </div>
         <div class="w-100"></div>
         <div class="mb-3 col-12 col-md-4">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input name="pass-first" type="password" class="form-control" id="exampleInputPassword1" placeholder="enter your password...">
         </div>
         <div class="w-100"></div>
         <div class="mb-3 col-12 col-md-4">
            <label for="exampleInputPassword2" class="form-label">Repeat password</label>
            <input name="pass-second" type="password" class="form-control" id="exampleInputPassword2" placeholder="repeat your password...">
         </div>
         <div class="w-100"></div>
         <div class="mb-3 col-12 col-md-4">
            <button type="submit" class="btn btn-secondary" name="button-reg">Sign up</button>
            <a href="<?php echo BASE_URL . 'log.php'; ?>">Sign in</a>
         </div>
      </form>
   </div>
   <!-- END Form -->

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