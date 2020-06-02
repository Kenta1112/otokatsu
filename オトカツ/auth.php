<?php

//====================================================
//ログイン認証
//===================================================

if(!empty($_SESSION['login_date'])){

  debug('ログイン済みのユーザーです');

  //ログイン有効期限内かチェック
  if(($_SESSION['login_date']+$_SESSION['login_limit'])<time()){
    debug('ログイン有効期限切れです');

    session_destroy();
    //ログイン画面へ
    header('Location:login.php');
  }else{
    debug('ログイン有効期限内です');
    $_SESSION['login_date']=time();

    //無限ループ対策
    if(basename($_SERVER['PHP_SELF'])==='login.php'){
      debug('マイページへ遷移');
      header('Location:mypage.php');
    }
    
  }

}else{
  debug('未ログインユーザーです');
  if(basename($_SERVER['PHP_SELF'])!=='login.php'){
  
  header("Location:login.php");
  }
}




