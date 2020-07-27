<?php
	require_once("../../sec.php");
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	
	///////////////////////////////////////////////////////////////////////////////
	if($pst_a=="newc"){
		$val="";
		$data=$pst_data;
		for($i=0;$i<count($data);$i++)
		{$val .= "'$data[$i]',";}
		$sql=qry("INSERT INTO cuentas(idcuenta,banco,nombre,descripcion,horacrea) values($val NOW())");
		echo "1";
	}
	
?>