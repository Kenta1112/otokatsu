<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('TOPページです');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

//カテゴリーデータを取得
$dbCategoryData=getCategoryData();
debug('カテゴリーデータ'.print_r($dbCategoryData,true));



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

  //1ページ当たりの表示件数
  $listSpan=12;
  //現在の表示ページの先頭の数
  $currentPageMin=($currentPageNum-1)*$listSpan;
  debug('現在のページの先頭'.print_r($currentPageMin,true));
  //現在のページのアーティスト情報を取得
  $dbArtistDataAll=getDBArtistdataAll($currentPageMin,$listSpan,$genre,$sort);
  debug('アーティストデータ'.print_r($dbArtistDataAll,true));



?>


<!DOCTYPE html>
<html lang='ja'>

<?php
$siteTitle='TOPページ';
require('head.php');
?>


<body>

<?php
require('header.php');
?>


<main>

  <section class="top-banner">
  <h1>音楽との出会いを</h1>
  </section>

  <div class="main-wrapper">

  <section class="side-bar">
    <form action="" method="GET" class="side-bar-wrapper">

    <p>アーティストを検索する</p>

    <p>ジャンル</p>
    
      <select name="g_id" id="" class="select">

        <option value="0" <?php if($genre===0){echo "selected";}?>>選択してください</option>

        <?php
        foreach($dbCategoryData as $key=>$val){
          
        ?>

        <option value="<?php echo $val["id"];?>" <?php if($genre==$val["id"]){echo "selected";}?>><?php echo $val["name"];?></option>

        <?php
        }
        ?>

      </select>
    

    
    <p>表示順</p>
    <select name="sort">
    <option value="2" <?php if($sort==2){echo "selected";}?>>古い順</option>
      <option value="1" <?php if($sort==1){echo "selected";}?>>新しい順</option>
      
    </select>

    <input type="submit" class="btn" value="検索">

    </form>

  </section>

  
  <section class="main-contents">
    <div class="main-contents-wrapper">

    <div class="match">
      <p class="match-left"><?php echo sanitize($dbArtistDataAll['total_recode']);?>件のアーティストに出逢いました</p>
      <p class="match-right"><?php if($dbArtistDataAll['total_recode']===0){echo 0;}else{echo sanitize($currentPageMin+1);}?>~<?php if($dbArtistDataAll['total_recode']>$currentPageNum*$listSpan){echo $currentPageMin+$listSpan;}else{echo $dbArtistDataAll['total_recode'];}?>/<?php echo sanitize($dbArtistDataAll['total_recode']);?></p>

    </div>

    
    <div class="box-wrapper">
    <?php
    foreach($dbArtistDataAll['artistInfomation'] as $key=>$val){
    ?>
    <div class="box">
    <!-- 商品IDと現在のページを追加する -->
    <a href="artistDetail.php?a_id=<?php echo $val['id'].'&p='.$currentPageNum;?><?php if(!empty($genre)){echo '&g_id='.$genre;}?><?php if(!empty($sort)){echo '&sort='.$sort;}?>" class="panel">
    <div class="top-img-wrapper">
    <img src="<?php showImg($val["pic"]);?>" alt=""> </a>
    </div>
    <h1><?php echo sanitize($val["name"]);?></h1>
    </div>
    
    <?php
    }
    ?>


    </div>

    </div>

    <!-- //ページネーション -->
<div class="pagenetion">
      <ul class="pagenetion-list">
      <?php
              $pageColNum = 5;
              $totalPageNum = $dbArtistDataAll['total_page'];
              // 現在のページが、総ページ数と同じかつ総ページ数が表示項目数以上なら、左にリンク４個出す
              if(($currentPageNum==$totalPageNum) && ($totalPageNum>=$pageColNum)){
                $minPageNum=$currentPageNum-4;
                $maxPageNum=$currentPageNum;
              // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
              }elseif($currentPageNum==($totalPageNum-1) && $totalPageNum>=$pageColNum){
                $maxPageNum=$currentPageNum+1;
                $minPageNum=$currentPageNum-3;
              // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
              }elseif($currentPageNum==2 && $totalPageNum>=$pageColNum){
                $maxPageNum=$currentPageNum+3;
                $minPageNum=$currentPageNum-1;
              // 現ページが1の場合は左に何も出さない。右に５個出す。
              }elseif($currentPageNum==1 && $totalPageNum>=$pageColNum){
                $maxPageNum= 5;
                $minPageNum=$currentPageNum;
              // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
              }elseif($totalPageNum<=$pageColNum){
                $maxPageNum=$totalPageNum;
                $minPageNum=1;
              // それ以外は左に２個出す。
              }else{
                $maxPageNum=$currentPageNum+2;
                $minPageNum=$currentPageNum-2;
              }

              debug('現在のページ'.print_r($currentPageNum,true));
              debug('合計ページ'.print_r($totalPageNum,true));
              debug('最大ページ'.print_r($maxPageNum,true));
              debug('最小ページ'.print_r($minPageNum,true));
            ?>

            <?php
            //<１ページ目に戻る
            if($currentPageNum!=1){?>
            <li class="list-style"><a href="?p=1<?php if(!empty($genre)){echo '&'.$genre;}?><?php if(!empty($sort)){echo '&sort='.$sort;}?>">&lt;</a></li>
            <?php
              } 
              ?>

              <?php 
              for($i=$minPageNum;$i<=$maxPageNum;$i++){
              ?>
              
              <li class="list-item <?php if($currentPageNum==$i) echo 'active';?>"><a href="?p=<?php echo $i;?><?php if(!empty($genre)){echo '&g_id='.$genre;}?><?php if(!empty($sort)){echo '&sort='.$sort;}?>"><?php echo $i;?></a></li>

              <?php
              }
              ?>

              <?php
              //>最後のページに飛ぶ
              if($currentPageNum!=$maxPageNum &&$totalPageNum!=0 &&$totalPageNum!=1){
                ?>

              <li class="list-style"><a href="?p=<?php echo $totalPageNum;?><?php if(!empty($genre)){echo '&g_id='.$genre;}?><?php if(!empty($sort)){echo '&sort='.$sort;}?>">&gt;</a></li>

              <?php
              }
              ?>
      </ul>
    </div>

</section>


    </div>

</div>


<?php
require('footer.php');
?>

</body>

</html>
