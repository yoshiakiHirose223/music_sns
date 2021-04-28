<?php
session_start();
require('dbconnect.php');

if(!empty($_POST)){
  if($_POST['email'] === ""){
    $error['email'] = "blank";
  }

  if($_POST['password'] === ""){
    $error['password'] = "blank";
  } elseif(strlen($_POST['password']) < 5){
    $error['password'] = "short";
  }

  if(empty($error)){
    $members = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
    $members->execute(array(
      $_POST['email'],
      sha1($_POST['password'])
    ));
    $member = $members->fetch();

    if($member){
      $_SESSION['id'] = $member['id'];
      header('Location: index.php');
      exit();
    } else {
      $error['login'] = 'false';
    }

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
<link rel="stylesheet" href="../style.css" />

<title>ログイン</title>
</head>

<body>

  <div class="container">
    <form action="" method="post">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input type="text" name="email" value="<?php print(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>">
          <?php if($error['email'] === "blank"): ?>
          <p>メールアドレスを入力してください</p>
          <?php endif; ?>
          <?php if($error['login'] === "false"): ?>
          <p>メールアドレスかパスワードが間違っています</p>
          <?php endif; ?>
        </dd>

        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>">
          <?php if($error['password'] === "blank"): ?>
            <p>パスワードを入力してください</p>
            <?php endif; ?>
            <?php if($error['password'] === "short"): ?>
            <p>5文字以上でパスワードを入力してください</p>
            <?php endif; ?>
        </dd>
      </dl>
        <input type="submit" value="LOGIN">
    </form>
  </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>    
</html>
