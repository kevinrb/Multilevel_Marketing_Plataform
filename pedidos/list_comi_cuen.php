<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	$niveles=1;
	
	if(isset($time_start)){
		
		
		
		function puntos($Xlogin){
			$qry1="select sum(puntos) from puntos a where login='$Xlogin' and if(day('2016-03-31')>4, a.hora between concat(year('2016-03-31'),'-',month('2016-03-31'),'-5') and '2016-03-31', if( month('2016-03-31')=1,a.hora between concat((year('2016-03-31')-1),'-12-5') and '2016-03-31' ,a.hora between concat(year('2016-03-31'),'-',(month('2016-03-31')-1),'-5') and '2016-03-31')) and estado=1";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$rpuna=mysql_fetch_row($res1);
			return $rpuna[0];
		}
		
		function comision($Xlogin,$Xnivel,$n,$igv,$Xfchcorte,$f_conf){
			if($Xnivel>0)
			{
				$descact_past=0;
				$desc=mysql_fetch_row(qry("select nivel$Xnivel from comision_red where nivel=$n"));
				if($desc[0]>0){
					$qry1="select if('$n'=1,if(a.hora_confirm between '$f_conf' and '$f_conf' + interval 6 month or a.hora_confirm between '$Xfchcorte' and '$Xfchcorte' + interval 1 month,sum(puntos*(($desc[0])/(100*$igv))),sum(puntos*((15)/(100*$igv)))),sum(puntos*(($desc[0])/(100*$igv)))) from puntos a where tipo=4 and login='$Xlogin' and estado=1 and date(a.hora_confirm)  between concat(year('2016-03-31'),'-',month('2016-03-31'),'-01') and '2016-03-31' ";
					$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
					$prespro_past=mysql_fetch_row($res1);
				}
				return $descact_past=$prespro_past[0];
			}
		}
		
		function comision2($Xlogin,$Xnivel,$igv,$Xfchcorte){
			
			if($Xnivel>0)
			{
				//echo 1;
				if(1==1){
					$qry1="select sum(puntos*((5)/(100*$igv))) from puntos a where tipo=6 and login='$Xlogin' and estado=1 and date(a.hora)  between concat(year('2016-03-31'),'-',month('2016-03-31'),'-01') and '2016-03-31'";
					$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
					$prespro_past=mysql_fetch_row($res1);
				}
				return $descact_past=$prespro_past[0];
			}
		}
		
		function hijos($log,$pa_log,$n,$Xnivel,$Xfchcorte,$f_conf){
			$n++;
			$temp=0;
			$qry = "select a.nombre, a.idpersona,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'$log',a.login,b.hora_in from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login where b.fecha='2016-03-31' and b.login=a.login and a.login in (select socio from asociadas where empresa='$log')and a.login not in ($pa_log) group by a.login order by nombre";
			$res = qry($qry) or die("ERROR D: " . mysql_error());
			$palog="";
			while($r = mysql_fetch_row($res)){
				//	echo "---$n---".$r[7]."<br />";
				//	echo $pa_log.=",'$r[7]'";
				$qryy=comision($r[7],$Xnivel,$n,1,$Xfchcorte,$f_conf);
				$temp+=$qryy;
				//$tbl.="<tr $cla><td $tempo>$n</td><td $tempo>$r[0]</td><td $tempo>$r[1]</td><td $tempo>$r[3]</td><td $tempo>$qryy</td><td $tempo>$r[4]</td><td $tempo>$r[5]</td><td $tempo>$r[6]</td><td $tempo>$r[8]</td></tr>";
				$tbl= hijos($r[7],$pa_log,$n,$Xnivel,$Xfchcorte,$f_conf);
				$temp+=$tbl;
			}
			return $temp;
		}
		
		function comision_auto($Xlogin,$Xnivel,$Xfchcorte,$f_conf){
			$temp=0;
			$n=1;
			$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login,b.hora_in from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login where b.fecha='2016-03-31' and b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
			$res = qry($qry) or die("ERROR D: " . mysql_error());
			while($r = mysql_fetch_row($res)) {
				//	echo "---$n---".$r[7]."<br />";
				$qryy=comision($r[7],$Xnivel,$n,1,$Xfchcorte,$f_conf);
				$temp+=$qryy;
				//$tbl.="<tr $cla><td $tempo>$n</td><td $tempo>$r[0]</td><td $tempo>$r[1]</td><td $tempo>$r[3]</td><td $tempo>$qryy</td><td $tempo>$r[4]</td><td $tempo>$r[5]</td><td $tempo>$r[6]</td><td $tempo>$r[8]</td></tr>";
				$tbl=hijos($r[7],"'$r[7]'",$n,$Xnivel,$Xfchcorte,$f_conf);
				$temp+=$tbl;
			}
			return $temp;
		}
		function comision_adic($Xlogin,$Xnivel,$Xfchcorte){
			$temp=0;
			$n=1;
			
			$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login,b.hora_in from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login where b.fecha='2016-03-31' and b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
			$res = qry($qry) or die("ERROR D: " . mysql_error());
			while($r = mysql_fetch_row($res)) {
				$qryy=comision2($r[7],$Xnivel,1,$Xfchcorte);
				$temp+=$qryy;
				//$tbl=hijos($r[7],"'$Xlogin'",$n,$Xnivel);
				//$temp+=$tbl;
			}
			return $temp;
		}
		
		
		function arbol($n,$login,$arr,$fech){
			$res=qry("select socio,fecha from asociadas_day where empresa='$login' and fecha between '$fech' and last_day('$fech')");
			while($r=mysql_fetch_row($res)){
				$arr[0][$r[1]][$r[0]]=$n;
				if(in_array($r[0],$arr[1])){
					
					}else{
					$arr[1][]=$r[0];
					if($n==1){
						$arr[2][]=$r[0];
					}
				}
				if($n==1)
				$arr[3][$r[1]][$r[0]]=1;
				$m=$n+1;
				$arr=arbol($m,$r[0],$arr,$fech);
			}
			return $arr;
		}
		
		$qry_comi=qry("select * from comision_red");
		$comi=array();
		while($r=mysql_fetch_row($qry_comi)){
			$comi[1][$r[0]]=$r[1];
			$comi[2][$r[0]]=$r[2];
			$comi[3][$r[0]]=$r[3];
			$comi[4][$r[0]]=$r[4];
			$comi[5][$r[0]]=$r[5];
		}
		
		$tan="";
		FOR($i=1;$i<11;$i++){
			$tan.=",round(sum(if(d.tipo=1 and d.nivel=$i,d.total,0)),2)";
		}
		
		$tbl="";
		$qry = "select concat(a.nombre,' ',coalesce(a.apellidos,'')),a.login,c.nombre,day(b.hora_update),date(hora_prof),b.volumen,b.tprof,round(sum(if(d.tipo=1,comision,0)),2),round(sum(if(d.tipo=2,comision,0)),2),round(sum(comision),2),a.iduser$tan from (usuarios a, user_nivel_day b,comisiones_historial_puntos d) left join niveles_red c on c.idnivel=b.nivel where d.iduser=a.iduser and a.iduser=b.iduser and hora_in>'2015-01-01' and d.fecha between '$time_start-01' and last_day('$time_start-01')  and d.fecha>='2017-05-03' and b.fecha=if(substr(curdate(),1,7)='$time_start',curdate(),last_day('$time_start-01')) group by a.login order by b.volumen desc ,a.nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			//echo $r[1]."<br />";
			$tan1="";
			FOR($i=1;$i<11;$i++){
				$tt=$i+10;
				$tan1.=" and nivel$i<={$r[$tt]}";	
			}
			$max=mysql_fetch_row(qry("select c.nombre,a.tipo from user_nivel_day a left join niveles_red c on c.idnivel=a.nivel where a.fecha = last_day('$time_start-01') and a.login='$r[1]' order by nivel desc limit 1"));
			$bono=mysql_fetch_row(qry("select bono from bono_autos where 1 $tan1 order by nivel desc limit 1"));
			$bonoadi=mysql_fetch_row(qry("select sum(comision) from comisiones_adicional where fecha between '$time_start-01' and last_day('$time_start-01') and tipo='UTILIDADES' and login='$r[1]'"));
			$bonobon=mysql_fetch_row(qry("select sum(comision) from comisiones_adicional where fecha between '$time_start-01' and last_day('$time_start-01') and tipo='BONO' and login='$r[1]'"));
			$bonocorre=mysql_fetch_row(qry("select sum(comision) from comisiones_adicional where fecha between '$time_start-01' and last_day('$time_start-01') and tipo='CORRECCION' and login='$r[1]'"));
			$tempo="style='".'mso-number-format:"\@"'."'";
			
			$qry2="select b.nombre,a.cuenta,a.cuenta_int,a.id from user_cuentas a left join bancos b on b.idbanco=a.idbanco where a.iduser='$r[10]'";
			$res2=qry($qry2);
			$cuentas="<table class='table table-bordered'>
<tbody >";
			$arrmarc=array();
			while($r2=mysql_fetch_row($res2)){
				$cuentas.="<tr><td>$r2[0]</td><td>$r2[1]</td><td>$r2[2]</td></tr>";
			}				
			$cuentas.="</tbody></table>";
			
			$qry2="select a.cuenta from user_detrac a where a.iduser='$r[10]'";
			$res2=qry($qry2);
			$detr=mysql_fetch_row($res2);
			
			$tbl.="<tr><td>$r[5]</td><td $tempo>$r[0]</td><td $tempo>$r[1]</td><td $tempo>$max[0]($max[1])</td><td $tempo>$r[3]</td><td $tempo>$r[4]</td><td $tempo>$r[6]</td><td >".($r[9]+$bono[0]+$bonoadi[0]+$bonobon[0]+$bonocorre[0])."</td><td $tempo>$cuentas</td><td>$detr[0]</td></tr>";
		}
		
		
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
		<link href="../css/bootstrap-datetimepicker.min.css" rel="stylesheet">
		<script src="../js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script src="../js/moment.min.js"></script>
		<script src="../js/bootstrap-datetimepicker.min.js"></script>
		<script>
			$(function(){
				var dd=new Date();
				dd.setTime(<?php echo $tr."000";?>-(300-dd.getTimezoneOffset())*60000);
				var dd=new Date();
				$('#fchini').datetimepicker({
					locale:'es',
					viewMode: 'years',
					format: 'YYYY-MM'
				});
				$(".det").click(function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					var fec=$(this).data("fecha");
					$.post("../res/pedidos/proc.php",{login:log,a:"det_comi2",fecha:fec,iduser:iduser},function(data){
						$("#mod_body").html(data);
					});
				});
				$(".tree").click(function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					$.post("../res/pedidos/proc.php",{login:log,a:"tree",iduser:iduser},function(data){
						$("#mod_body").html(data);
					});
				});	
				$(".venta").click(function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					$.post("../res/pedidos/proc.php",{login:log,a:"venta",iduser:iduser},function(data){
						$("#mod_body").html(data);
					});
				})
				$(".correc").click(function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					var fec=$(this).data("fecha");
					$.post("../res/pedidos/proc.php",{login:log,a:"correc",iduser:iduser,fecha:fec},function(data){
						$("#mod_body").html(data);
					});
				})
				$("body").on("click",".save_correc",function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					var fec=$(this).data("fecha");
					var montcorr=$("#moncorr").val();
					
					$.post("../res/pedidos/proc.php",{login:log,a:"mcorrec",iduser:iduser,fecha:fec,montcorr:montcorr},function(data){
						$("#mod_body").html(data);
					});
				})				
			});
			$(window).load(function() {
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
		<div class="">
			<form method="POST" >
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
				<button type="submit" class=" btn btn-info" >Mostrar</button>
			</form>
			<form method="POST" action="../test3.php" id="sub">
				<input type='hidden' name="local" value="<?php echo "rep"; ?>">
				<input id="excel" type="hidden" name="data" value=""> 
			</form>
			<button id="su" type="botton" disabled  class="imprimir btn btn-info" data-table="#tablee">Archivo Excel</button>
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Mis Socios</h3></div>
				<table class='table table-condensed' id='tablee'>
					<thead><th>VC</th>
						<th>Nombre</th><th>Login</th><th>Nivel</th><th>Corte</th><th>PROF</th><th>T. PROF</th><th>C. Total</th><th>Cuentas</th><th>Detraccion</th>
					</thead>
					
					<?php echo $tbl;?>
				</table>
			</div>
			
			
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Modal title</h4>
						</div>
						<div class="modal-body" id ="mod_body">
							...
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							
						</div>
					</div>
				</div>
			</div>
		</body>
	</html>	