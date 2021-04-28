<?php
session_start();
require('../dbconnect.php');

if(!isset($_SESSION['join'])) {
  header('Location: index.php');
  exit();
}

if(!empty($_POST)) {
  //データベースに登録する
  $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, profile_image=?, created=NOW()');
  $statement->execute(array(
    $_SESSION['join']['name'],
    $_SESSION['join']['email'],
    sha1($_SESSION['join']['password']),
    $_SESSION['join']['image']
  ));
  unset($_SESSION);
  header('Location: thanks.php');
  exit();

}
?>



<!doctype html>
<html lang="ja">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


<title>PHP</title>
</head>
<body>

<form action="" method="post">
<input type="hidden" name="action" value="submit"/>
  <dl>
    <dt>名前</dt>
      <dd>
        <?php print(htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES)); ?>
      </dd>

    <dt>メールアドレス</dt>
      <dd><?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES)); ?></dd>

    <dt>パスワード</dt>
      <dd><?php 
      $length = strlen($_SESSION['join']['password']);
      for($i=0; $i<$length; $i++){
        print("*");
      }
      ?>
      </dd>

      <dt>プロフィール画像</dt>
      <?php if($_SESSION['join']['image'] !== ""): ?>
      <dd><img src="../member_picture/<?php print(htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES)); ?>"></dd>
      <?php endif; ?>
  </dl>

  <ul>
  <li>
  <a href="index.php?action=rewrite">編集画面に戻る</a>
  </li>

  <li>
  <input type="submit" value="登録する">
  </li>
</ul>


</form>




</body>    
</html>
