<?php
require_once "path.php";
require_once "app/controllers/topics.php";
require_once "app/helps/site-info.php";

$blogSummary = buildBlogSummary($topics);
$topicCatalog = buildTopicCatalog($topics, $blogSummary['topic_post_counts']);
$domainParagraphs = getDomainParagraphs();
$siteAuthor = getSiteAuthor();

$contactEmail = '';
$contactText = '';
$contactErr = [];
$contactOk = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send-contact'])) {
   $contactEmail = trim((string) ($_POST['email'] ?? ''));
   $contactText = trim((string) ($_POST['message'] ?? ''));

   if ($contactEmail === '' || $contactText === '') {
      $contactErr[] = 'Please fill in all fields!';
   } elseif (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
      $contactErr[] = 'Please enter a valid email address.';
   } elseif (mb_strlen($contactText, 'UTF-8') < 10) {
      $contactErr[] = 'The message must be at least 10 characters.';
   } else {
      $contactOk = 'Thank you. Your message was received and processed on the server.';
      $contactEmail = '';
      $contactText = '';
   }
}

$wpmInput = isset($_GET['wpm']) ? trim((string) $_GET['wpm']) : '';
$readingError = '';
$readingMinutes = 0;
$readingRequested = $wpmInput !== '';
$wpmValue = 200;

if ($readingRequested) {
   if (!is_numeric($wpmInput)) {
      $readingError = 'Enter a numeric reading speed.';
   } else {
      $wpmValue = (int) $wpmInput;
      if ($wpmValue < 50 || $wpmValue > 600) {
         $readingError = 'Reading speed must be between 50 and 600 words per minute.';
      } elseif (!$blogSummary['has_content']) {
         $readingError = 'There are no published posts to estimate reading time.';
      } else {
         $readingMinutes = estimateReadingMinutes($blogSummary['avg_length'], $wpmValue);
      }
   }
}
?>
<!doctype html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
   <link rel="stylesheet" href="assets/css/style1.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;700&display=swap" rel="stylesheet">
   <title>About | My blog</title>
</head>

<body>
   <?php include "app/include/header.php"; ?>

   <div class="container">
      <div class="content row">
         <div class="main-content col-md-9 col-12">
            <h2>About <?= htmlspecialchars($siteAuthor['project']); ?></h2>

            <?php foreach ($domainParagraphs as $paragraph) : ?>
               <p><?= htmlspecialchars($paragraph); ?></p>
            <?php endforeach; ?>

            <?php include "app/include/blog-summary.php"; ?>

            <div class="author-card">
               <h3>Author of the work</h3>
               <p><strong>Name:</strong> <?= htmlspecialchars($siteAuthor['name']); ?></p>
               <p><strong>Project:</strong> <?= htmlspecialchars($siteAuthor['project']); ?></p>
               <p><strong>Assignment:</strong> <?= htmlspecialchars($siteAuthor['work']); ?></p>
               <p><strong>Email:</strong> <?= htmlspecialchars($siteAuthor['email']); ?></p>
            </div>

            <h2>Categories of the subject area</h2>
            <?php if (count($topicCatalog) > 0) : ?>
               <?php foreach ($topicCatalog as $topicItem) : ?>
                  <article class="topic-card">
                     <h3>
                        <a href="<?= BASE_URL . 'category.php?id=' . (int) $topicItem['id']; ?>">
                           <?= htmlspecialchars($topicItem['name']); ?>
                        </a>
                     </h3>
                     <p><?= htmlspecialchars($topicItem['description']); ?></p>
                     <p><strong>Published posts:</strong> <?= (int) $topicItem['post_count']; ?></p>
                     <p><strong>Status:</strong> <?= htmlspecialchars($topicItem['status']); ?></p>
                  </article>
               <?php endforeach; ?>
            <?php else : ?>
               <p class="info-empty">There are no categories yet.</p>
            <?php endif; ?>

            <div class="reading-box">
               <h3>Reading time estimate</h3>
               <p>
                  This calculation uses the average article length and the reading speed you enter.
                  Empty, non-numeric, or out-of-range values are rejected.
               </p>
               <form method="get" action="about.php">
                  <label for="wpm" class="form-label">Words per minute</label>
                  <input id="wpm" type="text" name="wpm" class="text-input" value="<?= htmlspecialchars((string) $wpmValue); ?>" placeholder="For example, 200">
                  <button type="submit" class="btn btn-primary mt-2">Calculate</button>
               </form>
               <?php if ($readingError !== '') : ?>
                  <p class="info-error"><?= htmlspecialchars($readingError); ?></p>
               <?php elseif ($readingRequested) : ?>
                  <p class="info-ok">
                     At <?= (int) $wpmValue; ?> words per minute, an average article takes about
                     <strong><?= (int) $readingMinutes; ?></strong>
                     <?= $readingMinutes === 1 ? 'minute' : 'minutes'; ?>.
                  </p>
               <?php endif; ?>
            </div>

            <div id="contact" class="reading-box">
               <h3>Contact</h3>
               <?php if ($contactOk !== '') : ?>
                  <p class="info-ok"><?= htmlspecialchars($contactOk); ?></p>
               <?php endif; ?>
               <?php if (count($contactErr) > 0) : ?>
                  <div class="err">
                     <?php foreach ($contactErr as $error) : ?>
                        <li><?= htmlspecialchars($error); ?></li>
                     <?php endforeach; ?>
                  </div>
               <?php endif; ?>
            </div>
         </div>
         <div class="sidebar col-md-3 col-12">
            <div class="section">
               <h3>Search</h3>
               <form action="search.php" method="post">
                  <input type="text" name="search-term" class="text-input" placeholder="Enter a search term...">
               </form>
            </div>
            <div class="section topics">
               <h3>Categories</h3>
               <ul>
                  <?php foreach ($topics as $topic) : ?>
                     <li><a href="<?= BASE_URL . 'category.php?id=' . $topic['id']; ?>"><?= htmlspecialchars($topic['name']); ?></a></li>
                  <?php endforeach; ?>
               </ul>
            </div>
         </div>
      </div>
   </div>

   <?php include "app/include/footer.php"; ?>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
