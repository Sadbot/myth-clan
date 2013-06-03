<?php

          //    вся процедура работает на сессиях. Именно в ней хранятся данные пользователя,    пока он находится на сайте. Очень важно запустить их в самом начале    странички!!!
    session_start();
	require_once 'bd.php';// файл bd.php должен быть в той же папке, что и все    остальные, если это не так, то просто измените путь 
	
	$title="Все пользователи";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';		
	
	if (empty($myrow))
            {

               //если данные    пользователя не верны
                echo("Вход на эту страницу разрешен    только зарегистрированным пользователям!");
            }
            else {      
			
	$order_by="`login`";
	$order_direction="ASC";
	
	$direction = array('DESC', 'DESC');
	$sort_field = array('','');
	if (isset($_GET['sort_field']) && $_GET['sort_field'] == 'login')
	{
		$direction[0] = 'DESC';
		$sort_field[0] = '<img src="img/ASC.png">';
		$order_by = '`login`';
		if ($_GET['direction'] == 'DESC')
		{
			$sort_field[0] = '<img src="img/DESC.png">';
			$direction[0] = 'ASC';
		}
	}
	else if (isset($_GET['sort_field']) && $_GET['sort_field'] == 'date')
	{
		$direction[1] = 'DESC';
		$sort_field[1] = '<img src="img/ASC.png">';
		$order_by = '`date`';
		if ($_GET['direction'] == 'DESC')
		{
			$sort_field[1] = '<img src="img/DESC.png">';
			$direction[1] = 'ASC';
		}
	}
	else
	{
		$order_by = '`login`';
		$sort_field[0] = '<img src="img/ASC.png">';
	}
	
	if (isset($_GET['direction']) && $_GET['direction'] == 'DESC')
		$order_direction = 'ASC';
	else 
		$order_direction = 'DESC';
		
	
				
      //выводим    меню            
	$result2 = mysql_query("SELECT id, login, avatar, email, date, statususer, last_time FROM users ORDER BY {$order_by} {$order_direction} ",$db); //извлекаем логин и идентификатор пользователей
    $myrow2 = mysql_fetch_array($result);
	echo"<table>
	<tr>
		<td>Аватар:</td>
		<td><a href='?sort_field=login&direction={$direction[0]}'>Имя:</a>{$sort_field[0]}</td>
		<td><a href='?sort_field=date&direction={$direction[1]}'>Дата регистрации:</a>{$sort_field[1]}</td>
		<td>email:</td>
		<td>Статус пользователя:</td>
		<td>Последнее посещение:</td>
	</tr>";
    do
    {	
		if ($myrow2['statususer'] == $USER_STATUS_ADMIN)
			$status = 'Администратор';
		else if ($myrow2['statususer'] == $USER_STATUS_USER)
			$status = 'Пользователь';
			
		echo "<tr>";
		//выводим их в цикле 
        printf("<td><img width='50px' src='%s'/></td><td><a  href='page.php?id=%s'>%s</a><br></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>",$myrow2['avatar'],$myrow2['id'],$myrow2['login'],$myrow2['date'],$myrow2['email'], $status,$myrow2['last_time']);
		echo "</tr>";
    }
    while($myrow2 = mysql_fetch_array($result2));
	echo"</table>";
}

	echo'	
		</section>
     </section>';
	require 'html_foot.php';