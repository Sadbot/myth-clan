<?php
require_once("/../code/safemysql.class.php");
		
$USER_STATUS_ADMIN = 2;
$USER_STATUS_USER = 0;	

$mysql = new SafeMySQL();

session_start();