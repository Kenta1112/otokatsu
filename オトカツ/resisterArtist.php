<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('アーティスト登録画面');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

require('auth.php');

//アーティストのGETパラメータを取得
$artist_id=(!empty($_GET['a_id']))?$_GET['a_id']:'';
//DBからアーティスト情報取得
$dbArtistData=getDBArtistData($artist_id);
//編集or新規登録
$edit_flg=(empty($dbArtistData))?false:true;

debug('エディットフラグ'.print_r($edit_flg,true));

//カテゴリーデータを取得
$dbCategoryData=getCategoryData();

debug('アーティスト情報'.print_r($dbArtistData,true));
debug('カテゴリー情報'.print_r($dbCategoryData,true));

//現在のページ情報の取得
$currentPageNum=(!empty($_GET['p']))?$_GET['p']:'1';
if(!is_int((int)$currentPageNum)){
  debug('不正な値が入力されました');
  header('Location:top.php');
}


//ジャンルのGETパラメータを取得
$genre=(!empty($_GET['g_id']))?$_GET['g_id']:'';
//ソートのGETパラメータを取得
$sort=(!empty($_GET['sort']))?$_GET['sort']:'';




//POST送信あり
if(!empty($_POST)){
  debug('アーティスト情報のPOST送信あり');
  debug('POST送信情報'.print_r($_POST,true));
  debug('FILES送信情報'.print_r($_FILES,true));


  //変数に格納
  $artist_name=$_POST['artist_name'];
  $profile=$_POST['profile'];
  $category=$_POST['category'];

  //画像をアップロードしてるときパスを格納
  $pic=(!empty($_FILES["pic"]["name"])) ? uploadImg($_FILES['pic'],'pic'):"";
  //画像をアップロードしてないときはDBに登録してあればDBの情報を取ってくる
  $pic=((empty($pic)) &&(!empty($dbArtistData['pic']))) ? $dbArtistData['pic']:$pic;


  //バリデーションチェック
  //新規登録の時
  if(!empty($_POST)){
    debug('POST送信あり');

    //未入力チェック
    validRequired($artist_name,'artist_name');
    validRequired($profile,'profile');
    validRequired($category,'category');

    
if(empty($err_msg)){
    //最大長さチェック
    validMaxLen($artist_name,'artist_name');
    //セレクトボックスチェック
    validSelectBox($category,'category');

  }

  if(empty($err_msg)){
    debug('バリデーション完了');

    //例外処理1(DBのartistテーブルに格納)
    try{
      //DB接続
      $dbh=dbConnect();

      //SQL文作成
      if($edit_flg){
        $sql="UPDATE artist SET name=:name,category_id=:category_id,profile=:profile,pic=:pic,resist_userid=:resist_userid WHERE id=:artist_id";
        //プレースホルダー
      $data=array(':name'=>$artist_name,':category_id'=>$category,':profile'=>$profile,':pic'=>$pic,':artist_id'=>$artist_id,':resist_userid'=>$_SESSION['user_id']);
      }else{
        $sql="INSERT into artist (name,category_id,profile,pic,create_date,resist_userid) VALUE (:name,:category_id,:profile,:pic,:date,:resist_userid)";
        
        //プレースホルダー
      $data=array(':name'=>$artist_name,':category_id'=>$category,':profile'=>$profile,':pic'=>$pic,':date'=>date('Y:m:d H-i-s'),':resist_userid'=>$_SESSION['user_id']);
      }
      
      //SQl実行
      $stmt=queryPost($dbh,$sql,$data);

      if(!$edit_flg){
        $artist_id=$dbh->lastInsertId('id');
      }
      
      debug('insertid::'.print_r($artist_id,true));

      if($stmt){
        debug('アーティスト情報登録OR更新完了');

        $artistDetail='artistDetail.php';

          if(!empty($artist_id)){
            $artistDetail.="?a_id=".$artist_id;
          }
          if(!empty($currentPageNum)){
            $artistDetail.="&p=".$currentPageNum;
          }
          if(!empty($genre)){
            $artistDetail.="&g_id=".$genre;
          }
          if(!empty($sort)){
            $artistDetail.="&sort=".$sort;
          }

          header("Location:$artistDetail");

          
          
      }else{
      error_log("DB接続エラー".$e->getMessage());
      $err_msg['common']=MSG07;
      }


    }catch(Exception $e){
      error_log("DB接続エラー".$e->getMessage());
      $err_msg['common']=MSG07;
    }



    // //例外処理2(DBのboardテーブルに格納)
    // try{
    //   //DB接続
    //   $dbh2=dbConnect();
    //   //SQL文作成
    //   $sql2='INSERT into board (artist_id,create_date) VALUE (:artist_id,:create_date)';
    //   //プレースホルダー
    //   $data2=array(':artist_id'=>$id,':create_date'=>date('Y:m:d H-i-s'));
    //   //SQL文実行
    //   $stmt2=queryPost($dbh2,$sql2,$data2);

    //   if($stmt2){
    //     debug('board情報登録完了');
    //     header("Location:$artistDetail");

    //   }else{
    //   error_log("DB接続エラー".$e->getMessage());
    //   $err_msg['common']=MSG07;
    //   }


    // }catch(Exception $e){
    //   error_log("DB接続エラー".$e->getMessage());
    //   $err_msg['common']=MSG07;
    // }



    }


    }

  }



