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
	        echo("Вход на эту    страницу разрешен только зарегистрированным пользователям!"); 
		}		
		else 
		{				 
			echo"<a href='all_soob.php'>вернуться к сообществам</a>";
			

			if (isset($_GET['id']) && is_numeric($_GET['id']))
			{
				$id_soob = $_GET['id'];
				$soob_mysql = mysql_query("SELECT name, rule, logo, hide
											from soobs
											WHERE id_soob=$id_soob");
				if (!$soob_mysql)
				{
					echo mysql_error();
				}//true mysql query
				else
				{
				$soob_row = mysql_fetch_array($soob_mysql);
				echo "<head id='head_themes'>
						<h1>$soob_row[name]</h1>
						<img src='$soob_row[logo]' />
						<h4>Правила: $soob_row[rule]</h4>
						</head>";
				echo"<article>
				<h1>темы:</h1>";
				
				$themes_mysql = mysql_query("SELECT id_theme, name, login, hide, themes.time as time
											from themes, users
											WHERE id_soob=$id_soob AND themes.id_author=users.id;");
				if($themes_mysql)
				{
					$themes_row = mysql_fetch_array($themes_mysql);
					if (!empty($themes_row))
					{
						echo"<table>
							<tr>
								<td>Название:</td>
								<td>Автор:</td>
								<td>Время создания:</td>
							</tr>";
						do
						{
							echo"<tr>
								<td><a href='$themes_row[id_theme]'>$themes_row[name]</a></td>
								<td>$themes_row[login]</td>
								<td>$themes_row[time]</td>
							</tr>";
						}while ($themes_row = mysql_fetch_array($themes_mysql));
						echo"</table>";
					}//if !empty row
					else
						echo "Нет тем";
						
						
					echo "<a href='newtheme.php?id=$id_soob' rel='nofollow'> Новая тема</a>";
					
				}//if $themes_mysql
				else 
				{
					echo mysql_error();
				}
				
				echo "</article>";
				
				}//if $soob_mysql true
			
			
			
		}// if is_numeric $id_soob
			
		else
				echo 'Неправильный параметр входа на страницу!';
			
			
	}//proverka vhoda user
	echo'
		</section>
     </section>';
	require 'html_foot.php';
?>