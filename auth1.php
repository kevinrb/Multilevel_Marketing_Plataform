<?php
	header("Cache-Control: no-cache, must-revalidate");

	include "global.php";
	include "cnx.php";
	
	foreach($_POST as $k => $v) $$k = $v;  
	
	$authuser = $authpass = $authpais = "";
	if(isset($usuario) and isset($passwd))
	{
		$authuser = mysql_real_escape_string($usuario);
		$authpass = mysql_real_escape_string($passwd);
		$authpais = mysql_real_escape_string($pais);
	}
	
	if(strlen($authuser) > 2 && strlen($authpass) > 2)
	{
		$res = mysql_query("select datediff(pass_end,now()) from usuarios u,locales l where u.local=l.local and 
		grupolocal='SANTA' and u.activo=1 and login='$authuser' and passwd='$authpass' and idpais='$authpais'");
		if(mysql_num_rows($res) > 0) {
			$r = mysql_fetch_row($res);
			$p = $r[0]+0;
			if($p <= 0) {
				header("Location: nuevopass.php?opc=act_pass&usuario=$authuser&pais=$authpais");
				exit();
			}
		}
		$newidsess=uniqid();
		$qry= "update usuarios set id='$newidsess'  where login='$authuser' and  passwd= '$authpass' and idpais='$authpais' and activo = 1";
		$result = MYSQL_QUERY($qry);
		$rows=mysql_affected_rows();
		if ($rows > 0 )
		{      
			setcookie("idsess",$newidsess,time()+200, "/");      
			$ip0=$_SERVER['REMOTE_ADDR'];
			$nav0=$_SERVER['HTTP_USER_AGENT'];
			$qry= "insert into logs (login,hora,ip,id,ua,screen) values ('$authuser',now(),'$ip0','$newidsess','$nav0','$w000 $h000')";
			$result = MYSQL_QUERY($qry);
			$clave=rand(1000,9999);
			$query2 = "insert into call01.logs (iduser, hora,ip,login,idsess,clave) values (1,now(),'$ip0','$authuser','$newidsess','$clave')";
			$result = MYSQL_QUERY($query2);
			header("Location: index2.php");      
			exit();
		}
		else
		print "Credenciales incorrectas<br>";
	}
	if(isset($_COOKIE["idsess"]))
	{
		$cookie__ = $_COOKIE["idsess"];
		$qry = "select count(*) from usuarios where id='$cookie__' ";
		$result = MYSQL_QUERY($qry);
		$nnn=mysql_fetch_row($result);
		if ($nnn[0]>0)  {
			setcookie("idsess",$_COOKIE["idsess"],time()-200, "/");
			header("Location: index.php");
			exit();
		}
		else
		{  // AUTENTICATE
			header("Location: index.php");      
			exit();
		}
	}
	else
	{
		header("Location: index.php");    
		exit();
	}
	exit();
?>


