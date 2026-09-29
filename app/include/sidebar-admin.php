<?php
$adminPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$adminLinks = [
   ['label' => 'Posts', 'href' => BASE_URL . 'admin/posts/index.php', 'match' => '/admin/posts/'],
   ['label' => 'Categories', 'href' => BASE_URL . 'admin/topics/index.php', 'match' => '/admin/topics/'],
   ['label' => 'Users', 'href' => BASE_URL . 'admin/users/index.php', 'match' => '/admin/users/'],
   ['label' => 'Comments', 'href' => BASE_URL . 'admin/comments/index.php', 'match' => '/admin/comments/'],
   ['label' => 'JSON / XML', 'href' => BASE_URL . 'exchange.php', 'match' => '/exchange.php'],
];
?>
<div class="sidebar col-3">
   <p class="side-label">Admin</p>
   <ul>
      <?php foreach ($adminLinks as $link) : ?>
         <li>
            <a class="<?= str_contains($adminPath, $link['match']) ? 'is-active' : ''; ?>" href="<?= $link['href']; ?>">
               <?= $link['label']; ?>
            </a>
         </li>
      <?php endforeach; ?>
   </ul>
</div>
