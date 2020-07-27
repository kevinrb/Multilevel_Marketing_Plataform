<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	function puntos($Xlogin){
		$qry1="select sum(puntos) from puntos a where login='$Xlogin' and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and estado=1";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		return $rpuna[0];
	}
	
	function comision($Xlogin,$igv){
		
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin);
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r2[0]>0)
		{
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*((100-$r2[0])/(100*$igv))*($r3[0]/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*((100-$r2[1])/(100*$igv))*($r3[1]/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*((100-$r2[2])/(100*$igv))*($r3[2]/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(comision/100)) from operacionesp a, stockmovesp b,promo_puntos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function comision_nivel2($Xlogin,$igv){
		
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin);
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r2[0]>0)
		{
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*((100-$r2[0])/(100*$igv))*(1/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*((100-$r2[1])/(100*$igv))*(1/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*((100-$r2[2])/(100*$igv))*(1/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(1/100)) from operacionesp a, stockmovesp b,promo_puntos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function hijos($log,$pa_log){
		$qry = "select a.nombre, a.idpersona,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'$log',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log')and a.login not in ($pa_log) group by a.login  order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$cla="";
			if($r[3]=="")
			{
				$cla="class='warning'";
			}
			$palog.=$pa_log.",'$r[7]'";
			$qryy=comision_nivel2($r[1],1.18);
			$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			//$tbl.= hijos($r[7],$palog);
		}
		return $tbl;
	}
	
	$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		$cla="";
		if($r[3]=="")
		{
			$cla="class='warning'";
		}
		$qryy=comision($r[1],1.18);
		$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
		$tbl.=hijos($r[7],"'$Xlogin'");
	}
	
	$qry = "select a.cod_usu_sn, a.nombre, a.login,a.direccion,a.email,a.telefono,a.celular,'',a.login from usuarios_temp a where a.nivel='$Xlogin' and a.activo=1   order by nombre";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		
		$tbl1.="<tr><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td><td>$r[7]</td></tr>";
		
	}
	
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Mis Socios</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" media="screen" href="../css/bootstrap-datetimepicker.min.css">
		<script src="../js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Mis Socios</h3></div>
				<table class='table '>
					<thead>
						<th>Nombre</th><th>DNI</th><th>Puntos</th><th>Comision</th><th>Telefono</th><th>Celular</th><th>Lider</th>
					</thead>
					
					<?php echo $tbl;?>
				</table>
			</div>
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Socios por Confirmar</h3></div>
				<table class='table '>
					<thead>
						<th>Codigo</th><th>Nombre</th><th>DNI</th><th>Direccion</th><th>Correo Electronico</th><th>Telefono</th><th>Celular</th><th>Lider</th>
					</thead>
					
					<?php echo $tbl1;?>
				</table>
			</div>
			<form method='POST' action='regnew2.php'>
				<input type="submit" value="Ingresar nuevo Socio">
			</div>
		</form>
	</body>
</html>