?>

<!DOCTYPE html>
<html lang="ja">

<?php
if($edit_flg){
  $siteTitle="アーティスト情報編集";
}else{
  $siteTitle="アーティスト情報登録";
}
?>

<?php
require('head.php');
?>

<body>

<?php
require('header.php');
?>

<main>

<div class="resistArtist-main-wrapper main-wrapper">
  <h1 class="profile-title">アーティスト情報登録</h1>
  

  <section class="profile-edit">

    <div class="profile-edit-wrapper">
      <form action="" method="post" enctype="multipart/form-data">

      <div class="img-wrapper">ドラッグ＆ドロップ
      <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
      <input type="file" class="input-file" name="pic">
      <img class="prev-img"src="<?php echo $dbArtistData["pic"];?>" alt="" style="<?php if(empty($dbArtistData["pic"]))echo "display:none;"?>">
      
      </div>
      <p class="err_msg"><?php err_msg('common');?></p>

      <p>アーティスト名</p>
      <input type="text" name="artist_name" value="<?php echo $dbArtistData["name"];?>" class="<?php addErrClass('artist_name');?>">
      <p class="err_msg"><?php err_msg('artist_name');?></p>

      <p>カテゴリー</p>
      <select name="category" id="" class="select" value="<?php echo $dbArtistData["category_id"];?>">

        <option value="0" <?php if($dbCategoryData[0]["id"]===0){echo "selected";}?>>選択してください</option>

        <?php
        foreach($dbCategoryData as $key=>$val){
          
        ?>

        <option value="<?php echo $val["id"];?>" <?php if($dbArtistData["category_id"]==$val["id"])echo "selected";?>><?php echo $val["name"];?></option>

        <?php
        }
        ?>

      </select>


      <p>プロフィール</p>
      <textarea name="profile" class="profile" rows="10" value="<?php showDBdata("profile")?>"><?php echo $dbArtistData["profile"];?></textarea>

      <div class="count"><span class="countshow">0</span><span>/3000<span></div>
      
      <div class="btn-wrapper prfileEdit-btn-wrapper">
      <input type="submit" class="btn prfileEdit-btn" value="<?php if($edit_flg){echo "編集";}else{echo "登録";}?>">
      </div>

      
      
      </form>
    </div>
  </section>




  </div>

  </main>


  <?php
  require("footer.php");
  ?>




</body>
</html>