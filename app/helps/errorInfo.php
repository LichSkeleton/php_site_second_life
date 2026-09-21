<?php
if (!isset($errMsg)) {
   $errMsg = [];
} elseif (!is_array($errMsg)) {
   $errMsg = ($errMsg === '' || $errMsg === null) ? [] : [$errMsg];
}
?>
<?php if (count($errMsg) > 0) : ?>
   <?php foreach ($errMsg as $error) : ?>
      <li><?= $error; ?></li>
   <?php endforeach; ?>
<?php endif; ?>
