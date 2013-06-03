<?php
	session_start();
	require_once 'bd.php';// файл bd.php должен быть в той же папке, что и все остальные, если это не так, то просто измените путь 
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
	        echo("Вход на эту    страницу разрешен только зарегистрированным пользователям!"); 
		}
			
		else 
		{
				
				    echo'<h2>Личные сообщения:</h2>';
					$tmp = mysql_query("SELECT * FROM    messages WHERE poluchatel='$login' ORDER BY id DESC",$db); 
				    $messages =    mysql_fetch_array($tmp);//извлекаем сообщения    пользователя, сортируем по идентификатору в обратном порядке, т.е. самые    новые сообщения будут вверху		
		
		
		if (empty($messages['id'])) 
		{
			//если сообщений не найдено
			echo    "Сообщений нет";
		}
		else    
		{
            echo"
			<form action = 'drop_post.php' method='POST'>
			<table>
                 <tr>
			<td><input type='checkbox' class='checkall'></td>	<td>Выделить всё</td>
				<td><input type='submit' value='Удалить'/></td>";
			do //выводим    все сообщения в цикле
            {
            	$author = $messages['author'];
	            $result4 = mysql_query("SELECT avatar,id FROM users WHERE login='$author'",$db); //извлекаем аватар автора
    	        $myrow4 = mysql_fetch_array($result4);
				if (!empty($myrow4['avatar']))    //если такового нет, то выводим стандартный (может    этого пользователя уже давно удалили)
		        	    $avatar = $myrow4['avatar'];
            	else 
					$avatar =    "avatars/net-avatara.jpg";
					
     			printf("
				<tr>
                 
				 <td>
				 	<input type='checkbox' class='checked' name='message_checked[]' value = '%s' />
				 </td>

                 <td><a href='page.php?id=%s'><img alt='аватар'    src='%s'></a></td>
              
                 <td>Автор:    <a href='page.php?id=%s'>%s</a><br>
                  Дата:    %s<br>
                                 Сообщение:<br>

                             %s<br>

              
                 </td>
                 </tr>
		 
                 ",$messages['id'],$myrow4['id'],$myrow['avatar'],$myrow4['id'],$author,$messages['date'],$messages['text'],$messages['id']);
            	  //выводим само сообщение 
			}
			while($messages = mysql_fetch_array($tmp));
			echo"
			                 </table>	
			</form>
				 <br>";
		}
		
			
}
            
       echo'
	     </section>
    </section>';
	require 'html_foot.php';