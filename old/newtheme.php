<?php
	session_start();
	require_once 'bd.php';// файл bd.php должен быть в той же папке, что и все остальные, если это не так, то просто измените путь 


	$title="Сайт клана Миф";
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

	if (empty($_GET['id']) && !is_numeric($_GET['id']))

		echo("Неправильный параметр входа на страницу!");
	else
	{			 
		$id_soob = $_GET['id'];
		echo "<a href='soob.php?id=$id_soob'>Вернуться к темам сообщества</a>";
			 
		$error="";
		
		if(isset($_POST['create']))
		{	
			if(empty($_POST['name']))
			{
				$error.='<li>Не ввели название темы</li>';
				unset($_POST['create']);
			}						
			if (!empty($error))
			{
				echo "<p>Возникли следующие ошибки:</p>";
				echo '<ul>';
				echo $error;
				echo '</ul>';
			}//if empty error
			else
			{			
				$name_theme = htmlspecialchars($_POST['name']);
				$name_theme = stripslashes($_POST['name']);
				if ($_POST['hide'] == '1')
					$hide_theme = 1;
				else
					$hide_theme = 0;
					
				
				if (mysql_query("INSERT INTO themes (name, id_author, hide,time, id_soob) VALUES ('$name_theme', $_SESSION[id], $hide_theme, NOW(), $id_soob);"))
					echo "Тема создана. Вернуться к <a href='soob.php?id=$id_soob'>темам</a>!";
				else 
					echo'Ошибка mysql запроса!'.mysql_error();
			
			}//else no error
		}//if isset post

			 

    echo"<form action='#' method='post'>
    	<label for='name'>Название темы:</label>
        <input type='text' name='name' /><br />
                
        <label for='hide' >Закрытая тема (только для зарегестрированных)</label>
        <input type='checkbox' name='hide' value='1' /><br />
        
        <input type='submit' name='create' value='Создать' />
    </form>";
    
            
	}
		}
	echo'
		</section>
     </section>';
	require 'html_foot.php'; 
?>