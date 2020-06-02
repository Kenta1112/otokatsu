<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログイン画面');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

//ログイン認証

// require('auth.php');

$err_msg=array();

    //POST送信時チェック
    if(!empty($_POST)){
        debug('POST送信あり'.print_r($_POST,true));
    
    
        $email=$_POST['email'];
        $password=$_POST['password'];
        $login_save=(!empty($_POST['login_save']))?true:false;

        //未入力チェック
        validRequired($email,'email');

        validRequired($password,'password');
    
    if(empty($err_msg)){
    
        //Email形式チェック
        validEmail($email,'email');
        
        //最大文字数チェック
        validMaxLen($email,'email');

        validMaxLen($password,'password');
        
   }
    
    //DBチェック
    if(empty($err_msg)){

        try{
        //DB接続
        $dbh=dbConnect();
        //SQL文作成
        $sql='SELECT password,id FROM users WHERE email=:email AND delete_flg=0';
        //プレースホルダーに格納
        $data=array(':email'=>$email);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);
        //結果取得
        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        debug('結果の中身'.print_r($result,true));

        
    if(!empty($result) && password_verify($password,array_shift($result))){
            

            //セッションの処理
            //ログイン日時
            $_SESSION['login_date']=time();
            //デフォルトのログイン有効期限(1 hour)
            $sesLimit=60*60;

            //ログイン保持機能判定
            if($login_save){
                debug('ログイン保持にチェックがあります');
                //ログイン保持を３０日に
                $_SESSION['login_limit']=$sesLimit*24*30;
                debug('ログインリミット'.print_r($_SESSION['login_limit'],true));
            }else{
                debug('ログイン保持チェックなし');
                $_SESSION['login_limit']=$sesLimit;
                debug('ログインリミット'.print_r($_SESSION['login_limit'],true));
            }

            //  ユーザーID格納
            $_SESSION['user_id']=$result['id'];

            debug('ログイン完了');
            header('Location:top.php');
            
        }else{
            debug('ログイン失敗');
            $err_msg['common']=MSG08;
        }

        }catch(Exception $e){
        error_log('DBに接続できません'.$e->getMessage());
        $err_msg['common']=MSG07;
        }
    
    }
 }

 debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');


?>

<!DOCTYPE html>
<html lang="ja">

<?php
    $siteTitle='ログイン';
    require('head.php');
    ?>


<body>
   <?php
    require('header.php');
?>
    
    <main>
        <div class="main-wrapper">
            
            
            <div class="form-wrapper">
               <h1 class="title">ログイン</h1>
                <form action="" method="post">
                <p class="err_msg"><?php err_msg('common');?></p>
                    <p>メールアドレス</>
                    <input type="text" name="email" value="<?php if(!empty($_POST))keep_type('email'); ?>" class="<?php addErrClass('email');?>">
                    <p class="err_msg"><?php err_msg('email'); ?></p>
                    <p>パスワード</p>
                    <input type="password" name="password" value="<?php if(!empty($_POST))keep_type('password');?>" class="<?php addErrClass('email');?>">
                    <p class="err_msg"><?php err_msg('password'); ?></p>
                    <div class="checkbox-wrapper">
                    <input type="checkbox" name="login_save">ログイン状態を維持する</div>
                    <a href="easyLogin.php" class="easy">簡単ログイン</a>
                    <div class="btn-wrapper">
                    <input type="submit" class="btn" value="ログイン">
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