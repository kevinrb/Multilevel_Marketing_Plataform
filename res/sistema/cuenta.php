<?php
	require_once("../../sec.php");
	
	if(isset($_GET["idc"])){
		$idc=$_GET["idc"];
		$sql=qry("select a.fecha,a.login,a.monto,a.detalles,a.valido,b.nombre,b.banco FROM cuentasmove a, cuentas b WHERE a.idcuenta=b.idcuenta AND b.idcuenta='$idc'");
		$cant=mysql_num_rows($sql);
		$sum=0;
		while($res=mysql_fetch_row($sql)){
			$name=$res[5];
			if($res[4]==0){$cl="danger"; $val="NO";}
			else{$cl=""; $val="SI";}
			$dd.='<tr class="first '.$cl.'"><td>'.$res[0].'</td><td>'.$res[1].'</td><td>S/. '.$res[2].'</td><td>'.$res[3].'</td><td>'.$val.'</td></tr>';
			$sum=$sum+$res[2];
		}
		$data=$dd.'<tr class="success"><td>TOTAL S/.</td><td></td><td>'.$sum.'</td><td></td><td></td></tr>';
	}
	
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $name;?></title>
		<link href="../../sb/css/bootstrap.min.css" rel="stylesheet">
		<link href="../../sb/font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="../../sb/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
		<link href="../../sb/css/sb-admin.css" rel="stylesheet">
		<script src="../../sb/js/jquery-1.10.2.js"></script>
		<script src="../../sb/js/bootstrap.min.js"></script>
		<script src="../../sb/js/plugins/metisMenu/jquery.metisMenu.js"></script>
		<script src="../../sb/js/plugins/dataTables/jquery.dataTables.js"></script>
		<script src="../../sb/js/plugins/dataTables/dataTables.bootstrap.js"></script>
		<script src="../../sb/js/sb-admin.js"></script>
		<link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../../css/datetimepicker.css"> 
		<script src='../../js/bootstrap-datetimepicker.min.js'></script>
		<script src='../../js/bootstrap-datetimepicker.es.js'></script>
		<script>
			$(document).ready(function(){
				if(<?php echo $cant;?>==0){$('#megac').html('<h4><span class="label label-danger">NO HAY DEPOSITOS EN ESTA CUENTA</span></h4>');}
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
				$('.datatab').dataTable({"oLanguage": {"sUrl": "../../css/spanish.txt"}});
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/*
					$("#datetime").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
					autoclose: true,
					language:'es',minView:2,
					startDate:'-1y',
					endDate:'+0d'
				});*/
				
				
				$('body').delegate('#search','click',function(){
					var ff=$("#datetime").val();
					if(ff==""){//alert("Elija una fecha");
						$("#datetime").focus();
					return false;}
					
					var idc=$("#idct").val();
					$.post("proc.php",{a:"reporte",idc:idc,ff:ff},function(data){
						$('#back').prop('disabled', false);
						$('#contenido').empty(); $('#contenido').append('<tr class="success"><td>FECHA</td><td>LOGIN</td><td>MONTO (S/.)</td><td>DETALLES</td><td>VALIDO</td></tr>'+data);
					});
				});
				
				$('body').delegate('#back','click',function(){
					var idc=$("#idct").val();
					$.post("proc.php",{a:"reporte",idc:idc,tar:"back"},function(data){
						$('#back').prop('disabled', true);
						$('#contenido').empty(); $('#contenido').append('<tr class="success"><td>FECHA</td><td>LOGIN</td><td>MONTO (S/.)</td><td>DETALLES</td><td>VALIDO</td></tr>'+data);
					});
				});
				
			});
		</script>
	</head>
	<body>
		<input type="hidden" id="idct" value="<?php echo $_GET["idc"];?>">
		<div class="container" id="megac">
			
			<div class="row">
				<!--
					<div class="form-group col-xs-11">
					<h4><span class="label label-info">VER DESDE :</span></h4>
					<div class="input-group">
					<input class="datac form-control" id="datetime" name="datetime" placeholder="CLICK AQUI" readonly>
					<span class="input-group-btn">
					<button class="btn btn-default" id="search" type="button"><span class="glyphicon glyphicon-search"></span></button>
					<button class="btn btn-default" id="back" type="button" disabled><span class="glyphicon glyphicon-chevron-left"></span></button>
					</span>
					</div>
					
				</div>-->
				
				
				
				<div class="form-group col-xs-11">
					<h5><span class="label label-primary">NOMBRE DE LA CUENTA : <?php echo $name;?></span></h5>
				</div>
				
			</div>
			
			<div class="row">
				<div class="panel panel-success">
					<div class="panel-heading">DEPOSITOS</div>                     
					<div class="panel-body">
						<div class="tabcont">
							<table class="table table-bordered datatab" >
								<thead><tr class="success"><th>FECHA</th><th>LOGIN</th><th>MONTO (S/.)</th><th>DETALLES</th><th>VALIDO</th></tr></thead>
								<tbody><?php echo $data;?></tbody>
							</table>
						</div> 
					</div>                   
				</div>
			</div>
			
		</div>	
	</body>
</html>