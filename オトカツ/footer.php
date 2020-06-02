<footer id='footer'>
    Copyright オトカツ. All Rights Reserved.
</footer>



<script src='js\vendor\jquery-3.5.1.min.js'></script>



<script type='text/javascript' src='footerFixed.js'></script>



<script type='text/javascript'>
  $(function(){

    //画面最下部固定
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
      
    }


    //画像ライブプレビュー
    var $dropArea=$('.img-wrapper'),
        $fileInput=$('.input-file');

    $dropArea.on('dragover', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border','3px #ccc dashed');
    });

    $dropArea.on('dragleave', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', 'none');
    });

    $fileInput.on('change', function(e){
      
      $dropArea.css('border', 'none');
      var file = this.files[0],            

          $img = $(this).siblings('.prev-img');
          $img.removeAttr('src');

      var fileReader = new FileReader();   

      
      fileReader.onload = function(event) {
        // 読み込んだデータをimgに設定
        
        $img.attr('src', event.target.result).show();
      };

      // 6. 画像読み込み
      fileReader.readAsDataURL(file);

    });


    //テキストカウント
    var $textarea=$('.profile');
    var $countshow=$('.countshow');

    $textarea.on('keyup',function(e){
      $countshow.html($(this).val().length);
    });


    //お気に入り登録
    var $like=$('.js-like')||null;
    var likeArtistId=$('.js-like').data('artistid')||null;

    if(likeArtistId!==undefined && likeArtistId!==null){
      $like.on('click',function(){
        var $this=$(this);

        $.ajax({
          type:'POST',
          url:'ajaxLike.php',
          data:{artistId:likeArtistId}
        }).done(function(data){
          
          $this.toggleClass('active');

        }).fail(function(msg){
          
        });

      });
    }

    //メニューバー
    var $menu=$('.js-menu');
    var $menu_bar=$('.header-menu-wrapper')
    $menu_bar.hide();

    $menu.on('click',function(){

      $menu.toggleClass('fa-caret-up');
      $menu.toggleClass('fa-caret-down');
      $menu_bar.toggle(2000);


    })


  });


</script>





 