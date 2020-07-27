<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	$ddate = "2015-09-01";
	$duedt = explode("-", $ddate);
	$date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
	$week  = (int)date('W', $date);
	//echo "Weeknummer: " . $week;
	
	if(isset($_GET["time_start"]) && $_GET["empresa"]==""){
		$ras=explode("-",$time_start);
		
		$me=$ras[1]+1;
		$yea=$ras[0];
		if($me==13){
			$me="01";
			$yea=$ras[0]+1;
		}
		
		$tbl="<thead>
		<tr><th>DNI</th><th>LIDER</th><th>COMISION</th></tr>
		</thead>";	 	 
		$qry = "select a.empresa,nombre, comis-tcomi,comis-comis_a from (select empresa,a.login, b.nombre, sum(comi) comis,sum(comi_a) comis_a from (select b.empresa,a.login,sum(puntos) t,if(sum(puntos)>1199,450,if(sum(puntos)>599,180,if(sum(puntos)>199,45,0))) comi,if(sum(if(hora<'$time_start-05',puntos,0))>1199,450,if(sum(if(hora<'$time_start-05',puntos,0))>599,180,if(sum(if(hora<'$time_start-05',puntos,0))>199,45,0))) comi_a from puntos a, asociadas b  where a.login=socio and   a.tipo=2 and a.estado=1 and hora<'$yea-$me-05' group by a.login) a, usuarios b where a.empresa=b.login group by a.empresa) a left join (select empresa,sum(comision) tcomi from comi_afi a, asociadas b  where a.login=socio and  estado=1 group by empresa) c on c.empresa=a.empresa ";
		//echo $qry;
		//and hora between '2015-$ras[1]-5' and '2015-$me-5' 
		
		// echo $qry;
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			
			$tbl.="<tr ><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td></tr>";
		}
	}
	elseif($_GET["empresa"]!=""){
		$ras=explode("-",$time_start);
		
		$me=$ras[1]+1;
		$yea=$ras[0];
		if($me==13){
			$me="01";
			$yea=$ras[0]+1;
		}
		$tbl="<thead>
		<tr><th>DNI</th><th>SOCIO</th><th>PUNTOS</th><th>COMISION MENSUAL</th><th>COMISION ANTERIOR</th></tr>
		</thead>";		
		$qry="select c.login,concat(c.nombre,' ',c.apellidos),sum(puntos) t,((if(sum(puntos)>1199,450,if(sum(puntos)>599,180,if(sum(puntos)>199,45,0)))) - (if(sum(if(hora<'$time_start-05',puntos,0))>1199,450,if(sum(if(hora<'$time_start-05',puntos,0))>599,180,if(sum(if(hora<'$time_start-05',puntos,0))>199,45,0))))) comi,if(sum(if(hora<'$time_start-05',puntos,0))>1199,450,if(sum(if(hora<'$time_start-05',puntos,0))>599,180,if(sum(if(hora<'$time_start-05',puntos,0))>199,45,0))) comi_a from (puntos a, asociadas b) left join usuarios c on c.login=a.login where a.login=socio and a.tipo=2 and a.estado=1 and hora<'$yea-$me-05' and b.empresa='$empresa' group by a.login";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		//echo $qry;
		while($r = mysql_fetch_row($res)) {
			$tbl.="<tr ><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td>$r[3]</td><td>$r[4]</td></tr>";
		}
	}
	$opt="";
	$res=qry("select b.login,b.nombre,b.apellidos from asociadas a, usuarios b where a.empresa=b.login group by a.empresa order by b.nombre");
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
			$("#empresa").val("<?php echo $empresa;?>");
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
							<select class="form-control input-sm" id="empresa" name="empresa" ><option value="">TODOS</option><?php echo $opt; ?></select>
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