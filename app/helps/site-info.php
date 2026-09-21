<?php

if (!defined('SITE_ROOT')) {
   require_once dirname(__DIR__, 2) . '/path.php';
}
require_once SITE_ROOT . '/app/database/db.php';

function getSiteAuthor()
{
   return [
      'name' => 'Vladyslav Zaplitnyi',
      'project' => 'My blog',
      'work' => 'PHP laboratory work: server-side web pages',
      'email' => 'info@myblog.com'
   ];
}

function getDomainParagraphs()
{
   return [
      'My blog is a learning web application about web development. The server uses PHP to read data, apply conditions and loops, and return ready HTML to the browser.',
      'The subject area is a publication archive: articles, categories, comments, and user accounts. Each article is an object with a title, author, date, and body text.',
      'PHP builds the public pages from those objects. Search, category filters, and reading-time estimates change the output according to the values sent in the HTTP request.'
   ];
}

function getBlogServices()
{
   return [
      [
         'title' => 'Article feed',
         'description' => 'Browse published posts with a title, author, date, and short preview.',
         'audience' => 'Everyone'
      ],
      [
         'title' => 'Category catalog',
         'description' => 'Open a topic and see only the articles that belong to it.',
         'audience' => 'Everyone'
      ],
      [
         'title' => 'Archive search',
         'description' => 'Find posts by a phrase in the title or the article text.',
         'audience' => 'Everyone'
      ],
      [
         'title' => 'Comments',
         'description' => 'Leave a review under a post after the text passes validation.',
         'audience' => 'Readers'
      ],
      [
         'title' => 'Admin panel',
         'description' => 'Create posts, manage categories, users, and comment moderation.',
         'audience' => 'Administrators'
      ]
   ];
}

function buildBlogSummary($topics = [])
{
   $publishedPosts = selectAll('posts', ['status' => 1]);
   if (!is_array($publishedPosts)) {
      $publishedPosts = [];
   }

   $comments = selectAll('comments', ['status' => 1]);
   if (!is_array($comments)) {
      $comments = [];
   }

   $postCount = count($publishedPosts);
   $topicCount = is_array($topics) ? count($topics) : 0;
   $commentCount = count($comments);
   $totalLength = 0;
   $topicPostCounts = [];

   foreach ($publishedPosts as $post) {
      $content = (string) ($post['content'] ?? '');
      $totalLength += mb_strlen($content, 'UTF-8');
      $topicId = (int) ($post['id_topic'] ?? 0);
      if ($topicId > 0) {
         $topicPostCounts[$topicId] = ($topicPostCounts[$topicId] ?? 0) + 1;
      }
   }

   $avgLength = $postCount > 0 ? (int) round($totalLength / $postCount) : 0;
   $hasContent = $postCount > 0 && $avgLength > 0;

   return [
      'post_count' => $postCount,
      'topic_count' => $topicCount,
      'comment_count' => $commentCount,
      'avg_length' => $avgLength,
      'has_content' => $hasContent,
      'topic_post_counts' => $topicPostCounts
   ];
}

function buildTopicCatalog($topics, $topicPostCounts = [])
{
   $catalog = [];
   if (!is_array($topics)) {
      return $catalog;
   }

   foreach ($topics as $topic) {
      $topicId = (int) ($topic['id'] ?? 0);
      $postCount = (int) ($topicPostCounts[$topicId] ?? 0);
      $catalog[] = [
         'id' => $topicId,
         'name' => (string) ($topic['name'] ?? ''),
         'description' => (string) ($topic['description'] ?? ''),
         'post_count' => $postCount,
         'status' => $postCount > 0 ? 'Active' : 'Empty'
      ];
   }

   return $catalog;
}

function estimateReadingMinutes($avgLength, $wpm)
{
   $avgLength = (int) $avgLength;
   $wpm = (int) $wpm;
   $charsPerWord = 5;
   $wordCount = $avgLength / $charsPerWord;

   if ($wpm <= 0 || $avgLength <= 0) {
      return 0;
   }

   return (int) ceil($wordCount / $wpm);
}
