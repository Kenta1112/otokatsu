<?php
//=====================================
//関数一覧ファイル
//======================================

//===================================
//ログ
//===================================
//ログの開始とログファイルの指定
ini_set('log_errors','on');
//ログファイルの指定
ini_set('error_log','php.log');


//========================================
//デバッグ
//=========================================

//デバッグの開始
$debug_flg=true;
//デバッグ関数
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ:'.$str);
    }
}

//デバッグログ関数
function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
    debug('SESSION ID:'.session_id());
    debug('SESSION変数の中身'.print_r($_SESSION,true));
    debug('現在時刻のUNIXタイムスタンプ'.time());
    if(!empty($_SESSION['login_limit'])&&!empty($_SESSION['login_date'])){
        debug('ログイン期限日時タイムスタンプ:'.(($_SESSION['login_date']+$_SESSION['login_limit'])));
    }

}



//===========================================================
//セッションの開始
//===========================================================

//セッションの保存ファイルを変更
session_save_path("var/tmp");
//ガーベージコレクションが削除する有効期限を変更
ini_set('session.gc_maxlifetime',60*60*24*30);
//cookieの有効期限を伸ばす
ini_set('session.cookie_lifetime',60*60*24*30);
//セッション開始
session_start();
//セッションIDを都度変更
session_regenerate_id();












//===========================================
//メッセージ一覧
//===========================================
define('MSG01','未入力です');
define('MSG02','Emailの形式が違います');
define('MSG03','最大文字数オーバーです');
define('MSG04','パスワードは８文字以上です');
define('MSG05','再入力が一致しません');
define('MSG06','登録済みのメールアドレスです');
define('MSG07','DBへの接続に失敗しました');
define('MSG08','メールアドレスまたはパスワードが違います');
define('MSG09','半角数字で入力してください');
define('MSG10','正しい年齢を入力してください');
define('MSG11','プロフィールの登録に失敗しました');
define('MSG12','元のパスワードと一致しません');
define('MSG13','半角英数字で入力してください');
define('MSG14','画像のアップロードに失敗しました');
define('MSG15','画像のアップロードしてください');
define('MSG16','選択してください');








//=====================================================
//サニタイズ
//=====================================================
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}

//====================================================
//データベース接続
//===================================================
function dbConnect(){
    $dsn='mysql:dbname=otokatsu;host=localhost;charset=utf8';
    $user='root';
    $password='root';
    $options=array(
        
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        
  );

    $dbh=new PDO($dsn,$user,$password,$options);
    
    return $dbh;
}

//SQL実行
function queryPost($dbh,$sql,$data){
    //Prepareメソッドでクエリ文作成
    $stmt=$dbh->prepare($sql);
    //SQL実行
    $stmt->execute($data);
    
    return $stmt;
}


//データベースユーザー情報取得
function getDBUserdata($u_id){
    
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQL文作成
        $sql='SELECT * FROM users WHERE id=:u_id';
        //プレースホルダー
        $data=array(':u_id'=>$u_id);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);

        if($stmt){
            debug('クエリ成功');
        }else{
            debug('クエリ失敗');
        }

    }catch(Exception $e){
        debug('DB接続失敗'.$e->getMessage());
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);

}

//アーティストのデータを取得
function getDBArtistData($artist_id){
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQL文作成
        $sql='SELECT * FROM artist WHERE id=:artist_id';
        //プレースホルダー
        $data=array(':artist_id'=>$artist_id);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);
        if($stmt){
            debug('クエリ成功');
            $result=$stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }else{
            debug('クエリ失敗');
            return false;
        }
    }catch(Exception $e){
        debug('DB接続エラー'.$e->getMessage());

    }
}


//一覧表示のためのアーティストデータを取得
function getDBArtistdataAll($currentPageMin,$listSpan,$genre,$sort){
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQL文作成
        $sql='SELECT * FROM artist';

        //ジャンル指定
        if(!empty($genre)) {
            $sql.=' WHERE category_id= '.$genre;
            }
    
            //ソート指定
            if(!empty($sort)){
                switch($sort){
    
                    case 1:
                    $sql.=' ORDER by create_date DESC ';
                    break;
    
                    case 2:
                    $sql.=' ORDER by create_date ASC ';
                    break;
                }
            }
            
        //プレースホルダー
        $data=array();
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);
        //総レコード数
        $result['total_recode']=$stmt->rowCount();
        $result['total_page']=ceil($result['total_recode']/$listSpan);


        //ページ分のアーティスト情報の取得
        $sql .= ' LIMIT ' .$listSpan .' OFFSET '.$currentPageMin;

        
        debug(print_r($sql,true));
        
        $data=array();

        $stmt=queryPost($dbh,$sql,$data);

        $result['artistInfomation']=$stmt->fetchAll();

        if($stmt){
            debug('クエリ成功'.print_r($result,true));
            return $result;
        }else{
            debug('クエリ失敗');
            return false;
        }
    }catch(Exception $e){
        debug('DB接続エラー'.$e->getMessage());
    }
}

