<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('簡単ログイン');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();





        try{
        //DB格納
        
            //DB接続
            $dbh=dbConnect();
            //SQL文作成
            $sql='INSERT INTO users (username,login_time,create_date) VALUES("名無し",:login_time,:create_date)';
            //プレースホルダー挿入
            $data=array(':login_time'=>date('Y-m-d H:i:s'),':create_date'=>date('Y-m-d H:i:s'));
            //SQL実行
            queryPost($dbh,$sql,$data); 


            //セッションに格納
            //セッション期限
            $sesLimit=60*60;
            //ログイン日時
            $_SESSION['login_date']=time();
            //ログインリミット(初期値)
            $_SESSION['login_limit']=$sesLimit;
            //ユーザーIDを格納
            $_SESSION['user_id']=$dbh->lastInsertId();
            //セッションの中身
            debug('セッションの中身:'.print_r('$_SESSION'));

            header("Location:top.php");
        
    }catch(Exception $e){
        error_log("DBに接続できませんでした".$e->getMessage());
        $err_msg['common']=MSG07;
    }



?>