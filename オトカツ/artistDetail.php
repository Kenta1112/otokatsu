<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('アーティスト詳細ページ');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

require('auth.php');

//アーティストIDのGETパラメータを取得
$a_id=(!empty($_GET['a_id']))?$_GET['a_id']:'';
debug('アーティストID'.print_r($a_id,true));
//取得したGETパラメータを基にDB情報の取得
$dbArtistDetail=getArtistDetail($a_id);
debug('アーティスト詳細データ'.print_r($dbArtistDetail,true));

//現在のページ情報の取得
$currentPageNum=(!empty($_GET['p']))?$_GET['p']:'1';
if(!is_int((int)$currentPageNum)){
  debug('不正な値が入力されました');
  header('Location:top.php');
}




$genre=(!empty($_GET['g_id']))?$_GET['g_id']:'';
//ソートのGETパラメータを取得
$sort=(!empty($_GET['sort']))?$_GET['sort']:'';

$err_msg=array();


if(!empty($_POST)){
  //POST送信あり
  debug('POST送信情報'.print_r($_POST,true));
  $comment=$_POST['comment'];

  //未入力チェック
  validRequired($comment,'comment');
  if(empty($err_msg)){
    //最大文字数チェック
    validMaxLen($comment,'comment',$max=3000);

    if(empty($err_msg)){
      //例外処理
      try{
        //DB接続
        $dbh=dbConnect();
        //SQL文作成
        $sql='INSERT INTO message (artist_id,user_id,message,create_date) VALUE (:artist_id,:user_id,:message,:create_date)';
        //プレースホルダー
        $data=array(':artist_id'=>$a_id,':user_id'=>$_SESSION['user_id'],':message'=>$comment,':create_date'=>date('Y-m-d H:i:s'));
        //SQl実行
        $stmt=queryPost($dbh,$sql,$data);

        $_POST=array();
      
        }catch(Exception $e){
        debug('DB接続エラー'.$e->getMessage());
  }

      }


    }
    
  }

  

  //コメントの取得
$dbComment=getComment($a_id);
debug('コメント'.print_r($dbComment,true));
$_POST=array();

?>



<!DOCTYPE html>
<html lang='ja'>

<?php
$siteTitle='アーティスト詳細ページ';
require('head.php');
?>

<body>

<?php
require('header.php');
?>

<main>

<div class="main-wrapper detail-wrapper artistDetail-wrapper">
  <h1>アーティスト詳細画面</h1>

  <div class="icon-wrapper">
  <i class="fas fa-heart fa-2x js-like <?php if(favorite_artist($dbArtistDetail['id'],$_SESSION['user_id'])){echo 'active';}?>" data-artistid='<?php echo $dbArtistDetail['id'];?>'></i>
  </div>

  <section class="detail artistDetail">
    <div class="top">
    <img src="<?php showImg($dbArtistDetail['pic'])?>" alt="">

    <table class="right" >
      <tr>
        <td class="title">アーティスト名</td>
        <td class="name"><?php echo $dbArtistDetail['name'];?></td>
      </tr>
      <tr>
      <td class="title">ジャンル</td>
      <td class="category"><?php echo $dbArtistDetail['category'];?></td>
      </tr>

    </table>

    <?php
    if($dbArtistDetail['resist_userid']==$_SESSION['user_id']){
      ?>
    <p class="editArtist"><a href="resisterArtist.php?a_id=<?php echo $a_id;?>">アーティスト情報を編集する</a></p>

    <?php
    }
    ?>

    </div>
    <p class="profile"><?php echo $dbArtistDetail['profile'];?></p>

  </section>
<?php
if(!empty($dbComment)){ 

  ?>
  <p>コメント</p>

  <?php
}
?>
  <section class="board">
    
      
    <?php 
    foreach($dbComment as $key=>$val){ 
      ?>
      <div class="message-wrapper">
      <p class="username"><?php echo $val['username'];?></p>
      
      <p><img src="<?php showImg($val['pic']);?>" alt="" class="pic"></p>

      <div class="comment-wrapper">
      <p class="message"><?php echo $val['message'];?></p>
      </div>
      
      <p class="send_date"><?php echo $val['send_date'];?></p>


      <?php
      if($_SESSION['user_id']===$val['user_id']){
        ?>

        <a href="delete_message.php?a_id=<?php echo $a_id;?><?php if(!empty($currentPageNum)){echo '&p='.$currentPageNum;}?><?php if(!empty($genre)){echo '&g_id='.$genre;}?><?php if(!empty($sort)){echo '&sort='.$sort;}?><?php echo '&message_id='.$val['message_id'];?>"><i class="fas fa-times"></i>削除する</a>


        <?php
    }
    ?>


      </div>
        
      <?php
    }
    ?>

    
    
  </section>

      <form action="" method="POST">
      <p class="comment-title">コメントする</p>
      <textarea name="comment" class="profile comment" rows="10"></textarea>

      <div class="count"><span class="countshow">0</span><span>/3000<span></div>
      
      <div class="btn-wrapper comment-btn-wrapper">
      <input type="submit" class="btn comment-btn" value="送信">
      </div>

      </form>






  <p class="back"><a href="top.php?p=<?php echo $_GET['p'];?><?php if(!empty($genre)){echo '&g_id='.$genre;}?><?php if(!empty($sort)){echo '&sort='.$sort;}?>">&lt;前のページに戻る</a></p>








</div>



<?php
require('footer.php');
?>
