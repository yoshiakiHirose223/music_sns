<?php
session_start();
require('../dbconnect.php');

//確認するボタンが押されていない場合はエラーチェックを走らせない
//確認ボタンが押された時に処理する
if(!empty($_POST)){
  if($_POST['name'] === "") {
    $error['name'] = "blank";
  }
  if($_POST['email'] === "") {
    $error['email'] = "blank";
  }
  if($_POST['password'] === "") {
    $error['password'] = "blank";
  } 
  if(strlen($_POST['password']) < 5) {
    $error['password'] = "short";
  }

  $fileName = $_FILES['image']['name'];
  if(!empty($fileName)){
    $ext = substr($fileName, -3);
    if($ext !== "jpg" && $ext !== "png") {
      $error['image'] = "ext";
    }
  }
  //既に登録されていないか確認する。
  if(empty($error)) {
    $members = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
    $members->execute(array($_POST['email']));
    $member = $members->fetch();
    if($member['cnt'] > 0){
      $error['email'] = "failed";
    }
  }

  if(empty($error)){
    $image = date('YmdHis') . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
    //次の画面で入力内容の確認をするためにSESSIONに保存
    $_SESSION['join'] = $_POST;
    $_SESSION['join']['image'] = $image;
    header('Location: check.php');
    exit();
  }
}

if($_REQUEST['action'] === "rewrite"){
  $_POST = $_SESSION['join'];
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
<h1 class="font-weight-normal">新規登録</h1>    
</header>

<main>

<form action="" method="post" enctype="multipart/form-data">
  <dl>
    <dt>プロフィール画像</dt>
    <dd>
      <input type="file" name="image" size="35" value="test"/>
      <?php if($error['image']==="ext"): ?>
      <p>.jpg、.pngのファイルを選択してください</p>
      <?php endif; ?>
    </dd>

    <dt>名前</dt>
    <dd>
      <input type="text" name="name" value="<?php print(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>">
      <?php if($error['name'] === "blank"): ?>
        <p>名前を入力してください</p>
      <?php endif; ?>
    </dd>

    <dt>メールアドレス</dt>
    <dd>
    <input type="text" name="email" value="<?php print(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>">
      <?php if($error['email'] === "blank"): ?>
        <p>メールアドレスを入力してください</p>
      <?php endif; ?>
      <?php if($error['email'] === "failed"): ?>
        <p>既に登録されています</p>
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
  <div><input type="submit" value="入力内容を確認する" /></div>

</form>


</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>    
</html>
