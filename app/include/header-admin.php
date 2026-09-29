<?php include __DIR__ . '/theme-boot.php'; ?>
<header class="site-header">
   <div class="container header-bar">
      <h1 class="brand">
         <a href="<?php echo BASE_URL ?>">My blog</a>
      </h1>
      <nav class="admin-nav">
         <ul>
            <li><a class="nav-quiet" href="<?php echo BASE_URL; ?>">Site</a></li>
            <li>
               <a class="nav-user" href="<?php echo BASE_URL . 'profile.php'; ?>">
                  <?php echo htmlspecialchars($_SESSION['login'] ?? ''); ?>
               </a>
            </li>
            <li>
               <a class="nav-logout" href="<?php echo BASE_URL . 'logout.php'; ?>">Log out</a>
            </li>
         </ul>
      </nav>
   </div>
</header>
