<?php
	
	require_once("../../sec.php");
	$id[0]="999"; $id[1]="998"; $id[2]="997";
	$nom[0]="Administrador"; $nom[1]="Gerente"; $nom[2]="Supervisor";
	for($i=0;$i<3;$i++){
		$sql="INSERT INTO rols (idrol,nombre)
		SELECT * FROM (SELECT '9$id[$i]','$nom[$i]') AS tmp
		WHERE NOT EXISTS (SELECT idrol FROM rols WHERE idrol='9$id[$i]' AND nombre='$nom[$i]')";
		$psql=qry($sql);
		
		$sql="INSERT INTO rolfunc (idrol,idfunc)
		SELECT * FROM (SELECT '9$id[$i]','$id[$i]') AS tmp
		WHERE NOT EXISTS (SELECT idrol FROM rolfunc WHERE idrol='9$id[$i]' AND idfunc='$id[$i]')";
		$psql=qry($sql);
	}
	
	
	$qry="SELECT login,nombre from usuarios WHERE login!='' and nombre!='' order by nombre";
	$pqry=qry($qry);
	$op="";
	while($tmp=mysql_fetch_assoc($pqry)){
		$op.='<option value="'.$tmp["login"].'">'.$tmp["nombre"].'</option>';
	}
	
	$sql="SELECT DISTINCT idfunc FROM menu order by idfunc";
	$psql=qry($sql);
	while($tmp=mysql_fetch_row($psql)){
		$func.='<option value="'.$tmp[0].'" idfunc="'.$tmp[0].'">'.$tmp[0].'</option>';
	}
	$selfunc='<select class="form-control"  name="funcs" id="sidfunc"><option value="" selected disabled>--☼--</option>'.$func.'</select>';
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
		<script src='../../js/jquery-1.10.1.min.js'></script>
		<script src='../../js/bootstrap.js'></script>
		<title>GESTION DE ROLES</title>
	</head>
	<script>
		$(function(){ var inlist=[]; 
			/////////////////////////////////////////////CREAR*EDITAR ROL///////////////////////////////////////////////
			$("body").delegate('#saverol','click',function(){
				
				var nn=$('#nombre').val();
				var items = [];
				$('.idfuncs').each(function(){items.push($(this).text());});
				var size=items.length;
				if(nn!="" && size!=0){
					
					
					var action=$("#holderec").val();
					if(action=="crear"){
						var c=1;
						$.post('rolsproc.php',{tar:c, nombre:nn, funcs:items, cc:size},function(){
							
							$('#modcrear').modal('hide');
							var c=2;
							$.post('rolsproc.php',{tar:c},function(data){
								$("#res").html(data);
								inlist.length = 0;
								var selfUrl = unescape(parent.window.location.pathname);
								parent.location.reload(true);
								parent.window.location.replace(selfUrl);
								parent.window.location.href = selfUrl;
							});
						});
					}
					
					else if(action=="editar"){
						var idr=$('#holderec').attr("idrol");
						var items2 = [];
						$('.quitados').each(function(){items2.push($(this).val());});
						var size2=items2.length;
						var items3 = [];
						$('.agregados').each(function(){items3.push($(this).val());});
						var size3=items3.length;
						
						
						var c=8;
						$.post('rolsproc.php',{tar:c, nombre:nn, idrol:idr, funcs:items, cc:size, remove:items2, cc2:size2 , add:items3, cc3:size3},function(res){
							var c=2;
							$('#modeditar'+idr).modal('hide');
							$.post('rolsproc.php',{tar:c},function(data){
								$("#res").html(data); inlist.length = 0;
								
								var selfUrl = unescape(parent.window.location.pathname);
								parent.location.reload(true);
								parent.window.location.replace(selfUrl);
								parent.window.location.href = selfUrl;
							});
						});
					}
					
				}
				
				else{$('#nombre').focus();}
				
			});
			
			/////////////////////////////////////////////////////////////////////////////////////////
			$("body").delegate('#asignar','click',function(){
				var c=2;
				$.post('rolsproc.php',{tar:c},function(data){
					if(data!=""){
						$("#res").html(data);
					}
					else{$("#res").html("NO SE ENCONTRARON ROLES");}
					
				});
				
			});
			$("#asignar").trigger("click");
			$("body").delegate('.aplicar','click',function(){
				var idr=$(this).attr('idrol');
				$('#holder').val(idr);
			});
			
			/////////////////////////////ASIGNAR ROLES A USUARIOS/////////////////////////////////
			$('body').delegate('#savech','click',function(){
				var id=$('#holder').val();
				var items = [];
				$('#userrol :selected').each(function(){items.push($(this).val());});
				var size=items.length;
				var c=3;
				$.post('rolsproc.php',{tar:c, idrol:id, user:items, cc:size},function(data){
					$('#modaplicar').modal('hide');
				});
				
			});
			
			/////////////////////////////ELIMINAR ROL////////////////////////////////
			$('body').delegate('.quitar','click',function(){
				
				var r=confirm("Esta seguro?");
				if (r==true)
				{
					var id=$(this).attr('idrol');
					var act=$(this).attr('act');
					var c=4;
					$.post('rolsproc.php',{tar:c, idrol:id},function(data){
						$(act).remove();
						
					});
				}
				else
				{}
				
			});
			//////////////////////////////////AGREGAR IDFUNC A LA LISTA////////////////////////////////////////////
			$('body').delegate('#sidfunc','change',function(){
				var idf=$('#sidfunc :selected').text();
				if(inlist.indexOf(idf)!= -1){return false;}
				inlist.push(idf);
				
				$("#elegidos").append('<div class="col-sm-12 col-xs-6"><div class="input-group"><span class="form-control input-sm input-group-addon idfuncs">'+idf+'</span><span class="input-group-btn"><button class="btn btn-sm btn-default xidf" type="button"><span class="glyphicon glyphicon-remove"></span></button></span></div></div>');
				
				if($('#holderec').val()=="editar"){
					$("#elegidos").append('<input type="hidden" class="agregados" value="'+idf+'">');
				}
				
				var items = [];
				$('.idfuncs').each(function(){items.push($(this).text());});
				var size=items.length;
				var c=5;
				$.post('rolsproc.php',{tar:c, idfunc:items, cc:size},function(data){
					$("#sgrupo").html(data.s1);
					$("#snombre").html(data.s2);
				}, "json");
			});
			
			$('body').delegate('.modbi','hidden.bs.modal', function (e) {
				inlist.length = 0;
			})
			////////
			////////////////////////////////////////////////////////////////////////////////
			$('body').delegate('#sgrupo','change',function(){
				
				var items = [];
				$('.idfuncs').each(function(){items.push($(this).text());});
				var size=items.length;
				var items2 = [];
				$('#sgrupo :selected').each(function(){
					if($(this).val()!="allgrupo"){items2.push($(this).val());}
					else{}
				});
				var size2=items.length;
				var c=6;
				$.post('rolsproc.php',{tar:c, grupo:items2, cc2:size2 , idfunc:items, cc:size},function(data){
					$("#snombre").html(data);
				});
			});
			///////////////////////////QUITAR FUNCIONES///////////////////////////////////////////
			$('body').delegate('.xidf','click',function(){
				var tod=$(this).parent().parent().find("span:nth-child(1)").text();
				var index = inlist.indexOf(tod);
				if (index > -1) {inlist.splice(index, 1);}
				$(this).parent().parent().parent().remove();
				
				
				if($('#holderec').val()=="editar"){
					var iii=$(this).parent().parent().children(".idfuncs").text();
					$("#elegidos").append('<input type="hidden" class="quitados" value="'+iii+'">');
				}
				
				var items = [];
				$('.idfuncs').each(function(){items.push($(this).text());});
				var size=items.length;
				if(size!=0){
					var c=5;
					$.post('rolsproc.php',{tar:c, idfunc:items, cc:size},function(data){
						$("#sgrupo").html(data.s1);
						$("#snombre").html(data.s2);
					}, "json");
				}
				
				else{$("#sgrupo").empty(); $("#snombre").empty();}
			});
			
			//////////////////////////////////////////////////////////////////////////////
			$('body').delegate('#allg','click',function(){
				var items = [];
				$('.idfuncs').each(function(){items.push($(this).text());});
				var size=items.length;
				var c=5;
				$.post('rolsproc.php',{tar:c, idfunc:items, cc:size},function(data){
					$("#sgrupo").html(data.s1);
					$("#snombre").html(data.s2);
				}, "json");
			});
			
			///////////////////////////////////////EDITAR///////////////////////////////////////
			$('body').delegate('.editar','click',function(){
				inlist.length = 0;
				$('#holderec').val("editar");
				var tar=$(this).attr("tar");
				$(".modbi").attr("id",tar);
				var id=$(this).attr("data-target");
				var idr=$(this).attr("idrol");
				$('#holderec').attr("idrol",idr);
				var c=7;
				$.post('rolsproc.php',{tar:c, idrol:idr},function(data){
					$("#modcuerpo").html(data);
					$('.idfuncs').each(function(){inlist.push($(this).text());});	
					$(id).modal('show');
				});
			});
			
			//////////////////////////////////////////////////////////////////////////////
			$('body').delegate('#crear','click',function(){
				$('#holderec').val("crear");
				$('#holderec').attr("idrol","");
				$(".modbi").attr("id","modcrear");
				$("#nombre").val("");
				$("#sgrupo").empty();
				$("#snombre").empty();
				$("#elegidos").empty();
				$("#modcrear").modal('show');
			});
			
			$("#asignar").hide();
		});
	</script>
	<body>
		<input type="hidden" id="holder" value="0">
		<input type="hidden" id="holderec" value="" idrol="">
		
		<div class="container">
			<br>
			<div class="row">
				
				<div class="col-md-6 col-md-offset-3">
					<div class="input-group">
						<span class="input-group-addon">ROLES</span>
						<span class="input-group-btn">
							<button type="button" class="btn btn-sm btn-default" id="crear" data-toggle="modal" data-target="#modcrear"><span class="glyphicon glyphicon-plus-sign"></span></button>
							<button type="button" class="btn btn-sm btn-default" id="asignar">ASIGNAR</button>
						</span>
					</div>
				</div>
				
			</div><br>
			<div class="row">
				<div class="col-lg-6 col-md-offset-3"><div id="res" class="bloqres"></div></div>
			</div>
		</div>
		
		<!-- Modal CREAR-->
		<div class="modal fade modbi" id="" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					
					<div class="modal-body" id="modcuerpo"><div class="row">
						
						<div class="col-md-12">
							<div class="form-group">
								<label for="exampleInputFile">Nombre</label>
								<input class="form-control input-sm" type="text" id="nombre">
							</div>
						</div>
						
						<div class="col-md-12">
							
							<div class="col-md-4"><label>Funciones</label><?php echo $selfunc;?>
								<div id="elegidos" class="row"></div>
							</div>
							<div class="col-md-4"><label>Grupo</label><select class="form-control" size="15" name="funcs" id="sgrupo" multiple></select></div>
							<div class="col-md-4"><label>Nombre</label><select class="form-control" size="15" name="funcs" id="snombre" multiple></select></div>
							
						</div>
					</div>   
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-sm btn-primary" id="saverol">Guardar</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		
		<!-- Modal ASIGNAR-->
		<div class="modal fade" id="modaplicar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<select class="form-control" name="userrol" size="30" id="userrol" multiple><?php echo $op;?></select>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-sm btn-primary" id="savech">Asignar</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
	</body>
</html>