<?php
	session_start();
	require_once 'bd.php'; 
	
	$title="Добавление нового пользователя";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';

 //print $_GET['$text'];
 if (empty($myrow))
	    {
			//Проверяем,    зарегистрирован ли вошедший
	        echo("Вход на эту    страницу разрешен только зарегистрированным пользователям!"); 
		}
			
		else 
		{
 

 if (isset($_POST['submit'])){ 
	$login=	htmlspecialchars(stripslashes(trim($_POST['login'])));
	$p=		htmlspecialchars(stripslashes(trim($_POST['pass'])));
	$ids=	htmlspecialchars(stripslashes($_POST['id']));
	$ava=	htmlspecialchars(stripslashes($_POST['avatarka']));
	$ymail=	htmlspecialchars(stripslashes($_POST['email']));
	
    include_once "/lib/phpmailer/class.phpmailer.php";
	include_once "/admin/mailconfig.php";
	
    $mail=new \PHPMailer();
//	$mail->CharSet = "ansii";
	$mail->CharSet = 'UTF-8';
    $mail->Mailer="smtp";
    $mail->Host="smtp.mail.ru";
    $mail->Port=587;
    $mail->SMTPAuth=true;
    $mail->Username=$mail_username;
    $mail->Password=$mail_pass;
    $mail->From=$mail_email;
	$mail->FromName = 'Администрация сайта Mythclan';
	$mail->Subject ="Регистрация на сайте Mythclan";
	$mail->AddAttachment($ava);

		$resulted = mysql_query("SELECT id FROM users WHERE login='$login'",$db);
		$myrows = mysql_fetch_array($resulted);
		if (!empty($myrows['id'])) {
			echo ("Данный пользователь уже был добавлен.");
		}
		else{
			$res=mysql_query("INSERT INTO `users` (login,password,avatar,email,date) VALUES('$login','$p','$ava','$ymail',CURDATE());");
			if ($res=='TRUE')
			{
				echo "Новый пользователь ".$login." был добавлен в сообщество <br>";
				$result3    = mysql_query ("SELECT id FROM users WHERE login='$login'",$db);//извлекаем    идентификатор пользователя. Благодаря ему у нас и будет уникальный код    активации, ведь двух одинаковых идентификаторов быть не может.
				$myrow3    = mysql_fetch_array($result3);
				$activation    = md5($myrow3['id']).md5($login);//код активации аккаунта. Зашифруем    через функцию md5 идентификатор и логин. Такое сочетание пользователь вряд ли    сможет подобрать вручную через адресную строку.
				$subject    = "Подтверждение регистрации";//тема сообщения
				
				
				
				$message    = "Здравствуйте! Спасибо за регистрацию на сайте http://".$_SERVER['HTTP_HOST']."/mythclan\nВаш логин:    ".$login."\n
				Перейдите    по ссылке, чтобы активировать ваш    аккаунт:\nhttp://".$_SERVER['HTTP_HOST']."/mythclan/activation.php?login=".$login."&code=".$activation."\nС    уважением,\n
				Администрация";//содержание сообщение
				//mail($ymail,    $subject, $message, "Content-type:text/plane;    Charset=windows-1251\r\n");//отправляем сообщение
				
				
				
				$mail->Body=$message; 
				$mail->AddAddress($ymail);
				
				if(!$mail->Send())
				{
					echo 'Не могу отослать письмо!';
				}
				else
				{
					//var_dump($mail,$mail->ErrorInfo);
					echo    "На E-mail пользователя ".$login." выслано пиcьмо с приглашением."; //говорим об    отправленном письме пользователю
				}
				$mail->ClearAddresses();
				$mail->ClearAttachments();				
            
				$res=mysql_query("DELETE FROM `user` WHERE `id_u`='$ids';");
      
	 
			}
	
		//else
		//{print "Запрс не выполнен";}
		}
		mysql_close($db);
	}
		}//provarka vhoda user

echo'	
		</section>
     </section>';
	require 'html_foot.php';