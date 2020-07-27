<?php
	$ttime=microtime(true);
	require("../sec.php");
	$ae=array("1"=>"Pedido Aprobado","2"=>"Pedido Cancelado");
	$ms="";
	if(isset($_GET["login"]))
	{ $login=$_GET["login"]; }
	$qrytime="";
	if(isset($_GET["time_start"]))
	{ $time_start=$_GET["time_start"]; }
	
	if(isset($_GET["time_end"]))
	{ $time_end=$_GET["time_end"]; }
	
	if($time_start!=0)
	{
		$qrytime=" and  a.hora between '$time_start' and '$time_end' ";
	}
	
	$res=qry("select a.idop,a.hora,a.local,a.localdst,a.login,a.estado,a.tipo,a.total,b.puntos from operacionesp a left join puntos b on a.idop=b.idop where a.idcliente in (select idcliente from clientes where codigo1='$login') and a.estado between 104 and 510 and a.estado not between 200 and 299 and a.tipo in ('PE','DV') $qrytime order by a.hora desc;");
	$html ="";
	$html2="";
	$dst="";
	$estados=array("104"=>"Pendiente Aprobacion","105"=>"Pendiente Aprobacion","106"=>"Aprobado Por Destino","504"=>"Pendiente","505"=>"Pendiente Aprobacion","506"=>"Aceptado");
	while($temp=mysql_fetch_row($res))
	{
		$dst="";
		if($temp[5]=="2")
		{
			$dst="danger";
		}
		elseif($temp[5]=="107")
		{
			$dst="success";
		}
		$html2 .= "<tr inf='$temp[0]' class='$dst'><td><a href='#' inf='$temp[0]'>$temp[0]</a></td><td>$temp[1]</td><td>$temp[3]</td><td>".$estados[$temp[5]]."</td><td>$temp[8]</td><td>$temp[7]</td><td><button inf='$temp[0]' tipo='$temp[7]' class='btn btn-success btn-sm dp'>Detalles</button></td></tr>"; 
	}
	
	
	
	
	
	$res=qry("select local,nombre from locales where grupolocal=(select grupolocal from locales where local='$Xlocal');");
	$loc="<option selected disabled>--Elija--</option>";
	if(mysql_num_rows($res)>0)
	{ while($temp=mysql_fetch_row($res)){ $loc .= "<option value='$temp[0]'>$temp[1]</option>"; } }
	
?>
<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Venta Productos</title>
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dataTables.bootstrap.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" media="screen" href="../css/bootstrap-datetimepicker.min.css">
		<script src="../js/jquery-1.10.2.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/jquery.dataTables.js"></script>
		<script src="../js/dataTables.bootstrap.js"></script>
		<script src="../js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap-datetimepicker.es.js"></script>
		<script>
			
			var ms="<?php echo $ms;?>";
			$(window).load(function(){
			var dd=new Date();
			$('.time').datetimepicker({format: 'yyyy-mm-dd hh:ii',
			showMeridian: true,
			autoclose: true,
			language:'es',
			endDate:dd,
			headTemplateV3:true,
			minView:0});
			$('#time_start').datetimepicker().on('changeDate', function(ev){ 
			$('#time_end').datetimepicker('setStartDate',ev.date);
			});
			
			$('#time_end').datetimepicker().on('changeDate', function(ev){
			$('#time_start').datetimepicker('setEndDate',ev.date);
			});
			$(".calendar").click(function(){
			$(this).parent().parent().children('.time').datetimepicker('show');
			});	 
			if(ms!=""){$(".container").prepend("<div style='top:2px;' class='text-center alert alert-warning'><b>"+ms+"</b></div>");}
			setTimeout(function(){$(".alert").fadeOut();},3000);
			///////////////////////////////////////////MAKING A REAL TABLE RESPONSIVE ///////////////////////////////////////
			if (screen.width < 500){$('.datatab').parent().css({ overflowX: 'scroll'});} 
			window.onresize = resize;
			function resize(){
			var w1=$('.datatab').parent().width(); var w2=$('.datatab').width();
			if(w1<w2){$('.datatab').parent().css({ overflowX: 'scroll'});}
			else if(w1>=w2){$('.datatab').parent().removeAttr("style");}
			}
			var w1=$('.datatab').parent().width(); var w2=$('.datatab').width();
			if(w1<w2){$('.datatab').parent().css({ overflowX: 'scroll'});}
			else if(w1==w2){$('.datatab').parent().removeAttr("style");}
			//$('#datatab').dataTable();
			$('.datatab').dataTable({"order": [[ 0, "desc" ]],"oLanguage": {"sUrl": "../css/spanish.txt"}});
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$("#filtra").click(function(){
			$.post("../res/productos/admin_local.php",{a:"fd",l:$("#loc").val(),hi:$("#hi").val(),hf:$("#hf").val()},function(data){$("#tabdata").html(data);});
			});	 
			$(".dp").click(function(){
			$.post("../res/pedidos/venta_canales.php",{a:"detalle_pedido",idop:$(this).attr("inf")},function(data){$("#cm").html(data); $("#myModal").modal();});
			});
			
			
			$(".imprimir").click(function(){
			//alert($(this).data('table'));
			$("#excel").val("<table>"+$($(this).data('table')).html()+"</table>");
			$("#sub").submit();
			});
			});
			
		</script>
		<style>
			.nomargin{
			padding-left:0px;
			padding-right:0px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<FORM role="form" class="form-horizontal" method="GET">
				<input type='hidden' name="login" value="<?php echo $login; ?>">
				<div class="form-group">
					<label class="col-sm-1 control-label">
						Del:
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
						Al:
					</label>
					<div class="col-sm-7">
						<div class="input-group">
							<input class="time form-control input-sm" id="time_end" name="time_end" type="text" value="<?php echo $time_end; ?>" readonly/>
							<span class="input-group-btn">
								<button class="btn btn-sm btn-info calendar" type="button" ><span class="glyphicon glyphicon-calendar"></span></button>
								
							</span>
						</div>
						<br />	
					</div>
					<div class="text-center">
						<button type="submit" class="btn btn-sm btn-primary">Consultar</button>
						<input type="button" value="Exportar Excel" class="btn btn-sm imprimir btn-info" data-table="#tablee">
						<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-sm ">Regresar</a>
					</div>
				</div>
			</form>
			<form method="POST" action="../test3.php" id="sub">
				<input type='hidden' name="local" value="<?php echo "repin$time_start - $time_end"; ?>">
				<input id="excel" type="hidden" name="data" value=""> 
				
			</form>
			<div class="row">
				<div class="panel panel-success">
					<div class="panel-heading">Lista de Pedidos</div>                     
					<div class="panel-body">
						<table class="table table-bordered table-hover datatab" id="tablee">
							<thead><tr><th>Operacion</th><th>Hora</th><th>Almacen</th><th>Estado</th><th>PUNTOS</th><th>TOTAL</th><th>Detalles</th></tr></thead>
							<tbody><?php echo $html2;?></tbody>
						</table>
					</div>       
				</div>
			</div>
			
		</div>
		
		
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Detalle Pedido</h4>
					</div>
					<div id='cm' class="modal-body"></div>
				</div>
			</div>
		</div>
	</body>
</html>
<?php //echo microtime(true)-$ttime;
	$ttro= microtime(true)-$ttime;
	$ttt=$ttro;
	echo $ttt;
?>