<?php
	$dbName = "registro";
	$username = $XXuserdb;
	$password = $XXpassdb;
	$link1=mysql_connect("127.0.0.1", $username, $password) OR die("Unable to connect to database 1");
	@mysql_select_db( "$dbName") or die( "Unable to select database 2");
	mysql_query ("SET NAMES 'utf8'");
	foreach($_POST as $k => $v)
	$$k=$v;
	foreach($_GET as $k => $v)
	$$k=$v;
?>
