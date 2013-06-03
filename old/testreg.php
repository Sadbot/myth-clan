<?php 
	session_start();
	require_once 'bd.php'; 
	
	
	
	$URL=$_SERVER['HTTP_REFERER'];
	
	
	
	$title="Проверка регистрации";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';	
			 
		echo'<div id="content">';
	
if (isset($_POST['login'])) { $login = $_POST['login']; if ($login == '') { unset($login);} } //заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
    if (isset($_POST['password'])) { $password=$_POST['password']; if ($password =='') { unset($password);} }
    //заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
if (empty($login) or empty($password)) //если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
    {
    echo ("Вы ввели не всю информацию, вернитесь назад и заполните все поля!");
	exit();
    }
    //если логин и пароль введены,то обрабатываем их, чтобы теги и скрипты не работали, мало ли что люди могут ввести
    $login = stripslashes($login);
    $login = htmlspecialchars($login);
	$password = stripslashes($password);
    $password = htmlspecialchars($password);
//удаляем лишние пробелы
    $login = trim($login);
    $password = trim($password);
// подключаемся к базе
   // файл bd.php должен быть в той    же папке, что и все остальные, если это не так, то просто измените путь          
// минипроверка на подбор паролей

			$ip=getenv("HTTP_X_FORWARDED_FOR");
			if (empty($ip) || $ip=='unknown') {    $ip=getenv("REMOTE_ADDR"); }//извлекаем ip           
			mysql_query ("DELETE FROM oshibka WHERE UNIX_TIMESTAMP() -    UNIX_TIMESTAMP(date) > 900");//удаляем ip-адреса ошибавшихся при входе пользователей через 15 минут.           
			$result = mysql_query("SELECT col FROM oshibka WHERE ip='$ip'",$db);// извлекаем из базы количество неудачных попыток входа за    последние 15 у пользователя с данным ip 
            $myrow = mysql_fetch_array($result);

/*if ($myrow['col'] > 2) {
            //если ошибок больше двух, т.е три, то выдаем сообщение.
            exit("Вы набрали логин или пароль неверно 3 раз. Подождите    15 минут до следующей попытки.");
            }*/
			$password    = md5($password);//шифруем пароль
            $password    = strrev($password);// для надежности добавим реверс
            $password    = $password."b3p6f";

            //можно добавить несколько своих символов по вкусу, например,    вписав "b3p6f". Если этот пароль будут взламывать методом подбора у себя на сервере этой же md5,то явно ничего хорошего не    выйдет. Но советую ставить другие символы, можно в начале строки или в середине.          
//При этом необходимо увеличить длину поля password в базе. Зашифрованный пароль может получится гораздо большего    размера.          
 

$result = mysql_query("SELECT * FROM users WHERE login='$login' AND    password='$password' AND activation='1'",$db); //извлекаем из базы все данные о пользователе с    введенным логином и паролем
            $myrow    = mysql_fetch_array($result);
            if (empty($myrow['id']))
            {
            //если пользователя с введенным логином и паролем не    существует
            //Делаем запись о том, что данный ip не смог войти.          
			$select = mysql_query ("SELECT ip FROM oshibka WHERE    ip='$ip'");
            $tmp = mysql_fetch_row ($select);
            if ($ip == $tmp[0]) {//проверяем, есть ли пользователь в таблице "oshibka" 
            	$result52 = mysql_query("SELECT col FROM oshibka WHERE ip='$ip'",$db);
	            $myrow52 = mysql_fetch_array($result52);
				$col = $myrow52[0] + 1;//прибавляем    еще одну попытку неудачного входа 
	            mysql_query ("UPDATE oshibka SET col=$col,date=NOW() WHERE ip='$ip'");
            }          
else {
            mysql_query ("INSERT INTO oshibka (ip,date,col) VALUES ('$ip',NOW(),'1')");
            //если за последние 15 минут ошибок не было, то вставляем    новую запись в таблицу "oshibka"
            }
           
echo ("Извините, введённый    вами логин или пароль неверный.");
            }
            else {          
         //если пароли    совпадают, то запускаем пользователю сессию! Можете его поздравить, он вошел!
                      echo "Поздравляем! Вы успешно вошли =)<br>
					  		Автоматическое <a href=\"$URL\">перенаправление</a> через 5 секунды";
							
							mysql_query ("UPDATE users SET last_time=now() WHERE login=$myrow[login]");
					  
								$_SESSION['password']=$myrow['password']; 
                                $_SESSION['login']=$myrow['login']; 

                      $_SESSION['id']=$myrow['id'];//эти    данные очень часто используются, вот их и будет "носить с собой"    вошедший пользователь
                                 
             }
			 
	echo'</div>';
			 
	echo '			 
			 </section>
     </section>';

	require 'html_foot.php'; 