<?php
require_once("common_funcs.php");
require_once("login_funcs.php");
require_once("site_code.php");

site_header();

if(isset($_SESSION['user']) && $_SESSION['user']==true)
{
	echo"Здарова, товарисч, зарегистрированный!";
	//echo("SESSION[user]:");
var_dump($_SESSION);
}



site_footer();