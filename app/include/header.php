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
                   <li><a href="<?php echo BASE_URL ?>">Home</a></li>
                   <li><a href="<?php echo BASE_URL . 'about.php'; ?>">About</a></li>
                   <li><a href="<?php echo BASE_URL . 'services.php'; ?>">Services</a></li>

                   <li>
                      <?php if (isset($_SESSION['id'])) : ?>
                         <a href="#">
                            <?php echo $_SESSION['login']; ?>
                         </a>
                         <ul>
                            <?php if ($_SESSION['admin']) : ?>
                               <li><a href="<?php echo BASE_URL . "admin/posts/index.php"; ?>">Admin panel</a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo BASE_URL . "logout.php"; ?>">Log out</a></li>
                         </ul>
                      <?php else : ?>
                         <a href="<?php echo BASE_URL . 'log.php'; ?>">
                            Sign in
                         </a>
                         <ul>
                            <li><a href="<?php echo BASE_URL . 'reg.php'; ?>">Sign up</a></li>
                         </ul>
                      <?php endif; ?>
                   </li>
                </ul>
             </nav>
          </div>
       </div>
    </header>