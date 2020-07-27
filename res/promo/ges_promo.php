<?php
	require("../../sec.php");
	$a=$_POST["a"];
	
	if($a=="dp"){
		$i=$_POST["i"];
		$p=$_POST["p"];
		
		$res1=qry("select b.idkit,DATE_FORMAT(b.horaini,'%Y-%m-%d') horaini,DATE_FORMAT(b.horafin,'%Y-%m-%d') horafin from kitparts$p b,productos$p a where b.idkit=a.idprod  and b.idkit='$i' group by b.idkit;");
		$res2=qry("select productolocal.local,locales.nombre from productolocal,locales where productolocal.local=locales.local and productolocal.idprod='$i';");
		$res3=qry("select prodcanal.canal,canales.nombre  from prodcanal,canales where prodcanal.canal=canales.idcanal and prodcanal.idprod='$i';");
		$tmp1=mysql_fetch_row($res1);
		
		
		$html="";
		$htm2="";
		$htm3="";
		$html .= "
		<div class='form-group col-sm-6'>
		<label>Fecha Inicio</label>
		<input type='date' id='fini' class='form-control' value='$tmp1[1]'>
		</div>
		<div class='form-group col-sm-6'>
		<label>Fecha Fin</label>
		<input type='date' id='ffin' class='form-control' value='$tmp1[2]'>
		</div>
		<input type='hidden' id='pais' value='$p'>
		";
		
		if(mysql_num_rows($res2)>0)
		{
			while($temp=mysql_fetch_row($res2))
			{ $htm2 .= "<span inf='$temp[0]' class='badge'>".$temp[1]."<span class='remove glyphicon glyphicon-remove'></span></span>"; }
		}
		
		if(mysql_num_rows($res3)>0)
		{
			while($temp=mysql_fetch_row($res3))
			{ $htm3 .= "<span inf='$temp[0]' class='badge'>".$temp[1]."<span class='remove glyphicon glyphicon-remove'></span></span>"; }
		}
		
		echo json_encode(array($html,$htm2,$htm3));
	}
	
	if($a=="upr")
	{
		$i    =$_POST["i"];
		$p    =$_POST["p"];
		$fini =$_POST["fini"];
		$ffin =$_POST["ffin"];
		$pack =$_POST["pack"];
		$loc  =$_POST["loc"];
		$can  =$_POST["can"];
		
		qry("update kitparts$p set horaini='$fini',horafin='$ffin' where idkit='$i';");
		/*	
			mysql_query("update productos set pack='$pack' where idprod='$i';");
			
			mysql_query("delete from productolocal where idprod='$i';");
			if(count($loc)==0)
			{
			mysql_query("delete from productolocal where idprod='$i';");  mysql_query(" update productos set porlocal='0' where idprod='$i';");
			}else{
			for($j=0;$j<count($loc);$j++)
			{ mysql_query("insert into productolocal(idprod,local) values('$i','".$loc[$j]."');"); }
			mysql_query(" update productos set porlocal='1' where idprod='$i';");
			}
			
			mysql_query("delete from prodcanal where idprod='$i';");
			if(count($can)==0){
			mysql_query("delete from prodcanal where idprod='$i';");  mysql_query(" update productos set porcanal='0' where idprod='$i';");
			}else{
			for($j=0;$j<count($can);$j++)
			{ mysql_query("insert into prodcanal(idprod,canal) values('$i','".$can[$j]."');"); }
			mysql_query(" update productos set porcanal='1' where idprod='$i';");
			}
		*/
		echo "1";
	}
	
?>