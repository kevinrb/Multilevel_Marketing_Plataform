<?php
	require("../../sec.php");
	$a=$_POST["a"];
	
	function generabarras($idprod)
	{
		$barras= 999000000000+$idprod;
		$barcode="$barras";
		//Compute the check digit
		$sum=0;
		for($i=1;$i<=11;$i+=2)
		$sum+=3*$barcode[$i];
		for($i=0;$i<=10;$i+=2)
		$sum+=$barcode[$i];
		$r=$sum%10;
		if($r>0)
		$r=10-$r;
		return $barras*10+$r;
	}
	
	if($a=="upb")
	{
		$prod=$_POST["prod"];
		qry("delete from bolsakit where login='$Xlogin';");
		for($i=0;$i<count($prod);$i++){
			qry("insert into bolsakit(idprod,cantidad,precio,precio1,precio2,precio3,precio4,precio5,precio6,precio7,precio8,precio9,precio10,precio11,precio12,hora,login) values('".$prod[$i][0]."','".$prod[$i][1]."','".$prod[$i][2]."','".$prod[$i][3]."','".$prod[$i][4]."','".$prod[$i][5]."','".$prod[$i][6]."','".$prod[$i][7]."','".$prod[$i][8]."','".$prod[$i][9]."','".$prod[$i][10]."','".$prod[$i][11]."','".$prod[$i][12]."','".$prod[$i][13]."','".$prod[$i][14]."',now(),'$Xlogin');");
		}
		//require("../gespromo/aplica_promo.php");
	}
	
	
	if($a=="rbs")
	{qry("delete from bolsakit where login='$Xlogin';");}
	
	
	if($a=="sp")
	{
		$pro=$_POST["prod"];
		$fei=$_POST["fei"];
		$fef=$_POST["fef"];
		$loc=$_POST["loc"];
		$can=$_POST["can"];
		$pck=$_POST["pck"];
		$pais=$_POST["pais"];
		$des=$_POST["desc"];
		if($loc=="0")
		{ $plocal="0"; }
		else
		{ $plocal="1"; }
		
		if($can=="0")
		{ $pcanal="0"; }
		else
		{ $pcanal="1"; }
		if(count($pro)>0 and $nom!="" and $fei!="" and $fef!=""){
			$precios=array();
			for($i=0;$i<count($pro);$i++)
			{
				for($j=2;$j<3;$j++){
					$precios[$j-2]=$precios[$j-2]+$pro[$i][1]*$pro[$i][$j]; 
				}
			}
			$qry="insert into productos$pais(precio,promo,nombre,categoria,estado) values ('".$precios[0]."','1','$nom','',1);";
			
			$r=qry($qry);
			$idprod=mysql_insert_id();
			if($r)
			{
				$qok="1";
				//qry("delete from bolsakit where login='$Xlogin';");
			}
			else
			{$qok="0";}
			
			
			
			for($i=0;$i<count($pro);$i++){
				qry("insert into kitparts$pais(idkit,idprod,precio,precio1,horaini,horafin,cantidad) values('$idprod','".$pro[$i][0]."','".$pro[$i][2]."','".$pro[$i][2]."','$fei','$fef 23:59:59','".$pro[$i][1]."');");
			}
			
			
			/*
				if($plocal=="1")
				{
				for($i=0;$i<count($loc);$i++){ qry("insert into productolocal(idprod,local) values('$idprod','".$loc[$i]."');");} 
				}
				if($pcanal=="1")
				{
				for($i=0;$i<count($can);$i++){ qry("insert into prodcanal(idprod,canal) values('$idprod','".$can[$i]."');");} 
				}
			*/
			//$barritas=generabarras($idprod);
			//qry("update productos set barras='$barritas' where idprod='$idprod'");
			
			qry("update productos$pais p,(select idkit,sum(cantidad*precio) precio,sum(cantidad*precio1) precio1 from kitparts$pais group by idkit) a set p.precio=a.precio,p.precio1=a.precio1 where p.idprod=a.idkit;");
			
			//require("../gespromo/aplica_promo.php");
			
			echo $qok;
			}else{
			echo 0;
		}
	}
?>