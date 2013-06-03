<?php
	session_start();//    вся процедура работает на сессиях. Именно в ней хранятся данные пользователя,    пока он находится на сайте. Очень важно запустить их в самом начале    странички!!!
	require_once 'bd.php';// файл bd.php должен быть в той же папке, что и все    остальные, если это не так, то просто измените путь 
	
	$title="Профиль пользователя";
		require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

	echo'
    	<section id="centerunit">';
		require 'mainmenu.php';
	
    if (!isset($_GET['id'])) 
    { 	
		echo("Вы зашли на страницу без параметра!");//если не    указали id, то выдаем ошибку
	} 
        
	else	
	{	
		$id = mysql_real_escape_string($_GET['id']);  //id "хозяина" странички 
		if (!preg_match("|^[\d]+$|", $id))
		{
			echo("<p>Неверный формат запроса! Проверьте URL</p>");//если id не число, то выдаем    ошибку
		}
		else
		{		
   
		if (empty($myrow))
	    {			
				//Если не действительны (может мы удалили    этого пользователя из базы за плохое поведение)
				echo("Вход на эту страницу разрешен только зарегистрированным пользователям!");
		}
		else 
		{				
			$result3 = mysql_query("SELECT * FROM users WHERE id='$id'",$db);
	        $myrow3 = mysql_fetch_array($result3);
			if ($myrow3['login'] == $myrow['login'])
			{
				echo "<h2>Изменение профиля пользователя $myrow3[login]</h2>";
		    	//Если    страничка принадлежит вошедшему, то предлагаем изменить данные и выводим    личные сообщения
				print <<<HERE
	
					<a href='exit.php'>Выход</a><br><br>
					<form action='update_user.php'    method='post'>
						Изменить логин:<br>
						<label for="old_login">Старый логин:</label>		<input name='old_login' type='text'><br>
						<label for="new_login1">Новый логин:</label>		<input name='new_login1' type='text'><br>
						<label for="new_login2">Повторите логин:</label>	<input name='new_login2' type='text'><br>
			            <input type='submit' name='submit' class="submitstone" value='изменить'>
					</form>
		            <br>
					<form action='update_user.php'    method='post'>
			            Изменить пароль:<br>
			            <label for="old_password">Старый пароль:</label>	<input name='old_password' type='password'><br>
				        <label for="new_password1">Новый пароль:</label>	<input name='new_password1' type='password'><br>		
	            		<label for="new_password2">Повторите пароль:</label><input name='new_password2' type='password'><br>
			            <input type='submit' name='submit' class="submitstone" value='изменить'>
					</form>
		            <br>
					<form action='update_user.php'    method='post' enctype='multipart/form-data'>
			            Ваш аватар:<br>
			            <img alt='аватар' src='$myrow3[avatar]'><br>
			            Изображение должно быть    формата jpg, gif или png. Изменить аватар:<br>
			            <input type="FILE"    name="fupload">
			            <input type='submit' name='submit' class="submitstone" value='изменить'>
					</form>
HERE;
				}
				else
				{
		          
				    //если    страничка чужая, то выводим только некторые данные и форму для отправки    личных сообщений
					echo"<img alt='аватар' src='$myrow3[avatar]'> $myrow3[login]<br>";
					print <<<HERE
			        <form action='post.php' method='post'>
				        <br>
			            <h2>Отправить пользователю сообщение:</h2>
			            <textarea cols='43' rows='4' name='text'></textarea><br>
			            <input type='hidden' name='poluchatel'    value='$myrow3[login]'>
			            <input type='hidden' name='id'    value='$myrow3[id]'>
			            <input type='submit' name='submit' value='Отправить'>
		            </form>
HERE;
				}
			}
		}
	}
	echo'
		</section>
     </section>';
	 
	require 'html_foot.php';