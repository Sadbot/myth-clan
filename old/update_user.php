<?php
	session_start();
	require_once("bd.php");// файл bd.php должен быть в той же папке, что и все    остальные, если это не так, то измените путь 
if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
{
            //если    существует логин и пароль в сессиях, то проверяем, действительны ли они
            $login = $_SESSION['login'];
            $password = $_SESSION['password'];
            $result2 = mysql_query("SELECT id FROM    users WHERE login='$login' AND password='$password'",$db); 
            $myrow2 = mysql_fetch_array($result2); 
            if (empty($myrow2['id']))
            {
               //Если не    действительны, то закрываем доступ
                exit("Вход на эту страницу разрешен    только зарегистрированным пользователям!");
            }
}
else {
            //Проверяем,    зарегистрирован ли вошедший
            exit("Вход на эту    страницу разрешен только зарегистрированным пользователям!"); }
			$old_login = $_SESSION['login']; //Старый логин нам    пригодиться
			$old_password = $_SESSION['password'];
            $id = $_SESSION['id'];//идентификатор пользователя тоже нужен
            $ava =    "avatars/lfooto.png";//стандартное    изображение будет кстати
////////////////////////
            ////////ИЗМЕНЕНИЕ    ЛОГИНА
            ////////////////////////
	if (isset($_POST['old_login']))		
	{
		if($_POST['old_login']==$old_login)
		{
			if (!empty($_POST['new_login1']) && !empty($_POST['new_login2']) && ($_POST['new_login1']==$_POST['new_login2']))//Если существует логин
	        {
				$login = $_POST['new_login1'];
				$login = stripslashes($login); $login =    htmlspecialchars($login); $login = trim($login);//удаляем все лишнее 
				if ($login == '') {    echo("Вы не ввели логин");exit();} //Если    логин пустой, то останавливаем 
				if (strlen($login) < 3 or strlen($login)    > 15) {//проверяем    дину 
					echo ("Логин должен состоять не менее чем из 3 символов и не более чем из 15."); //останавливаем выполнение сценариев
					exit();
				}
//    проверка на существование пользователя с таким же логином
        	    $result = mysql_query("SELECT id FROM    users WHERE login='$login'",$db);
            	$myrow = mysql_fetch_array($result);
	            if (!empty($myrow['id'])) {	
		            echo ("Извините,    введённый вами логин уже зарегистрирован. Введите другой логин."); //останавливаем выполнение сценариев
					exit();
            }
			}
			else
			{	
				echo("Вы не ввели новый логин");
				exit();
			}
			$result4 = mysql_query("UPDATE users SET    login='$login' WHERE login='$old_login'",$db);//обновляем в базе логин пользователя 

            if ($result4=='TRUE') {//если выполнено верно, то обновляем все сообщения,    которые отправлены ему
            mysql_query("UPDATE messages SET    author='$login' WHERE author='$old_login'",$db);
            $_SESSION['login'] = $login;//Обновляем логин в сессии 
			if    (isset($_COOKIE['login'])) {
          setcookie("login",    $login, time()+9999999);//Обновляем логин в куках 
          } 
            echo "<html><head><meta    http-equiv='Refresh' content='5;    URL=page.php?id=".$_SESSION['id']."'></head><body>Ваш логин изменен! Вы    будете перемещены через 5 сек. Если не хотите ждать, то <a    href='page.php?id=".$_SESSION['id']."'>нажмите    сюда.</a></body></html>";//отправляем    пользователя назад	
		}
	}
	else
	{
		echo('Введите старый логин для изменения');
		exit();
	}
}

			////////////////////////
            ////////ИЗМЕНЕНИЕ    ПАРОЛЯ
            ////////////////////////
