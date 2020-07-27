<?php 
	require_once("../sec.php");
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	
	if($pst_tar==1){
		$dd=$pst_data;
		for($i=0;$i<count($dd);$i++){
			$a=$dd["$i"][1]; $b=$dd["$i"][2]; $c=$dd["$i"][3]; $d=$dd["$i"][4]; $e=$dd["$i"][5]; $ii=$dd["$i"][0]; 
			mysql_query("UPDATE menu set nombre='$a',url='$b',idfunc='$c',grupo='$d',orden='$e' where idmenu='$ii'");
		}
		echo "1";
	}
	
	else if($pst_tar==2){
		$sql="INSERT INTO menu(nombre,url,idfunc,grupo,orden) VALUES('$pst_nombre','$pst_url','$pst_idfunc','$pst_grupo','$pst_orden')";
		$psql=mysql_query($sql);
		echo "1"; 
	}
	
?>