<?php 

// Подключаемся к БД 
require_once('config/bd.php');
require_once('common_funcs.php');
// Строка применяемая для шифрования по алгоритму md5 
$supersecret_hash_padding = 'В экстремум кибернетик попадал..';


// Эта функция производит обновление времени последнего посещения зарегистрированного
// пользователя. Вызывается при каждом просмотре страницы форума зарегестрированным
// пользователем (если пользователь авторизовался)
function setTimeVisit()
{
  global $mysql;
  $mysql -> query ( "UPDATE users
	        SET last_time=NOW()
			WHERE id=?i",$_COOKIE['id']);
}


function user_isloggedin() {
   
  global $supersecret_hash_padding, $mysql;

  if (!empty($_COOKIE['id']) && !empty($_COOKIE['hash']))
  {


	$result=$mysql->query("SELECT count(*) as count FROM users WHERE id=?s AND confirm_hash=?s AND remote_addr=INET_ATON(?s)", $_COOKIE['id'],$_COOKIE['hash'],getRealIp());

    if (mysqli_num_rows($result) > 0)
	{
	  $_SESSION['user']=true;
	  setTimeVisit();	
      return true;
	}
  }
  return false;
}

// Функция getNewThemes() помещает в массив $_SESSION['newThemes'] ID тем, 
// в которых были новые сообщения со времени последнего посещения пользователя
function getNewThemes()
{
	
	global $mysql;
  // Получаем список тем форума, где были новые сообщения
  $res= $mysql->query ("SELECT a.id_theme, MAX(UNIX_TIMESTAMP(b.time)) AS unix_last_post
	        FROM themes as a INNER JOIN posts as b
			ON a.id_theme=b.id_theme 
			GROUP BY a.id_theme
			HAVING unix_last_post>?s",$_SESSION['unix_last_visit']);

  if ( $res ) {
    while ( $id = mysqli_fetch_row( $res ) ) {
	  $_SESSION['newThemes'][$id[0]] = $id[0];	  
	}	
  }
}


function user_login() {
	
	global $mysql;
	unset($_SESSION);
$feedback=1;

  if (empty($_POST['login']) && !preg_match( "#^[- _0-9a-zА-Яа-я]+$#i", $login ))
  {
    $feedback = 'Имя пользователя является недопустимым';
    return $feedback;
  }
  
  elseif (empty($_POST['password']) && !preg_match( "#^[- _0-9a-zА-Яа-я]+$#i", $password ))	
  {
    $feedback = 'Пароль является недопустимым';
    return $feedback;
	
  } else {
	// Обрезаем переменные до длины, указанной в параметре тега input
	$login     = substr( $_POST['login'], 0, 25 );
  	$password  = substr( $_POST['password'], 0, 40 );
	
	// Обрезаем лишние пробелы
    $login      = trim( $login );
    $password  = trim( $password );
    
	$login = strtolower($_POST['login']);
    $crypt_pwd = pass_convertion($password);
	
	$login = htmlspecialchars($login);
	
	$result = $mysql->query ("SELECT id as id_author, login, first_name, last_name, avatar, email, activation, status, locked, UNIX_TIMESTAMP(last_time) as unix_last_visit, date
              FROM users
              WHERE login = ?s
              AND password = ?s", $login,$crypt_pwd);
	
    if (!$result || mysqli_num_rows($result) < 1){
      $feedback = 'Неправильный логин или пароль';
      return $feedback;
    } else {		
		$row=mysqli_fetch_assoc($result);
		if ( $row['locked'] )
    		return $feedback ='Ваша учетная запись заблокирована.Обратитесь к администратору.';
	
    if ($row['activation'] == '1') {
    	$_SESSION['status']=$row['status'];
		user_add_cookie($row['id_author'], $row['login']);
		getNewThemes();
          
		return $feedback;
    } else {
    	$feedback = 'Вы не подтвердили свою регистрацию';
        return $feedback;
     }
   }
  }
}

function user_logout() {
  unset($_SESSION);
  setcookie('id', '', (time()+2592000), '/', '', 0, 1);
  setcookie('hash', '', (time()+2592000), '/', '', 0, 1);
  setcookie('ip', '', (time()+2592000), '/', '', 0, 1);
}


function user_add_cookie($user_id, $login_in) {
  global $supersecret_hash_padding, $mysql;
  if (!$login_in) {
    $feedback =  'Нет имени пользователя';
    return false;
  }
  $id_hash = md5(md5($login_in.$supersecret_hash_padding.date('YmdHis')));
  
  $result = $mysql->query ("UPDATE users SET confirm_hash=?s, last_time=NOW(), remote_addr=INET_ATON(?s) WHERE login=?s", $id_hash, GetRealIp(), $login_in);
  
  if (!$result)
  {
	  $feedback="Неправильный логин";
	  return $feedback;         // ЗНАЧИТ В ЛОГИНЕ ПЕРЕДАН скрипт, о чём нужно сообщить.
  }

  setcookie('id', $user_id, (time()+86400), '/', '', 0, 1);
  setcookie('hash', $id_hash, (time()+86400), '/', '', 0, 1);
  setcookie('ip', GetRealIp(), (time()+86400), '/', '', 0, 1);
  
  return true;
}
?>