//詳細画面のためのアーティストデータの取得
function getArtistDetail($a_id){
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQL文作成
        $sql='SELECT a.id,a.name,a.pic,a.category_id,a.profile,a.resist_userid,c.name as category,b.id as board FROM artist as a LEFT JOIN category as c ON a.category_id=c.id LEFT JOIN board as b ON a.id=b.artist_id WHERE a.id=:a_id AND a.delete_flg=0 AND c.delete_flg=0';
        //プレースホルダー
        $data=array(':a_id'=>$a_id);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);
        //SQL実行結果格納
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt){
            debug('クエリ成功');
            debug(''.print_r($result,true));
            return $result;
            
        }else{
            debug('クエリ失敗');
            return false;
        }
    }catch(Exception $e){
        debug('DB接続エラー'.$e->getMessage());
    }
    
}


//カテゴリーのデータを取得
function getCategoryData(){
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQL文作成
        $sql='SELECT * FROM category';
        //プレースホルダー
        $data=array();
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);
        if($stmt){
            $result=$stmt->fetchALL();
            debug('クエリ成功');
            return $result;
        }else{
            debug('クエリ失敗');
            return false;
        }
    }catch(Exception $e){
        debug('DB接続エラー'.$e->getMessage());
    }
}


//フォーム入力保持（プロフィール編集）
function showDBdata($key){
    global $getDBUserdata;
    global $err_msg;

    //フォームに情報があるとき
    if(!empty($getDBUserdata[$key])){
        debug('ユーザー情報あり');
        
      //フォームのエラーがある場合
      if(!empty($err_msg[$key])){
          debug('フォームにエラーあり');
          
        //POSTにデータがある場合
      
        if(isset($_POST[$key])){//金額や郵便番号などのフォームで数字や数値の0が入っている場合もあるので、issetを使うこ
            debug('フォームにエラーあり'.print_r($err_msg[$key],true));
            debug('post送信ありなのでそのままPOST送信を返す'.print_r($_POST[$key],true));
            
          echo sanitize($_POST[$key]);
        }else{
          //ない場合（フォームにエラーがある＝POSTされてるハズなので、まずありえないが）はDBの情報を表示
            
          echo sanitize($getDBUserdata[$key]);
        }
      }else{
        //POSTにデータがあり、DBの情報と違う場合（このフォームも変更していてエラーはないが、他のフォームでひっかかっている状態）
          debug('POSTにデータがあり、DBの情報と違う場合（このフォームも変更していてエラーはないが、他のフォームでひっかかっている状態）');
        if(isset($_POST[$key]) && $_POST[$key] !== $getDBUserdata[$key]){
            debug('入力されたやつをそのまま返す'.print_r($_POST[$key],true));
          echo sanitize($_POST[$key]);
        }else{//そもそも変更していない
            debug('変更なしなのでDBのデータ'.print_r($getDBUserdata[$key],true));
          echo sanitize($getDBUserdata[$key]);
        }
      }
    }else{
        
      if(isset($_POST[$key])){
          debug('元々DBに情報ないのでそのまま'.print_r($_POST[$key],true));
        echo sanitize($_POST[$key]);
      }
    }
  }


  //画像アップロード
  function uploadImg($files,$key){
      debug('画像アップロード処理開始');
      debug('$_FILESの中身'.print_r($files,true));

      //画像アップロード時のバリデーション
      //エラーメッセージチェック
      if(isset($files["error"]) && is_int($files["error"])){
      //例外処理
      try{

            switch($files["error"]){

                case UPLOAD_ERR_OK://エラーなし
                break;

                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException1('ファイルがありません');

                case UPLOAD_ERR_INI_SIZE:
                    throw new RuntimeException('INIサイズの大きさを超えています');

                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('HTMLファイルで指定したサイズを超えています');

                default:
                throw new RuntimeException('その他のエラーが発生しました');

            }
        
        //MIMEタイプチェック
        $type=@exif_imagetype($files['tmp_name']);
        if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
            throw new RuntimeException('画像形式が未対応です');
        }

        //新規パス生成(かぶらないようにハッシュ化)
        $path='uploads/'.sha1_file($files['tmp_name']).image_type_to_extension($type);
        //ファイルを保存する（移動）
        move_uploaded_file($files['tmp_name'],$path);
        //ファイルを保存する(権限の変更)
        chmod($path,0644);

        
        debug('アップロードファイルのパス'.print_r($path,true));

        return $path;

        
      }catch(RuntimeException $e){
          debug('エラーメッセージ'.$e->getMessage());
            global $err_msg;
            $err_msg[$key]=MSG14;

        }catch(RuntimeException1 $e){
            debug('エラーメッセージ'.$e->getMessage());
              global $err_msg;
              $err_msg[$key]=MSG15;
          }
        }
  }


  //コメントの取得
  function getComment($a_id){
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQL作成
        $sql='SELECT u.username,u.pic,m.send_date as send_date, m.message as message, m.user_id as user_id, m.id as message_id FROM users as u LEFT JOIN message as m ON u.id = m.user_id WHERE m.artist_id=:a_id ORDER BY m.send_date DESC';
        //プレースホルダー
        $data=array(':a_id'=>$a_id);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);
        //SQL実行結果格納
        $result=$stmt->fetchALL();

        if($stmt){
            debug('クエリ成功');
            debug(''.print_r($result,true));
            return $result;
            
        }else{
            debug('クエリ失敗');
            return false;
        }
     }catch(Exception $e){
        debug('DB接続エラー'.$e->getMessage());
    }

    }



    
    //お気に入りクラス追加
