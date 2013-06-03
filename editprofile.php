<?php
require_once("common_funcs.php");
require_once("login_funcs.php");
require_once("site_code.php");
require_once("config/config.php");

site_header("Изменение профиля пользователя");

if(!isset($_SESSION['user']))
{
	echo ("Страница не доступна для незарегистрированных пользователей!");
	header ("Location: index.php");
}

	$feedback = '';
	
	$id = mysql_real_escape_string($_COOKIE['id']);
	$hash = mysql_real_escape_string($_COOKIE['hash']);
	$ip = mysql_real_escape_string($_COOKIE['ip']);
  
	$result = $mysql->query("SELECT id, first_name, last_name, email, avatar, url, icq, about 
							FROM users WHERE id=?i AND confirm_hash=?s AND remote_addr=INET_ATON(?s)",$id, $hash, $ip);
	if (!mysqli_num_rows($result))
	{
		echo ("Нет такого пользователя!");
		header ("Location: index.php");
	}
	
	$row=mysqli_fetch_assoc($result);
	
	try
	{
		$row=update_user($row);  //common_funcs.php
		$_SESSION=$row;
	}
	catch (Exception $e)
	{
		$feedback = $e->getMessage();
	}
	$action=$_SERVER['PHP_SELF'];
	echo $feedback."<a href='index.php?logout=1'>Выход</a><br />";
	$tpl = file_get_contents( './templates/editUserForm.html' );
	$tpl = str_replace( '{action}', $action, $tpl );
	$tpl = str_replace( '{first_name}', htmlspecialchars( $row['first_name'] ), $tpl );
	$tpl = str_replace( '{last_name}', htmlspecialchars( $row['last_name'] ), $tpl );
	$tpl = str_replace( '{email}', htmlspecialchars( $row['email'] ), $tpl );
	$tpl = str_replace( '{icq}', htmlspecialchars( $row['icq'] ), $tpl );
	$tpl = str_replace( '{url}', htmlspecialchars( $row['url'] ), $tpl );
	$tpl = str_replace( '{about}', htmlspecialchars( $row['about'] ), $tpl );
		  
	$tpl = str_replace( '{servertime}', date( "d.m.Y H:i:s" ), $tpl );
	// Если ранее был загружен файл - надо предоставить возможность удалить его
		  
	$unlinkfile = $row['avatar'];

	$tpl = str_replace( '{unlinkfile}', $unlinkfile, $tpl );
		  
	echo $tpl;	
					
site_footer();