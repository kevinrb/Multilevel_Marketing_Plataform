<?php       
	
	include "global.php";
	
	include "cnx.php";
	
	//  if(isset($_COOKIE["idsess"]))
	//  {
	//    $cookie__= $_COOKIE["idsess"];
	//    $qry= "update  erp.usuarios set id='' where id='$cookie__'";
	//    mysql_query($qry);
	//    setcookie("idsess",0,time()-2000);
	//  }
	//  header("Location: auth1.php?logoff=1");
	header("Location: auth1.php");
?>