function favorite_artist($a_id,$u_id){
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQl文作成
        $sql='SELECT * FROM favorite_artist WHERE artist_id=:a_id AND user_id=:u_id';
        //プレースホルダー
        $data=array(':a_id'=>$a_id,':u_id'=>$u_id);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);

        $result=$stmt->rowCount();
        debug('resultの中身'.print_r($result,true));
        return $result;

    }catch(Excepion $e){
        debug('DB接続エラー'.$e->getMessage());

    }
}

//お気に入り情報取得
function getDBFavoriteArtistData($u_id){
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQl文作成
        $sql='SELECT f.artist_id, f.user_id, a.name as name, a.pic as pic FROM favorite_artist as f LEFT JOIN artist as a ON f.artist_id=a.id WHERE f.user_id=:u_id';
        //プレースホルダー
        $data=array(':u_id'=>$u_id);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);

        $result=$stmt->fetchAll();
        debug('resultの中身'.print_r($result,true));
        return $result;

    }catch(Excepion $e){
        debug('DB接続エラー'.$e->getMessage());

    }
}

//登録したアーティスト情報取得
function getDBResistArtist($u_id){
    //例外処理
    try{
        //DB接続
        $dbh=dbConnect();
        //SQl文作成
        $sql='SELECT id,name, pic FROM artist WHERE resist_userid=:u_id';
        //プレースホルダー
        $data=array(':u_id'=>$u_id);
        //SQL実行
        $stmt=queryPost($dbh,$sql,$data);

        $result=$stmt->fetchAll();
        debug('resultの中身'.print_r($result,true));
        return $result;

    }catch(Excepion $e){
        debug('DB接続エラー'.$e->getMessage());

    }
}









//=======================================================
//バリデーションチェック関数
//=======================================================

//未入力チェック
function validRequired($str,$key){
    global $err_msg;
    if(empty($str)){
        $err_msg[$key]=MSG01;
    }
}

//最大文字数チェック
function validMaxLen($str,$key,$max=255){
    global $err_msg;
    if(mb_strlen($str)>$max){
            $err_msg[$key]=MSG03;
        }
}

//Email形式チェック
function validEmail($str,$key){
    global $err_msg;

    // if(!filter_var($str,FILTER_VALIDATE_EMAIL)){
       
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
        $err_msg[$key]=MSG02;
        debug('emailの形式が違います');
        
    }
}



//半角文字チェック
function validHalf($str,$key){
    global $err_msg;
   if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
       $err_msg[$key]=MSG09;
   }
}


//パスワード最小文字数チェック
function validPasswordMin($str,$key,$min=8){
    global $err_msg;
    if(mb_strlen($str)<$min){
            $err_msg['key']=MSG04;
        }
}

//パスワード一致チェック
function validPassword($password,$pass_retype,$key){
    global $err_msg;
    if($password !== $pass_retype){
            $err_msg[$key]=MSG05;
    }
}

//Email重複チェック
function validEmailDup($email,$key){
    global $err_msg;

        try{
            //DB接続
            $dbh=dbConnect();
            //SQL作成
            $sql='SELECT count(*) FROM users WHERE email=:email AND delete_flg=0';
            //プレースホルダーに挿入
            $data=array(':email'=>$email);
            //SQL実行
            $stmt=queryPost($dbh,$sql,$data);
            
            $result=$stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!empty(array_shift($result))){
                $err_msg[$key]=MSG06;
            }
        }catch(Exception $e){
            error_log("DB接続エラー".$e->getMessage());
            $err_msg['common']=MSG07;
        }

}

//半角数字チェック
function validHalfNum($str,$key){
    global $err_msg;
   if(!preg_match("/^[0-9]+$/", $str)){
       $err_msg[$key]=MSG09;
   }
}


//最大年齢チェック
function validMaxAge($str,$key,$max=105){
    global $err_msg;
    if($str>$max){
        $err_msg[$key]=MSG10;
    }
}

//セレクトボックスチェック
function validSelectBox($str,$key){
    global $err_msg;
    if($str===0){
        $err_msg[$key]=MSG16;
    }
}







//========================================================
//エラーメッセージ出力関数関係
//=========================================================

//エラーメッセージ出力
function err_msg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        echo $err_msg[$key];
    }
}

//エラー時入力保持
function keep_type($key){
    global $err_msg;
    
    if(empty($err_msg[$key])){
        echo $_POST[$key];
    }
}

//エラー時クラス追加
function addErrClass($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        echo "err";
    }
}


//==============================================
//その他
//=============================================

//画像表示
function showImg($path){
    if(!empty($path)){
        echo $path;
    }else{
        echo "pic\sample-img.png";
    }
}

//検索結果に関する関数
function searchResult(){
    if($dbArtistDataAll['total_recode']<$listSpan){

    }
}




