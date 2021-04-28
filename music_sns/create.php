<?php
session_start();
require('dbconnect.php');

if(!isset($_SESSION['id'])){
  header('Location: login.php');
  exit();
}

if(isset($_SESSION)){
  $tweet = $_SESSION['tweet'];
  unset($_SESSION['tweet']);
}

//検索する
if(!empty($_GET)){
  $itunes_url = "https://itunes.apple.com/search?country=jp&media=music&term=";
  $keyword = rawurlencode($_GET['keyword']);
  $url = $itunes_url . $keyword;
  $json = json_decode(file_get_contents($url),true);
  $results = $json['results'];
}
//TWEETするボタンが押された時
if(!empty($_POST)){
  $_SESSION['tweet']['artistName'] = $_POST['artistName'];
  $_SESSION['tweet']['song_title'] = $_POST['song_title'];
  $_SESSION['tweet']['thumbnail_url'] = $_POST['thumbnail_url'];
  $_SESSION['tweet']['play_url'] = $_POST['play_url'];
  $_SESSION['tweet']['message'] = $_POST['message'];

  header('Location: create.php');
  exit();

}

?>

<!doctype html>
<html lang="ja">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

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
    <!-- 検索窓と検索ボタンの追加 -->
    <form action="" method="get">
      <input type="text" name="keyword" value="">
      <input type="submit" value="検索">
    </form>

    <!-- 曲が選択された場合 -->
    <?php if(!empty($tweet)): ?>
      <div class="col-9">
        <form action="index.php" method="post">
          <input type="hidden" name="thumbnail_url" value="<?php print($tweet['thumbnail_url']); ?>">
          <input type="hidden" name="artistName" value="<?php print($tweet['artistName']); ?>">
          <input type="hidden" name="song_title" value="<?php print($tweet['song_title']); ?>">
          <input type="hidden" name="play_url" value="<?php print($tweet['play_url']); ?>">
          <img src="<?php print($tweet['thumbnail_url']) ?>">
          <p><?php print($tweet['artistName']) ?></p>
          <p><?php print($tweet['song_title']) ?></p>
          <textarea name="message" cols="50" rows="5" placeholder="この曲の印象を書こう"></textarea>
        <input type="submit" value="TWEETする">
        </form>
      </div>
    <?php endif; ?>

    <!-- 検索結果一覧の表示 -->
      <?php if(!empty($_GET['keyword'])): ?>
        <div class="row">
          <?php foreach($results as $result): ?>
            <div class="col-12">
            <form action="" method="post">
              <input type="hidden" name="thumbnail_url" value="<?php print($result['artworkUrl100']); ?>">
              <input type="hidden" name="artistName" value="<?php print($result['artistName']); ?>">
              <input type="hidden" name="song_title" value="<?php print($result['trackName']); ?>">
              <input type="hidden" name="play_url" value="<?php print($result['previewUrl']); ?>">
              <button type="submit">
                <img src="<?php print($result['artworkUrl100']); ?>">
                <p><?php print($result['artistName']); ?></p>
                <p><?php print($result['trackName']); ?></p>
                <?php if($result['previewUrl'] !== NULL): ?>
                  <audio src="<?php print($result['previewUrl']) ?>" controls></audio>
                <?php endif; ?>
              </button>
            </form>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
  </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>    
</html>
