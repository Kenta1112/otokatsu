<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('マイページ');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

require('auth.php');


//ユーザー情報取得
$dbUserData=getDBUserData($_SESSION['user_id']);
debug('ユーザー情報'.print_r($dbUserData,true));

//お気に入りアーティスト情報取得
$dbFavoriteArtistData=getDBFavoriteArtistData($_SESSION['user_id']);
debug('お気に入り情報'.print_r($dbFavoriteArtistData,true));

//登録したユーザ情報取得
$dbResistArtist=getDBResistArtist($_SESSION['user_id']);
debug('ユーザが登録したアーティスト'.print_r($dbResistArtist,true));



?>



<!DOCTYPE html>
<html lang='ja'>

<?php
$siteTitle='マイページ';
require('head.php');
?>

<body>

<?php
require('header.php');
?>

<main>

<div class="main-wrapper detail-wrapper mypageDetail-wrapper">
  <h1>マイページ</h1>

  

  <section class="detail mypageDetail">
    <div class="top">
    <img src="<?php showImg($dbUserData['pic']);?>" alt="">

    <table class="right" >
      <tr>
        <td class="title">ユーザ名</td>
        <td class="name"><?php echo sanitize($dbUserData['username']);?></td>
      </tr>
      <tr>
      <td class="title">年齢</td>
      <td class="age"><?php echo sanitize($dbUserData['age']);?>歳</td>
      </tr>

    </table>
    </div>
    <p class="sub-title">プロフィール</p>
    <p class="profile userprofile"><?php echo sanitize($dbUserData['profile']);?></p>

    <p class="to-profileEdit"><a href="profileEdit.php">プロフィールを編集する</a><p>

  </section>

  <section class="favorite">
  <p class="sub-title">お気に入り</p>
  <div class="box-wrapper ">
    <?php
    foreach($dbFavoriteArtistData as $key=>$val){
    ?>
    <div class="box favorite-box">
    <!-- 商品IDと現在のページを追加する -->
    <a href="artistDetail.php?a_id=<?php echo $val['artist_id']?>" class="panel">
    <div class="top-img-wrapper">
    <img src="<?php showImg($val["pic"]);?>" alt=""> </a>
    </div>
    <h1><?php echo sanitize($val["name"]);?></h1>
    </div>
  
    <?php
    }
    ?>
  </section>

  <section class="resister">
  <p class="sub-title">登録したアーティスト</p>
  <div class="box-wrapper ">
    <?php
    foreach($dbResistArtist as $key=>$val){
    ?>
    <div class="box resister-box">
    <!-- 商品IDと現在のページを追加する -->
    <a href="artistDetail.php?a_id=<?php echo $val['id']?>" class="panel">
    <div class="top-img-wrapper">
    <img src="<?php showImg($val["pic"]);?>" alt=""> </a>
    </div>
    <h1><?php echo sanitize($val["name"]);?></h1>
    </div>
  
    <?php
    }
    ?>
  </section>


</div>




<?php
require('footer.php');
?>
