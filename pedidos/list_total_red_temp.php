<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	$niveles=1;
	
	
	
	function puntos($Xlogin){
		$qry1="select sum(puntos) from puntos a where login='$Xlogin' and if(day('2016-03-31')>4, a.hora between concat(year('2016-03-31'),'-',month('2016-03-31'),'-5') and '2016-03-31', if( month('2016-03-31')=1,a.hora between concat((year('2016-03-31')-1),'-12-5') and '2016-03-31' ,a.hora between concat(year('2016-03-31'),'-',(month('2016-03-31')-1),'-5') and '2016-03-31')) and estado=1";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		return $rpuna[0];
	}
	
	function comision($Xlogin,$Xnivel,$n,$igv,$Xfchcorte){
		
		if($Xnivel>0)
		{
			$descact_past=0;
			$desc=mysql_fetch_row(qry("select nivel$Xnivel from comision_red where nivel=$n"));
			if($desc[0]>0){
				//$qry1="select sum(puntos*(($desc[0])/(100*$igv))) from puntos a where tipo=3 and login='$Xlogin' and estado=1 and if('2016-03-31'>concat(year('2016-03-31'),'-',month('2016-03-31'),'-',day('$Xfchcorte'),' 23:59:59'),a.hora>concat(year('2016-03-31'),'-',month('2016-03-31'),'-',day('$Xfchcorte'),' 23:59:59'),a.hora>concat(year('2016-03-31'),'-',month('2016-03-31'),'-01'))";
				$qry1="select sum(puntos*(($desc[0])/(100*$igv))) from puntos a where tipo=3 and login='$Xlogin' and estado=1 and a.hora between concat(year('2016-03-31'),'-',month('2016-03-31'),'-01') and concat(year('2016-03-31'),'-',month('2016-03-31'),'-',day('$Xfchcorte'),' 23:59:59')";
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
				//$qry1="select sum(puntos*((5)/(100*$igv))) from puntos a where tipo=4 and login='$Xlogin' and estado=1 and if('2016-03-31'>concat(year('2016-03-31'),'-',month('2016-03-31'),'-',day('$Xfchcorte'),' 23:59:59'),a.hora>concat(year('2016-03-31'),'-',month('2016-03-31'),'-',day('$Xfchcorte'),' 23:59:59'),a.hora>concat(year('2016-03-31'),'-',month('2016-03-31'),'-01')) ";
				$qry1="select sum(puntos*((10)/(100*$igv))) from puntos a where tipo=5 and login='$Xlogin' and estado=1 and a.hora between concat(year('2016-03-31'),'-',month('2016-03-31'),'-01') and concat(year('2016-03-31'),'-',month('2016-03-31'),'-',day('$Xfchcorte'),' 23:59:59') ";
				$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
				$prespro_past=mysql_fetch_row($res1);
			}
			return $descact_past=$prespro_past[0];
		}
	}
	
	function hijos($log,$pa_log,$n,$Xnivel,$Xfchcorte){
		$n++;
		$temp=0;
		$qry = "select a.nombre, a.idpersona,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'$log',a.login,b.hora_in from (usuarios a, user_nivel b) left join puntos c on c.login=a.login where b.login=a.login and a.login in (select socio from asociadas where empresa='$log')and a.login not in ($pa_log) group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)){
			$palog.=$pa_log.",'$r[7]'";
			$qryy=comision($r[7],$Xnivel,$n,1,$Xfchcorte);
			$temp+=$qryy;
			//$tbl.="<tr $cla><td>$n</td><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td><td>$r[8]</td></tr>";
			$tbl= hijos($r[7],$palog,$n,$Xnivel,$Xfchcorte);
			$temp+=$tbl;
		}
		return $temp;
	}
	
	function comision_auto($Xlogin,$Xnivel,$Xfchcorte){
		$temp=0;
		$n=1;
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login,b.hora_in from (usuarios a, user_nivel b) left join puntos c on c.login=a.login where b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$qryy=comision($r[7],$Xnivel,$n,1,$Xfchcorte);
			$temp+=$qryy;
			//$tbl.="<tr $cla><td>$n</td><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td><td>$r[8]</td></tr>";
			$tbl=hijos($r[7],"'$Xlogin'",$n,$Xnivel,$Xfchcorte);
			$temp+=$tbl;
		}
		return $temp;
	}
	function comision_adic($Xlogin,$Xnivel,$Xfchcorte){
		$temp=0;
		$n=1;
		
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login,b.hora_in from (usuarios a, user_nivel b) left join puntos c on c.login=a.login where b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$qryy=comision2($r[7],$Xnivel,1,$Xfchcorte);
			$temp+=$qryy;
			//$tbl=hijos($r[7],"'$Xlogin'",$n,$Xnivel);
			//$temp+=$tbl;
		}
		return $temp;
	}
	
	$tbl="";
	$qry = "select concat(a.nombre,' ',coalesce(a.apellidos,'')),a.login,b.nivel,date(b.hora_in) from usuarios a, user_nivel b where a.login=b.login order by nombre";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		$qry1="select comision from comisionesmoves a where tipo=3 and login='$r[1]' and estado=1 and ano=year('2016-03-31') and mes=month('2016-03-31') ";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		//$temp=mysql_fetch_row($res1);
		$tauto=round(comision_auto($r[1],$r[2],$r[3])+$temp[0],2);
		$qry1="select comision from comisionesmoves a where tipo=5 and login='$r[1]' and estado=1 and ano=year('2016-03-31') and mes=month('2016-03-31') ";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		//$temp=mysql_fetch_row($res1);
		$tadic=round(comision_adic($r[1],$r[2],$r[3])+$temp[0],2);
		$tot=$tauto+$tadic;
		qry("insert ignore into comisionesmoves(login,ano,mes,nivel,comision,tipo) values ('$r[1]',2015,12,'$r[2]','$tauto',3)");
		qry("insert ignore into comisionesmoves(login,ano,mes,nivel,comision,tipo) values ('$r[1]',2015,12,'$r[2]','$tadic',5)");	
		$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td>$r[3]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle' data-toggle='modal' data-target='#myModal' /></td></tr>";
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
		<script>
			$(function(){
			$(".det").click(function(){
			var log=$(this).data("login");
			$.post("../res/pedidos/proc.php",{login:log,a:"det_comi"},function(data){
			$("#mod_body").html(data);
			});
			});
			
			});
		</script>
	</head>
	<body>
		<div class="container">
			
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Mis Socios</h3></div>
				<table class='table '>
					<thead><th>Nº</th>
						<th>Nombre</th><th>Login</th><th>Nivel</th><th>Corte</th><th>C. Autoconsumo</th><th>C. Adicional</th><th>C. Total</th><th>Detalle</th>
					</thead>
					
					<?php echo $tbl;?>
				</table>
			</div>
			
			
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
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
							<button type="button" class="btn btn-primary">Save changes</button>
						</div>
					</div>
				</div>
			</div>
		</body>
	</html>	