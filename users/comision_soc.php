<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	/*
		$ddate = "2015-09-01";
		$duedt = explode("-", $ddate);
		$date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
	$week  = (int)date('W', $date);*/
	//echo "Weeknummer: " . $week;
	function puntos($Xlogin){
		$qrytime="if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now()))";
		if($time_start!=0)
		{
			$qrytime=" a.hora between '$time_start' and '$time_end' ";
		}
		$qry1="select sum(puntos) from puntos a where login='$Xlogin' and estado=1 and tipo=2";
		//echo $qry1;
		$res1 = qry($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		$rpuna[0] = isset($rpuna[0]) ? $rpuna[0] : 0 ;
		return $rpuna;
	}
	function comision($Xlogin,$igv,$mes0,$ano0,$mes1,$ano1){
		
		//if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now()))
		
		$mes="a.hora between '$ano0-$mes0-05' and '$ano1-$mes1-05'";
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin);
		$puntos=$puntos[0];
		$re=qry("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=qry("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r3[0]>0)
		{
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.total,0))/($igv)*($r3[0]/100),sum(if((c.categoria=2 and b.promo=0),b.total,0))/($igv)*($r3[1]/100),sum(if((b.promo=0 and c.categoria=4),b.total,0))/($igv)*($r3[2]/100),COUNT(distinct a.idop) from operacionesp a, pedido_detalle b,productos c,puntos d where d.idop=a.idop and d.tipo=1 and idcliente in ($idc) and $mes and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO'  and a.estado=107";
			//echo $qry1."<br />";
			$res1 = qry($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(0/100)) from operacionesp a, stockmovesp b,promo_puntos c,puntos d where d.idop=a.idop and d.tipo=1 and idcliente in ($idc) and $mes and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = qry($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function hijos($log,$pa_log,$mes0,$ano0,$mes1,$ano1){
		$comi=0;
		$comit=0;
		$tbl="";
		$qry = "select concat(a.nombre,' ',a.apellidos), a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'$log',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login and c.tipo=2  where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log') and a.login not in ($pa_log) group by a.login order by a.nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$palog.=$pa_log.",'$r[7]'";
			$comi=comision($r[1],1.18,$mes0,$ano0,$mes1,$ano1);
			$comit+=$comi;
			$tbl.="<tr $cla><td>$r[1]</td><td>$r[0]</td><td>$r[3]</td><td>$comi</td><td><span class='label label-info'>nivel 2</span></td></tr>";
			//$tbl.= hijos($r[7],$palog);
		}
		$arr[0]=$comit;
		$arr[1]=$tbl;
		return $arr;
	}
	
	$tbl="<thead>
	<tr><th>DNI</th><th>LIDER</th><th>PUNTOS</th><th>COMISION</th><th></th></tr>
	</thead>";	 	 
	if($_GET["socio"]!=""){
		
		$ras=explode("-",$time_start);
		$mes0=$ras[1];
		$mes1=$ras[1]+1;
		$ano0=$ras[0];
		$ano1=$ras[0]+1;
		if($mes1==13){
			$mes1="01";
			$ano1=$ras[0]+1;
		}
		
		$comi_global=0;
		$comi_temp=0;
		$comi_tot=0;
		$qry = "select concat(a.nombre,' ',a.apellidos), a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login and c.tipo=2 where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$socio') group by a.login order by a.nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$cla="";
			if($r[3]=="")
			{
				$cla="class='warning'";
			}
			$comi_temp=comision($r[1],1.18,$mes0,$ano0,$mes1,$ano1);
			$tbl.="<tr $cla><td>$r[1]</td><td>$r[0]</td><td>$r[3]</td><td>$comi_temp</td><td><span class='label label-primary'>nivel 1</span></td></tr>";
			$comi_tot=hijos($r[7],"'$r[7]'",$mes0,$ano0,$mes1,$ano1);
			$tbl.=$comi_tot[1];
			$comi_global=$comi_global+$comi_tot[0]+$comi_temp;
		}
		echo  $comi_global;
	}
	$tbl.="</tbody><tfoot><tr $cla><td>$r[1]</td><td>$r[0]</td><td>TOTAL</td><td>$comi_global</td><td></td></tr></tfoot>";
	$opt="";
	$res=qry("select b.login,b.nombre,b.apellidos from usuarios b order by b.nombre");
	while($r=mysql_fetch_row($res))
	{
		$opt.="<option value='$r[0]'>$r[1] $r[2]</option>";
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
			$("#socio").val("<?php echo $socio;?>");
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
						<div class="input-group">
							<select class="form-control input-sm" id="socio" name="socio" ><option value="">TODOS</option><?php echo $opt; ?></select>
						</div>
						<br />	
					</div>
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