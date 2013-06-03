<?php
          session_start();//запускаем сессии
          require_once 'bd.php';//подключаемся к базе
		  

		$URL=$_SERVER['HTTP_REFERER'];
		
		
			$title="Сообщения пользователя";
		require 'html_head.php';	
		echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';

		if (empty($myrow))
	    {
			//Проверяем,    зарегистрирован ли вошедший
	        echo("Вход на эту страницу разрешен только зарегистрированным пользователям!"); 
		}
			
		else 
		{	
			$id_message= $_POST['message_checked'];
			$in = implode(",", $id_message); 
			if(mysql_query("DELETE FROM `messages` WHERE `id` IN ($in) AND poluchatel='$login'"))
			{
				if(count($_POST['message_checked'])>1)
					echo"Ваши сообщения удалены! Вы будете перемещены через 5 сек. Если не хотите ждать, то <a    href='$URL'>нажмите    сюда.</a>";
				else
					echo"Ваше сообщение удалено! Вы будете перемещены через 5 сек. Если не хотите ждать, то <a    href='$URL'>нажмите    сюда.</a>";
			}
			else 
			{
				if(count($_POST['message_checked'])>1)
					echo"Ошибка! Ваши сообщения не удалены. Вы будете перемещены через 5 сек. Если не хотите ждать, то <a    href='$URL'>нажмите    сюда.</a>";
				else
					echo"Ошибка! Ваше сообщение не удалено. Вы будете перемещены через 5 сек. Если не хотите ждать, то <a    href='$URL'>нажмите    сюда.</a>";
			}
		}
		
       echo'
	     </section>
    </section>';

	require 'html_foot.php';