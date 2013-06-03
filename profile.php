<?php
require_once("common_funcs.php");
require_once("login_funcs.php");
require_once("site_code.php");
require_once("config/config.php");

site_header("Профиль пользователя");

if($_SESSION['user']==true)
{
    if ( !isset( $_GET['id'] ) ) {
	    header( 'Location: userslist.php' );
  	}

	$id = intval($_GET['id']);  //id "хозяина" странички 
	// ID зарегистрированного пользователя не может быть меньше 
	// единицы - значит функция вызвана по ошибке
	if ( $id < 1 ) {
    	header( 'Location: userslist.php' );
	}
	
	$status = array( 'user' => 'Пользователь',
                   'moderator' => 'Модератор',
				   'admin' => 'Администратор' );
				   			
	$result = $mysql->query("SELECT id, login, first_name, last_name, avatar, url, icq, last_time, date, about, posts, status 
							FROM users WHERE id=?i",$id);
	if (mysqli_num_rows($result) == 0)
		echo ("Пользователя с таким id не существует!");
	else
	{
		$row = mysqli_fetch_assoc($result);
		$feedback = "";
		
		//если    страничка чужая, то выводим только некторые данные и форму для отправки    личных сообщений
		  $res = $mysql->query ("SELECT time FROM ".TABLE_POSTS." WHERE id_author=?i ORDER BY time DESC LIMIT 1",$id);
		  $lastPost = '';
		  if ( $res ) {
		    if ( mysqli_num_rows( $res ) > 0 )
		      $lastPost = mysqli_fetch_row( $res );
		  
		
		$html = file_get_contents( './templates/showUserInfo.html' );
  		$html = str_replace( '{name}', $row['login'], $html );
  		$html = str_replace( '{first_name}', $row['first_name'], $html );
  		$html = str_replace( '{last_name}', $row['last_name'], $html );
  		$html = str_replace( '{date}', $row['date'], $html ); 
  		$html = str_replace( '{status}', $status[$row['status']], $html );
  		$html = str_replace( '{lastvisit}', $row['last_time'], $html );
		$html = str_replace( '{lastpost}', $lastPost, $html ); 
		$html = str_replace( '{totalposts}', $row['posts'], $html );
  		$html = str_replace( '{url}', $row['url'], $html );
  		$html = str_replace( '{icq}', $row['icq'], $html );
  		$html = str_replace( '{about}', $row['about'], $html );
		echo $html;
		
		if ( isset( $_SESSION['user'] ) ) 
					{
	    				
						print <<<HERE
			          	<form action='post.php' method='post'>
					        <br>
				            <h2>Отправить пользователю сообщение:</h2>
				            <textarea cols='43' rows='4' name='text'></textarea><br>
				            <input type='hidden' name='poluchatel'    value='$row[login]'>
				            <input type='hidden' name='id'    value='$row[id]'>
				            <input type='submit' name='submit' value='Отправить'>
			            </form>
HERE;
					}
				}
			
	}

	
		 
}
else
{
	echo("Недоступно для незарегистрированных пользователей");
}



site_footer();