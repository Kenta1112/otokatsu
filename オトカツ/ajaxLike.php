<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ajax開始');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

require('auth.php');

//POST送信あり
if(isset($_POST['artistId']) && isset($_SESSION['user_id'])){

  

    try{

      $dbh=dbConnect();
      
      //既にお気に入り登録済みか確認
      if(!empty(favorite_artist($_POST['artistId'],$_SESSION     ['user_id']))){
      debug('お気に入り登録済み');
      $sql='DELETE FROM favorite_artist WHERE artist_id=:a_id AND user_id=:u_id';
      $data=array(':a_id'=>$_POST['artistId'],
                  ':u_id'=>$_SESSION['user_id']);
      }else{
        debug('お気に入り登録なし');
      $sql='INSERT into favorite_artist(artist_id,user_id,create_date) VALUE (:a_id,:u_id,:date)';
      $data=array(':a_id'=>$_POST['artistId'],
                  ':u_id'=>$_SESSION['user_id'],
                  ':date'=>date('Y-m-d H:i:s'));
      }

      $stmt=queryPost($dbh,$sql,$data);

    }catch(Excepion $e){
      debug('DB接続エラー'.$e->getMessage());

  }

  }


?>