<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	$niveles=1;
	
	if(isset($time_start)){
		
		
		$tbl="";
		$qry = "select a.login,b.nombre,b.apellidos,d.nombre,date(a.hora_in),if(a.tprof=1,'PROF.',if(a.tprof=2,'PROF. ADIC.',if(a.tprof=2,'PROF. X9',''))),date(a.hora_prof),c.empresa,a.tipo,z.login from user_nivel_day a left join usuarios b on b.login=a.login left join asociadas_day c on c.socio=a.login and c.fecha=a.fecha left join niveles_red d on d.idnivel=a.nivel left join usuarios z on z.iduser=c.idpatro where a.fecha = '$time_start' and a.hora_in is not null  order by b.nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			//echo $r[1]."<br />";
			$tempo="style='".'mso-number-format:"\@"'."'";
			$tbl.="<tr><td $tempo>$r[0]</td><td $tempo>$r[1]</td><td $tempo>$r[2]</td><td $tempo>$r[3]</td><td $tempo>$r[4]</td><td $tempo>$r[5]</td><td $tempo>$r[6]</td><td $tempo>$r[7]</td><td $tempo>$r[9]</td><td $tempo>$r[8]</td></tr>";
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
			format: 'YYYY-MM-DD'
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
					FECHA :
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
			<table class='table ' id='tablee'>
				<thead><th>DNI</th>
					<th>NOMBRE</th><th>APELLIDOS</th><th>NIVEL</th><th>INICIO</th><th>T. PROF</th><th>F. PROF</th><th>DNI UPLINE</th><th>DNI PATRO</th><th>TIPO</th>
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