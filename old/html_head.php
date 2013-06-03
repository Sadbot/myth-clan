<!DOCTYPE HTML>
<html>
<head>
<?php
	if(empty($meta))
		echo '<meta charset="utf-8" />';
	else 
		echo '$meta';
?>

<title><?php echo"$title"?></title>
<link rel="shortcut icon" href="../css/favicon.ico" />


<!--[if lt IE 8]>
	<script>
		var e = ("article,aside,figcaption,figure,footer, header, hgroup,nav,section,time").split(',');
		for (var i = 0; i < e.legth; i++) {
			document.createElement(e[i]);
		}
	</script>
<![endif]-->

<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Tangerine">
<link rel="stylesheet" type="text/css" href="../css/special.css">
<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="css/special_ie.css">
<![endif]-->
<script src="../script/jquery.js"></script>
<script src="../script/registration.js"></script>

<script src="../script/uppod-0.3.9.js" type="text/javascript"></script>
<script src="../script/swfobject.js" type="text/javascript"></script>

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

</head>


<body>

<div class="container">
<header id="h"><a href="index.php"></a></header>

<?php
if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
{
//если существет логин и пароль в сессиях, то проверяем их и извлекаем аватар
$login = $_SESSION['login'];
$password = $_SESSION['password'];
$result = mysql_query("SELECT * FROM users WHERE login='$login' AND password='$password' AND activation='1'",$db);
$myrow = mysql_fetch_array($result);
//извлекаем нужные данные о пользователе
}
?>