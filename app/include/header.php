    <?php include __DIR__ . '/theme-boot.php'; ?>
    <header class="site-header">
       <div class="container header-bar">
          <h1 class="brand">
             <a href="<?php echo BASE_URL ?>">My blog</a>
          </h1>
          <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-hidden="true">
          <label class="nav-burger" for="nav-toggle">
             <span class="visually-hidden">Menu</span>
             <span></span>
          </label>
          <nav class="site-nav">
             <ul>
                <li><a href="<?php echo BASE_URL ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL . 'catalog.php'; ?>">Catalog</a></li>
                <li><a href="<?php echo BASE_URL . 'exchange.php'; ?>">JSON/XML</a></li>
                <li><a href="<?php echo BASE_URL . 'about.php'; ?>">About</a></li>
                <li><a href="<?php echo BASE_URL . 'services.php'; ?>">Services</a></li>
                <li>
                   <form class="theme-switch" method="post" action="<?php echo BASE_URL . 'theme.php'; ?>">
                      <button type="submit" name="theme" value="<?php echo siteTheme() === 'dark' ? 'light' : 'dark'; ?>">
                         <?php echo siteTheme() === 'dark' ? 'Light' : 'Dark'; ?>
                      </button>
                   </form>
                </li>
                <li class="nav-account">
                   <?php if (isset($_SESSION['id'])) : ?>
                      <a href="<?php echo BASE_URL . 'profile.php'; ?>">
                         <?php echo htmlspecialchars($_SESSION['login']); ?>
                      </a>
                      <ul>
                         <li><a href="<?php echo BASE_URL . 'profile.php'; ?>">Profile</a></li>
                         <li><a href="<?php echo BASE_URL . 'write.php'; ?>">Write a post</a></li>
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
    </header>
