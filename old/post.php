<?php
	session_start();
    require 'bd.php'; // соединяемся с базой, укажите свой путь, если у вас    уже есть соединение
	
	
	$title="Отправление сообщения";

	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';	

if (empty($myrow))

               {
               //если логин    или пароль не действителен
                echo("Вход на эту страницу разрешен    только зарегистрированным пользователям!");
               }
else {
           

			if (isset($_POST['id'])) { $id    = stripslashes(htmlspecialchars($_POST['id']));}//получаем идентификатор страницы    получателя
            if (isset($_POST['text'])) { $text =   stripslashes(htmlspecialchars($_POST['text']));}//получаем текст сообщения 
            if (isset($_POST['poluchatel'])) {    $poluchatel = stripslashes(htmlspecialchars($_POST['poluchatel']));}//логин получателя 
            $author = $myrow['login'];//логин автора 
            $date = date("Y-m-d");//дата добавления 
	if (empty($author) or empty($text) or    empty($poluchatel) or empty($date)) 
	{//есть ли все необходимые    данные? Если нет, то останавливаем
            echo ("Вы ввели не всю    информацию, вернитесь назад и заполните все поля");
	}
	else
	{
			$text = stripslashes($text);//удаляем обратные слеши
            $text = htmlspecialchars($text);//преобразование    спецсимволов в их HTML эквиваленты

            $result2 = mysql_query("INSERT INTO    messages (author, poluchatel, date, text) VALUES    ('$author','$poluchatel','$date','$text')",$db);//заносим в базу сообщение 


		if($result2)   	
			echo "Ваше сообщение передано! Вы    будете перемещены через 5 сек. Если не хотите ждать, то <a    href='page.php?id=".$id."'>нажмите    сюда.</a>";//перенаправляем    пользователя

		else 
			echo "Ошибка отправления сообщения! Вы    будете перемещены через 5 сек. Если не хотите ждать, то <a    href='page.php?id=".$id."'>нажмите    сюда.</a>";
	}
}
	
	echo"       
		</section>
     </section>";

	require 'html_foot.php'; 
