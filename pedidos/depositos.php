<?php 
	require_once("../sec.php");
	require_once("../ws/func.php");
	
	$tipo=array("ch" => "CHEQUE","ef" => "EFECTIVO","tx" => "DEPOSITO");
	$pag = substr($_SERVER["PHP_SELF"],1);
	
	$qry=qry("select idmenu FROM menu WHERE idfunc in(select idfunc from permisos where login='$Xlogin') AND url='$pag'");
	//echo "select idmenu FROM menu WHERE idfunc in(select idfunc from permisos where login='$Xlogin') AND url='$pag'";
	//select idmenu FROM menu WHERE idfunc in(select idfunc from permisos where login='kevin') AND url='depositos.php'
	$xxres=mysql_num_rows($qry);
	
	$cuentas=posturl(array("a"=>"lista_cuentas"),"http://perushop.pe/santa2/webservices/pedidos.php");
	
	$cuentas=json_decode($cuentas);
	
	$aa=$cuentas[0];
	$cuen=$cuentas[1];
	
	if($xxres!=0){
		//$qry=qry("select idfunc from permisos where login='$Xlogin' AND idfunc>997"); $xre=mysql_num_rows($qry);
		/*
			if($xre!=0){//$vxx='<button class="btn btn-success btn-xs" data-toggle="modal" data-target="#modnewcc"><small>CREAR CUENTA</small></button>';
			$rql=qry("select local,nombre from locales order by nombre"); while($r=mysql_fetch_row($rql)){
			$select="";
			if($r[0]==$Xlocal)
			$select="selected";
			$ll.='<option value="'.$r[0].'" '.$select.'>'.$r[1].'</option>';
			}
			$loc='<select class="datac form-control" required><option value="">--ELEGIR--</option>'.$ll.'</select>';
			$sql2=qry("select b.nombre,a.idcuenta,a.fecha,a.login,a.monto,a.loginliq,a.horaliq,a.loginvalido,a.horavalido,a.loginanula,a.horaanula,a.estado,a.detalles,a.tipo FROM cuentasmove a, cuentas b WHERE b.idcuenta=a.idcuenta");
			}
		else{*/
		$vxx="";
		//$loc='<input class="datac form-control" value="'.$Xlocal.'" readonly>';
		$sql2=qry("select '',a.idcuenta,a.fecha,a.login,a.monto,a.loginliq,a.horaliq,a.loginvalido,a.horavalido,a.loginanula,a.horaanula,a.estado,a.detalles,a.tipo FROM cuentasmove a WHERE  a.idcliente in (select idcliente from usuarios where login='$Xlogin') and a.idpadre>0");
		//}
		$bq1='DEPOSITOS RECIENTES<div class="col-xs-8 pull-right">'.$vxx.'&nbsp;<button class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#modnewmov"><small>REGISTRAR DEPOSITO</small></button></div>';
		
		$bqm='<div class="form-group"><label>LOCAL</label>'.$loc.'</div>';
		/*$cc=mysql_num_rows($sql);
			$cc2=mysql_num_rows($sql2);
			while($res=mysql_fetch_row($sql)){
			$bb.='<tr><td>'.$res[0].'</td><td>'.$res[1].'</td><td>'.$res[2].'</td><td>'.$res[3].'</td><td>S/. '.$res[4].'</td><td>'.$tipo[$res[8]].'</td><td>'.$res[7].'</td></tr>';
		}*/
		
		while($res2=mysql_fetch_row($sql2)){
			$cantcuen=count($cuen);
			$nomcuen="";
			for($i=0;$i<$cantcuen;$i++)
			{
				if($cuen[$i][0]==$res2[1])
				{
					$nomcuen=$cuen[$i][1];
				}
			}
			if($res2[9]=="" && $res2[11]==0){
				$bb.='<tr><td>'.$nomcuen.'</td><td>'.$res2[2].'</td><td>'.$res2[3].'</td><td>S/. '.$res2[4].'</td><td>'.$res2[12].'</td></tr>';
			}
			else{
				if($res2[11]==1){$val="SI"; $class="";} else{ $val="NO"; $class="warning";}
				$bb2.='<tr class="'.$class.'"><td>'.$nomcuen.'</td><td>'.$res2[2].'</td><td>'.$res2[3].'</td><td>'.$res2[4].'</td><td>'.$tipo[$res2[13]].'</td><td>'.$res2[7].'</td><td>'.$res2[8].'</td><td>'.$res2[9].'</td><td>'.$res2[10].'</td><td>'.$val.'</td><td>'.$res2[12].'</td></tr>';
			}
		}
		/*
			$listc=array();
			$sql=qry("SELECT idcuenta,nombre from cuentas where tipo='B'");
			$aa="";
			while($res=mysql_fetch_row($sql)){
			$aa.='<option value="'.$res[0].'">'.$res[1].'</option>';
			$listc[]=json_encode($res);
		}*/
		
	}
	else{
		echo "NO TIENE ACCESO A ESTA FUNCIONALIDAD";
	}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dataTables.bootstrap.css" rel="stylesheet">
		<script src="../js/jquery-1.10.2.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/jquery.dataTables.js"></script>
		<script src="../js/dataTables.bootstrap.js"></script>
		<link href="../sb/font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="../sb/css/sb-admin.css" rel="stylesheet">
		<link rel='stylesheet' type='text/css' href='../css/datetimepicker.css'/> 
		<script src='../js/bootstrap-datetimepicker.min.js'></script>
		<script src='../js/bootstrap-datetimepicker.es.js'></script>
		<script>
			//var listc=<?php echo $listc;?>;
			$(document).ready(function(){ var sizeini="";
				if(<?php echo $xxres;?>==0){
					$('body').html("NO TIENE ACCESO A ESTA FUNCIONALIDAD");
				}
				///////////////////////////////////////////MAKING A REAL TABLE RESPONSIVE ///////////////////////////////////////
				if(sizeini==""){
					var w1=$('.datatab').parent().width(); var w2=$('.datatab').width();
					if(w1<w2){$('.datatab').parent().css({ overflowX: 'scroll'}); sizeini="1";}
					else if(w1==w2){$('.datatab').parent().removeAttr("style"); sizeini="1";}
				}
				
				if (screen.width < 500){$('.datatab').parent().css({ overflowX: 'scroll'});} 
				window.onresize = resize;
				function resize(){
					var w1=$('.datatab').parent().parent().width(); var w2=$('.datatab').width();
					if(w1<w2){$('.datatab').parent().parent().css({ overflowX: 'scroll'});}
					else if(w1>=w2){$('.datatab').parent().parent().removeAttr("style");}
				} 
				
				$('.datatab').dataTable({"oLanguage": {"sUrl": "css/spanish.txt"}});
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				var dd=new Date();
				$('#datetime').datetimepicker({format: 'yyyy-mm-dd',
					showMeridian: true,
					autoclose: true,
					language:'es',
					endDate:dd,
					headTemplateV3:true,
				minView:2});
				
				
				$("#formnewmov").submit(function(event){
					event.preventDefault();
					data=new Array();
					var patt = new RegExp("\d+(\.\d{1,2})?");
					if(patt.test($('#monto').val())==true || $('#monto').val()==0){$('#monto').val("").attr("placeholder","Monto Invalido"); return false;}
					
					else if(patt.test($("#nrodoc").val())==true){$('#nrodoc').val("").attr("placeholder","SOLO NUMEROS").focus();}
					
					else{
						if($('#datetime').val()==""){$('#datetime').focus(); $('#datetime').attr("placeholder","FALTA ESTE CAMPO"); return false;}
						
						var data = $('#formnewmov').serializeArray();
						data.push({name: 'a',value: 'newmov'});
						
						$.post("../res/pedidos/proc.php",data,function(fff){
							if(fff=="1"){location.href="depositos.php";}
						});
					}
				});
				/////////////////////////////////////////////////////////////////
				/*
					$("body").delegate('#idcuenta','change',function(){
					var rr=$('#idcuenta :selected').val();
					$.post("../res/pedidos/proc.php",{a:"loadinfo",idcuenta:rr},function(data){
					$('#loadinfo').html(data);
					});
					});
				*/
				////////////////////////////////////
				$("#formnewc").submit(function(event){
					event.preventDefault();
					var patt = new RegExp("(\\D|\\s)");
					if(patt.test($("#idcc").val())==true){$("#idcc").val("").attr("placeholder","SOLO NUMEROS"); return false;}
					data=new Array();
					$(".datacc").each(function(){ data.push($(this).val()); });
					$.post("../res/historial/proc.php",{a:"newc",data:data},function(data){
						if(data=="1"){alert("Cuenta Guardada"); location.href="historial.php";}
					});
				});
				//////////////////////////////////////////////////////////////////
				$("body").delegate('#tipopago','change',function(){
					var rr=$('#tipopago :selected').val();
					if(rr=="tx"){$("#dpo").html('<div class="form-group"><label>NOMBRE DE LA CUENTA</label><select class="datac form-control" id="idcuenta" required><option value="">--ELEGIR--</option><?php echo $aa;?></select></div>');}
					else{$("#dpo").empty(); $("#loadinfo").html("");}
				});
				/////////////////////////////////////////////////////////////////
				
				
			});
		</script>
		<style>
			@media only screen and (max-width: 400px) {
			.tabcont
			{
			overflow-x:scroll;
			}
			}
			
			.datetimepicker{
			z-index: 10000 !important;
			}
			
		</style>
	</head>
	<body>
		
		<div class="container">
			<div class="row">
				<div class="panel panel-success">
					<div class="panel-heading"><?php echo $bq1;?></div> 
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered" >
								<thead><tr><th>CUENTA</th><th>FECHA</th><th>LOGIN</th><th>MONTO (S/.)</th><th>DETALLES</th></tr></thead>
								<tbody><?php if($bb!=""){echo $bb;}?></tbody>
							</table>
						</div> 
					</div> 
				</div>
			</div><br><br>
			
			<div class="row">
				<div class="panel panel-success">
					<div class="panel-heading">DEPOSITOS REGISTRADOS</div> 
					<div class="panel-body">
						<div class="tabcont">
							<table class="table table-bordered datatab" >
								<thead><tr><th>CUENTA</th><th>FECHA</th><th>LOGIN</th><th>MONTO(S/.)</th><th>TIPO</th><th>LOGIN/VALIDO</th><th>HORA/VALIDO</th><th>LOGIN/ANULA</th><th>HORA/ANULA</th><th>VALIDO</th><th>DETALLES</th></tr></thead>
								<tbody><?php if($bb2!=""){echo $bb2;}?></tbody>
							</table>
						</div> 
					</div> 
				</div>
			</div>
		</div>
		<!-- Modal -->
		<div class="modal fade" id="modnewmov" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">REGISTRAR DEPOSITO</h4>
					</div>
					<form id="formnewmov">
						<div class="modal-body">
							<!--<div class="form-group">
								<label>TIPO DE PAGO</label>
								<select class="datac form-control" id="tipopago" required><option value="" selected disabled>--ELEGIR--</option>
								<option value="tx">DEPOSITO</option>
								<option value="ch">CHEQUE</option>
								<option value="ef">EFECTIVO</option>
								<option value="pl">PLANILLA</option>
								</select>
							</div>-->
							<div id="dpo">
								<div class="form-group"><label>NOMBRE DE LA CUENTA</label><select class="datac form-control" name="idc" id="idcuenta" required><option value="" disabled selected>--ELEGIR--</option><?php echo $aa;?></select></div>
							</div>
							<div id="loadinfo"></div>
							
							<div class="form-group"><label>NRO DE DOCUMENTO:</label><input class="datac form-control" name="nrodoc" id="nrodoc" required></div>
							
							<div class="form-group col-md-6">
								<label>FECHA DEPOSITO</label>
								<input class="datac form-control" id="datetime" name="fechadep" placeholder="CLICK AQUI" readonly required>
							</div>
							
							<div class="form-group col-md-6">
								<label>MONTO</label>
								<input class="datac form-control" id="monto" name="monto" placeholder="S/." required>
							</div>
							
							<div class="form-group">
								<label>DETALLES</label>
								<textarea class="datac form-control" name="det" placeholder="Detalles ..."></textarea>
							</div>
							<?php //echo $bqm;?>
							<input type="hidden" class="datac" value="<?php echo $Xlogin;?>">
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button type="submit" class="btn btn-primary">GUARDAR DEPOSITO</button>
						</div></form>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		
		<!-- Modal -->
		<div class="modal fade" id="modnewcc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">CREANDO NUEVA CUENTA</h4>
					</div>
					<form id="formnewc" role="form">
						<div class="modal-body">
							
							<div class="form-group">
								<label>NRO. de CUENTA</label>
								<input class="datacc form-control" id="idcc" placeholder="123456" required>
							</div>
							
							<div class="form-group">
								<label>BANCO</label>
								<select class="datacc form-control" required><option disabled selected value="">--seleccione--</option><option>BANCO DE COMERCIO</option><option>BANCO DE CREDITO</option><option>BANCO FINANCIERO</option><option>BBVA BANCO CONTINENTAL</option><option>CITIBANK</option><option>INTERBANK</option><option>SCOTIABANK</option></select>
							</div>
							
							<div class="form-group">
								<label>NOMBRE</label>
								<input class="datacc form-control" placeholder="Nombre de la Cuenta" required>
							</div>
							
							<div class="form-group">
								<label>DESCRIPCION</label>
								<textarea class="datacc form-control" placeholder="Descripcion de la Cuenta"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button type="submit" class="btn btn-primary">GUARDAR CUENTA</button>
						</div></form>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
	</body>
</html>