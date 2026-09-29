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
function dbIdent($name)
{
   if (!is_string($name) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
      throw new InvalidArgumentException('Invalid SQL identifier');
   }
   return '`' . $name . '`';
}

function dbWhere($params)
{
   $clauses = [];
   $values = [];
   foreach ($params as $key => $value) {
      $clauses[] = dbIdent((string) $key) . ' = ?';
      $values[] = $value;
   }
   return [$clauses, $values];
}

// Fetch all rows from one table
function selectAll($table, $params = [])
{
   global $pdo;
   $sql = 'SELECT * FROM ' . dbIdent($table);
   $values = [];

   if (!empty($params)) {
      [$clauses, $values] = dbWhere($params);
      $sql .= ' WHERE ' . implode(' AND ', $clauses);
   }

   $query = $pdo->prepare($sql);
   $query->execute($values);
   dbCheckError($query);
   return $query->fetchAll();
}

// Fetch a single row from the selected table
function selectOne($table, $params = [])
{
   global $pdo;
   $sql = 'SELECT * FROM ' . dbIdent($table);
   $values = [];

   if (!empty($params)) {
      [$clauses, $values] = dbWhere($params);
      $sql .= ' WHERE ' . implode(' AND ', $clauses);
   }
   $sql .= ' LIMIT 1';

   $query = $pdo->prepare($sql);
   $query->execute($values);
   dbCheckError($query);
   return $query->fetch();
}

// Insert a row into a table
function insert($table, $params)
{
   global $pdo;
   $columns = [];
   $placeholders = [];
   $values = [];
   foreach ($params as $key => $value) {
      $columns[] = dbIdent((string) $key);
      $placeholders[] = '?';
      $values[] = $value;
   }

   $sql = 'INSERT INTO ' . dbIdent($table) . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';

   $query = $pdo->prepare($sql);
   $query->execute($values);
   dbCheckError($query);
   return $pdo->lastInsertId();
}

// Update a row in a table
function update($table, $id, $params)
{
   global $pdo;
   $assignments = [];
   $values = [];
   foreach ($params as $key => $value) {
      $assignments[] = dbIdent((string) $key) . ' = ?';
      $values[] = $value;
   }
   $values[] = (int) $id;
   $sql = 'UPDATE ' . dbIdent($table) . ' SET ' . implode(', ', $assignments) . ' WHERE id = ?';

   $query = $pdo->prepare($sql);
   $query->execute($values);
   dbCheckError($query);
}

// Delete a row from a table
function delete($table, $id)
{
   global $pdo;
   $sql = 'DELETE FROM ' . dbIdent($table) . ' WHERE id = ?';

   $query = $pdo->prepare($sql);
   $query->execute([(int) $id]);
   dbCheckError($query);
}

// Fetch posts with authors for the admin panel
function selectAllFromPostsWithUsers($table1, $table2)
{
   global $pdo;
   $posts = dbIdent($table1);
   $users = dbIdent($table2);
   $sql = "
   SELECT 
   t1.id,
   t1.title,
   t1.img,
   t1.content,
   t1.status,
   t1.id_topic,
   t1.created_date,
   t1.updated_date,
   t2.username
   FROM $posts AS t1 JOIN $users AS t2 ON t1.id_user = t2.id
   ORDER BY t1.status ASC, t1.created_date ASC, t1.id ASC";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

function selectPostsByTopicWithUsers($table1, $table2, $topicId)
{
   global $pdo;
   $posts = dbIdent($table1);
   $users = dbIdent($table2);
   $sql = "SELECT p.*, u.username FROM $posts AS p JOIN $users AS u ON p.id_user = u.id WHERE p.status=1 AND p.id_topic = ?";
   $query = $pdo->prepare($sql);
   $query->execute([(int) $topicId]);
   dbCheckError($query);
   return $query->fetchAll();
}

// Fetch published posts with authors for the homepage
function selectAllFromPostsWithUsersOnIndex($table1, $table2, $limit, $offset)
{
   global $pdo;
   $posts = dbIdent($table1);
   $users = dbIdent($table2);
   $limit = max(0, (int) $limit);
   $offset = max(0, (int) $offset);
   $sql = "SELECT p.*, u.username FROM $posts AS p JOIN $users AS u ON p.id_user = u.id WHERE p.status=1 ORDER BY p.id DESC LIMIT $limit OFFSET $offset";
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

// Fetch featured posts for the homepage carousel
function selectTopTopicFromPostsOnIndex($table1)
{
   global $pdo;
   $sql = 'SELECT * FROM ' . dbIdent($table1) . ' WHERE id_topic = 8 AND status = 1';
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchAll();
}

// Simple search by title and content
function searchInTitleAndContent($text, $table1, $table2)
{
   global $pdo;
   $text = trim(strip_tags((string) $text));
   $posts = dbIdent($table1);
   $users = dbIdent($table2);
   $like = '%' . $text . '%';
   $sql = "SELECT
    p.*, u.username 
    FROM $posts AS p 
    JOIN $users AS u 
    ON p.id_user = u.id 
    WHERE p.status=1
    AND (p.title LIKE ? OR p.content LIKE ?)";
   $query = $pdo->prepare($sql);
   $query->execute([$like, $like]);
   dbCheckError($query);
   return $query->fetchAll();
}

// Fetch a single post with its author
function selectPostFromPostsWithUserOnSingle($table1, $table2, $id)
{
   global $pdo;
   $posts = dbIdent($table1);
   $users = dbIdent($table2);
   $sql = "SELECT p.*, u.username FROM $posts AS p JOIN $users AS u ON p.id_user = u.id WHERE p.id = ?";
   $query = $pdo->prepare($sql);
   $query->execute([(int) $id]);
   dbCheckError($query);
   return $query->fetch();
}

// Count published posts
function countRow($table)
{
   global $pdo;
   $sql = 'SELECT COUNT(*) FROM ' . dbIdent($table) . ' WHERE status = 1';
   $query = $pdo->prepare($sql);
   $query->execute();
   dbCheckError($query);
   return $query->fetchColumn();
}

// Older databases were created before updated_date existed.
function ensurePostsUpdatedDate()
{
   global $pdo;
   $column = $pdo->prepare(
      'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
   );
   $column->execute(['posts', 'updated_date']);
   if ((int) $column->fetchColumn() > 0) {
      return;
   }

   $table = $pdo->prepare(
      'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
   );
   $table->execute(['posts']);
   if ((int) $table->fetchColumn() === 0) {
      return;
   }

   $pdo->exec(
      'ALTER TABLE `posts` ADD COLUMN `updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_date`'
   );
   $pdo->exec('UPDATE `posts` SET `updated_date` = `created_date`');
}

ensurePostsUpdatedDate();

function ensureUsersLastLogin()
{
   global $pdo;
   $column = $pdo->prepare(
      'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
   );
   $column->execute(['users', 'last_login']);
   if ((int) $column->fetchColumn() > 0) {
      return;
   }

   $table = $pdo->prepare(
      'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
   );
   $table->execute(['users']);
   if ((int) $table->fetchColumn() === 0) {
      return;
   }

   $pdo->exec('ALTER TABLE `users` ADD COLUMN `last_login` DATETIME NULL DEFAULT NULL AFTER `password`');
}

ensureUsersLastLogin();
