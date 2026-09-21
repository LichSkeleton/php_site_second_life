<?php

if (defined('APP_DB_LOADED')) {
   return;
}
define('APP_DB_LOADED', true);

if (session_status() !== PHP_SESSION_ACTIVE) {
   session_start();
}
require_once __DIR__ . '/connect.php';

function tt($value)
{
   echo '<pre>';
   print_r($value);
   echo '</pre>';
   exit();
}

function tte($value)
{
   echo '<pre>';
   print_r($value);
   echo '</pre>';
}

// Check whether a database query succeeded
function dbCheckError($query)
{
   $errInfo = $query->errorInfo();
   if ($errInfo[0] !== PDO::ERR_NONE) {
      echo $errInfo[2];
      exit();
   }
   return true;
}
// Fetch all rows from one table
function selectAll($table, $params = [])
{
   global $pdo;
   $sql = "SELECT * FROM $table";

   if (!empty($params)) {
      //echo tt($params);
      $i = 0;
      foreach ($params as $key => $value) {
         if (!is_numeric($value)) {
            $value = "'" . $value . "'";
         }
         if ($i === 0) {
            $sql = $sql . " WHERE $key = $value";
         } else {
            $sql = $sql . " AND $key = $value";
         }
         $i++;
      }
   }

   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

// Fetch a single row from the selected table
function selectOne($table, $params = [])
{
   global $pdo;
   $sql = "SELECT * FROM $table";

   if (!empty($params)) {
      $i = 0;
      foreach ($params as $key => $value) {
         if (!is_numeric($value)) {
            $value = "'" . $value . "'";
         }
         if ($i === 0) {
            $sql = $sql . " WHERE $key = $value";
         } else {
            $sql = $sql . " AND $key = $value";
         }
         $i++;
      }
   }
   $sql = $sql . " LIMIT 1";

   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetch();
}

// Insert a row into a table
function insert($table, $params)
{
   global $pdo;
   $i = 0;
   $coll = '';
   $mask = '';
   foreach ($params as $key => $value) {
      if ($i === 0) {
         $coll = $coll . "$key";
         $mask = $mask . "'" . "$value" . "'";
      } else {
         $coll = $coll . ", $key";
         $mask = $mask . ", '" . "$value" . "'";
      }
      $i++;
   }

   $sql = "INSERT INTO $table ($coll) VALUES ($mask)";

   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $pdo->lastInsertId();
}

// Update a row in a table
function update($table, $id, $params)
{
   global $pdo;
   $i = 0;
   $str = '';
   foreach ($params as $key => $value) {
      if ($i === 0) {
         $str = $str . $key . " = '" . $value . "'";
      } else {
         $str = $str . ", " . $key . " = '" . $value . "'";
      }
      $i++;
   }
   $sql = "UPDATE $table SET $str WHERE id = $id";

   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
}

// Delete a row from a table
function delete($table, $id)
{
   global $pdo;

   $sql = "DELETE FROM $table WHERE id =" . $id;

   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
}

// Fetch posts with authors for the admin panel
function selectAllFromPostsWithUsers($table1, $table2)
{
   global $pdo;
   $sql = "
   SELECT 
   t1.id,
   t1.title,
   t1.img,
   t1.content,
   t1.status,
   t1.id_topic,
   t1.created_date,
   t2.username
   FROM $table1 AS t1 JOIN $table2 AS t2 ON t1.id_user = t2.id";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

function selectPostsByTopicWithUsers($table1, $table2, $topicId)
{
   global $pdo;
   $topicId = (int) $topicId;
   $sql = "SELECT p.*, u.username FROM $table1 AS p JOIN $table2 AS u ON p.id_user = u.id WHERE p.status=1 AND p.id_topic = $topicId";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

// Fetch published posts with authors for the homepage
function selectAllFromPostsWithUsersOnIndex($table1, $table2, $limit, $offset)
{
   global $pdo;
   $sql = "SELECT p.*, u.username FROM $table1 AS p JOIN $table2 AS u ON p.id_user = u.id WHERE p.status=1 LIMIT $limit OFFSET $offset";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

// Fetch featured posts for the homepage carousel
function selectTopTopicFromPostsOnIndex($table1)
{
   global $pdo;
   $sql = "SELECT * FROM $table1 WHERE id_topic = 8";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

// Simple search by title and content
function searchInTitleAndContent($text, $table1, $table2)
{
   global $pdo;
   $text = trim(strip_tags(stripslashes(htmlspecialchars($text))));
   $sql = "SELECT
    p.*, u.username 
    FROM $table1 AS p 
    JOIN $table2 AS u 
    ON p.id_user = u.id 
    WHERE p.status=1
    AND p.title LIKE '%$text%' OR p.content LIKE '%$text%'";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

// Fetch a single post with its author
function selectPostFromPostsWithUserOnSingle($table1, $table2, $id)
{
   global $pdo;
   $sql = "SELECT p.*, u.username FROM $table1 AS p JOIN $table2 AS u ON p.id_user = u.id WHERE p.id=$id";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetch();
}

// Count published posts
function countRow($table)
{
   global $pdo;
   $sql = "SELECT COUNT(*) FROM $table WHERE status = 1";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchColumn();
}
