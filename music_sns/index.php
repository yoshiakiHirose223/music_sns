<?php
session_start();
require('dbconnect.php');

//$_SESSION['id']がない時はログイン画面に返す
if(!isset($_SESSION['id'])){
  header('Location: login.php');
  exit();
}

//create.phpから$_POSTを受け取ってDBに保存,INSERTする。
if(!empty($_POST)){
  $tweetStatement = $db->prepare('INSERT INTO posts SET member_id=?, artist_name=?, song_title=?, message=?, thumbnails_url=?, play_url=?, created=NOW()');
  $tweetStatement->execute(array(
    $_SESSION['id'],
    $_POST['artistName'],
    $_POST['song_title'],
    $_POST['message'],
    $_POST['thumbnail_url'],
    $_POST['play_url']
  ));
}

if(isset($_SESSION['id'])){
  //自分の名前、プロフィール画像、自分の投稿
  //フォローしている人の名前,プロフィール画像、投稿
  $id = $_SESSION['id'];
  //フォローしている人たちを取り出してIN句を作る
  $follows = $db->prepare('SELECT follows_id FROM follows WHERE member_id=?');
  $follows->execute(array($id));
  $follows_array=$follows->fetchALL(PDO::FETCH_COLUMN);
  //共通のSQL文
  $first_statement = "SELECT p.*, m.name, m.profile_image FROM posts p LEFT JOIN members m ON p.member_id=m.id WHERE p.member_id=$id ";
  $latter_statemant = "GROUP BY p.id ORDER BY p.created DESC";
  //フォローしている人がいた場合
  if(count($follows_array) !== 0){
    //WHERE member_id IN (?,?,?,?...)<-フォローしている人の人数分
    $in_statement = substr(str_repeat(',?', count($follows_array)), 1);
    $posts = $db->prepare($first_statement . "OR p.member_id IN " . "(" .$in_statement. ") " . $latter_statemant);
  
    for($i=1; $i<=count($follows_array); $i++){
      $posts->bindParam($i,$follows_array[$i-1], PDO::PARAM_INT);
    }

  } else {
    //誰もフォローしていなかった場合
    $posts=$db->prepare($first_statement . $latter_statemant);
  }
  $posts->execute();
}



?>
<!doctype html>
<html lang="ja">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<link rel="stylesheet" href="style.css" />

<title>PHP</title>
</head>
<body>
<header>
</header>

<div class="row">
  <div class="col-3 bg-dark">
  <?php include "sideBar.html" ?>
  </div>
  <div class="col-9">
        <!-- 投稿を作っていく -->
    <?php foreach($posts as $post): ?>
    <div class="row post">
        <!-- プロフィール画像 -->
      <div class="col-3">
        <div class="profile_image">
          <a href="profile.php?id=<?php print(htmlspecialchars($post['member_id'])); ?>">
            <img src="member_picture/<?php print(htmlspecialchars($post['profile_image'], ENT_QUOTES)); ?>">
          </a>
        </div>
      </div>
      <div class="col-9">
        <!-- 名前 -->
        <p class="font-weight-bold"><?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?></p>
        <!-- 投稿時間 -->
        <p><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></p>
      </div>
      <div class="col-3 thumbnails">
        <!-- サムネイル -->
        <img src="<?php print($post['thumbnails_url']); ?>">
      </div> 
      <div class="col-9">
        <!-- アーティスト名 -->
        <p><?php print(htmlspecialchars($post['artist_name'], ENT_QUOTES)); ?></p>
        <!-- 曲名 -->
        <p><?php print(htmlspecialchars($post['song_title'], ENT_QUOTES)); ?></p>
      </div>
      <?php if($post['play_url'] !== NULL): ?>
        <div class="col-12">
          <!-- 曲を再生 -->
            <audio src="<?php print($post['play_url']) ?>" controls></audio>
        </div>
      <?php endif; ?>
        <div class="col-12">
          <!-- メッセージ -->
          <p class="message"><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?></p>
        </div>
    </div>
      <?php endforeach; ?>
  </div>
</div>






<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>    
</html>
