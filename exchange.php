<?php
require_once 'path.php';
require_once SITE_ROOT . '/app/database/db.php';
require_once SITE_ROOT . '/app/include/structured.php';

$isAdmin = !empty($_SESSION['id']) && (int) ($_SESSION['admin'] ?? 0) === 1;
$topics = selectAll('topics');
$feedJson = postsFeedJson(publishedPostsFeed());
$feedXml = postsFeedXml(publishedPostsFeed());
$jsonBody = (string) ($_POST['json_body'] ?? "{\n  \"title\": \"Post created from JSON\",\n  \"content\": \"This record was sent to the server as JSON and stored in MySQL.\",\n  \"topic_id\": 2,\n  \"status\": 0\n}");
$xmlBody = (string) ($_POST['xml_body'] ?? "<post>\n  <title>Post created from XML</title>\n  <content>This record was sent to the server as XML and stored in MySQL.</content>\n  <topic_id>2</topic_id>\n  <status>0</status>\n</post>");
$importResult = null;
$importFormat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['import_json']) || isset($_POST['import_xml']))) {
    if (!$isAdmin) {
        $importResult = ['ok' => false, 'errors' => ['Only an administrator can add a post this way.'], 'post' => null];
    } elseif (isset($_POST['import_json'])) {
        $importFormat = 'JSON';
        $data = json_decode($jsonBody, true);
        $importResult = is_array($data)
            ? importStructuredPost($data, (int) $_SESSION['id'])
            : ['ok' => false, 'errors' => ['The text is not a JSON object.'], 'post' => null];
    } else {
        $importFormat = 'XML';
        $importResult = importPostFromXml($xmlBody, (int) $_SESSION['id']);
    }
    if ($importResult && $importResult['ok']) {
        $feedJson = postsFeedJson(publishedPostsFeed());
        $feedXml = postsFeedXml(publishedPostsFeed());
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
   <title>JSON and XML | My blog</title>
</head>

<body>
   <?php include 'app/include/header.php'; ?>

   <div class="container">
      <div class="content row">
         <div class="main-content col-12">
            <h2>Structured data</h2>
            <p>The catalog below is built from published posts. Open the raw files: <a href="<?= BASE_URL; ?>api/posts.php">api/posts.php</a> and <a href="<?= BASE_URL; ?>api/catalog.xml.php">api/catalog.xml.php</a>. Dates use Europe/Kyiv.</p>

            <h3>JSON</h3>
            <pre class="feed-preview"><?= htmlspecialchars($feedJson); ?></pre>

            <h3>XML</h3>
            <pre class="feed-preview"><?= htmlspecialchars($feedXml); ?></pre>

            <h3>Add a post from JSON or XML</h3>
            <?php if (!$isAdmin) : ?>
               <p>Guests and readers can download the catalog. Adding a record is allowed only for an administrator. <a href="<?= BASE_URL; ?>log.php">Sign in</a></p>
            <?php else : ?>
               <p>Categories:
                  <?php foreach ($topics as $topic) : ?>
                     <?= htmlspecialchars($topic['name']); ?> (<?= (int) $topic['id']; ?>)
                  <?php endforeach; ?>
               </p>
               <?php if ($importResult) : ?>
                  <?php if ($importResult['ok']) : ?>
                     <p>The <?= htmlspecialchars($importFormat); ?> document was accepted. Post #<?= (int) $importResult['post']['id']; ?> was saved at <?= htmlspecialchars(formatAppDate($importResult['post']['created_at'])); ?>.</p>
                  <?php else : ?>
                     <div class="err">
                        <ul>
                           <?php foreach ($importResult['errors'] as $error) : ?>
                              <li><?= htmlspecialchars($error); ?></li>
                           <?php endforeach; ?>
                        </ul>
                     </div>
                  <?php endif; ?>
               <?php endif; ?>
               <form method="post" action="exchange.php">
                  <label for="json_body">JSON</label>
                  <textarea class="form-control mb-3" id="json_body" name="json_body" rows="8"><?= htmlspecialchars($jsonBody); ?></textarea>
                  <button class="btn btn-primary mb-4" type="submit" name="import_json" value="1">Send JSON</button>
               </form>
               <form method="post" action="exchange.php">
                  <label for="xml_body">XML</label>
                  <textarea class="form-control mb-3" id="xml_body" name="xml_body" rows="8"><?= htmlspecialchars($xmlBody); ?></textarea>
                  <button class="btn btn-primary" type="submit" name="import_xml" value="1">Send XML</button>
               </form>
            <?php endif; ?>
         </div>
      </div>
   </div>

   <?php include 'app/include/footer.php'; ?>
</body>

</html>
