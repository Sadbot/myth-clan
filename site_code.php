<?php
require_once("common_funcs.php");
require_once("login_funcs.php");

function site_header($title = "Сайт клана Миф" , $leftunit= null, $rightunit = null, $meta = "<meta charset='utf-8' />") 
{ 
print<<<HTML_HEAD
<!DOCTYPE html>
<html>
<head>
$meta
<link rel="shortcut icon" href="css/favicon.ico" />
<!--[if lt IE 8]>
	<script>
		var e = ("article,aside,figcaption,figure,footer, header, hgroup,nav,section,time").split(',');
		for (var i = 0; i < e.legth; i++) {
			document.createElement(e[i]);
		}
	</script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Tangerine">
<link rel="stylesheet" type="text/css" href="css/special.css">
<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="css/special_ie.css">
<![endif]-->
<script src="script/jquery.js"></script>
<script src="script/registration.js"></script>
<script>
	$(document).ready(function () {
		var check = $('.checked');
	   
	    $('.checkall').click( function () {
			
			if($(this).attr('checked', true))
			{
				$(this).prop('checked', this.checked);
				check.prop('checked', this.checked);
			}
			else if ($(this).attr('checked', false))
			{
				$(this).prop('checked', this.unchecked);
				check.prop('checked', this.unchecked);
			}
	    });
	});
</script>
<title>$title</title>

</head>
<body>
<div class="container">
	<header id="h"><a href="index.php"></a></header>
		<section id="mainunit">
HTML_HEAD;

if($leftunit == null)
	leftmenu();
if($rightunit == null)
	rightmenu();
echo"        
			<section id='centerunit'>";            
mainmenu();
}


function mainmenu()
{
global $leftunit;
$id = intval(@$_COOKIE['id']);
echo"<nav id = 'menu'>";
	if (isset($_SESSION['user']) && $_SESSION['user'] == true && $leftunit == null)
		echo "			
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='index.php'>
						<span class = 'menuEntry'>Главная</span></a></div>
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='video.php'>
						<span class = 'menuEntry'>Видео</span></a></div>
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='forum.php'>
						<span class = 'menuEntry'>Сообщества</span></a></div>
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='editprofile.php'>
						<span class = 'menuEntry'>Профиль</span></a></div>
		";
	else
		echo "
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='index.php'>
						<span class = 'menuEntry'>Главная</span></a></div>
				<div class = 'menuEntry'>
					<a class = 'menuEntry' href='forum.php'>
						<span class = 'menuEntry'>Сообщества</span></a></div>";
		
	echo"
			</nav>";
}

function rightmenu()
{
global $rightunit, $mysql;
if (isset($_SESSION['user']) && $_SESSION['user'] == true && $rightunit == null)
{	
	//Вывод количества полученных сообщений
	$messages = $mysql->getOne("SELECT count(id_msg) as kol FROM messages WHERE to_user=?s",$_COOKIE['id']);

	$li_messages="Сообщения";
	if (!empty($messages['kol']))
		$li_messages.=" [$messages[kol]]";

	//Вывод меню
	echo '
	<nav id="rightunit">';
		
		echo "
			<ul>
				<li><a href='messages.php'>$li_messages</a></li>
				<li><a href='userslist.php'>Список пользователей</a></li>
			</ul>";
	echo "		
	</nav>";
}
}

function leftmenu()
{


global $mysql;

echo'<aside id="leftunit">';

if (($row = user_isloggedin()) == false)
{
  user_logout();
  
//проверка заполнения формы
if (isset($_POST['submit']) && $_POST['submit'] == 'Войти') {
  if (strlen($_POST['login']) <= 25 && strlen($_POST['password']) <=32) {
    $feedback = user_login();
  } else { 
    $feedback = 'ОШИБКА - Имя пользователя и пароль слишком длинные';
  }
  if ($feedback == 1) {
    // Перенаправить пользователя на главную страницу сайта,
    // после удачного входа в систему.
    header("Location: index.php");
  } else { 
    $feedback_str = "<P class=\"errormess\">$feedback</P>";
  }
} else {
  $feedback_str = '';
}


// ---------------- 
// DISPLAY THE FORM 
// ---------------- 

// Superglobals don't work with heredoc 
$php_self = htmlspecialchars($_SERVER['PHP_SELF']);

print <<<EOLOGINFORM
$feedback_str
<FORM ACTION="$php_self" METHOD="POST"> 
<P CLASS="bold">Логин<BR> 
<INPUT TYPE="TEXT" NAME="login" VALUE="" SIZE="10" MAXLENGTH="15" required></P> 
<P CLASS="bold">Пароль<BR> 
<INPUT TYPE="password" NAME="password" VALUE="" SIZE="10" MAXLENGTH="15" required></P>

<P><INPUT TYPE="SUBMIT" NAME="submit" VALUE="Войти"></P>
</FORM>

<!-- ссылка на регистрацию, ведь как-то же должны гости туда попадать  --> 
<P><a href="register.php">Зарегистрироваться</a></P>

<!-- ссылка на восстановление пароля  --> 
<P><a href="send_pass.php">Забыли пароль?</a></P>
EOLOGINFORM;

}
else 
{
	if (isset($_GET['logout']))
    {
	  user_logout();
      header("Location: index.php");
    }
    else
	{
		$_SESSION['user']=true;
        $id=mysql_real_escape_string( $_COOKIE['id']);
        $hash=mysql_real_escape_string( $_COOKIE['hash']);
        $ip=mysql_real_escape_string( $_COOKIE['ip']);
        
        $result = $mysql->query("SELECT login, first_name, last_name, avatar, date from users WHERE id=?i AND confirm_hash=?s AND remote_addr=INET_ATON(?s)", $id,$hash,$ip);
        $row = mysqli_fetch_assoc($result);
        
        echo"
        
        <header>$row[first_name] $row[last_name]</header>
        <article><img src='$row[avatar]' /></article>";
		//echo"Залогинился!<br />";
	    echo'<a href="index.php?logout=1">Выйти</a>';
	}
    
}
echo'</aside>';

}



function site_footer()
{
echo"
			</section>
	     </section>
   		<div id='push'></div>
	</div>
<footer></footer>
</body>
</html>";
}