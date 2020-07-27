<?php
	error_reporting(E_ERROR | E_PARSE);
	include "../global.php";
	$host= "localhost";
	$user=$XXuserdb;
	$pass=$XXpassdb;
	$db=$XXdbName;
	$conexion = mysql_connect($host, $user, $pass);
	mysql_select_db($db,$conexion);
	mysql_query ("SET NAMES 'utf8'");
	require("/var/www/html/redsanta/ws/func.php");
	upda_ped();
	upda_depo();
	
?>
