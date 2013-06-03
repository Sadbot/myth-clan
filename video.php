<?php
require_once("common_funcs.php");
require_once("login_funcs.php");
require_once("site_code.php");

site_header("Browser-based Youtube Uploader Script");     
        //If the 1st step form has been submited, run the token script.
        if( isset( $_POST['video_title'] ) && isset( $_POST['video_description'] ) ) {
            $video_title = htmlspecialchars (stripslashes( $_POST['video_title'] ));
            $video_description = htmlspecialchars (stripslashes( $_POST['video_description'] ));
            include_once( 'get_youtube_token.php' );
        }
        
        // Specifies the url that youtube will return to. The data it returns are as get variables         
        $nexturl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        // These are the get variables youtube returns once the video has been uploaded.
        $unique_id = @$_GET['id'];
        $status = @$_GET['status'];
        ?>
        
        <!-- Step 1 of the youtube upload process -->
        <?php if( empty( $_POST['video_title'] ) && $unique_id == "" ) : ?>                    
            <form action="" method="post">                
                <p><label for="video_title">Название видео</label>
                <input type="text" name="video_title" /></p>
                <p><label for="video_description">Описание видео</label>
                <textarea id="video-description" name="video_description"></textarea></p>
                <input type="submit" value="Шаг 2" />
            </form> <!-- /form -->
            
        <!-- Final Step -->
        <?php elseif( $unique_id != '' && $status = '200' ) : 
		//$mysql->query("INSERT INTO video VALUES (NULL, $SESSION['user']['id_author'],NOW(),$video_title");?>
        <div id="video-success">        	
            <h4>Видео успешно загружено!</h4>
            <p>Видео обрабатывается от нескольких секунд до нескольких минут. Пожалуйста, зайдите позже на страницу видеозаписей клана, чтобы увидеть своё видео.</p> 
            <p>Видео будет утверждено также на Youtube примерно через 2-3 часа.</p>
            <p>По ссылке:<a href="http://www.youtube.com/watch?v=<?php echo $unique_id; ?>" target="_blank">http://www.youtube.com/watch?v=<?php echo $unique_id; ?></a></p>
        </div> <!-- /div#video-success -->
        
        <!-- Step 2 -->           
        <?php elseif( $response->token != '' ) : ?>
            <h4>Название:</h4>
            <p><?php echo $video_title; ?></p>
            <h4>Описание:</h4>
            <p><?php echo $video_description; ?></p>
            <form action="<?php echo( $response->url ); ?>?nexturl=<?php echo( urlencode( $nexturl ) ); ?>" method="post" enctype="multipart/form-data">
                <p class="block">
                    <label>Загрузите видео</label>
                    <span class="youtube-input">
                        <input id="file" type="file" name="file" />
                    </span>                        
                </p>
                <input type="hidden" name="token" value="<?php echo( $response->token ); ?>"/>
                <input type="submit" value="Загрузить видео" />
                <p class="rule">
                Загружайте HD-видео продолжительностью до 15 мин. в различных форматах.
Вы должны обладать авторскими или всеми необходимыми правами на любое содержание, которое вы загружаете. <a href="http://www.youtube.com/yt/copyright/what-is-copyright.html">Подробнее...</a>
                </p>
            </form> <!-- /form -->
        
        <?php endif; 
        
site_footer();