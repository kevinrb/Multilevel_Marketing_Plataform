<?php
	foreach($_POST as $k => $v)
	$$k=mysql_real_escape_string($v);
	foreach($_GET as $k => $v)
	$$k=mysql_real_escape_string($v);
	foreach($_GET as $k => $v)
	$v=mysql_real_escape_string($v);
	foreach($_POST as $k => $v)
	$v=mysql_real_escape_string($v);
	
	if (isset($_COOKIE["idsess"]))
	{
		$cookie__ = $_COOKIE["idsess"];
		include "global.php";
		include "cnx.php";
		
		$qry="select a.iduser,a.login,a.local, a.nombre,a.timeout,a.idcliente,b.estado,b.nivel,b.hora_update,b.acumu,a.nivel,b.deuda,b.tipo,b.monto,b.nivel_1,(hora_disabled is null),b.tinkuy,b.tin_acumu,b.plati_mas,b.diaman_mas,a.idpais,b.volumen,b.hora_in,c.moneda from usuarios a left join user_nivel b on b.login=a.login left join paises c on c.idpais=a.idpais where a.id='$cookie__'";
		$res = MYSQL_QUERY($qry);
		$r=mysql_fetch_row($res);
		$Xiduser=$r[0]+0;
		$Xlogin=$r[1];
		$Xlocal=$r[2];
		$Xnombre=$r[3];
		$Xtimeout=$r[4];
		$Xidcliente=$r[5];
		$Xnivelestado=$r[6];
		$Xnivel=$r[7];
		$Xfchcorte=$r[8];
		$Xacum=$r[9];
		$Xtipo=$r[10];
		$Xdeuda=$r[11];
		$Xtipo_n=$r[12];
		$Xmonto=$r[13];
		$Xnivel_1=$r[14];
		$Xdisabled=$r[15];
		$Xtinkuy=$r[16];
		$Xtin_acumu=$r[17];
		$Xplati=$r[18];
		$Xdiama=$r[19];
		$Xpais=$r[20];
		$Xvolumen=$r[21];
		$Xfchin=$r[22];
		$Xmoneda=$r[23];
		////Limite de NIVEL
		$res=MYSQL_QUERY("select limite1 from niveles where idnivel=1");
		$limit=mysql_fetch_row($res);
		$Xlimit=$limit[0];
		$Xlimitr=40;
		
		$qry="select idfunc from permisos where login='$Xlogin' order by idfunc desc";
		$qry=mysql_query($qry) or die("Error en la consulta permisos: ".mysql_error());
		$Xpermiso=mysql_fetch_row($qry);
		$Xpermiso=$Xpermiso[0];
		
		if($Xiduser == 0)
		{
			header("Location: /auth1.php");
			exit;
		}
		else
		{
			setcookie("idsess",$cookie__,time()+$Xtimeout, "/");
			$qry="update logs set horafin=now() where login='$Xlogin' and id='$cookie__'";
			mysql_query($qry) or die("Error en la consulta permisos: ".mysql_error());
		}
		//----- Comprueba si tiene permiso para ingresar a esta pagina -----//
		$pag = substr($_SERVER["PHP_SELF"],7);
		$resf = mysql_query("select idfunc from menu where url='$pag'");
		$ntp = true;
		while($idf = mysql_fetch_row($resf)) {
			$ptp = mysql_fetch_row(mysql_query("select * from permisos where idfunc=$idf[0] and login='$Xlogin'"));
			if($ptp[0]) $ptp = true;
			else $ptp = false;
			$ntp = $ptp | $ntp;
		}
		if(!$ntp)
		die("No tienes acceso a esta funcionalidad!");
		//----- Registro de accesos a funcionalidades -------
		$qry = "insert into accesos(pagina,local,login) select '".$_SERVER['PHP_SELF']."','$Xlocal','$Xlogin' from menu where url='$pag'";
		mysql_query($qry);    
	}
	else
    header("Location: /auth1.php");  
	
	function check($login,$idfunc)
	{
		$query = "select * from permisos where login='$login' and idfunc='$idfunc'";
		$resddd = MYSQL_QUERY($query);
		if ( mysql_num_rows($resddd) < 1)
		{
			print "No tienes acceso a esta funcionalidad!";
			exit;
		}
	}
	function qry($a){
		$b=mysql_query($a) or die(mysql_error()."-----".$a) ;
		return $b;
	}
	
	
	function tracechange($login,$table,$idname,$id,$campo,$n,$valor){
		if($n=="M"){
			$arr=array();
			$res=qry("select $campo from $table where $idname='$id'");
			while($r=mysql_fetch_row($res)){
				$arr[]=$r[0];
			}
			$before=json_encode($arr);
			if(is_array($valor)){
				$after=json_encode($valor);
				}else{
				$taf=array();
				$res=mysql_query($valor);
				while($r=mysql_fetch_row($res)){
					$taf[]=$r[0];
				}
				$after=json_encode($taf);
			}
		}
		elseif($n=="N"){
			$before="";
			$after=$id;
		}
		else{
			$tran=mysql_fetch_row(qry("select if('$valor'=$campo,1,0) from $table where $idname='$id'"));
			if($tran[0]==0){
				$res=qry("select $campo from $table where $idname='$id'");
				$r=mysql_fetch_row($res);
				$before=$r[0];
				$after=$valor;
				}else{
				return;
			}
		}
		qry("insert into change_$table(login,hora,id,campo,tipo,bef_change,af_change) values ('$login',now(),'$id','$campo','$n','$before','$after')");
	}
	
?>
