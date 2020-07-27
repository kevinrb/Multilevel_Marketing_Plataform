<?php
	include "../global.php";
	require("cnx.php");
	//require_once("../../ws/func.php");
	$a=$_POST["a"];
	if($a=="new")
	{ 
		$cpc=$_POST["cpc"];
		$cp2=$_POST["cp2"];
		
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
		
		$tel=$_POST["tel"]; $fcont[]=array(0,$tel,"tel","num",0,0);
		$cel=$_POST["cel"]; $fcont[]=array(0,$cel,"cel","num",9,0);
		$emp=$_POST["emp"]; $fcont[]=array(0,$emp,"emp","num",8,1,"usuarios","login");
		
		$reg_num="/^\d+$/";
		$reg_ema="/^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/";
		$reg_flo="/^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$/";
		$reg_txt="/^[a-zA-ZñÑ ]*$/";
		
		//if($_SESSION['cap_code']!=$cap){echo json_encode(array("0","cap")); exit;}	
		require_once('recaptchalib.php');
		$privatekey = "6LfgQfcSAAAAAOcyO_3nrh27JXxlZybM6fadlJ14";
		$resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$cpc,$cp2);
		if (!$resp->is_valid){echo json_encode(array("0","recaptcha_response_field")); exit;}
		
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
		
		
		$ubig=ubigeo($depa,$prov,$dist);
		$res=mysql_query("select login from redsanta.usuarios where login='".$dni."';");
		if(mysql_num_rows($res)==0)
		{
			if($fc=="1"){
				if($ubig!=0 and $ubig!="")
				{
					$sql=$qry1="INSERT ignore INTO registro.registers (login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,activo,passwd,local,nivel,meta1,edobs,id,pass_end) values ('".$dni."','".$dni."','".$nom."','".$ape."','".$ema."','".$anio."-".$mes."-".$dia."','".$dir."','".$tel."','".$cel."','1','123456','".$Xlocal."','".$emp."','".$mon."','','".$ubig."',curdate());";
					//$qrycli=$qry2="insert ignore into clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal,ubigeo) values('".$dni."','".$nom."','".$dir."','".$ema."','".$tel."','1','".$cel."','".$dni."',now(),'".$Xlocal."','CATALOGO','".$ubig."');";
					//$qryupd="update clientes set lastupdate=now(), nombre='$nom $ape',direccion='$dir',ubigeo='$ubig',email='$ema',fono1='$tel',tipo=1,fono2='$cel',codigo1='$dni',local='$Xlocal' where idcliente='$dni' and canal='CATALOGO'";
					$res1=mysql_query($qry1);
					//$res2=mysql_query($qry2);
					//$res3=mysql_query($qryupd);
					if($res1)
					{
						//$idsms=posturl(array("a"=>"new_registro","qryuser"=>$sql,"qrycli"=>$qrycli,"qryupd"=>$qryupd),"http://perushop.pe/santa2/webservices/pedidos.php");
						$idsms=1;
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
	
	
	
	
	function vertabla($val,$tab,$cmp){
		$res=mysql_query("select * from redsanta.$tab where $cmp='".$val."';");
		if(mysql_num_rows($res)>0)
		{return true;}
		else
		{return false;}
	}
	
	function ubigeo($dep,$pro,$dis){
		$res=mysql_query("select id from redsanta.ubigeo where departamento='".$dep."' and provincia='".$pro."' and distrito='".$dis."';");
		if(mysql_num_rows($res)>0)
		{$res=mysql_fetch_row($res); return $res[0];}
		else
		{return false;}
	}
	
	
	
?>