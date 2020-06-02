<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('退会画面');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

require('auth.php');

$err_msg=array();


//POST送信があるとき
if(!empty($_POST)){
  debug('POST送信があります');

  //例外処理
  try{

    //DB接続
    $dbh=dbConnect();
    //SQL文作成
    $sql1='UPDATE users SET delete_flg=1 WHERE id=:id';
    $sql2='UPDATE favorite_album SET delete_flg=1 WHERE id=:id';
    $sql3='UPDATE favorite_artist SET delete_flg=1 WHERE id=:id';
    //プレースホルダー
    $data=array(':id'=>$_SESSION['user_id']);
    //SQL文実行
    $stmt1=queryPost($dbh,$sql1,$data);
    $stmt2=queryPost($dbh,$sql2,$data);
    $stmt3=queryPost($dbh,$sql3,$data);

    if($stmt1){
      debug('クエリ成功');

      session_destroy();
      header('Location:top.php');
    }

  }catch(Exception $e){

    debug('接続エラー'.$e->getMessage());
    $err_msg['common']=MSG07;

  }
}


?>

<!DOCTYPE html>
<html lang="ja">

<?php
$siteTitle='退会';
require('head.php');
?>

<main>

<?php
require('header.php');
?>

<div class="withdrawal-btn-wrapper">
  <p>退会する</p>
  <p class="<?php addErrClass('common');?>"><?php err_msg('common');?></p> 
  <form action="" method="POST">
  <input type="submit" class='withdrawal-btn' value="退会" name="submit">
  </form>
</div>

<?php
require('footer.php');
?>


</main>
</html>