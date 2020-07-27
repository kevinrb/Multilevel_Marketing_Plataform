<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	
	
	if($cambiar>0)
	{
		
		for($i=0;$i<count($id);$i++)
		{
			$tbl="";
			$m=11;
			for($j=1;$j<$m;$j++){
				$tem="com".$j;
				$temr=$$tem;
				
				$tbl.="nivel{$j}='{$temr[$i]}',";
			}
			//echo "update niveles_red set $tbl idnivel='$id[$i]' where idnivel='{$id[$i]}'"."<br />";
			
			qry("update comision_red set $tbl nivel='$id[$i]' where nivel='{$id[$i]}'");
		}
		//$idsms=posturl(array("a"=>"nivel","lim1"=>$lim1,"lim2"=>$lim2,"desc1"=>$desc1,"id"=>$id,"canal"=>"VENTALIBRE"),$XXurl_erp4);
	}
	
	if($comi>0 and $time_start!="")
	{
		$fec=$time_start;
		/////CALCULO DE COMISIONES
		
		function tree_comi($login,$jj,$fec,$monto,$idop,$log,$t){
			$jj++;
			$tbl="";
			if($t=="R"){
				$amar="b.idpatro=a.iduser";
				}else{
				$amar="b.idempresa=a.iduser";
			}
			
			$qry = "select a.iduser,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in>'2016-12-01') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3) or ('$fec' between hora_prof and hora_prof + interval 5 month and tprof=4),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)),tprof,hora_prof,if('$fec' between date(hora_in) and date(hora_in) + interval 1 month,'R','A') from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and $amar and  b.idsocio='$login' and b.fecha='$fec'";
			
			
			$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
			while($r = mysql_fetch_row($res)) {
				$tcom=0;
				if($r[1]>0){
					//echo "select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red where nivel=$jj";
					$rte=mysql_fetch_row(mysql_query("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red where nivel=$jj"));
					if($rte[0]>0){
						if($jj==1 and $r[2]==1){
							$tcom=30;
							if($r[3]==4)
							{
								$rte1=mysql_fetch_row(mysql_query("select if(hora_in>='$r[4]',1,0) from user_nivel_day where fecha='$fec' and iduser='$login' "));
								if($rte1[0]>0){
									$tcom=30;
									}else{
									$tcom=0;
								}
							}
						}
						if($jj==1 and $r[2]==2){
							$tcom=40;
						}
						if($tcom>0){
							$rte[0]=round($tcom*$monto/100,3);
							$rte[1]=$tcom;
						}
						//echo "HHH1-$jj---$r[1]-$jj($rte[1])-$r[0]--$rte[0]--($r[2])<BR />";
						$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],1,$monto,'$r[5]')";
						mysql_query($tbl);	
						
						}else{
						$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto,'$r[5]')";
						qry($tbl);				
					}
					}else{
					$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto,'$r[5]')";
					qry($tbl);
				}
				if($jj>30){
					//echo "BREAK-$jj---$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
					break;
				}
				if($r[0]!="")
				tree_comi($r[0],$jj,$fec,$monto,$idop,$log,$r[5]);
			}
			return 1;
		}
		
		///////COMISION DIARIO
		/////funcion comi
		function tree_comi2($login,$jj,$fec,$monto,$idop,$log){
			$jj++;
			$tbl="";
			$qry = "select a.iduser,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in<'2016-12-01') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3) or ('$fec' between hora_prof and hora_prof + interval 5 month and tprof=4),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)),tprof,hora_prof from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.idempresa=a.iduser and  b.idsocio='$login' and b.fecha='$fec'";
			
			$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
			while($r = mysql_fetch_row($res)){
				$tcom=0;
				if($r[1]>0){
					$rte=mysql_fetch_row(mysql_query("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red_acumu where nivel=$jj"));
					if($rte[0]>0){
						//echo "HHH2-$jj---$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
						$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],2,$monto)";
						mysql_query($tbl);	
						
						}else{
						$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,2,$monto)";
						qry($tbl);				
					}
					}else{
					$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,2,$monto)";
					qry($tbl);
				}
				
				if($jj>30){
					//echo "BREAK-$jj---$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
					break;
				}
				if($r[0]!="")
				tree_comi2($r[0],$jj,$fec,$monto,$idop,$log);
			}
			return 1;
		}
		function tree_comi_adi($login,$jj,$fec,$monto,$idop,$log,$desc){
			$jj++;
			$tbl="";
			$qry = "select a.login,a.nivel from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.empresa=a.login and  b.socio='$login' and b.fecha='$fec'";
			$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
			while($r = mysql_fetch_row($res)) {
				$tcom=0;
				if($r[1]>0){			
					$rte=mysql_fetch_row(mysql_query("select round(($desc*$monto)/100,3),$desc from comision_red where nivel=$jj"));
					if($rte[0]>0){
						//echo "---$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
						$tbl="insert ignore into comisiones_historial(fecha,login,socio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],2,$monto)";
						mysql_query($tbl);
					}
				}
			}
			return 1;
		}
		
		qry("delete from comisiones_historial_puntos where fecha between '$fec-01' and concat(last_day('$fec-01'),' 23:59:59')");
		//////genera COMISIONES
		$res=qry("select date(hora_confirm),a.iduser,a.puntos,a.idop,if(date(b.hora_in)=date(fecha_venc),'R','A') from puntos a left join  user_nivel_day b  on date(a.hora_confirm)=b.fecha and a.iduser=b.iduser where a.tipo=9 and a.estado=1 and hora_confirm between '$fec-01' and concat(last_day('$fec-01'),' 23:59:59')
		");
		while($r=mysql_fetch_row($res)){
			//echo "<br />CCCCC1----$r[0]----$r[2]---C1-$r[1]<br />";
			tree_comi($r[1],0,$r[0],$r[2],$r[3],$r[1],$r[4]);
			$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,1,'$r[2]','$r[4]')";
			$res2 = mysql_query($tbl) or die(mysql_error());
		}
		
		
		////genera comisiones adicionales
		$res=qry("select date(hora_confirm),iduser,puntos,idop from puntos where tipo=10 and estado=1  and hora_confirm between '$fec-01' and concat(last_day('$fec-01'),' 23:59:59')");
		while($r=mysql_fetch_row($res)){
			//echo "<br />CCCCC2--$r[0]----$r[2]---C2-$r[1]<br />";
			tree_comi2($r[1],0,$r[0],$r[2],$r[3],$r[1]);
			$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,2,'$r[2]')";
			$res2 = mysql_query($tbl) or die(mysql_error());
		}		
		
	}
	
	
	$qry1="select * from comision_red";
	$tbl="";
	//echo $qry1;
	$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
	while($r1=mysql_fetch_row($res1)) {
		$tbl.="<tr><td>$r1[0]</td>";
		$m=count($r1);
		for($i=1;$i<$m;$i++){
			$tbl.="<td>$r1[$i]</td>";
		}
		
		$tbl.="<td><input type='checkbox' class='chksel'/></td></tr>";
		$tbl.="<tr class='htr'><td><input name='id[]' type='hidden' value='$r1[0]'/> $r1[0]</td>";
		for($i=1;$i<$m;$i++){
			$tbl.="<td><input name='com{$i}[]' type='text' value='{$r1[$i]}'/></td>";
		}
		
		$tbl.="<td><input type='checkbox' class='chknosel' checked='checked'/></td></tr>";
	}
	
	
	
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dataTables.bootstrap.css" rel="stylesheet">
		
		
		<link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="../css/sb-admin.css" rel="stylesheet">
		<link href="../css/bootstrap-datetimepicker.min.css" rel="stylesheet">
		<title>Gestion de Niveles</title>
		<style type="text/css">
			.htr {
			display: none;
			}
			input[type=text]{
			width: 70px;
			}
		</style>
		<script src="../js/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script src="../js/moment.min.js"></script>
		<script src="../js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript">
			$(function(){
				$("tbody .chksel, tbody .chknosel").click(function(e){
					e.preventDefault()
					var jo_tr1 = $(this.parentNode.parentNode), jo_tr2;
					if($(this).hasClass("chksel"))
					jo_tr2 = jo_tr1.next();
					else
					jo_tr2 = jo_tr1.prev();
					jo_tr1.addClass("htr");
					jo_tr2.removeClass("htr");
				})
				$('#fchini').datetimepicker({
					locale:'es',
					viewMode: 'years',
					format: 'YYYY-MM'
				});
			})
		</script>
	</head>
	<body>
		<div class="container">
			<h2>Gestion de Niveles(Descuentos)</h2>
			<form method='POST' action='gesniv.php'>
				<div class="panel panel-success">
					<div class="panel-heading">Gestion de Descuentos</div>                   
					<div class="panel-body">
						<table class="table">
							<thead>
								<th>PROFUND.</th><th>BRONCE</th><th>PLATA</th><th>ORO</th><th>PLATINO</th><th>ESMERALDA</th><th>ZAFIRO</th><th>RUBI</th><th>DIAMANTE</th><th>DOBLE</th><th>TRIPLE</th>
							</thead>
							<?php echo $tbl; ?>
						</table>
					</div></div>
					<input type="hidden" value="1" name="cambiar">
					<input type="submit" value="Actualizar">
			</form>
			<form method='POST' action='gesniv.php'>
				<input type="hidden" value="1" name="comi" />
				<div class="form-group">
					<label class="col-sm-1 control-label">
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
				<input type="submit" value="RECALCULAR COMISIONES" />
			</form>
		</div>
	</body>
</html>