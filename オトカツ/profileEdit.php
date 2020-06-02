<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('プロフィール編集画面');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

require('auth.php');

$err_msg=array();

//DBからユーザー情報を取得
$getDBUserdata=getDBUserdata($_SESSION['user_id']);
debug('DBから取得したユーザー情報'.print_r($getDBUserdata,true));

//POST送信あり
if(!empty($_POST)){
  debug('POST送信あり');
  debug('POST送信情報'.print_r($_POST,true));
  debug('FILESの情報'.print_r($_FILES,true));

  //変数に格納
  $username=$_POST['name'];
  $age=$_POST['age'];
  $email=$_POST['email'];
  $profile=$_POST['profile'];

  //画像をアップロードしてるときパスを格納
  $pic=(!empty($_FILES["pic"]["name"])) ? uploadImg($_FILES['pic'],'pic'):"";
  debug('pib'.print_r($pic,true));
  //画像をアップロードしてないときはDBに登録してあればDBの情報を取ってくる
  $pic=(empty($pic) && !empty($getDBUserdata['pic'])) ? $getDBUserdata['pic']:$pic;
 

  //DBと情報が異なるときにバリデーションチェック

  //$usernameについてバリデーションチェック
  if($username!==$getDBUserdata['username']){
    debug('名前変更有バリデーション開始');
    
    //未入力チェック
    validRequired($username,'username');
    //最大文字数チェック
    validMaxLen($username,'username');
  }

  //$ageについてバリデーションチェック
  if($age!==$getDBUserdata['age']){
    debug('年齢変更有バリデーション開始');
    //未入力チェック
    validRequired($age,'age');
    //半角数字チェック
    validHalfNum($age,'age');
    //最大年齢チェック
    validMaxAge($age,'age');
  }

  //$emailについてバリデーションチェッック
  if($email!==$getDBUserdata['email']){
    debug('Email変更有バリデーション開始');

    //未入力チェック
    validRequired($email,'email');

    if(empty($err_msg)){
      //最大文字数チェック
      validMaxLen($email,'email');
      //Email形式チェック
      validEmail($email,'email');

    if(!empty($err_msg)){
      //Email重複チェック
      validEmailDup($email,$key);
      }
    }

  }

  //バリデーションチェック完了，DB更新
  if(empty($err_msg)){

    //例外処理
    try{
      //DB接続
      $dbh=dbConnect();
      //SQL文作成
      $sql="UPDATE users SET username=:username,age=:age,email=:email,profile=:profile,pic=:pic WHERE id=:u_id";
      //プレースホルダー
      $data=array(':username'=>$username,':age'=>$age,':email'=>$email,':profile'=>$profile,':pic'=>$pic,':u_id'=>$_SESSION['user_id']);
      ///SQL文実行
      $stmt=queryPost($dbh,$sql,$data);

      if($stmt){
        debug('クエリ成功,DB更新完了');
        header('Location:mypage.php');
      }else{
        debug('クエリ失敗');
        $err_msg['common']=MSG11;
      }

    }catch(Exception $e){
      error_log('DB接続エラー発生'.$e->getMessage());
      $err_msg['common']=MSG07;
    }
  }

}



?>

<!DOCTYPE html>
<html lang="ja">

<?php
$siteTitle="プロフィール編集";
require('head.php');
?>

<main>

<?php
require('header.php');
?>

<div class="profileEdit-main-wrapper main-wrapper">
  <h1 class="profile-title">プロフィール編集</h1>
  

  <section class="profile-edit">

    <div class="profile-edit-wrapper">
      <form action="" method="post" enctype="multipart/form-data">

      <div class="img-wrapper">ドラッグ＆ドロップ
      <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
      <input type="file" class="input-file" name="pic">
      <img class="prev-img"src="<?php echo $getDBUserdata["pic"]?>" alt="" style="<?php if(empty($getDBUserdata["pic"]))echo "display:none;"?>">
      
      </div>

      <p class="err_msg"><?php err_msg('common');?></p>
      <p>名前</p>
      <input type="text" name="name" value="<?php showDBdata("username");?>" class="<?php addErrClass('username');?>">
      <p class="err_msg"><?php err_msg('username');?></p>
      <p>年齢</p>
      <input type="age" name="age" class="<?php addErrClass('age');?>" value="<?php showDBdata("age");?>"><span>歳</span>
      <p class="err_msg"><?php err_msg('age');?></p>
      <p>メールアドレス変更</p>
      <input type="text" name="email" value="<?php showDBdata("email")?>" class="<?php addErrClass('email');?>">
      <p class="err_msg"><?php err_msg('email');?></p>
      <p>プロフィール</p>
      <textarea name="profile" class="profile" rows="10" value="<?php showDBdata("profile")?>"><?php showDBdata("profile"); ?></textarea>
      
      <div class="btn-wrapper prfileEdit-btn-wrapper">
      <input type="submit" class="btn prfileEdit-btn">
      </div>
      
      </form>
    </div>
  </section>

 


  </div>


  <?php
  require("footer.php");
  ?>




</main>

