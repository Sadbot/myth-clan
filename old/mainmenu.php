<?php
	//Выводим основное меню пользователя
	echo"<nav id = 'menu'>";
	if (!empty ($_SESSION['id'])) 
		echo "			
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='index.php'>
						<span class = 'menuEntry'>Главная</span></a></div>
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='video.php'>
						<span class = 'menuEntry'>Видео</span></a></div>
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='all_soob.php'>
						<span class = 'menuEntry'>Сообщества</span></a></div>
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='page.php?id=$_SESSION[id]'>
						<span class = 'menuEntry'>Профиль</span></a></div>
		";
	else
		echo "		
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='index.php'>
						<span class = 'menuEntry'>Главная</span></a></div>
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='soob.php'>
						<span class = 'menuEntry'>Сообщества</span></a></div>";
		
	echo"
			</nav>";
	
	echo"
		<hr>";