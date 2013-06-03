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
		{?><head>			
	
    
    <a href='create_soob.php' rel='nofollow'>Создать сообщество</a>
    <h1>Список:</h1>
    </head>
    
   	<article>
            
<?php
	$soob_mysql=mysql_query('SELECT id_soob, name, logo,hide, login
							FROM soobs, users
							WHERE login=(SELECT login FROM users WHERE id=id_author);',$db);
	if ($soob_mysql)
	{		
		$soob_row=mysql_fetch_array($soob_mysql);		
		if(empty($soob_row['id_soob']))
			
			echo"Нет сообществ";
		else
		{
		echo '<table>';
		do
		{
				if($soob_row['hide'] == 1)
					$hide = 'закрытое';
				else 
					$hide = 'открытое';
				
				echo'<tr>';
				echo"<td><a href='$soob_row[id_soob]'><img width='100'src='$soob_row[logo]'></a></td>
					<td><ul>
						<li><a href='soob.php?id=$soob_row[id_soob]'>$soob_row[name]</a>
						<li>$soob_row[login]</ul>
					</td><td>$hide</td>";
		}while($soob_row=mysql_fetch_array($soob_mysql));
		
		echo '</table>';
		}
	}//if mysql_query true
	else
		echo 'Ошибка запроса!';
		
		
		echo'</article>';
		
	}//proverka vhoda user
	

	echo'
		</section>
     </section>';
	require 'html_foot.php';