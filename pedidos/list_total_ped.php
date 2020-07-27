<?php
	include "../sec.php";
	include "../ws/func2.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	$niveles=1;
	
	if(isset($time_start)){
		$tbl="";
		$fech=mysql_fetch_row(qry("select  '$time_start-01',last_day('$time_start-01')"));
		//echo $fech[0]."----".$fech[1];
		$arrp=upda_ped($fech[0],$fech[1]);
		//print_r($arrp);
		foreach($arrp as $a=>$b ){
			$bb=explode("----",$b);
			$tbl.="<tr><td>$a</td><td>$bb[0]</td><td>$bb[1]</td>";
			$tempo=mysql_fetch_row(qry("select if(b.tipo=4,'AUTOCON.','ADICIONAL'),b.puntos,b.login,a.local from operacionesp a, puntos b  where a.idop=b.idop and a.idpadre2=$a and b.tipo in (4,6) limit 1"));
			$tbl.="<td>$tempo[3]</td><td>$tempo[1]</td><td>$tempo[0]</td><td>$tempo[2]</td></tr>";
		}
		
		
		
		/*$qry = "select concat(a.nombre,' ',coalesce(a.apellidos,'')),a.login,c.nombre,date(b.hora_in),date(hora_prof),b.tipo,b.tprof,round(sum(if(d.tipo=1,comision,0)),2),round(sum(if(d.tipo=2,comision,0)),2),round(sum(comision),2)$tan from (usuarios a, user_nivel b,comisiones_historial d) left join niveles_red c on c.idnivel=b.nivel where d.login=a.login and a.login=b.login and hora_in>'2015-01-01' and d.fecha between '$time_start-01' and last_day('$time_start-01') group by a.login order by a.login";
			$res = qry($qry) or die("ERROR D: " . mysql_error());
			while($r = mysql_fetch_row($res)) {
			//echo $r[1]."<br />";
			$tan1="";
			FOR($i=1;$i<11;$i++){
			$tt=$i+9;
			$tan1.=" and nivel$i<={$r[$tt]}";	
			}
			$bono=mysql_fetch_row(qry("select bono from bono_autos where 1 $tan1 order by nivel desc limit 1"));
			$bonoadi=mysql_fetch_row(qry("select sum(comision) from comisiones_adicional where fecha between '$time_start-01' and last_day('$time_start-01') and tipo='UTILIDADES' and login='$r[1]'"));
			
			
			$tempo="style='".'mso-number-format:"\@"'."'";
			$tbl.="<tr><td>$r[5]</td><td $tempo>$r[0]</td><td $tempo>$r[1]</td><td $tempo>$r[2]</td><td $tempo>$r[3]</td><td $tempo>$r[4]</td><td $tempo>$r[6]</td><td >$r[7]</td><td >$r[8]</td><td >$bono[0]</td><td >$bonoadi[0]</td><td >".($r[9]+$bono[0]+$bonoadi[0])."</td><td $tempo><input type='button' class='det btn btn-sm' data-login='$r[1]' data-fecha='$time_start-01' value='Det.' data-toggle='modal' data-target='#myModal' /> <input type='button' class='tree btn btn-sm' data-login='$r[1]' value='Arb.' data-toggle='modal' data-target='#myModal' /> <input type='button' class='venta btn btn-sm' data-login='$r[1]' value='Vent.' data-toggle='modal' data-target='#myModal' /></td></tr>";
		}*/
		
		
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
			var fec=$(this).data("fecha");
			$.post("../res/pedidos/proc.php",{login:log,a:"det_comi2",fecha:fec},function(data){
			$("#mod_body").html(data);
			});
			});
			$(".tree").click(function(){
			var log=$(this).data("login");
			$.post("../res/pedidos/proc.php",{login:log,a:"tree"},function(data){
			$("#mod_body").html(data);
			});
			});	
			$(".venta").click(function(){
			var log=$(this).data("login");
			$.post("../res/pedidos/proc.php",{login:log,a:"venta"},function(data){
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
		<div class="container">
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
				<div class="panel-heading"><h3 class="panel-title">VENTAS</h3></div>
				<table class='table ' id='tablee'>
					<thead><th>IDOP</th>
						<th>HORA</th><th>COBRADO</th><th>LOCAL</th><th>COMISIONABLE</th><th>TIPO</th><th>SOCIO</th>
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
							<button type="button" class="btn btn-primary">Save changes</button>
						</div>
					</div>
				</div>
			</div>
		</body>
	</html>	