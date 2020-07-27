<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	$niveles=1;
	
	function hijos($log,$pa_log,$nn,$Xnivel,$comi,$iduser,$tred,$qry_day){
		$nn++;
		$qry = "select a.nombre, a.login,a.direccion,round(sum(if(c.tipo=1,c.total,0)),2),a.telefono,a.celular,'$log',a.login,b.hora_in,date(min(c.fecha)),round(sum(if(c.tipo=2,c.total,0)),2),a.apellidos,if(b.hora_in is null,'SIN INSCRIP.',if(deuda>0,'INACTIVO','ACTIVO')),day(b.hora_update),b.deuda,round(sum(if(c.tipo=1,c.comision,0)),2),round(sum(if(c.tipo=2,c.comision,0)),2),c.nivel,b.volumen,b.iduser  from (usuarios a, user_nivel b) left join comisiones_historial_puntos c on  c.idsocio_from=a.iduser and c.$qry_day and c.iduser='$iduser'  where b.login=a.login and b.hora_in is not null and a.login in (select socio from asociadas where empresa='$log')and a.login not in ($pa_log) group by a.login order by volumen desc ,nombre";
		
		//print "$qry <br>";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$cla="";
			if($r[3]=="")
			{
				$cla="class='warning'";
			}
			$pa_log.=",'$r[7]'";
			
			$nomb=explode(" ",$r[0]);
			$ape=explode(" ",$r[11]);
			if($r[17]>0){
				$nn=$r[17];
				$tred[$r[17]]+=$r[15];
			}
			
			$nombre=$nomb[0]." ".$ape[0];	
			$tbl.="<tr $cla><td>$nn</td><td>$nombre</td><td>$r[1]</td><td>$r[19]</td><td>$r[18]</td><td>$r[3]</td><td>$r[15]</td><td>$r[10]</td><td>$r[16]</td><td>$r[5]</td><td>$r[6]</td><td>$r[12]</td><td>$r[13]</td></tr>";
			$tbl.= hijos($r[7],$pa_log,$nn,$Xnivel,$comi,$iduser,$tred,$qry_day);
		}
		return $tbl;
	}
	
	
	
	
	$qry = "select a.nombre, a.login,a.direccion,round(sum(if(c.tipo=1,c.total,0)),2),a.telefono,a.celular,'',a.login,b.hora_in,date(min(c.fecha)),round(sum(if(c.tipo=2,c.total,0)),2),a.apellidos,if(b.hora_in is null,'SIN INSCRIP.',if(deuda>0,'INACTIVO','ACTIVO')),day(b.hora_update),b.deuda,round(sum(if(c.tipo=1,c.comision,0)),2),round(sum(if(c.tipo=2,c.comision,0)),2),coalesce(c.nivel,0),b.volumen,b.iduser from (usuarios a, user_nivel b) left join comisiones_historial_puntos c on c.idsocio_from=a.iduser and c.$qry_day and c.iduser='$Xiduser'  where b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by volumen desc ,nombre";
	
	//print "$qry <br>";+
	$tred=array();
	//$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)){
		$cla="";
		if($r[3]==""){
			$cla="class='warning'";
		}
		$nomb=explode(" ",$r[0]);
		$ape=explode(" ",$r[11]);
		$nombre=$nomb[0]." ".$ape[0];
		if($r[17]>0){
			$niveles=$r[17];
		}
		$tbl.="<tr $cla><td>$niveles</td><td>$nombre</td><td>$r[1]</td><td>$r[19]</td><td>$r[18]</td><td>$r[3]</td><td>$r[15]</td><td>$r[10]</td><td>$r[16]</td><td>$r[5]</td><td>$r[6]</td><td>$r[12]</td><td>$r[13]</td></tr>";
		$tbl.=hijos($r[7],"'$Xlogin'",$niveles,$Xnivel,$comi,$Xiduser,$tred,$qry_day);
	}
	
	//////
	
	$qry = "select a.nombre,a.apellidos, a.iduser,a.celular,coalesce((select nombre   from niveles_red where idnivel=b.nivel),'SIN NIVEL') from usuarios a, user_nivel b, asociadas c where c.empresa=a.login and b.login=a.login and c.socio in ('$Xlogin')";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		$tbl1.="<tr><td>$r[2]</td><td>$r[0] $r[1]</td><td>$r[3]</td><td>$r[4]</td></tr>";
	}
	
	$qry = "select b.iduser,c.nombre,c.apellidos,coalesce(sum(b1.estado),0),datediff(curdate(),b.hora_in) from (asociadas a,user_nivel b,usuarios c) left join asociadas a1 on a1.idempresa=a.idsocio left join user_nivel b1 on a1.idsocio=b1.iduser and b1.hora_in between b.hora_in and b.hora_in + interval 10 day where a.idempresa='$Xiduser' and a.idsocio=b.iduser and a.idsocio=c.iduser and b.hora_in>'2017-04-18' group by  b.iduser";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		$ttr="";
		if($r[3]>1){
			$ttr="class='success'";
		}
		$tbl3.="<tr $ttr><td>$r[0]</td><td>$r[1] $r[2]</td><td>$r[3]</td><td>$r[4]</td></tr>";
	} 
	
	
	$qry = "select b.nombre,b.apellidos,a.total,b.iduser,c.volumen from detalle_volumen a, usuarios b, user_nivel c where c.iduser=b.iduser and b.iduser=a.idsocio and a.iduser ='$Xiduser' and c.estado=1 order by total desc";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	$tot=array();
	$i=0;
	while($r = mysql_fetch_row($res)){
		$ttr="";
		if($i==0){
			$ttr="class='info'";
		}
		$tbl2.="<tr $ttr><td>$r[3]</td><td>$r[0] $r[1]</td><td>$r[2]</td><td>$r[4]</td></tr>";
		$tot[]=$r[2];
		$i++;
	}
	
	$stot=0;
	for($i=1;$i<count($tot);$i++){
		$stot+=$tot[$i];
	}
	$tbl1="";
	if(isset($time_start) and $time_start!=""){
		$qry_day="fecha between '$time_start-01' and last_day('$time_start-01')";
		
		
		$tan="";
		FOR($i=1;$i<11;$i++){
			$tan.=",round(sum(if(d.tipo=1 and d.nivel=$i,d.total,0)),2)";
		}
		
		$tbl1="";
		
		$tem=5;
		$qry = "select concat(a.nombre,' ',coalesce(a.apellidos,'')),a.login,c.nombre,day(b.hora_update),date(hora_prof),b.volumen,b.tprof,round(sum(if(d.tipo=1,comision,0)),2)*$tem,round(sum(if(d.tipo=2,comision,0)),2)*$tem,round(sum(comision),2)*$tem,a.iduser$tan from (usuarios a, user_nivel_day b,comisiones_historial_puntos d) left join niveles_red c on c.idnivel=b.nivel where d.iduser=a.iduser and a.iduser=b.iduser and hora_in>'2015-01-01' and d.fecha between '$time_start-01' and last_day('$time_start-01')  and d.fecha>='2017-05-03' and b.fecha=if(substr(curdate(),1,7)='$time_start',curdate(),last_day('$time_start-01')) and a.iduser='$Xiduser' order by b.volumen desc ,a.nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			//echo $r[1]."<br />";
			$tan1="";
			FOR($i=1;$i<11;$i++){
				$tt=$i+10;
				$tan1.=" and nivel$i<={$r[$tt]}";	
			}
			$max=mysql_fetch_row(qry("select c.nombre,a.tipo from user_nivel_day a left join niveles_red c on c.idnivel=a.nivel where a.fecha = last_day('$time_start-01') and a.login='$r[1]' order by nivel desc limit 1"));
			//$bono=mysql_fetch_row(qry("select bono*$tem from bono_autos where 1 $tan1 order by nivel desc limit 1"));
			$bono=mysql_fetch_row(qry("select sum(comision)*$tem from comisiones_adicional where fecha between '$time_start-01' and last_day('$time_start-01') and tipo='AUTO' and iduser='$r[10]'"));
			$bonoadi=mysql_fetch_row(qry("select sum(comision)*$tem from comisiones_adicional where fecha between '$time_start-01' and last_day('$time_start-01') and tipo='UTILIDADES' and login='$r[1]'"));
			$bonobon=mysql_fetch_row(qry("select sum(comision)*$tem from comisiones_adicional where fecha between '$time_start-01' and last_day('$time_start-01') and tipo='BONO' and login='$r[1]'"));
			$bonocorre=mysql_fetch_row(qry("select sum(comision)*$tem from comisiones_adicional where fecha between '$time_start-01' and last_day('$time_start-01') and tipo='CORRECCION' and login='$r[1]'"));
			$tempo="style='".'mso-number-format:"\@"'."'";
			$tbl1.="<tr><td >$r[7]</td><td >$r[8]</td><td >$bono[0]</td><td >$bonobon[0]</td><td >$bonoadi[0]</td><td >$bonocorre[0]</td><td >".($r[9]+$bono[0]+$bonoadi[0]+$bonobon[0]+$bonocorre[0])."</td></tr>";
		}
		
		
		
		
		
		
		}else{
		$qry_day="fecha>= DATE_FORMAT(NOW() ,'%Y-%m-01')";
	}
	
	
	
	
	
	$tbl.="<table class='table table-bordered' id='tablee'><thead><tr><th>FECHA</th><th>PROFUND.</th><th>CODIGO</th><th>NOMBRE</th><th>TIPO</th><th>PUNTOS</th><th>%</th><th>COMISION</th></tr></thead>";
	
	$tan="";
	FOR($i=1;$i<11;$i++){
		$tan.=",round((if(d.tipo=1 and d.nivel=$i,d.total,0)),2)";
	}
	$res2=qry("select d.fecha,e.nivel,d.nivel,d.idsocio_from,concat(a.nombre,' ',coalesce(a.apellidos,'')),concat(if(d.tipo=1,'AUTOCONSUMO',if(d.tipo=2,'ADICIONAL',''))),d.total,d.porcentaje,d.comision,round((comision),2)$tan from (usuarios a, user_nivel b,comisiones_historial_puntos d) left join niveles_red c on c.idnivel=b.nivel left join user_nivel_day e on e.fecha=d.fecha and e.login=b.login where d.iduser=b.iduser and a.iduser=d.idsocio_from and b.hora_in>'2015-01-01' and d.$qry_day and  b.iduser='$Xiduser' and d.fecha>'2017-05-02' order by d.fecha");
	
	//print "select d.fecha,e.nivel,d.nivel,d.idsocio_from,concat(a.nombre,' ',coalesce(a.apellidos,'')),concat(if(d.tipo=1,'AUTOCONSUMO',if(d.tipo=2,'ADICIONAL',''))),d.total,d.porcentaje,d.comision,round((comision),2)$tan from (usuarios a, user_nivel b,comisiones_historial_puntos d) left join niveles_red c on c.idnivel=b.nivel left join user_nivel_day e on e.fecha=d.fecha and e.login=b.login where d.iduser=b.iduser and a.iduser=d.idsocio_from and b.hora_in>'2015-01-01' and d.$qry_day and  b.iduser='$Xiduser' and d.fecha>'2017-05-02' order by d.fecha" ;
	
	
	$subto=array(0,0,0,0,0,0,0,0,0,0,0);
	while($rr=mysql_fetch_row($res2)){
		$tan1="";
		FOR($i=1;$i<11;$i++){
			$tt=$i+9;
			$subto[$i]+=$rr[$tt];
		}
		$tbl.="<tr><td>$rr[0]</td><td>$rr[2]</td><td>$rr[3]</td><td>$rr[4]</td><td>$rr[5]</td><td>$rr[6]</td><td>$rr[7]</td><td>$rr[8]</td></tr>";
	}
	
	
	$res2=qry("select d.fecha,e.nivel,d.nivel,d.idsocio_from,concat(a.nombre,' ',coalesce(a.apellidos,'')),concat(if(d.tipo=1,'AUTOCONSUMO',if(d.tipo=2,'ADICIONAL',''))),d.total,d.porcentaje,d.comision,round((comision),2)$tan from (usuarios a, user_nivel b,comisiones_historial_puntos3 d) left join niveles_red c on c.idnivel=b.nivel left join user_nivel_day e on e.fecha=d.fecha and e.login=b.login where d.iduser=b.iduser and a.iduser=d.idsocio_from and b.hora_in>'2015-01-01' and d.$qry_day and  b.iduser='$Xiduser' and d.fecha>'2017-05-02' order by d.fecha");
	$subto=array(0,0,0,0,0,0,0,0,0,0,0);
	while($rr=mysql_fetch_row($res2)){
		$tan1="";
		FOR($i=1;$i<11;$i++){
			$tt=$i+9;
			$subto[$i]+=$rr[$tt];
		}
	}
	
	$vol=mysql_fetch_row(qry("select volumen from user_nivel_day where iduser='$Xiduser' and fecha=last_day('$time_start-01')"));
	if($vol[0]>0){
		$tbbl="<TR><th>VOLUMEN HISTORICO $time_start : </th><td>$vol[0]</td></tr>";
	}
	else{
		$tbbl="";
		
	}
	
	$tbl.="</table><table class='table table-bordered'><thead>$tbbl<tr><th colspan='2'>VOLUMEN POR NIVEL</th></tr><TR><th>PROFUNDIDAD</th><th>VOLUMEN (para el Bono de Auto)</th></tr></thead>";
	FOR($i=1;$i<11;$i++){
		$tbl.="<tr><td>$i</td><td>{$subto[$i]}</td></tr>";
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
		<script src="../js/moment.min.js"></script>
		<script src="../js/bootstrap-datetimepicker.min.js"></script>
		<script>
			$(window).load(function() {
			$('#fchini').datetimepicker({
			locale:'es',
			viewMode: 'years',
			format: 'YYYY-MM'
			});
			$("#gcampa").change();
			$(".imprimir").prop( "disabled", false );
			$(".imprimir").click(function(){
			//alert($(this).data('table'));
			$("#excel").val("<table>"+$($(this).data('table')).html()+"</table>");
			$("#sub").submit();
			});
			});
		</script>
	</head>
	<body>
		<div class="col-sm-12">
			<form method="POST" action="../test3.php" id="sub">
				<input type='hidden' name="local" value="<?php echo "rep"; ?>">
				<input id="excel" type="hidden" name="data" value=""> 
			</form>
			<form method="POST" >
				<div class="form-group">
					<label for='fchini' class="col-sm-1 control-label">
						MES :
					</label>
					<div class="col-sm-7">
						<div class="input-group date"  id="fchini">
							<input class="form-control input-sm" id="time_start" name="time_start"  type="text" value="<?php echo $time_start; ?>" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<button type="submit" class=" btn btn-info" >Mostrar</button>
			</form><br />			
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">COMISION</h3></div>
				<div class="table-responsive">
					<table class='table'>
						<thead><th>AUTOCONSUMO</th><th>ADICIONAL</th><th>AUTO</th><th>LOGRO</th><th>UTILIDAD</th><th>CORRECION<th>TOTAL</th></th>
						</thead>
						<?php echo $tbl1;?>
					</table>
				</div>
			</div>
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">DETALLE RESIDUAL<button id="su" type="botton" disabled  class="imprimir btn btn-info btn-xs" data-table="#tablee">Archivo Excel</button></h3></div>
				<div class="table-responsive">
					
					<?php echo $tbl;?>
				</div>
			</div>
			
		</body>
	</html>
