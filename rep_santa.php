<?php
	require("sec.php");
	
	if(isset($time_start)){
		
		IF($gcampa==1){
			
			$qi = qry("select c.idprod,c.nombre,sum(b.cantidad) n from puntos a,stockmovesp b,productos c where c.idprod=b.codigo1 and a.estado=1 and a.hora_confirm>='2017-04-01' and  a.idop=b.idop and a.hora_confirm between '$time_start' and '$time_end 23:59:59' and a.tipo in (9,10) group by b.idprod order by n desc ");	
			$table="<table class='table table-hover table-bordered' id='tablee'><thead><tr><th>IDPROD</th><th>PRODUCTO</th><th>CANTIDAD</th></tr></thead>";
			while($r = mysql_fetch_row($qi))
			{
				$table.="<tr>";
				$n=count($r);
				for($i=0;$i<$n;$i++){
					$table.="<td>$r[$i]</td>"; 			
				}
				$table.="</tr>";
			}
			$table.="</table>";
		}
		IF($gcampa==2){
			
			$qi = qry("select c.idprod,c.nombre,sum(b.cantidad*b.precio) n from puntos a,stockmovesp b,productos c where c.idprod=b.codigo1 and a.estado=1 and a.hora_confirm>='2017-04-01' and  a.idop=b.idop and a.hora_confirm between '$time_start' and '$time_end 23:59:59'  and a.tipo in (9,10) group by b.idprod order by n desc ");	
			$table="<table class='table table-hover table-bordered' id='tablee'><thead><tr><th>IDPROD</th><th>PRODUCTO</th><th>MONTO</th></tr></thead>";
			while($r = mysql_fetch_row($qi))
			{
				$table.="<tr>";
				$n=count($r);
				for($i=0;$i<$n;$i++){
					$table.="<td>$r[$i]</td>"; 			
				}
				$table.="</tr>";
			}
			$table.="</table>";
		}
		IF($gcampa==3){
			
			$qi = qry("select c.departamento,c.provincia,c.distrito,count(*) n from puntos a, usuarios b, ubigeo c where a.iduser=b.iduser and c.idpais=b.idpais and b.ubigeo=c.id and a.estado=1 and  a.hora_confirm between '$time_start' and '$time_end 23:59:59' and a.tipo in (4,6) group by b.ubigeo order by n desc ");	
			$table="<table class='table table-hover table-bordered' id='tablee'><thead><tr><th>DEP.</th><th>PROV.</th><th>DIST.</th><th>CANT</th></tr></thead>";
			while($r = mysql_fetch_row($qi))
			{
				$table.="<tr>";
				$n=count($r);
				for($i=0;$i<$n;$i++){
					$table.="<td>$r[$i]</td>"; 			
				}
				$table.="</tr>";
			}
			$table.="</table>";
		}
		IF($gcampa==4){
			
			$qi = qry("select c.departamento,c.provincia,c.distrito,SUM(puntos) n from puntos a, usuarios b, ubigeo c where a.iduser=b.iduser and c.idpais=b.idpais and b.ubigeo=c.id and a.estado=1 and a.hora_confirm between '$time_start' and '$time_end 23:59:59' and a.tipo in (4,6) group by b.ubigeo order by n desc ");	
			$table="<table class='table table-hover table-bordered' id='tablee'><thead><tr><th>DEP.</th><th>PROV.</th><th>DIST.</th><th>MONTO</th></tr></thead>";
			while($r = mysql_fetch_row($qi))
			{
				$table.="<tr>";
				$n=count($r);
				for($i=0;$i<$n;$i++){
					$table.="<td>$r[$i]</td>"; 			
				}
				$table.="</tr>";
			}
			$table.="</table>";
		}
		IF($gcampa==5){
			
			$qi = qry("select c.departamento,c.provincia,c.distrito,count(*) n from puntos a, usuarios b, ubigeo c,operacionesp d  where d.idop=a.idop and d.iddircli>0 and  a.iduser=b.iduser and c.idpais=b.idpais and b.ubigeo=c.id and a.estado=1 and  a.hora_confirm between '$time_start' and '$time_end 23:59:59' and a.tipo in (4,6) group by b.ubigeo order by n desc ");	
			$table="<table class='table table-hover table-bordered' id='tablee'><thead><tr><th>DEP.</th><th>PROV.</th><th>DIST.</th><th>CANT</th></tr></thead>";
			while($r = mysql_fetch_row($qi))
			{
				$table.="<tr>";
				$n=count($r);
				for($i=0;$i<$n;$i++){
					$table.="<td>$r[$i]</td>"; 			
				}
				$table.="</tr>";
			}
			$table.="</table>";
		}
		IF($gcampa==6){
			
			$qi = qry("select c.departamento,c.provincia,c.distrito,sum(f.unidades*e.cantidad) n from puntos a, usuarios b, ubigeo c,operacionesp d,stockmovesp e, productos f  where f.idprod=e.codigo1 and e.idop=d.idop and  d.idop=a.idop and d.iddircli>0 and  a.iduser=b.iduser and c.idpais=b.idpais and b.ubigeo=c.id and a.estado=1 and  a.hora_confirm between '$time_start' and '$time_end 23:59:59' and a.tipo in (4,6) group by b.ubigeo order by n desc ");	
			$table="<table class='table table-hover table-bordered' id='tablee'><thead><tr><th>DEP.</th><th>PROV.</th><th>DIST.</th><th>PESO(g.)</th></tr></thead>";
			while($r = mysql_fetch_row($qi))
			{
				$table.="<tr>";
				$n=count($r);
				for($i=0;$i<$n;$i++){
					$table.="<td>$r[$i]</td>"; 			
				}
				$table.="</tr>";
			}
			$table.="</table>";
		}
	}
	
	//$qi = qry("SELECT id,nombre from grupocarga where estado in (1) order by nombre");
	$moti="<label class='col-sm-1 control-label' for='gcampa'>REPORTE:</label><div class='col-sm-4'><select class='form-control input-sm' id='gcampa' name='gcampa' required><option value='0' disabled selected>-seleccione-</option>";
	
	$moti.="<option value='1'>PRODUCTOS X UNIDADES</option><option value='2'>PRODUCTOS X DINERO</option><option value='3'>ZONA GEOGRAFICA X PEDIDOS</option><option value='4'>ZONA GEOGRAFICA X DINERO</option><option value='5'>DELIVERY X CIUDAD</option><option value='6'>DELIVERY X PESO</option>";
	$moti.="</select></div>";	
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Historico</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="css/jquery.dataTables.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">
		<style>
			
		</style>
		<script src="js/jquery-1.10.1.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/moment.min.js"></script>
		<script src="js/bootstrap-datetimepicker1.min.js"></script>
		<script>
			function xhr(){
			if(window.XMLHttpRequest){
			return new XMLHttpRequest();
			}
			else if(window.ActiveXObject){
			return new ActiveXObject("Microsoft.XMLHTTP");
			}
			}
			function reporte(myAudioin){
			var peticion = xhr();
			peticion.onreadystatechange = function () {
			if(peticion.readyState == 4){			
			var data = eval('(' + peticion.responseText + ')');
			
			
			
			setTimeout(info_llamada_p(myAudioin),1);
			}
			}
			peticion.open("POST","ajax/ajax_reportes.php",true);
			peticion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			peticion.send("action=info_llamada");
			}
			$(function(){
			
			
			
			$(".data").click(function(){
			var idop=$(this).data('idop');
			var tip=$(this).data('tipo');
			$.post("ajax/interfaz_ajaxnew_rec.php",{idop:idop,tipo:"datauser",tip:tip},function(data){
			
			$("#tabdata").html(data);
			
			});
			});	
			
			var dd=new Date();
			$('#fchini').datetimepicker({
			maxDate:dd
			
			});
			
			$('#fchfin').datetimepicker({
			
			maxDate:dd
			});
			
			$('#fchini').datetimepicker().on('dp.change', function(e){ 
			$('#fchfin').data("DateTimePicker").setMinDate(e.date);
			});
			
			$('#fchfin').datetimepicker().on('dp.change', function(e){
			$('#fchini').data("DateTimePicker").setMaxDate(e.date);
			});	
			});
			$(window).load(function(){
			$("#exp").show();
			$(".imprimir").click(function(){
			
			$("#excel").val("<table>"+$($(this).data('table')).html()+"</table>");
			$("#sub").submit();
			});
			
			
			});
			
			
		</script>
		
		
	</head>
	<body class="text-center">
		
		
		
		<div class="row text-center">
			<div class="col-sm-10 ">
				<FORM role="form" class="form-horizontal" method="GET">
					<div class="form-group">
						<?php echo $moti;?>
						<div class='col-sm-4'></div>
					</div>		
					<div class="form-group">
						<label class="col-sm-1 control-label">
							Del:
						</label >
						<div class="col-sm-7">
							<div class="input-group" id="fchini">
								<input class="time form-control input-sm" id="time_start" name="time_start" type="text" data-date-format="YYYY-MM-DD" value="<?php echo $time_start; ?>" readonly/>
								<span class="input-group-btn">
									<button class="btn btn-sm btn-info calendar" type="button" ><span class="glyphicon glyphicon-calendar"></span></button>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">			 
						<label class="col-sm-1 control-label">
							Al:
						</label>
						<div class="col-sm-7">
							<div class="input-group"  id="fchfin">
								<input class="time form-control input-sm" id="time_end" name="time_end" type="text" data-date-format="YYYY-MM-DD" value="<?php echo $time_end; ?>" readonly/>
								<span class="input-group-btn">
									<button class="btn btn-sm btn-info calendar" type="button" ><span class="glyphicon glyphicon-calendar"></span></button>
									
								</span>
							</div>
							<br />	
						</div>
						<div class="text-center">
							<button type="submit" class="btn btn-sm btn-primary">Consultar</button>
							<input type="button" value="Exportar Excel" class="btn btn-sm imprimir" data-table="#tablee">
						</div>
					</div>
				</form>
				<form method="POST" action="test3.php" id="sub">
					<input type='hidden' name="local" value="<?php echo "repin$time_start - $time_end"; ?>">
					<input id="excel" type="hidden" name="data" value=""> 
					
				</form>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1" id="contab">
				<?php echo $table;?>
			</div>
		</div>
		
		<div>
			
		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Datos del cliente</h4>
					</div>
					<div class="modal-body">
						<div class="table-responsive" id="tabdata">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
