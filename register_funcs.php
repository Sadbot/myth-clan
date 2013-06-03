<?php 

// Подключаемся к БД 
require_once('config/bd.php');
require_once('common_funcs.php');

// Строка, используемая для md5 шифрования. Вы можете переместить её в файл 
// вне дерева веб-документов для большей безопасности. 
$supersecret_hash_padding = 'public class WriteTest2public static...';

function generate_code() //запускаем    функцию, генерирующую код. Можно даже вывести ее в отдельный файл.
{
	$hours = date("H"); // час       
	$minuts = substr(date("H"), 0 ,    1);// минута 
	$month = date("m");    // месяц
	$year_day = date("z"); // день в году
	$str = $hours . $minuts . $month .    $year_day; //создаем строку
	$str =    md5(md5($str)); //дважды шифруем в md5
              $str =    strrev($str);// реверс строки
              $str =    substr($str, 5, 4); // извлекаем 4 символов,    начиная с 5
              $array_mix = preg_split('//',    $str, -1, PREG_SPLIT_NO_EMPTY);
              srand ((float)microtime()*1000000);
              shuffle ($array_mix);
              return implode("",    $array_mix);
}

function    chec_code($code) //проверяем код
{
	$code = trim($code);//удаляем пробелы
	$array_mix = preg_split ('//',    generate_code(), -1, PREG_SPLIT_NO_EMPTY);
	$m_code = preg_split ('//', $code, -1,    PREG_SPLIT_NO_EMPTY);
	$result = array_intersect ($array_mix,    $m_code);
	if    (strlen(generate_code())!=strlen($code))
	{
		return    FALSE;
	}
	if    (sizeof($result) == sizeof($array_mix))
	{   
		return TRUE;
	}
    else
    {   
		return FALSE;
	}
}


function user_register() {
	global $mysql;
	global $supersecret_hash_padding;
  // Эта функция будет работать только с суперглобальными массивами. 

  // Проверяем данные от пользователя на соответствие заданным параметрам, условиям. 
if    (chec_code($_POST['code']))
	throw new Exception("Вы ввели неверно код с картинки");
if (strlen($_POST['login']) <= 3 && strlen($_POST['login']) >= 25)
	throw new Exception( 'ОШИБКА - логин не должен быть длинее 25 символов и короче 3 символов');
	if(strlen($_POST['password1']) <=5 && strlen($_POST['password1']) >= 32)
		throw new Exception( 'ОШИБКА - пароль не должен быть длинее 32 символов и короче 5 символов');
	if($_POST['password1'] != $_POST['password2'])
		throw new Exception( 'ОШИБКА - Пароли должны совпадать');
	if(strlen($_POST['email']) >= 50)
		throw new Exception( 'ОШИБКА - Email не должен быть длинее 50 символов');
	if(!validate_email($_POST['email'])) 
		throw new Exception('ОШИБКА - Email должен содержать знак @ и похож на something@somewhere.com');
    // Проверка имени пользователя и пароля 
    if (!account_namevalid($_POST['login'])) 
		throw new Exception('ОШИБКА - Логин пользователя является недопустимым!');
		
	if (!account_firstlastvalid($_POST['first_name']))
		throw new Exception('ОШИБКА - Имя пользователя является недопустимым!');
		
	if (!account_firstlastvalid($_POST['last_name']))
		throw new Exception('ОШИБКА - Фамилия пользователя является недопустимой!');
	

      $login = strtolower($_POST['login']);
      $login = trim($login);
      $email= $_POST['email'];
      // Сопоставление логина и email, заявленных новым пользователем, с уже имеющимися в БД 
      // В БД не должно найтись совпадений, ни логину не email. 
	  $result = $mysql->query("SELECT id
								FROM users
								WHERE login = ?s 
								OR email = ?s",$login,$email);
	  
      if (mysqli_num_rows($result) > 0) {
        $feedback = 'ОШИБКА - Имя пользователя или адрес электронной почты уже существует';
        return $feedback;
      } else {
		  $avatar=load_image($_FILES['fupload']);
		  
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
	$password  = pass_convertion($_POST['password1']);
	
        // Создайте новый хэш для вставки в БД и подтверждение по электронной почте
        $hash = md5($email.$supersecret_hash_padding);
		
        $result = $mysql->query ("INSERT INTO users (login, first_name, last_name, password, avatar, email, confirm_hash, activation, date,status) VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, 0, CURDATE(),'user')", $login, $first_name, $last_name, $password, $avatar, $email, $hash);
				
        if (!$result) { 
          throw new Exception('ОШИБКА - Ошибка запроса на запись в базу данных'.mysqli_error());
        } else {
			
			include_once "/lib/phpmailer/class.phpmailer.php";
			include_once "admin/mailconfig.php";
			
			$encoded_email = urlencode($_POST['email']);
		
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
	$mail->Subject ="Подтверждение регистрации на сайте Mythclan";
	$mail->AddAttachment($avatar);
					
	$message    = "Спасибо за регистрацию на mythclan.ru. Щелкните по этой ссылке для подтверждения регистрации: \n
http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?hash=$hash&email=$encoded_email \n
Как только вы пройдёте по ссылке, вы будете зарегистрированы на сайте mythclan.ru\nС    уважением,\n
				Администрация";//содержание сообщение
				
				
	$mail->Body=$message;
	$mail->AddAddress($email);
			
			if(!$mail->Send())    // Отправить подтверждение по электронной почте 
			{
				throw new Exception('ОШИБКА - Подтверждающее сообщение не было отправлено на email пользователя');
			}
			else
		    {	// Give a successful registration message 
				return $feedback = 'Вы успешно зарегистрировались. 
				 Вы вскоре получите подтверждение по электронной почте'; 
			}
        } 
      }
  }


function user_confirm() { 
  // Эта функция будет работать только с суперглобальными массивами 
  global $supersecret_hash_padding; 
  global $mysql;

  // Проверка на соответствие указанного адреса при регистрации и подтверждение этого адреса 
  $new_hash = md5($_GET['email'].$supersecret_hash_padding); 
  if ($new_hash && ($new_hash == $_GET['hash'])) { 
    /*$query = "SELECT login 
              FROM users
              WHERE confirm_hash = '$new_hash'"; 
    $result = mysqli_query($mysql->getConnection(),$query);*/
	
	$result = $mysql->query ("SELECT login 
              FROM users
              WHERE confirm_hash = ?s",$new_hash); 
	
    if (!$result || mysqli_num_rows($result) < 1) { 
      throw new Exception('ОШИБКА - Hash не найден'); 
    } else { 
      // Подтверждение регистрации через email указанный пользователем при регистрации 
      // Обновление поля is_confirmed, т.е. зарегистрирован и проверен. 
      $email = $_GET['email'];
	  $hash = md5($email.$supersecret_hash_padding);
	  
	  $result = $mysql->query ("UPDATE users SET email=?s, activation=1 WHERE confirm_hash=?s",$email,$hash);
      return 1;
    } 
  } else { 
    throw new Exception('ОШИБКА - Значения не совпадают');
  }
}