else if (isset($_POST['old_password'])) //Если существует    пароль
     {	//преобразование старого пароля
	 	$old_post_pass=$_POST['old_password'];$old_post_pass = stripslashes($old_post_pass);$old_post_pass = htmlspecialchars($old_post_pass);$old_post_pass = trim($old_post_pass); $old_post_pass=md5($old_post_pass); $old_post_pass=strrev($old_post_pass); $old_post_pass=$old_post_pass."b3p6f"; 
		//преобразование старого пароля
       if(!empty($old_post_pass) && ($old_post_pass==$old_password))
	   {
         if(!empty($_POST['new_password1']) && !empty($_POST['new_password2']) && ($_POST['new_password1']==$_POST['new_password2']))
		 {
			$password = $_POST['new_password1'];
            $password = stripslashes($password);$password = htmlspecialchars($password);$password = trim($password);//удаляем все лишнее             
			if (strlen($password) < 3    or strlen($password) > 15) //проверка на    количество символов
			{
            	echo ("Пароль должен    состоять не менее чем из 3 символов и не более чем из 15."); //останавливаем выполнение сценариев
				exit();
            }
			$password = md5($password);//шифруем пароль
            $password = strrev($password);// для надежности добавим реверс
            $password = $password."b3p6f";
            //можно    добавить несколько своих символов по вкусу, например, вписав    "b3p6f". Если этот пароль будут взламывать методом подбора у себя    на сервере этой же md5,то явно ничего хорошего не выйдет. Но советую ставить    другие символы, можно в начале строки или в середине.
            //При    этом необходимо увеличить длину поля password в базе. Зашифрованный пароль    может получится гораздо большего размера.
 
			$result4 = mysql_query("UPDATE users SET    password='$password' WHERE login='$old_login'",$db);//обновляем пароль 

            if ($result4=='TRUE') //если верно, то обновляем его в сессии
			{
            	$_SESSION['password'] = $password;
				if    (isset($_COOKIE['password'])) 
				{
	            	setcookie("password",$_POST['password'],    time()+9999999);//Обновляем пароль в куках, если они есть 
	            } 
	            echo "<html><head><meta    http-equiv='Refresh' content='5;    URL=page.php?id=".$_SESSION['id']."'></head><body>Ваш пароль изменен! Вы    будете перемещены через 5 сек. Если не хотите ждать, то <a    href='page.php?id=".$_SESSION['id']."'>нажмите    сюда.</a></body></html>";//отправляем    обратно на его страницу
			}
		 }
		 else
		 {
			 echo("Вы не ввели пароль для изменения");
			 exit();
		 }
	   }
	   else
	   {
			echo"Прежний пароль введён неправильно.";
			exit();
	   }
	} 
            ////////////////////////
            ////////ИЗМЕНЕНИЕ    АВАТАРЫ
            ////////////////////////
            else if    (isset($_FILES['fupload']['name'])) //отправлялась    ли переменная
                  {
if (empty($_FILES['fupload']['name']))
            {
            //если    переменная пустая (пользователь не отправил изображение),то присваиваем ему    заранее приготовленную картинку с надписью "нет аватара"
            $avatar =    "avatars/lfooto.png"; //можете    нарисовать net-avatara.jpg или взять в исходниках
            $result7 = mysql_query("SELECT avatar    FROM users WHERE login='$old_login'",$db);//извлекаем текущий аватар 
            $myrow7 = mysql_fetch_array($result7);
            if ($myrow7['avatar'] == $ava)    {//если аватар был стандартный, то не удаляем    его, ведь у на одна картинка на всех.
            $ava = 1;
            }
            else {unlink    ($myrow7['avatar']);}//если аватар был свой, то    удаляем его, затем поставим стандарт
            }
else 
            {
            //иначе    - загружаем изображение пользователя для обновления
            $path_to_90_directory =    'avatars/';//папка, куда будет загружаться    начальная картинка и ее сжатая копия
                
            if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)|(gif)|(GIF)|(png)|(PNG)$/',$_FILES['fupload']['name']))//проверка формата исходного изображения

                             {             
                                                            
                                           $filename    = $_FILES['fupload']['name'];
                                           $source    = $_FILES['fupload']['tmp_name'];        
                                           $target    = $path_to_90_directory . $filename;
                                           move_uploaded_file($source, $target);//загрузка оригинала в папку $path_to_90_directory 
                if(preg_match('/[.](GIF)|(gif)$/',    $filename)) {
                            $im    = imagecreatefromgif($path_to_90_directory.$filename) ; //если оригинал был в формате gif, то создаем    изображение в этом же формате. Необходимо для последующего сжатия
                            }
                            if(preg_match('/[.](PNG)|(png)$/', $filename)) {

                            $im =    imagecreatefrompng($path_to_90_directory.$filename) ;//если    оригинал был в формате png, то создаем изображение в этом же формате.    Необходимо для последующего сжатия
                            }
                            
                            if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/',    $filename)) {
                                           $im =    imagecreatefromjpeg($path_to_90_directory.$filename); //если оригинал был в формате jpg, то создаем изображение в этом же    формате. Необходимо для последующего сжатия
                            }
                            
            //СОЗДАНИЕ    КВАДРАТНОГО ИЗОБРАЖЕНИЯ И ЕГО ПОСЛЕДУЮЩЕЕ СЖАТИЕ ВЗЯТО С САЙТА www.codenet.ru
//    Создание квадрата 90x90
            //    dest - результирующее изображение 
            //    w - ширина изображения 
            //    ratio - коэффициент пропорциональности 
$w = 90;  // квадратная    90x90. Можно поставить и другой размер.
//    создаём исходное изображение на основе 
            //    исходного файла и определяем его размеры 
            $w_src = imagesx($im); //вычисляем ширину
            $h_src = imagesy($im); //вычисляем высоту изображения
         //    создаём пустую квадратную картинку 
                     // важно именно truecolor!, иначе    будем иметь 8-битный результат 
                     $dest = imagecreatetruecolor($w,$w); 
nbsp;        //    вырезаем квадратную серединку по x, если фото горизонтальное 
                     if ($w_src>$h_src) 
                        imagecopyresampled($dest, $im, 0, 0,
                                         round((max($w_src,$h_src)-min($w_src,$h_src))/2),
                                     0, $w, $w,    min($w_src,$h_src), min($w_src,$h_src)); 
            // вырезаем квадратную верхушку по    y, 
                     // если фото вертикальное (хотя    можно тоже серединку) 
                     if ($w_src<$h_src) 
                        imagecopyresampled($dest, $im, 0, 0, 0, 0, $w, $w,
                                      min($w_src,$h_src),    min($w_src,$h_src)); 
         //    квадратная картинка масштабируется без вырезок 
                     if ($w_src==$h_src) 
                     imagecopyresampled($dest, $im, 0, 0, 0, 0, $w, $w, $w_src, $w_src); 
                                            
$date=time(); //вычисляем время в настоящий момент.
            imagejpeg($dest, $path_to_90_directory.$date.".jpg");//сохраняем изображение формата jpg в нужную папку,    именем будет текущее время. Сделано, чтобы у аватаров не было одинаковых    имен.
//почему    именно jpg? Он занимает очень мало места + уничтожается анимирование gif    изображения, которое отвлекает пользователя. Не очень приятно читать его    комментарий, когда краем глаза замечаешь какое-то движение.
$avatar =    $path_to_90_directory.$date.".jpg";//заносим в переменную путь до аватара.
$delfull = $path_to_90_directory.$filename; 
            unlink ($delfull);//удаляем оригинал загруженного изображения, он нам    больше не нужен. Задачей было - получить миниатюру.
$result7 =    mysql_query("SELECT avatar FROM users WHERE    login='$old_login'",$db);//извлекаем текущий аватар пользователя

            $myrow7 = mysql_fetch_array($result7);
if ($myrow7['avatar'] == $ava)    {//если он стандартный, то не удаляем его, ведь у    нас одна картинка на всех.
            $ava = 1;
            }
            else {unlink    ($myrow7['avatar']);}//если аватар был свой, то    удаляем его
 
}
            else 
                    {
                                          //в    случае несоответствия формата, выдаем соответствующее сообщение

                    exit ("Аватар должен быть в    формате <strong>JPG,GIF или PNG</strong>");

                                          }
}
$result4 = mysql_query("UPDATE users SET    avatar='$avatar' WHERE login='$old_login'",$db);//обновляем аватар в базе 

            if ($result4=='TRUE') {//если верно, то отправляем на личную страничку
            echo "<html><head><meta    http-equiv='Refresh' content='5;    URL=page.php?id=".$_SESSION['id']."'></head><body>Ваша аватарка изменена! Вы    будете перемещены через 5 сек. Если не хотите ждать, то <a href='page.php?id=".$_SESSION['id']."'>нажмите    сюда.</a></body></html>";}
      } 
?>