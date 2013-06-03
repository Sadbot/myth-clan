<?php
	session_start();
	require_once 'bd.php';
	$title="Проверка";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';
			 
		if (isset($_POST['about'])) { $about=$_POST['about']; 
			if ($about=='') {$about="нет описания" ;} }
		if (isset($_POST['login'])) { $login = $_POST['login']; 
			if ($login == '') { unset($login);} } //заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
        if (isset($_POST['password'])) { $password=$_POST['password']; 
			if ($password =='') { unset($password);} }
			
		if (isset($_POST['password2'])) { $password2=$_POST['password2']; 
			if ($password2 =='') { unset($password2);} }
//заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
        if (isset($_POST['code'])) { $code = $_POST['code']; 
			if ($code == '') { unset($code);} } //заносим введенный пользователем защитный код в переменную $code, если он пустой, то уничтожаем переменную

        if (isset($_POST['email'])) { $email = $_POST['email']; 
			if ($email == '') { unset($email);} } //заносим введенный пользователем e-mail, если он пустой, то уничтожаем переменную


if (empty($login) or empty($password) or empty($password2) or empty($code) or empty($email)) //если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт		  
{
	exit ("Вы ввели не всю информацию, вернитесь назад и заполните все поля!"); //останавливаем    выполнение сценариев
}
else
{
	if ($password != $password2)
		exit ("Пароли должны совпадать");
	else
	if    (!preg_match("/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i", $email)) //проверка    е-mail адреса регулярными выражениями на корректность
    	exit    ("Неверно введен е-mail!");
	else
	{
          // после сравнения проверяем,    пускать ли пользователя дальше или, он сделал ошибку, и остановить скрипт
          if    (chec_code($_POST['code']))

          {
         	exit ("Вы ввели неверно код с картинки"); //останавливаем выполнение сценариев
          }
		  else{
    //если логин и пароль введены,то обрабатываем их, чтобы теги и скрипты не работали, мало ли что люди могут ввести
    $login = stripslashes($login);
    $login = htmlspecialchars($login);
	$password = stripslashes($password);
    $password = htmlspecialchars($password);
	$about = stripslashes($about);
    $about = htmlspecialchars($about);
	//удаляем лишние пробелы
    $login = trim($login);
    $password = trim($password);
	//добавляем проверку на длину логина и пароля
            if    (strlen($login) < 3 or strlen($login) > 15) {
           		exit    ("Логин должен состоять не менее чем из 3 символов и не более чем из    15.");
            }
            if    (strlen($password) < 3 or strlen($password) > 32 or strlen($password2) < 3 or strlen($password2) > 32) {
            	exit ("Пароль должен состоять не менее чем из 3 символов и не более чем из  32.");
            }
                    
/*if    (empty($_FILES['fupload']))
            {
				
            //если переменной не существует (пользователь не отправил    изображение),то присваиваем ему заранее приготовленную картинку с надписью    "нет аватара"
            $avatar    = "avatars/lfooto.png"; //можете    нарисовать net-avatara.jpg или взять в исходниках
            }          
else 
            {

            //иначе - загружаем изображение пользователя
            $path_to_90_directory= 'avatars/';//папка,    куда будет загружаться начальная картинка и ее сжатая копия          
         
            if(preg_match('/[.](JPG)|(jpg)|(gif)|(GIF)|(png)|(PNG)$/',$_FILES['fupload']['name']))//проверка формата исходного изображения
                      {                 
                               $filename =    $_FILES['fupload']['name'];
                               $source =    $_FILES['fupload']['tmp_name']; 
                               $target =    $path_to_90_directory . $filename;
                               move_uploaded_file($source,    $target);//загрузка оригинала в папку $path_to_90_directory           
         if(preg_match('/[.](GIF)|(gif)$/',    $filename)) {
                     $im    = imagecreatefromgif($path_to_90_directory.$filename) ; //если оригинал был в формате gif, то создаем    изображение в этом же формате. Необходимо для последующего сжатия
                     }
                     if(preg_match('/[.](PNG)|(png)$/',    $filename)) {
                     $im =    imagecreatefrompng($path_to_90_directory.$filename) ;//если    оригинал был в формате png, то создаем изображение в этом же формате.    Необходимо для последующего сжатия
                     }
                     
                     if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/',    $filename)) {
                               $im =    imagecreatefromjpeg($path_to_90_directory.$filename); //если оригинал был в формате jpg, то создаем изображение в этом же    формате. Необходимо для последующего сжатия
                     }           
//СОЗДАНИЕ КВАДРАТНОГО ИЗОБРАЖЕНИЯ И ЕГО ПОСЛЕДУЮЩЕЕ СЖАТИЕ    ВЗЯТО С САЙТА www.codenet.ru           
// Создание квадрата 90x90
            // dest - результирующее изображение 
            // w - ширина изображения 
            // ratio - коэффициент пропорциональности           
$w    = 90;  //    квадратная 90x90. Можно поставить и другой размер.          
// создаём исходное изображение на основе 
            // исходного файла и определяем его размеры 
            $w_src    = imagesx($im); //вычисляем ширину
            $h_src    = imagesy($im); //вычисляем высоту изображения
                     // создаём    пустую квадратную картинку 
                     // важно именно    truecolor!, иначе будем иметь 8-битный результат 
                     $dest = imagecreatetruecolor($w,$w);           
         //    вырезаем квадратную серединку по x, если фото горизонтальное 
                     if    ($w_src>$h_src) 
                     imagecopyresampled($dest, $im, 0, 0,
                                         round((max($w_src,$h_src)-min($w_src,$h_src))/2),
                                      0, $w, $w,    min($w_src,$h_src), min($w_src,$h_src));           
         // вырезаем    квадратную верхушку по y, 
                     // если фото    вертикальное (хотя можно тоже серединку) 
                     if    ($w_src<$h_src) 
                     imagecopyresampled($dest, $im, 0, 0,    0, 0, $w, $w,
                                      min($w_src,$h_src),    min($w_src,$h_src));           
         // квадратная картинка    масштабируется без вырезок 
                     if ($w_src==$h_src) 
                     imagecopyresampled($dest,    $im, 0, 0, 0, 0, $w, $w, $w_src, $w_src);           
$date=time();    //вычисляем время в настоящий момент.
            imagejpeg($dest,    $path_to_90_directory.$date.".jpg");//сохраняем    изображение формата jpg в нужную папку, именем будет текущее время. Сделано,    чтобы у аватаров не было одинаковых имен.          
//почему именно jpg? Он занимает очень мало места + уничтожается    анимирование gif изображения, которое отвлекает пользователя. Не очень    приятно читать его комментарий, когда краем глаза замечаешь какое-то    движение.          
$avatar    = $path_to_90_directory.$date.".jpg";//заносим в переменную путь до аватара. 

$delfull    = $path_to_90_directory.$filename; 
            unlink($delfull);//удаляем оригинал загруженного    изображения, он нам больше не нужен. Задачей было - получить миниатюру.
            }
            else 
                     {
                                //в случае    не соответствия формата, выдаем соответствующее сообщение
                     echo ("Аватар должен быть в    формате <strong>JPG,GIF или PNG</strong>");
                             }
            //конец процесса загрузки и присвоения переменной $avatar адреса    загруженной авы
            }         */ 
			
	$valid_types =  array("gif","jpg", "png", "jpeg","GIF","JPG", "PNG", "JPEG");
	// создаем главную рабочую директорию =============================================
	$dir="avatars/";
	if (!is_dir($dir)) {
		mkdir($dir,0755);
		// создали папку gallery в корне нашего сайта и установили права на чтение и запись
	}
	if ($_FILES['fupload']['tmp_name']!="") 
	{
			// первая проверка на наличие загружаемого файла
			$ext = substr($_FILES['fupload']['name'], 1 + strrpos($_FILES['fupload']['name'], "."));
			//получаем расширение загружаемого файла
			if (in_array ($ext, $valid_types))
			{
				// сверяемся с массивом допустимых расширений и если совпадение найдено продолжаем работать
				// если нет - выводим сообщение об ошибке
				$imageinfo = getimagesize($_FILES['fupload']['tmp_name']);
				var_dump($imageinfo);
				// получаем информацию о загруженном файле
				// функция getimagesize позволяет получить размер изображения в пикселях, а также mime-тип загруженного файла
				if($imageinfo['type'] != 'image/gif' && $imageinfo['type'] != 'image/jpeg')
				{
					// проверяем действительно ли загрузенный файл является рисунком, и если все правильно продолжаем работу
					// такая проверка необходима для того, чтобы не было скрытой загрузки вредоносного исполняемого файла
					// т.е. банальной смены расширения php на jpg и попытке загрузки его на сервер

					$apend=date('YmdHis').rand(100,1000);
					// это имя, которое будет присвоенно изображению
					$output=$apend.".".$ext;
					// новое полное имя файла (добавили расширение к имени)
					$input=$_FILES['fupload']['tmp_name'];
					// временный файл который создается автоматически при загрузке изображения
					createphoto(200,$input,$dir.$output);
					// вызов функции по работе с изображением.
					// передаем два параметра: имя исходного изображения и то, которое нужно получить
					if (file_exists($dir.$output)) 
					{
						// проверка на существование загруженного файла
						echo ("<p>Аватар загружен.</p>");
						$avatar = $dir.$output;
					}
	else 
					{
						echo "<p>Аватар не был загружен и был заменен стандартным изображением</p>";
						$avatar="avatars/lfooto.png";
					}
				}
				else echo "<p>Неверный тип загружаемого файла. Аватар был заменен стандартным изображением</p>";
						$avatar="avatars/lfooto.png";
			}
			else echo "<p>Данное расширение недопустимо для загрузки. Аватар был заменен стандартным изображением</p>";
					$avatar="avatars/lfooto.png";
		}
	
$password    = md5($password);//шифруем пароль          
$password    = strrev($password);// для надежности добавим реверс          
$password    = $password."b3p6f";

//можно добавить несколько своих символов по вкусу, например,    вписав "b3p6f". Если этот пароль будут взламывать методом подбора у    себя на сервере этой же md5,то явно ничего хорошего не выйдет. Но советую    ставить другие символы, можно в начале строки или в середине.          
//При этом необходимо увеличить длину поля password в базе.    Зашифрованный пароль может получится гораздо большего размера.          
 // подключаемся к базе
   
/*    $result3 = mysql_query("SELECT id_u FROM user WHERE login='$login'",$db);
    $myrow = mysql_fetch_array($result3);
    if (!empty($myrow['id_u'])) {
    echo ("Извините, введённый вами логин уже стоит в очереди на регистрацию. Введите другой логин.");
    }
	else
	{*/
	    $result = mysql_query("SELECT id FROM users WHERE login='$login'",$db) or mysql_error();
	    $myrow = mysql_fetch_array($result);
	    if (!empty($myrow['id'])) 
		{
	    	exit ("Извините, введённый вами логин уже зарегистрирован. Введите другой логин.");
		}
		else
		{
	
 // если такого нет, то сохраняем данные

    // Проверяем, есть ли ошибки
  
    //echo "Вы успешно отправили запрос о приёме вас в сообщество.";
	
	include_once "/lib/phpmailer/class.phpmailer.php";
	include_once "admin/mailconfig.php";
	
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
	$mail->AddAttachment($avatar);

		/*$resulted = mysql_query("SELECT id FROM users WHERE login='$login'",$db);
		$myrows = mysql_fetch_array($resulted);
		if (!empty($myrows['id'])) {
			echo ("Данный пользователь уже был добавлен.");
		}
		else{
			$res=mysql_query("INSERT INTO `users` (login,password,avatar,email,date) VALUES('$login','$p','$ava','$ymail',CURDATE());");
			if ($res=='TRUE')
			{*/
				
				$result3    = mysql_query ("SELECT id FROM users WHERE login='$login'",$db);//извлекаем    идентификатор пользователя. Благодаря ему у нас и будет уникальный код    активации, ведь двух одинаковых идентификаторов быть не может.
				$myrow3    = mysql_fetch_array($result3);
				$activation    = md5($myrow3['id']).md5($login);//код активации аккаунта. Зашифруем    через функцию md5 идентификатор и логин. Такое сочетание пользователь вряд ли    сможет подобрать вручную через адресную строку.
				$subject    = "Подтверждение регистрации";//тема сообщения
				
				
				
				$message    = "Здравствуйте! Спасибо за регистрацию на сайте http://".$_SERVER['HTTP_HOST']."/mythclan\nВаш логин:    ".$login."\n
				Перейдите    по ссылке, чтобы активировать ваш    аккаунт:\nhttp://".$_SERVER['HTTP_HOST']."/mythclan/activation.php?login=".$login."&code=".$activation."\nС    уважением,\n
				Администрация";//содержание сообщение				
				
				
				$mail->Body=$message; 
				$mail->AddAddress($email);
				
				if(!$mail->Send())
				{
					exit ('Не могу отослать письмо!');
				}
				else
				{
					//var_dump($mail,$mail->ErrorInfo);
					echo    "На E-mail пользователя ".$login." выслано пиcьмо с приглашением."; //говорим об    отправленном письме пользователю
					    $result2 = mysql_query ("INSERT INTO `users` (login,password,avatar,email,about,date) VALUES('$login','$password','$avatar','$email','$about',CURDATE());");
						if ($result2)
							echo "Новый пользователь ".$login." был добавлен в сообщество <br>";
						else
							echo mysql_error();
				}
				$mail->ClearAddresses();
				$mail->ClearAttachments();		
			}
		}
	}
}

echo'	
		</section>
     </section>';
	require 'html_foot.php';