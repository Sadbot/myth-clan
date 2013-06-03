<?php
require_once('bd.php');

if (!empty($myrow))
{	
	//Вывод количества полученных сообщений
	$tmp = mysql_query("SELECT count(poluchatel) as kol FROM    messages WHERE poluchatel='$login'",$db); 
	$messages = mysql_fetch_array($tmp);//извлекаем сообщения    пользователя, сортируем по идентификатору в обратном порядке, т.е. самые    новые сообщения будут вверху
	$li_messages="Сообщения";
	if (!empty($messages['kol']))
		$li_messages.=" [$messages[kol]]";

	//Вывод меню
	echo '
	<nav id="rightunit">';
		
		echo "
			<ul>
				<li><a href='messages.php'>$li_messages</a></li>
				<li><a href='all_users.php'>Список пользователей</a></li>
				<li><a href='unregistered.php'>Пользователи</a></li>
			</ul>";
	echo "		
	</nav>";
}