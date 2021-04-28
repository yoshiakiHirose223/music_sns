<?php
try {
  $db = new PDO('mysql:dbname=music_sns;host=localhost;charset=utf8', 'root', 'root');
} catch(PDOException $e) {
  print("DB接続エラー");
}
?>