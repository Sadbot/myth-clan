<?php
    session_start();
	require_once 'bd.php';
	$title="Отправка пароля";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';
			 
		require_once("../admin/mailconfig.php");
		register_shutdown_function('shutdown');

/*		 if    (isset($_POST['login'])) { $login = $_POST['login']; if ($login == '') { unset($login);}    } //заносим введенный пользователем логин в переменную $login, если он пустой,    то уничтожаем переменную
 if    (isset($_POST['email'])) { $email = $_POST['email']; if ($email == '') {    unset($email);} } //заносим введенный пользователем e-mail, если он    пустой, то уничтожаем переменную*/
					 
 if    (isset($login) and isset($email)) {//если существуют необходимые переменные  
                     
                     $result2    = mysql_query("SELECT id FROM users WHERE login='$login' AND    email='$email' AND activation='1'",$db);//такой ли у пользователя е-мейл 
                     $myrow2    = mysql_fetch_array($result2);
                     if    (empty($myrow2['id']) or $myrow2['id']=='') {
                              //если активированного пользователя с таким логином и е-mail    адресом нет
                              exit ("Пользователя с    таким e-mail адресом не обнаружено ни в одной базе ЦРУ :) <a    href='index.php'>Главная страница</a>");
                              }
                     //если пользователь с таким логином и е-мейлом найден,    то необходимо сгенерировать для него случайный пароль, обновить его в базе и    отправить на е-мейл
                     $datenow = date('YmdHis');//извлекаем    дату 
                     $new_password = md5($datenow);// шифруем    дату
                     $new_password = substr($new_password,    2, 6); //извлекаем из шифра 6 символов начиная    со второго. Это и будет наш случайный пароль. Далее запишем его в базу,    зашифровав точно так же, как и обычно.                 
            

                     //формируем сообщение

 			include_once "/lib/phpmailer/class.phpmailer.php";
			include_once "/admin/mailconfig.php";
	
		    $mail=new \PHPMailer();
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
			
			$message = "Здравствуйте,    ".$login."! Мы сгененриоровали для Вас пароль, теперь Вы сможете    войти на сайт http://".$_SERVER['SERVER_NAME']."/mythclan, используя его. После входа желательно его сменить.\n    Пароль:".$new_password;//текст сообщения
			
			$mail->Body=$message; 
			$mail->AddAddress($email);
				
				if(!$mail->Send())
					exit ('Не могу отослать письмо!');   
					
				
				$mail->ClearAddresses();
				$mail->ClearAttachments();	
				
			$new_password_sh =    strrev(md5($new_password))."b3p6f";//зашифровали 
            mysql_query("UPDATE users SET    password='$new_password_sh' WHERE login='$login'",$db) or  exit(mysql_error());// обновили в базе 
					
				echo "На Ваш e-mail отправлено письмо с паролем. Вы    будете перемещены через 5 сек. Если не хотите ждать, то <a href='index.php'>нажмите сюда.</a>";//перенаправляем    пользователя
 		}
			
       
	   
	   echo'	
		</section>
     </section>';
	require 'html_foot.php';