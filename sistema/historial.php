<?php 
	require_once("../sec.php");
	$sql=qry("SELECT idcuenta,banco,nombre FROM cuentas");
	$aa="";
	while($res=mysql_fetch_row($sql)){
		$aa.='<tr class="more" idcuenta='.$res[0].'><td>'.$res[1].'</td><td>'.$res[2].'</td><td>'.$res[0].'</td></tr>';
	}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Gestion de Cuentas</title>
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dataTables.bootstrap.css" rel="stylesheet">
		<script src="../js/jquery-1.10.2.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/jquery.dataTables.js"></script>
		<script src="../js/dataTables.bootstrap.js"></script>
		<link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="../css/sb-admin.css" rel="stylesheet">
		<script>
			$(document).ready(function(){
				
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
				$('.datatab').dataTable({"oLanguage": {"sUrl": "css/spanish.txt"}});
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				//////////////////////////////////////////////////////////////////////////////////////
				$('body').delegate('.more','click',function(){
					var idc=$(this).attr("idcuenta");
					var ppp=window.open('../res/sistema/cuenta.php?idc='+idc+'','_blank ','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
					ppp.focus();
				});
				////////////////////////////////////
				$("#formnewc").submit(function(event){
					event.preventDefault();
					/*
						var patt = new RegExp("(\\D|\\s)");
						if(patt.test($("#idcc").val())==true){$("#idcc").val("").attr("placeholder","SOLO NUMEROS"); return false;}
					*/
					data=new Array();
					$(".datac").each(function(){ data.push($(this).val()); });
					$.post("../res/sistema/proc.php",{a:"newc",data:data},function(data){
						if(data=="1"){alert("Cuenta Guardada"); location.href="historial.php";}
					});
				});
				////////////////////////////////////
				
			});
		</script>	
	</head>
	<body>
		<div class="container">
			
			<div class="row">
				<div class="panel panel-success">
					<div class="panel-heading">Gestion De Depositos <div class="col-sm-2 pull-right"><button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modnewcc">Crear Nueva Cuenta</button></div></div> 
					<div class="panel-body">
						<div class="tabcont">
							<table class="table table-bordered datatab" >
								<thead><tr><th>BANCO</th><th>NOMBRE DE LA CUENTA</th><th>NRO DE CUENTA</th></tr></thead>
								<tbody><?php echo $aa;?></tbody>
							</table>
						</div> 
					</div> 
				</div>
			</div>
			
			<!-- Modal -->
			<div class="modal fade" id="modnewcc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabel">CREACION DE CUENTAS</h4>
						</div>
						<form id="formnewc" role="form">
							<div class="modal-body">
								
								<div class="form-group ">
									<label class="text-center">NRO. De Cuenta</label>
									<input class="datac form-control" id="idcc" placeholder="123456" required>
								</div>
								
								<div class="form-group ">
									<label class="text-center">BANCO</label>
									<select class="datac form-control" required><option value="" disabled selected>--seleccione--</option>
										<OPTION>BANCO DE COMERCIO</option>
										<OPTION>BANCO DE CREDITO</option>
										<OPTION>BANBIF</option>
										<OPTION>BANCO FINANCIERO</option>
										<OPTION>BANCO CONTINENTAL</option>
										<OPTION>CITIBANK</option>
										<OPTION>INTERBANK</option>
										<OPTION>MI BANCO</option>
										<OPTION>SCOTIABANK</option>
									</select>
								</div>
								
								<div class="form-group ">
									<label class="text-center">NOMBRE</label>
									<input class="datac form-control" placeholder="Nombre de la Cuenta" required>
								</div>
								
								<div class="form-group ">
									<label class="text-center">DESCRIPCION</label>
									<textarea class="datac form-control" placeholder="Descripcion de la Cuenta"></textarea>
								</div>
								
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								<button type="submit" class="btn btn-primary">GUARDAR CUENTA</button>
							</div></form>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			
			
		</div>	
	</body>
</html>