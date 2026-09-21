<header class="container-fluid">
   <div class="container">
      <div class="row">
         <div class="col-4">
            <h1>
               <a href="<?php echo BASE_URL ?>">My blog</a>
            </h1>
         </div>
         <nav class="col-8">
            <ul>
               <li>
                  <a href="#">
                     <?php echo $_SESSION['login'] ?? ''; ?>
                  </a>
               </li>
               <li>
                  <a href="<?php echo BASE_URL . "logout.php"; ?>">Log out</a>
               </li>
            </ul>
         </nav>
      </div>
   </div>
</header>