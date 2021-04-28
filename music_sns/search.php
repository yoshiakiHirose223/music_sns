<?php
session_start();
require('dbconnect.php');

// if(!isset($_SESSION['id'])){
//   header('Location: login.php');
//   exit();
// }

if(!empty($_POST)){
  //何kも入力していないのに検索が押された場合
if($_POST['keyword'] === ""){
  $error['keyword'] = "blank";
}

//検索するカラムとキーワードを指定して検索
$postsCulum = $_POST['postsCulum'];
$keyword = "\"%" . $_POST['keyword'] . "%\"";
$posts = $db->prepare("SELECT p.*, m.name, m.profile_image FROM posts p LEFT JOIN members m ON p.member_id=m.id WHERE p.". $postsCulum ." LIKE ".$keyword." GROUP BY p.id ORDER BY p.created DESC");
$posts->execute();
if(!$posts){
  $error['posts'] = "failed";
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
</header>

<main>
<div class="row">
  <div class="col-3 bg-dark">
    <?php include "sideBar.html" ?>
  </div>

  <div class="col-9">
    <div class="row">
      <div class="search">
        <div class="col-12">
          <!-- 検索窓と検索ボタンの追加 -->
          <form action="" method="post">
            <input type="text" name="keyword" value="">
            <select name="postsCulum">
              <option value="artist_name" selected>アーティスト名</option>
              <option value="song_title">曲名</option>
              <option value="message">コメント</option>
            </select>
            <input type="submit" value="検索">
          </form>
        </div>
      </div>
      <?php if($error['post'] === "failed"): ?>
        <p>投稿が入手できませんでした。他のキーワードをお試しください</p>
        <?php endif; ?>
      <!-- 検索結果があった時 -->
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
              <p><?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?></p>
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
  </div>

</div>


</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>    
</html>
