<?php if (!empty($blogSummary) && is_array($blogSummary)) : ?>
   <div class="blog-summary-bar">
      <h3>Blog summary</h3>
      <ul>
         <li><strong>Published posts:</strong> <?= (int) $blogSummary['post_count']; ?></li>
         <li><strong>Categories:</strong> <?= (int) $blogSummary['topic_count']; ?></li>
         <li><strong>Visible comments:</strong> <?= (int) $blogSummary['comment_count']; ?></li>
         <li><strong>Average article length:</strong> <?= (int) $blogSummary['avg_length']; ?> characters</li>
      </ul>
   </div>
<?php endif; ?>
