<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	$niveles=1;
	
	
	
	function puntos($Xlogin){
		$qry1="select sum(puntos) from puntos a where login='$Xlogin' and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and estado=1";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		return $rpuna[0];
	}
	
	function comision2($Xlogin,$Xnivel,$igv,$Xfchcorte){
		
		if($Xnivel>0)
		{
			//echo 1;
			if(1==1){
				$qry1="select sum(puntos*((5)/(100*$igv))),5 from puntos a where tipo=4 and login='$Xlogin' and estado=1 and if(now()>concat(year(now()),'-',month(now()),'-',day('$Xfchcorte'),' 23:59:59'),a.hora_confirm>concat(year(now()),'-',month(now()),'-',day('$Xfchcorte'),' 23:59:59'),a.hora_confirm>concat(year(now()),'-',month(now()),'-01')) ";
				$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
				$prespro_past=mysql_fetch_row($res1);
			}
			return $descact_past=$prespro_past;
		}
	}
	
	function comision($Xlogin,$ownnivel,$socnivel,$igv){
		
		if($ownnivel>0)
		{
			$descact_past=0;
			$desc=mysql_fetch_row(qry("select nivel$ownnivel from comision_red where nivel=$socnivel"));
			$qry1="select sum(puntos*(($desc[0])/(100*$igv))) from puntos a where tipo=4 and login='$Xlogin' and estado=1 and if(now()>concat(year(now()),'-',month(now()),'-',day('$Xfchcorte'),' 23:59:59'),a.hora_confirm>concat(year(now()),'-',month(now()),'-',day('$Xfchcorte'),' 23:59:59'),a.hora_confirm>concat(year(now()),'-',month(now()),'-01')) ";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			return $descact_past=$prespro_past[0];
		} 
	}
	
	
	
	
	function hijos($log,$pa_log,$nn,$Xnivel,$comi,$iduser,$tred,$qry_day){
		$nn++;
		$qry = "select a.nombre, a.login,a.direccion,round(sum(if(c.tipo=1,c.total,0)),2),a.telefono,a.celular,'$log',a.login,b.hora_in,date(min(c.fecha)),round(sum(if(c.tipo=2,c.total,0)),2),a.apellidos,if(b.hora_in is null,'SIN INSCRIP.',if(deuda>0,'INACTIVO','ACTIVO')),day(b.hora_update),b.deuda,round(sum(if(c.tipo=1,c.comision,0)),2),round(sum(if(c.tipo=2,c.comision,0)),2),c.nivel,b.volumen,b.iduser,tt.idpatro  from (usuarios a, user_nivel b) LEFT JOIN asociadas tt on tt.idsocio=b.iduser left join comisiones_historial_puntos c on  c.idsocio_from=a.iduser and c.$qry_day and c.iduser='$iduser'  where b.login=a.login and b.hora_in is not null and a.login in (select socio from asociadas where empresa='$log')and a.login not in ($pa_log) group by a.login order by volumen desc ,nombre";
		
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
			//$tbl.="<tr $cla><td>$nn</td><td>$nombre</td><td>$r[1]</td><td>$r[19]</td><td>$r[18]</td><td>$r[3]</td><td>$r[15]</td><td>$r[10]</td><td>$r[16]</td><td>$r[5]</td><td>$r[6]</td><td>$r[12]</td><td>$r[13]</td></tr>";
			$tbl.="<tr $cla><td>$nn</td><td>$nombre</td><td>$r[1]</td><td>$r[19]</td><td>$r[18]</td><td>$r[3]</td><td>$r[10]</td><td>$r[5]</td><td>$r[6]</td><td>$r[20]</td><td>$r[12]</td><td>$r[13]</td></tr>";
			$tbl.= hijos($r[7],$pa_log,$nn,$Xnivel,$comi,$iduser,$tred,$qry_day);
		}
		return $tbl;
	}
	
	
	if(isset($time_start) and $time_start!=""){
		$qry_day="fecha between '$time_start-01' and last_day('$time_start-01')";
		}else{
		$qry_day="fecha>= DATE_FORMAT(NOW() ,'%Y-%m-01')";
	}
	$qry_day;
	
	$qry = "select a.nombre, a.login,a.direccion,round(sum(if(c.tipo=1,c.total,0)),2),a.telefono,a.celular,'',a.login,b.hora_in,date(min(c.fecha)),round(sum(if(c.tipo=2,c.total,0)),2),a.apellidos,if(b.hora_in is null,'SIN INSCRIP.',if(deuda>0,'INACTIVO','ACTIVO')),day(b.hora_update),b.deuda,round(sum(if(c.tipo=1,c.comision,0)),2),round(sum(if(c.tipo=2,c.comision,0)),2),coalesce(c.nivel,0),b.volumen,b.iduser,tt.idpatro from (usuarios a, user_nivel b) LEFT JOIN asociadas tt on tt.idsocio=b.iduser left join comisiones_historial_puntos2 c on c.idsocio_from=a.iduser and c.$qry_day and c.iduser='$Xiduser'  where b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by volumen desc ,nombre";
	
	//print "$qry <br>";+
	$tred=array();
	$res = qry($qry) or die("ERROR D: " . mysql_error());
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
		//	$tbl.="<tr $cla><td>$niveles</td><td>$nombre</td><td>$r[1]</td><td>$r[19]</td><td>$r[18]</td><td>$r[3]</td><td>$r[15]--</td><td>$r[10]</td><td>$r[16]--</td><td>$r[5]</td><td>$r[6]</td><td>$r[12]</td><td>$r[13]</td></tr>";
		$tbl.="<tr $cla><td>$niveles</td><td>$nombre</td><td>$r[1]</td><td>$r[19]</td><td>$r[18]</td><td>$r[3]</td><td>$r[10]</td><td>$r[5]</td><td>$r[6]</td><td>$r[20]</td><td>$r[12]</td><td>$r[13]</td></tr>";
		$tbl.=hijos($r[7],"'$Xlogin'",$niveles,$Xnivel,$comi,$Xiduser,$tred,$qry_day);
	}
	
	//////
	$tan="";
	FOR($i=1;$i<11;$i++){
		$tan.=",round(sum(if(d.tipo=1 and d.nivel=$i,d.total,0)),2)";
	}
	$qry = "select 1$tan from comisiones_historial d where d.$qry_day and login='$Xlogin' group by login";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		//echo $r[1]."<br />";
		$tan1="";
		FOR($i=1;$i<11;$i++){
			$tt=$i;
			$tan1.=" and nivel$i<={$r[$tt]}";
			
		}
		$bono=mysql_fetch_row(qry("select nombre from bono_autos where 1 $tan1 order by nivel desc limit 1"));
	}
	
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
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Mi Upline</h3></div>
				<div class="table-responsive">
					<table class='table'>
						<thead><th>IDSOCIO</th><th>NOMBRE</th><th>Cel.</th><th>NIVEL</th>
						</thead>
						<?php echo $tbl1;?>
					</table>
				</div>
			</div>
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Volumen de Construccion (PUNTOS: <?php echo $stot;?>)</h3></div>
				<div class="table-responsive">
					<table class='table'>
						<thead><th>IDSOCIO</th><th>NOMBRE</th><th>PROFUNDIDAD</th><th>VOLUMEN</th>
						</thead>
						<?php echo $tbl2;?>
					</table>
				</div>
			</div>
			<!--<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">3G</h3></div>
				<div class="table-responsive">
				<table class='table'>
				<thead><th>IDSOCIO</th><th>NOMBRE</th><th>DOWNLINE</th><th>DIAS</th>
				</thead>
				<?php echo $tbl3;?>
				</table>
				</div>
			</div>-->
			<form method="POST" >
				<div class="form-group">
					<label for='fchini' class="col-sm-1 control-label">
						MES :
					</label >
					<div class="col-sm-7">
						<div class="input-group date"  id="fchini">
							<input class="form-control input-sm" id="time_start" name="time_start"  type="text" value="<?php echo $time_start; ?>" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<button type="submit" class=" btn btn-info" >Mostrar</button> TU ID:<strong>
				<?php echo $Xiduser;?></strong>
			</form><br />
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Mis Socios <button id="su" type="botton" disabled  class="imprimir btn btn-info btn-xs" data-table="#tablee">Archivo Excel</button></h3></div>
				<div class="table-responsive">
					<table class='table' id='tablee'>
						<thead><th>Nivel</th>
							<th>NOMBRE</th><th>DNI</th><th>IDUSER</th><th>VOL.</th><th>AUTO.</th><th>ADIC.</th><th>Cel.</th><th>UPLINE</th><th>PATROCINADOR</th><th>NIVEL</th><th>F. CORTE</th>
						</thead>
						
						<?php echo $tbl;?>
					</table>
				</div>
			</div>
			<?php 
				if($Xnivelestado==0){
					echo "<h3>USUARIO DESACTIVADO</h3>";
				}
				elseif($Xfchcorte!="" || ($Xtipo=="VENTAS" && $Xacum>0)){
				?>
				<form method='POST' action='regnew2_red.php'>
					<input type="submit" value="Ingresar nuevo Socio">
				</div>
			</form>
			<?php 	
				}else{
				echo "<h3>USUARIO SIN NIVEL</h3>";
			}
		?>
	</body>
</html>
