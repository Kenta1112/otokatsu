    <header>
        <div class="header-wrapper">
        <a href="top.php"><h1 class="header-title">オトカツ</h1></a>

        <?php 
        if(empty($_SESSION['user_id'])){?>
        <a href="signup.php"><p>ユーザー登録</p></a>
        <a href="login.php"><p>ログイン</p></a>
        

        <?php }else{    ?>
        
        <i class="fas fa-caret-down fa-3x js-menu"></i>

        <a href="mypage.php"><p>マイページへ</p></a>
        <a href="logout.php"><p>ログアウト</p></a>
        
        <?php }?>

        </div>
        </header>


        <?php
        if(isset($_SESSION['user_id'])){?>
        <div class="header-menu-wrapper">
        <p><a href="top.php">TOPページ</a></p>
        <p><a href="profileEdit.php">プロフィール編集</a></p>
        <p><a href="resisterArtist.php">アーティストを登録</a></p>
        <p><a href="changePassword.php">パスワード変更</a></p>
        <p><a href="withdrawal.php">退会する</a></p>
        </div>

        <?php
        }
        ?>


        
        
    