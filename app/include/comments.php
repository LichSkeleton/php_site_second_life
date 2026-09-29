<?php
include_once SITE_ROOT . "/app/controllers/commentaries.php";
?>
<div class="col-md-12 col-12 comments">
   <h3>Leave a comment</h3>
   <p class="info-empty">Signed-in comments are published right away. Guest comments are saved and appear after an administrator publishes them.</p>
   <?php if ($commentNotice !== '') : ?>
      <p class="info-ok"><?= htmlspecialchars($commentNotice); ?></p>
   <?php endif; ?>
   <form action="<?= BASE_URL . "single.php?post=$page" ?>" method="post">
      <input type="hidden" name="page" value="<?= (int) $page; ?>">
      <div class="mb-3 col-12 err">
         <?php include SITE_ROOT . "/app/helps/errorInfo.php"; ?>
      </div>
      <div class="mb-3">
         <label for="exampleFormControlInput1" class="form-label">Email address</label>
         <input name="email" value="<?= htmlspecialchars($email); ?>" type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
      </div>
      <div class="mb-3">
         <label for="exampleFormControlTextarea1" class="form-label">Write your review</label>
         <textarea name="comment" class="form-control" id="exampleFormControlTextarea1" rows="4"><?= htmlspecialchars($commentText); ?></textarea>
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
               <span><i class="far fa-envelope"></i> <?= htmlspecialchars($comment['email']); ?></span>
               <span><i class="far fa-calendar-check"></i> <?= htmlspecialchars(formatAppDate($comment['created_date'])); ?></span>
               <?php if (!empty($comment['pending'])) : ?>
                  <span>Awaiting moderation</span>
               <?php endif; ?>
               <div class="col-12 text">
                  <span><?= nl2br(htmlspecialchars($comment['comment'])); ?></span>
               </div>
            </div>
         <?php endforeach; ?>
      </div>
   <?php endif; ?>
</div>
