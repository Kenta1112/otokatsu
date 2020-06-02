<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード変更画面');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

require('auth.php');

$err_msg=array();


//POST送信あり
if(!empty($_POST)){

  debug('POST送信あり');
  
  //変数に格納
  $oldpass=$_POST['oldpass'];
  $password=$_POST['password'];
  $pass_retype=$_POST['pass_retype'];

  //入力チェック
  validRequired('$oldpass',"oldpass");
  validRequired('$password',"password");
  validRequired('$pass_retype',"pass_retype");


  if(empty($err_msg)){
  //元々のパスワード情報取得
  try{
    //DB接続
    $dbh=dbConnect();
    //SQL文作成
    $sql='SELECT password FROM users WHERE id=:id';
    //プレースホルダー
    $data=array(':id'=>$_SESSION['user_id']);
    //SQL実行
    $stmt=queryPost($dbh,$sql,$data);

    if($stmt){
    debug('クエリ成功');
    //結果を格納
    $result=$stmt->fetch(PDO::FETCH_ASSOC);

    //元のと一致しているかチェック
    if(!password_verify($oldpass,$result['password'])){
      debug('元のパスワードと一致しません');
      $err_msg['common']=MSG12;
    }

    }else{
      debug('クエリ失敗');
      $err_msg['common']=MSG07;
    }

  }catch(Exception $e){
    error_log('DB接続エラー'.$e->getMessage());
    $err_msg['common']=MSG07;
  }
  
  //新しいパスワードの再入力が一致しているかチェック
  validPassword($password,$pass_retype,"password");
  //半角文字チェック
  validHalf($password,"password");
  //パスワード最小文字数チェック
  validPasswordMin($password,"password");

}

if(empty($err_msg)){
  debug('パスワードバリデーションチェック完了');
  //DB登録
  try{
    //DB接続
    $dbh=dbConnect();
    //SQl文作成
    $sql='UPDATE users SET password=:password WHERE id=:id';
    //プレースホルダー
    $data=array(':password'=>password_hash($password,PASSWORD_DEFAULT),':id'=>$_SESSION['user_id']);
    //SQL実行
    $stmt=queryPost($dbh,$sql,$data);

    if($stmt){
      debug('クエリ成功');
      header('Location:top.php');
  }else{
      debug('クエリ失敗');
  }

  }catch(Exception $e){
    debug('DB接続失敗'.$e->getMessage());
    $err_msg['common']=MSG07;
  }
}


}

?>

<!DOCTYPE html>
<html lang='ja'>
<?php
$siteTitle='パスワード変更';
require('head.php');
?>

<body>

    <?php
require('header.php');
?>

    <main>
        <div class="main-wrapper">


            <div class="form-wrapper">
                <h1 class="title">パスワード変更</h1>
                <form action="" method="post">
                <p class="err_msg"><?php err_msg('common');?></p>
                    <p class="form-title">現在のパスワード</p>
                    <input type="password" name="oldpass" value="<?php if(!empty($_POST))keep_type('oldpass'); ?>" class="<?php addErrClass('oldpass');?>">
                    <p class="err_msg"><?php err_msg('oldpass'); ?></p>
                    <p class="form-title">新しいパスワード<span style="font-size:16px"> *8文字以上</span></p>
                    <input type="password" name="password" value="<?php if(!empty($_POST))keep_type('password'); ?>" class="<?php addErrClass('password');?>">
                    <p class="err_msg"><?php err_msg('password'); ?></p>
                    <p class="form-title">新しいパスワード再入力</p>
                    <input type="password" name="pass_retype" value="<?php if(!empty($_POST))keep_type('pass_retype'); ?>" class="<?php addErrClass('pass_retype');?>">
                    <p class="err_msg"><?php err_msg('pass_retype'); ?></p>
                    <div class="btn-wrapper">
                    <input type="submit" class="btn" value="変更">
                    </div>

                </form>
            </div>
        </div>
    </main>

    <?php
require('footer.php');
?>

</body>

</html>
