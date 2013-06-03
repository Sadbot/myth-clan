<?php
	session_start();
	require_once 'bd.php';// файл bd.php должен быть в той же папке, что и все остальные, если это не так, то просто измените путь 


	$title="Видео клана Миф";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';
		

			
			
			echo'<h1>Мои видео</h1>
				<div class="row">';
					
					require_once '../Zend/Loader.php'; // the Zend dir must be in your include_path
					Zend_Loader::loadClass('Zend_Gdata_YouTube');
					/*$yt = new Zend_Gdata_YouTube();

					$videofeed = $yt->getVideoFeed('http://gdata.youtube.com/feeds/users/semseriou/uploads');
						foreach($videofeed as $v)
						echo $v->getVideoTitle();*/
function getAndPrintVideoFeed($location = Zend_Gdata_YouTube::VIDEO_URI)
{
  $yt = new Zend_Gdata_YouTube();
  // set the version to 2 to receive a version 2 feed of entries
  $yt->setMajorProtocolVersion(2);
  $videoFeed = $yt->getVideoFeed($location);
  printVideoFeed($videoFeed);
}
					
			echo'</div>';

		echo'</section>
     </section>';

	require 'html_foot.php';