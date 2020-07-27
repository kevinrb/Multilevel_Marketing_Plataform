<?php
 include "sec.php"; 

 $data=$_POST['data'];
 //echo $idop;
 if($nexcel=="")
 $nexcel="nombre_del_archivo";
 
header('Content-type: application/vnd.ms-excel; charset=UTF-8');

header("Content-Disposition: attachment; filename=$nexcel.xls");

header("Pragma: no-cache");

header("Expires: 0");

 echo utf8_decode ($data);
 ?>