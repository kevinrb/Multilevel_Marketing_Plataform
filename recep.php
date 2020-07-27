<?php
	$pst_a=$_POST["a"];
	$ip=$_SERVER['REMOTE_ADDR'];
	if($pst_a=="ip_pro")
	{
		exec("echo $ip > /var/www/html/ip_robot.txt");
	}
	
?>