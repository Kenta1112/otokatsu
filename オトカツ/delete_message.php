
<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('メッセージ削除ページ');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

require('auth.php');



//アーティストのGETパラメータを取得
$a_id=(!empty($_GET['a_id']))?$_GET['a_id']:'';

//ジャンルのGETパラメータを取得
$genre=(!empty($_GET['g_id']))?$_GET['g_id']:'';
//ソートのGETパラメータを取得
$sort=(!empty($_GET['sort']))?$_GET['sort']:'';
//メッセージのGETパラメータを取得
$message_id=(!empty($_GET['message_id']))?$_GET['message_id']:'';



    //コメント削除
    
        try{
            //DB接続
        $dbh=dbConnect();
        //SQl文作成
        $sql='DELETE from message WHERE id=:id';
        //プレースホルダー
        $data=array(':id'=>$message_id);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);


        if($stmt){
          debug('クエリ成功コメント削除');

          $artistDetail='artistDetail.php';

          if(!empty($a_id)){
            $artistDetail.="?a_id=".$a_id;
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
          debug('クエリ失敗コメント削除失敗');
          
      }

        }catch(Exception $e){
            debug('DB接続エラー'.$e->getMessage());
        }
    

    ?>


