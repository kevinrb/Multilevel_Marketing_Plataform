<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	$ddate = "2015-09-01";
	$duedt = explode("-", $ddate);
	$date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
	$week  = (int)date('W', $date);
	//echo "Weeknummer: " . $week;
	
	$ras=explode("-",$time_start);
	
	$me=$ras[1]+1;
	$yea="2015";
	if($me==13){
		$me="01";
		$yea="2016";
	}
	function puntos($Xlogin,$time_start=0,$time_end=0){
		$qrytime="if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now()))";
		if($time_start!=0)
		{
			$qrytime=" a.hora between '$time_start' and '$time_end' ";
		}
		$qry1="select sum(puntos) from puntos a where login='$Xlogin' and $qrytime and estado=1";
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
		$comi=0;
		$qry = "select a.nombre, a.idpersona,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'$log',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log') and a.login not in ($pa_log) group by a.login  order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$palog.=$pa_log.",'$r[7]'";
			$comi=$comi+comision_nivel2($r[1],1.18);
			//	$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			//$tbl.= hijos($r[7],$palog);
		}
		return $comi;
	}
	
	function global_comision($log){
		$comi_global=0;
		$comi_temp=0;
		$comi_tot=0;
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$cla="";
			if($r[3]=="")
			{
				$cla="class='warning'";
			}
			$comi_temp=comision($r[1],1.18);
			//$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			$comi_tot=hijos($r[7],"'$log'");
			$comi_global=$comi_global+$comi_tot+$comi_temp;
		}
		return $comi_global;
	}
	
	function hijospp($log,$pa_log,$rass){
		$comi=0;
		$mres=$rass+1;
		$yea="2015";
		if($mres==13){
			$mres="01";
			$yea="2016";
		}
		$qry = " select c.nombre,a.empresa,c.codigo1,sum(if(total>0 and hora  between '2015-$rass-5 0:0:0' and '$yea-$mres-4 23:59:59',total,0)),
		sum(if(total>0 and hora  between '2015-$rass-5 0:0:0' and '2015-$rass-12 23:59:59',1,0)) s1,
		sum(if(total>0 and hora  between '2015-$rass-13 0:0:0' and '2015-$rass-19 23:59:59',1,0)) s2,
		sum(if(total>0 and hora  between '2015-$rass-20 0:0:0' and '2015-$rass-26 23:59:59',1,0)) s3,
		sum(if(total>0 and hora  between '2015-$rass-27 0:0:0' and '$yea-$mres-4 23:59:59',1,0)) s4
		from operacionesp o, clientes c, asociadas a where a.empresa='$log' and c.codigo1 not in ($pa_log) and o.estado=107 and a.socio=c.codigo1 and o.idcliente=c.idcliente  and  o.estado=107 and c.canal='CATALOGO'
		group by  c.codigo1 ;";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$palog.=$pa_log.",'$r[2]'";
		 	$pun=puntos($r[2],"2015-$rass-5","$yea-$mres-5");
			$tbl.="<tr ><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td>$r[3]</td><td><a href='det_socio.php?login=$r[2]&time_start=2015-$rass-5&time_end=2015-$rass-13' class='btn btn-sm btn-info'>$r[4]</a></td><td><a href='det_socio.php?login=$r[2]&time_start=2015-$rass-13&time_end=2015-$rass-20' class='btn btn-sm btn-info'>$r[5]</a></td><td><a href='det_socio.php?login=$r[2]&time_start=2015-$rass-20&time_end=2015-$rass-27' class='btn btn-sm btn-info'>$r[6]</a></td><td><a href='det_socio.php?login=$r[2]&time_start=2015-$rass-27&time_end=$yea-$mres-5' class='btn btn-sm btn-info'>$r[7]</a></td><td>$pun</td></tr>";
			//	$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			$tbl.= hijospp($r[2],$palog);
		}
		return $tbl;
	}
	
	if($socio!="TODOS")
	{
		$qrylog="and c.codigo1='$socio'";
	}
	
	
	$qry = " select c.nombre,a.empresa,c.codigo1,sum(if(total>0 and hora  between '2015-$ras[1]-5 0:0:0' and '$yea-$me-4 23:59:59',total,0)),
	sum(if(total>0 and hora  between '2015-$ras[1]-5 0:0:0' and '2015-$ras[1]-12 23:59:59',1,0)) s1,
	sum(if(total>0 and hora  between '2015-$ras[1]-13 0:0:0' and '2015-$ras[1]-19 23:59:59',1,0)) s2,
	sum(if(total>0 and hora  between '2015-$ras[1]-20 0:0:0' and '2015-$ras[1]-26 23:59:59',1,0)) s3,
	sum(if(total>0 and hora  between '2015-$ras[1]-27 0:0:0' and '$yea-$me-4 23:59:59',1,0)) s4
	from operacionesp o, clientes c, asociadas a where o.estado=107 and a.socio=c.codigo1 and o.idcliente=c.idcliente  and  o.estado=107 and c.canal='CATALOGO' $qrylog
	group by  c.codigo1 ;";
	//and hora between '2015-$ras[1]-5' and '2015-$me-5' 
	
	// echo $qry;
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		$qryy=global_comision($r[2],1.18);
		$pun=puntos($r[2],"2015-$ras[1]-5","$yea-$me-5");
		$tbl.="<tr ><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td>$r[3]</td><td><a href='det_socio.php?login=$r[2]&time_start=2015-$ras[1]-5&time_end=2015-$ras[1]-13' class='btn btn-sm btn-info'>$r[4]</a></td><td><a href='det_socio.php?login=$r[2]&time_start=2015-$ras[1]-13&time_end=2015-$ras[1]-20' class='btn btn-sm btn-info'>$r[5]</a></td><td><a href='det_socio.php?login=$r[2]&time_start=2015-$ras[1]-20&time_end=2015-$ras[1]-27' class='btn btn-sm btn-info'>$r[6]</a></td><td><a href='det_socio.php?login=$r[2]&time_start=2015-$ras[1]-27&time_end=$yea-$me-5' class='btn btn-sm btn-info'>$r[7]</a></td><td>$pun</td></tr>";
		if($socio!="TODOS")
		$tbl.=hijospp($r[2],"'$r[2]'",$ras[1]);
	}
	
	
	$qry = " select login,concat(apellidos,', ',nombre) from usuarios where login>0 order by apellidos";
	//echo $qry;
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	$optlog="";
	while($r = mysql_fetch_row($res)) {
		$sel="";
		if($r[0]==$socio)
		$sel="selected";
		$optlog.="<option value='$r[0]' $sel>$r[1]</option>";
	}
	
	/* select month(hora),week(hora),concat(apellidos,', '),codigo1,count(*) from operacionesp a,clientes b,usuarios c where c.login=b.codigo1 and a.estado=107 and a.hora between '2015-09-01' and last_day('2015-09-01') and a.idcliente=b.idcliente and b.canal='CATALOGO' group by codigo1,week(a.hora);*/
	
	
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
		<script src="../js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap-datetimepicker.es.js"></script>
		<script>
			$(function(){
			//var dd=new Date();
			$('.time').datetimepicker({format: 'yyyy-mm-dd hh:ii',
			showMeridian: true,
			autoclose: true,
			language:'es',
			headTemplateV3:true,
			minView:3,
			startView:3,
			format: 'yyyy-mm'});
			$('#time_start').datetimepicker().on('changeDate', function(ev){ 
			$('#time_end').datetimepicker('setStartDate',ev.date);
			});
			
			$('#time_end').datetimepicker().on('changeDate', function(ev){
			$('#time_start').datetimepicker('setEndDate',ev.date);
			});
			$(".calendar").click(function(){
			$(this).parent().parent().children('.time').datetimepicker('show');
			});
			
			$(".deta").click(function(){
			var log=$(this).data('login');
			
			
			});
			});
			$(window).load(function(){
			$(".imprimir").click(function(){
			//alert($(this).data('table'));
			$("#excel").val("<table>"+$($(this).data('table')).html()+"</table>");
			$("#sub").submit();
			});
			
			
			});
		</script>
	</head>
	<body>
		<div class="container">
			<FORM role="form" class="form-horizontal" method="GET">
				<div class="form-group">
					<label class="col-sm-1 control-label">
						Mes:
					</label >
					<div class="col-sm-7">
						<div class="input-group">
							<input class="time form-control input-sm" id="time_start" name="time_start" type="text" value="<?php echo $time_start; ?>" readonly/>
							<span class="input-group-btn">
								<button class="btn btn-sm btn-info calendar" type="button" ><span class="glyphicon glyphicon-calendar"></span></button>
							</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-1 control-label">
						Socio:
					</label>
					<div class="col-sm-7">
						<select name="socio" id="socio" class="form-control input-sm">
							<option>TODOS</option>
							<?php echo $optlog;?>
						</select>
						<br />	
					</div>	 
					<!--	 <label class="col-sm-1 control-label">
						Al:
						</label>
						<div class="col-sm-7">
						<div class="input-group">
						<input class="time form-control input-sm" id="time_end" name="time_end" type="text" value="<?php echo $time_end; ?>" readonly/>
						<span class="input-group-btn">
						<button class="btn btn-sm btn-info calendar" type="button" ><span class="glyphicon glyphicon-calendar"></span></button>
						
						</span>
						</div>
						<br />	
					</div>-->
					<div class="text-center">
						<button type="submit" class="btn btn-sm btn-primary">Consultar</button>
						<input type="button" value="Exportar Excel" class="btn btn-sm imprimir" data-table="#tablee">
					</div>
				</div>
			</form>
			<form method="POST" action="../test3.php" id="sub">
				<input type='hidden' name="local" value="<?php echo "repin$time_start - $time_end"; ?>">
				<input id="excel" type="hidden" name="data" value="">				
			</form>
			<div class="panel panel-success">
				<div class="panel-heading">
					<h3 class="panel-title">Mis Socios</h3>
				</div>
				<table class='table ' id="tablee">
					<thead>
						<th>SOCIO</th><th>LIDER</th><th>DNI</th><th>MONTO</th><th>SEMANA 1</th><th>SEMANA 2</th><th>SEMANA 3</th><th>SEMANA 4</th><th>PUNTOS</th>
					</thead>
					<?php echo $tbl;?>
				</table>
			</div>
			<!--
				<form method='POST' action='regnew2.php'>
				<input type="submit" value="Ingresar nuevo Socio">
				</div>
				</form>
			-->
		</body>
	</html>		