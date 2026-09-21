<?php
if (!function_exists('getSiteAuthor')) {
   require_once SITE_ROOT . '/app/helps/site-info.php';
}
$siteAuthor = getSiteAuthor();
?>
    <div class="footer container-fluid">
      <div class="footer-content container">
        <div class="row">
          <div class="footer-section about col-md-4 col-12">
            <h3 class="logo-text"><?= htmlspecialchars($siteAuthor['project']); ?></h3>
            <p>
              <?= htmlspecialchars($siteAuthor['work']); ?>.
              The pages are assembled on the server with PHP.
            </p>
            <div class="contact">
              <span><i>*</i> &nbsp; <?= htmlspecialchars($siteAuthor['name']); ?></span>
              <span><i>*</i> &nbsp; <?= htmlspecialchars($siteAuthor['email']); ?></span>
            </div>
            <div class="socials">
              <a href="#"><i>Face</i></a>
              <a href="#"><i>Inst</i></a>
              <a href="#"><i>Twit</i></a>
              <a href="#"><i>YouT</i></a>
            </div>            
          </div>
          
          <div class="footer-section links col-md-4 col-12">
            <h3>Quick Links</h3>
            <br>
            <ul>
              <a href="<?php echo BASE_URL; ?>">
                <li>Home</li>
              </a>
              <a href="<?php echo BASE_URL . 'about.php'; ?>">
                <li>About</li>
              </a>
              <a href="<?php echo BASE_URL . 'services.php'; ?>">
                <li>Services</li>
              </a>
              <a href="<?php echo BASE_URL . 'log.php'; ?>">
                <li>Sign in</li>
              </a>
              <a href="<?php echo BASE_URL . 'reg.php'; ?>">
                <li>Sign up</li>
              </a>
            </ul>
          </div>

          <div class="footer-section contact-formm col-md-4 col-12">
            <h3>Contact</h3>
            <br>
            <form action="<?php echo BASE_URL . 'about.php#contact'; ?>" method="post">
              <input type="email" name="email" class="text-input contact-input" placeholder="Your email address..." value="<?= htmlspecialchars($contactEmail ?? ''); ?>">  
              <textarea rows="4" name="message" class="text-input contact-input" placeholder="Your message..."><?= htmlspecialchars($contactText ?? ''); ?></textarea>
              <button type="submit" name="send-contact" class="btn btn-big contact-btn">
                <i>*</i>
                Send
              </button>            
            </form>
          </div>
        </div>

        <div class="footer-bottom">
          &copy; <?= htmlspecialchars($siteAuthor['name']); ?>
        </div>
      </div>
    </div>
