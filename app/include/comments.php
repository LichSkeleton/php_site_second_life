<?php
include_once SITE_ROOT . "/app/controllers/commentaries.php";
?>
<div class="col-md-12 col-12 comments">
   <h3>Leave a comment</h3>
   <form action="<?= BASE_URL . "single.php?post=$page" ?>" method="post">
      <input type="hidden" name="page" value="<?= $page; ?>">
      <div class="mb-3">
         <label for="exampleFormControlInput1" class="form-label">Email address</label>
         <input name="email" type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
      </div>
      <div class="mb-3">
         <label for="exampleFormControlTextarea1" class="form-label">Write your review</label>
         <textarea name="comment" class="form-control" id="exampleFormControlTextarea1" rows="4"></textarea>
      </div>
      <div class="col-12">
         <button type="submit" name="goComment" class="btn btn-primary">Send</button>
      </div>
   </form>
   <?php if (count($comments) > 0) : ?>
      <div class="row all-comments">
         <h3 class="col-12">Comments on this post</h3>
         <?php foreach ($comments as $comment) : ?>
            <div class="one-comment col-12">
               <span><i class="far fa-envelope"></i> <?= $comment['email'] ?></span>
               <span><i class="far fa-calendar-check"></i> <?= $comment['created_date'] ?></span>
               <div class="col-12 text">
                  <span><?= $comment['comment'] ?></span>
               </div>
            </div>
         <?php endforeach; ?>
      </div>
   <?php endif; ?>
</div>