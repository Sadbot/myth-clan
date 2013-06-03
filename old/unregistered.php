<?php

	session_start();
	require_once('bd.php');
	
	$title="Сообщество";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';	

		$res = mysql_query('SELECT * FROM `user`');
		if (!$res)
			echo 'Нет пользователей, которые зарегистрировались на сайте';
		else
		while ($row=mysql_fetch_array($res))
		{
			$id = 		$row['id_u'];
			$login=		$row['login'];
			$password=	$row['password'];
			$text= 		$row['text'];
			$ava= 		$row['avatar'];
			$mail= 		$row['email'];
	
	
		echo "
		<div class='messages'>
		<form  action=\"isert_user.php\" method=\"POST\">
			Ник: $login <br>
			О себе: $text <br>
			<input type=\"submit\"  name=\"submit\"  value=\"Принять\" />
			<input name=\"login\" type=\"hidden\" value=\"$login\" />
			<input name=\"pass\" type=\"hidden\" value=\"$password\" />
			<input name=\"id\" type=\"hidden\" value=\"$id\" />
			<input name=\"avatarka\" type=\"hidden\" value=\"$ava\" />
			<input name=\"email\" type=\"hidden\" value=\"$mail\" />
		</form>
		</div>";
		}
	  
     //http://ruseller.com/lessons.php?rub=37&id=369
		
		
			
		echo'
		</section>
     </section>';

	require 'html_foot.php';