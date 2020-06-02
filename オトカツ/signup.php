<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ユーザー登録');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();




$err_msg=array();

    //ポスト送信時チェック
    if(!empty($_POST)){
        debug('POST送信あり');
        debug('POST送信情報'.print_r($_POST,true));
    
        $email=$_POST['email'];
        $password=$_POST['password'];
        $pass_retype=$_POST['pass_retype'];
       

        //未入力チェック

        validRequired($email,'email');

        validRequired($password,'password');

        validRequired($pass_retype,'pass_retype');

        if(empty($err_msg)){
        
        

        //メールアドレス形式チェック
        
        validEmail($email,'email');

         //Email重複チェック
         validEmailDup($email,'email');

         //最大文字数チェック
        
        validMaxLen($email,'email');

       

        validMaxLen($password,'password');


        validMaxLen($pass_retype,'pass_retype');

        
        
        //パスワード最小文字数チェック
           
        validPasswordMin($password,'password');
           
        validPasswordMin($pass_retype,'pass_retype');
        
    
        //パスワード再入力チェック
        validPassword($password,$pass_retype,'pass_retype');
          
       

        if(empty($err_msg)){
            debug('DB格納します');
        try{
        //DB格納
        
            //DB接続
            $dbh=dbConnect();
            //SQL文作成
            $sql='INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:password,:login_time,:create_date)';
            //プレースホルダー挿入
            $data=array(':email'=>$email,':password'=>password_hash($password,PASSWORD_DEFAULT),
            ':login_time'=>date('Y-m-d H:i:s'),':create_date'=>date('Y-m-d H:i:s'));
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

            header("Location:profileEdit.php");
        
    }catch(Exception $e){
        error_log("DBに接続できませんでした".$e->getMessage());
        $err_msg['common']=MSG07;
    }
}
        }

}


?>

<!DOCTYPE html>
<html lang='ja'>
<?php
$siteTitle='ユーザー登録';
require('head.php');
?>

<body>

    <?php
require('header.php');
?>

    <main>
        <div class="main-wrapper">


            <div class="form-wrapper">
                <h1 class="title">ユーザー登録</h1>
                <form action="" method="post">
                <p class="err_msg"><?php err_msg('common');?></p>
                    <p class="form-title">メールアドレス</p>
                    <input type="text" name="email" value="<?php if(!empty($_POST))keep_type('email'); ?>" class="<?php addErrClass('email');?>">
                    <p class="err_msg"><?php err_msg('email'); ?></p>
                    <p class="form-title">パスワード<span style="font-size:16px"> *8文字以上</span></p>
                    <input type="password" name="password" value="<?php if(!empty($_POST))keep_type('password'); ?>" class="<?php addErrClass('password');?>">
                    <p class="err_msg"><?php err_msg('password'); ?></p>
                    <p class="form-title">パスワード再入力</p>
                    <input type="password" name="pass_retype" value="<?php if(!empty($_POST))keep_type('pass_retype'); ?>" class="<?php addErrClass('pass_retype');?>">
                    <p class="err_msg"><?php err_msg('pass_retype'); ?></p>
                    <a href="easyLogin.php" class="easy">簡単ログイン</a>
                    <div class="btn-wrapper">
                    <input type="submit" class="btn" value="登録">
                    </div>

                </form>
            </div>
        </div>
    </main>

    <?php
require('footer.php');
?>

</body>

</html>
