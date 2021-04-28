<?php
session_start();
require('dbconnect.php');

//$_SESSION['id']がない時はログイン画面に返す
if($_REQUEST['id'] === "" || !is_numeric($_REQUEST['id'])){
  header('Location: login.php');
  exit();
}

//アンフォロー、フォローの処理
if(!empty($_POST)){
  $myId = $_SESSION['id'];
  $followId = $_REQUEST['id'];
  if($_POST['follow'] === "follow"){
    //フォローする
    $followStatement = $db->prepare("INSERT INTO follows SET member_id=?, follows_id=?");
    $followStatement->execute(array(
      $myId,
      $followId
    ));
    $canFollow = "NO";
  } elseif($_POST['follow'] === "unfollow"){
    //フォローを外す
    $unfollowStatement = $db->prepare("DELETE FROM follows WHERE member_id=? AND follows_id=?");
    $unfollowStatement->execute(array(
      $myId,
      $followId
    ));
    $canFollow = "YES";
  }

}


if($_REQUEST['id'] !== "" && is_numeric($_REQUEST['id'])){
  //idの人の名前、プロフィール画像、自分の投稿
  $id = $_REQUEST['id'];

  //共通のSQL文 ->その人の投稿を取得
  $posts=$db->prepare("SELECT p.*, m.name, m.profile_image FROM posts p LEFT JOIN members m ON p.member_id=m.id WHERE p.member_id=? GROUP BY p.id ORDER BY p.created DESC");
  $posts->execute(array($id));

  //フォロー数、フォロワー数の取得
  //プロフィール情報の取得
  $profile = $db->prepare("SELECT m.name, m.profile_image, COUNT(f.member_id=? OR NULL) as follows_count, COUNT(f.follows_id=? OR NULL) AS followers_count FROM follows f, members m WHERE m.id=?");
  $profile->execute(array($id,$id,$id));
  $profileInfo = $profile->fetch();

    if($_REQUEST['id'] !== $_SESSION['id']) {
      //自分のプロフィールページじゃない場合
      //自分がその人をフォローしているか判別
      $myId = $_SESSION['id'];
      $followed = $db->prepare("SELECT COUNT(*) as cnt FROM follows WHERE member_id=? AND follows_id=?");
      $followed->execute(array($myId, $id));
      $checkFollow = $followed->fetch();
      if($checkFollow['cnt'] == 0){
        //フォローできる
        $canFollow = "YES";
      } else {
        //フォローしてる
        $canFollow = "NO";
      }
    } else {
      //自分
      $canFollow = "ME";
    }

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
<!-- <h1 class="font-weight-normal">PHP</h1>     -->
</header>



<div class="row">
  <?php if(!empty($profileInfo)): ?>
    <div class="col-12">
      <div class="row">
        <div class="col-8 profile">
          <img src="member_picture/<?php print($profileInfo['profile_image']); ?>">
        </div>
        <div class="col-4">
          <p><?php print($profileInfo['name']); ?></p>
          <form action="" method="post">
            <?php if($canFollow === "YES"): ?> 
              <button type="submit" name="follow" value="follow">フォローする</button>
            <?php elseif($canFollow === "NO"): ?>
              <button type="submit" name="follow" value="unfollow">フォローを解除する</button>
            <?php endif; ?>
          </form>
          <p>フォロー：<?php print($profileInfo['follows_count']); ?></p>
          <p>フォロワー：<?php print($profileInfo['followers_count']); ?></p>
        </div>
      </div>
    </div>
  <?php endif; ?>

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
