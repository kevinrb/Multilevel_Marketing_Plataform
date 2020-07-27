<?php
	require("../../sec.php");
	require_once("../../ws/func.php");
	$a=$_POST["a"];
	if($a=="new")
	{
		$fcont=array();
		$dni=$_POST["dni"]; $fcont[]=array(1,$dni,"dni","num",8,0);
		$nom=$_POST["nom"]; $fcont[]=array(1,$nom,"nom","txt",0,0);
		$ape=$_POST["ape"]; $fcont[]=array(1,$ape,"ape","txt",0,0);
		$ema=$_POST["ema"]; $fcont[]=array(0,$ema,"ema","ema",0,0);
		$dir=$_POST["dir"]; $fcont[]=array(1,$dir,"dir","",0,0);
		$depa=$_POST["depa"]; $fcont[]=array(1,$depa,"depa","",0,1,"ubigeo","departamento");
		$prov=$_POST["prov"]; $fcont[]=array(1,$prov,"prov","",0,1,"ubigeo","provincia");
		$dist=$_POST["dist"]; $fcont[]=array(1,$dist,"dist","",0,1,"ubigeo","distrito");
		$anio=$_POST["anio"]; $fcont[]=array(1,$anio,"anio","num",4,0);
		$mes =$_POST["mes"];  $fcont[]=array(1,$mes,"mes","num",0,0);
		$dia =$_POST["dia"];  $fcont[]=array(1,$dia,"dia","num",0,0);
		$aniod=$_POST["aniod"]; $fcont[]=array(1,$aniod,"aniod","num",4,0);
		$mesd =$_POST["mesd"];  $fcont[]=array(1,$mesd,"mesd","num",0,0);
		$diad =$_POST["diad"];  $fcont[]=array(1,$diad,"diad","num",0,0);
		$tel=$_POST["tel"]; $fcont[]=array(0,$tel,"tel","",0,0);
		$cel=$_POST["cel"]; $fcont[]=array(0,$cel,"cel","num",9,0);
		$emp=$_POST["emp"]; $fcont[]=array(1,$emp,"emp","",0,1,"usuarios","login");
		$loc=$_POST["loc"]; $fcont[]=array(1,$loc,"loc","",0,1,"locales","local");
		$cue=$_POST["cue"]; $fcont[]=array(1,$cue,"cue","",0,1,"cuentas","idcuenta");
		$vou=$_POST["vou"]; $fcont[]=array(1,$vou,"vou","num",0,0);
		$mon=$_POST["mon"]; $fcont[]=array(1,$mon,"mon","flo",0,0);
		
		$reg_num="/^\d+$/";
		$reg_ema="/^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/";
		$reg_flo="/^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$/";
		$reg_txt="/^[a-zA-ZñÑ ]*$/";
		
		for($i=0;$i<count($fcont);$i++)
		{
			$af=$fcont[$i][0];
			$vl=$fcont[$i][1];
			$nj=$fcont[$i][2];
			$fl=$fcont[$i][3];
			$tf=$fcont[$i][4];
			$vb=$fcont[$i][5];
			
			if($af=="1" || ($af=="0" && $vl!="" && $vl!=null))
			{
				if($vl!="" && $vl!=null)
				{
					if($fl!="")
					{
						if($fl=="num"){if(preg_match($reg_num,$vl) && ($tf==0 || ($tf!=0 && strlen($vl)==$tf))){
							if($vb==1){ if(vertabla($vl,$fcont[$i][6],$fcont[$i][7])){}else{echo json_encode(array("0",$nj)); exit;} }
						}else{echo json_encode(array("0",$nj)); exit;}}
						if($fl=="txt"){if(preg_match($reg_txt,$vl) && ($tf==0 || ($tf!=0 && strlen($vl)==$tf))){
							if($vb==1){ if(vertabla($vl,$fcont[$i][6],$fcont[$i][7])){}else{echo json_encode(array("0",$nj)); exit;} }
						}else{echo json_encode(array("0",$nj)); exit;}}
						if($fl=="ema"){if(preg_match($reg_ema,$vl) && ($tf==0 || ($tf!=0 && strlen($vl)==$tf))){
							if($vb==1){ if(vertabla($vl,$fcont[$i][6],$fcont[$i][7])){}else{echo json_encode(array("0",$nj)); exit;} }
						}else{echo json_encode(array("0",$nj)); exit;}}
						if($fl=="flo"){if(preg_match($reg_flo,$vl) && ($tf==0 || ($tf!=0 && strlen($vl)==$tf))){
							if($vb==1){ if(vertabla($vl,$fcont[$i][6],$fcont[$i][7])){}else{echo json_encode(array("0",$nj)); exit;} }
						}else{echo json_encode(array("0",$nj)); exit;}}
					}
				}
				else
				{echo json_encode(array("0",$nj)); exit;}
			}
		}
		
		
		if(cal_days_in_month(CAL_GREGORIAN, $mes, $anio)>=$dia && $dia>0 && $mes>0 && $mes<13)
		{$fc="1";}
		else
		{$fc="0";}
		
		if($diad>0)
		{
			$dia_deposito=$aniod."-".$mesd."-".$diad;
		}
		
		$ubig=ubigeo($depa,$prov,$dist);
		$res=mysql_query("select login from usuarios where login='".$dni."';");
		if(mysql_num_rows($res)==0)
		{
			if($fc=="1"){
				if($ubig!=0 and $ubig!="")
				{
					$sql=$qry1="INSERT ignore INTO usuarios_temp (login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,activo,passwd,local,nivel,meta1,edobs,id,ffii) values('".$dni."','".$dni."','".$nom."','".$ape."','".$ema."','".$anio."-".$mes."-".$dia."','".$dir."','".$tel."','".$cel."','1','123456','".$Xlocal."','".$Xlogin."','".$mon."','".$vou."-=-".$cue."-=-$dia_deposito-=-DEPOSITO-=-$Xlogin','".$ubig."',curdate());";
					$qrycli=$qry2="insert ignore into clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal,ubigeo) values('".$dni."','".$nom."','".$dir."','".$ema."','".$tel."','1','".$cel."','".$dni."',now(),'".$Xlocal."','CATALOGO','".$ubig."');";
					$qrycli1="insert ignore into clientes1 (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal,ubigeo) values('".$dni."','".$nom."','".$dir."','".$ema."','".$tel."','1','".$cel."','".$dni."',now(),'".$Xlocal."','CATALOGO','".$ubig."');";	
					$qryupd="update clientes set lastupdate=now(), nombre='$nom $ape',direccion='$dir',ubigeo='$ubig',email='$ema',fono1='$tel',tipo=1,fono2='$cel',codigo1='$dni',local='$Xlocal' where idcliente='$dni' and canal='CATALOGO'";
					$qryupd1="update clientes1 set lastupdate=now(), nombre='$nom $ape',direccion='$dir',ubigeo='$ubig',email='$ema',fono1='$tel',tipo=1,fono2='$cel',codigo1='$dni',local='$Xlocal' where idcliente='$dni' ";	
					$res1=mysql_query($qry1);
					$res2=mysql_query($qry2);
					$res3=mysql_query($qryupd);
					if($res1 && $res2 && $res3)
					{
						$idsms=posturl(array("a"=>"new_user","qryuser"=>$sql,"qrycli"=>$qrycli,"qryupd"=>$qryupd,"qrycli1"=>$qrycli1,"qryupd1"=>$qryupd1),$XXurl_erp1);
						if($idsms==1){
							echo json_encode(array("5","Usuario Creado")); exit;
						}
						else{
							echo json_encode(array("2","Error Al Sincronizar")); exit;
						}
					}
					else
					{echo json_encode(array("2","Error Al Crear Dato")); exit;}
				}
				else{echo json_encode(array("2","Error En La Direccion")); exit;}
			}
			else{echo json_encode(array("2","Error En La Fecha")); exit;}
		}
		else
		{echo json_encode(array("1","Ya Existe el DNI")); exit;}
	}
	
	
	if($a=="old")
	{
		$tipo=$_POST["tipo"];
		$fcont=array();
		$dni=$_POST["dni"]; $fcont[]=array(1,$dni,"dni","num",8,0);
		$nom=$_POST["nom"]; $fcont[]=array(1,$nom,"nom","txt",0,0);
		$ape=$_POST["ape"]; $fcont[]=array(1,$ape,"ape","txt",0,0);
		$ema=$_POST["ema"]; $fcont[]=array(0,$ema,"ema","ema",0,0);
		$dir=$_POST["dir"]; $fcont[]=array(1,$dir,"dir","",0,0);
		$depa=$_POST["depa"]; $fcont[]=array(1,$depa,"depa","",0,1,"ubigeo","departamento");
		$prov=$_POST["prov"]; $fcont[]=array(1,$prov,"prov","",0,1,"ubigeo","provincia");
		$dist=$_POST["dist"]; $fcont[]=array(1,$dist,"dist","",0,1,"ubigeo","distrito");
		$anio=$_POST["anio"]; $fcont[]=array(1,$anio,"anio","num",4,0);
		$mes =$_POST["mes"];  $fcont[]=array(1,$mes,"mes","num",0,0);
		$dia =$_POST["dia"];  $fcont[]=array(1,$dia,"dia","num",0,0);
		$tel=$_POST["tel"]; $fcont[]=array(0,$tel,"tel","num",7,0);
		$cel=$_POST["cel"]; $fcont[]=array(0,$cel,"cel","num",9,0);
		$emp=$_POST["emp"]; $fcont[]=array(1,$emp,"emp","",0,1,"usuarios","login");
		$loc=$_POST["loc"]; $fcont[]=array(1,$loc,"loc","",0,1,"locales","local");
		if($tipo=="web")
		{
			$pago =$_POST["pago"];  $fcont[]=array(1,$pago,"pago","txt",0,0);
		}
		else{
			$pago =$_POST["pago"];  $fcont[]=array(0,$pago,"pago","txt",0,0);	
		}
		
		if($pago=="DEPOSITO"){
			$cue=$_POST["cue"]; $fcont[]=array(1,$cue,"cue","",0,1,"cuentas","idcuenta");
			$vou=$_POST["vou"]; $fcont[]=array(1,$vou,"vou","num",0,0);
			$mon=$_POST["mon"]; $fcont[]=array(1,$mon,"mon","flo",0,0);
			$aniod=$_POST["aniod"]; $fcont[]=array(1,$aniod,"aniod","num",4,0);
			$mesd =$_POST["mesd"];  $fcont[]=array(1,$mesd,"mesd","num",0,0);
			$diad =$_POST["diad"];  $fcont[]=array(1,$diad,"diad","num",0,0);	
		}
		ELSE{
			$cue=$_POST["cue"]; $fcont[]=array(0,$cue,"cue","",0,1,"cuentas","idcuenta");
			$vou=$_POST["vou"]; $fcont[]=array(0,$vou,"vou","num",0,0);
			$mon=$_POST["mon"]; $fcont[]=array(0,$mon,"mon","flo",0,0);
			$aniod=$_POST["aniod"]; $fcont[]=array(0,$aniod,"aniod","num",4,0);
			$mesd =$_POST["mesd"];  $fcont[]=array(0,$mesd,"mesd","num",0,0);
			$diad =$_POST["diad"];  $fcont[]=array(0,$diad,"diad","num",0,0);	
		}
		
		
		$reg_num="/^\d+$/";
		$reg_ema="/^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/";
		$reg_flo="/^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$/";
		$reg_txt="/^[a-zA-ZñÑ ]*$/";
		
		for($i=0;$i<count($fcont);$i++)
		{
			$af=$fcont[$i][0];
			$vl=$fcont[$i][1];
			$nj=$fcont[$i][2];
			$fl=$fcont[$i][3];
			$tf=$fcont[$i][4];
			$vb=$fcont[$i][5];
			
			if($af=="1" || ($af=="0" && $vl!="" && $vl!=null))
			{
				if($vl!="" && $vl!=null)
				{
					if($fl!="")
					{
						if($fl=="num"){if(preg_match($reg_num,$vl) && ($tf==0 || ($tf!=0 && strlen($vl)==$tf))){
							if($vb==1){ if(vertabla($vl,$fcont[$i][6],$fcont[$i][7])){}else{echo json_encode(array("0",$nj)); exit;} }
						}else{echo json_encode(array("0",$nj)); exit;}}
						if($fl=="txt"){if(preg_match($reg_txt,$vl) && ($tf==0 || ($tf!=0 && strlen($vl)==$tf))){
							if($vb==1){ if(vertabla($vl,$fcont[$i][6],$fcont[$i][7])){}else{echo json_encode(array("0",$nj)); exit;} }
						}else{echo json_encode(array("0",$nj)); exit;}}
						if($fl=="ema"){if(preg_match($reg_ema,$vl) && ($tf==0 || ($tf!=0 && strlen($vl)==$tf))){
							if($vb==1){ if(vertabla($vl,$fcont[$i][6],$fcont[$i][7])){}else{echo json_encode(array("0",$nj)); exit;} }
						}else{echo json_encode(array("0",$nj)); exit;}}
						if($fl=="flo"){if(preg_match($reg_flo,$vl) && ($tf==0 || ($tf!=0 && strlen($vl)==$tf))){
							if($vb==1){ if(vertabla($vl,$fcont[$i][6],$fcont[$i][7])){}else{echo json_encode(array("0",$nj)); exit;} }
						}else{echo json_encode(array("0",$nj)); exit;}}
					}
				}
				else
				{echo json_encode(array("0",$nj)); exit;}
			}
		}
		
		
		if(cal_days_in_month(CAL_GREGORIAN, $mes, $anio)>=$dia && $dia>0 && $mes>0 && $mes<13)
		{$fc="1";}
		else
		{$fc="0";}
		if($diad>0)
		{
			$dia_deposito=$aniod."-".$mesd."-".$diad;
		}
		$ubig=ubigeo($depa,$prov,$dist);
		$res=qry("select local from usuarios where login='$emp'");
		$local=mysql_fetch_row($res);
		$local_emp=$local[0];
		
		$res=mysql_query("select login from usuarios where login='".$dni."';");
		if(mysql_num_rows($res)==0)
		{
			if($fc=="1"){
				if($ubig!=0 and $ubig!="")
				{
					$qry1="INSERT ignore INTO usuarios (login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,activo,passwd,local,nivel,meta1,edobs,id) values('".$dni."','".$dni."','".$nom."','".$ape."','".$ema."','".$anio."-".$mes."-".$dia."','".$dir."','".$tel."','".$cel."','1','123456','".$local_emp."','".$emp."','".$mon."','".$vou."-=-".$cue."-=-$dia_deposito-=-$pago-=-$Xlogin','".$ubig."');";
					if($mon>0)
					{
						$qry1=$sql="INSERT ignore INTO usuarios_temp (login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,activo,passwd,local,nivel,meta1,edobs,id,ffii) values('".$dni."','".$dni."','".$nom."','".$ape."','".$ema."','".$anio."-".$mes."-".$dia."','".$dir."','".$tel."','".$cel."','1','123456','".$local_emp."','".$emp."','".$mon."','".$vou."-=-".$cue."-=-$dia_deposito-=-$pago-=-$Xlogin','".$ubig."',curdate());";
					}
					else
					{
						$qryasoc="insert ignore into asociadas(empresa,socio) values ('$emp','$dni')";
					}
					
					
					
					$qrycli=$qry2="insert ignore into clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal,ubigeo) values('".$dni."','".$nom."','".$dir."','".$ema."','".$tel."','1','".$cel."','".$dni."',now(),'".$local_emp."','CATALOGO','".$ubig."');";
					$qrycli1="insert ignore into clientes1 (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal,ubigeo) values('".$dni."','".$nom."','".$dir."','".$ema."','".$tel."','1','".$cel."','".$dni."',now(),'".$local_emp."','CATALOGO','".$ubig."');";
					$qryupd="update clientes set lastupdate=now(), nombre='$nom $ape',direccion='$dir',ubigeo='$ubig',email='$ema',fono1='$tel',tipo=1,fono2='$cel',codigo1='$dni',local='$local_emp' where idcliente='$dni' and canal='CATALOGO'";
					$qryupd1="update clientes1 set lastupdate=now(), nombre='$nom $ape',direccion='$dir',ubigeo='$ubig',email='$ema',fono1='$tel',tipo=1,fono2='$cel',codigo1='$dni',local='$local_emp' where idcliente='$dni'";		
					
					mysql_query($qryasoc);
					$res1=mysql_query($qry1);
					$res2=mysql_query($qry2);
					$res3=mysql_query($qryupd);
					if($res1 && $res2 && $res3)
					{
						if($mon>0)
						{
							$idsms=posturl(array("a"=>"new_user","qryuser"=>$sql,"qrycli"=>$qrycli,"qryupd"=>$qryupd,"qrycli1"=>$qrycli1,"qryupd1"=>$qryupd1),$XXurl_erp1);
							
							if($idsms==1){
								if($tipo=="web")
								{
									qry("update registro.registers set activo=2 where login='$dni'");
								}
								echo json_encode(array("5","Usuario Creado")); exit;
							}
							else{
								echo json_encode(array("2","Error Al Sincronizar")); exit;
							}
							}else{
							$idsms=posturl(array("a"=>"new_user","qrycli"=>$qrycli,"qryupd"=>$qryupd,"qrycli1"=>$qrycli1,"qryupd1"=>$qryupd1),$XXurl_erp1);
							if($idsms==1){
								echo json_encode(array("5","Usuario Creado")); exit;
							}
							else{
								echo json_encode(array("2","Error Al Sincronizar")); exit;
							}
						}
					}
					else
					{echo json_encode(array("2","Error Al Crear Dato")); exit;}
				}
				else{echo json_encode(array("2","Error En La Direccion")); exit;}
			}
			else{echo json_encode(array("2","Error En La Fecha")); exit;}
		}
		else
		{echo json_encode(array("1","Ya Existe el DNI")); exit;}
	}
	
	
	
	function vertabla($val,$tab,$cmp){
		$res=mysql_query("select * from $tab where $cmp='".$val."';");
		if(mysql_num_rows($res)>0)
		{return true;}
		else
		{return false;}
	}
	
	function ubigeo($dep,$pro,$dis){
		$res=mysql_query("select id from ubigeo where departamento='".$dep."' and provincia='".$pro."' and distrito='".$dis."';");
		if(mysql_num_rows($res)>0)
		{$res=mysql_fetch_row($res); return $res[0];}
		else
		{return false;}
	}
	
	
	
?>