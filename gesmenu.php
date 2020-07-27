<?php
	require_once("sec.php");
	
	$qry1="select * from menu";
	$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
	while($r1=mysql_fetch_row($res1)) {
		$tbl.='<tr>
		<td>'.$r1[0].'</td><td>'.$r1[1].'</td><td>'.$r1[2].'</td><td>'.$r1[3].'</td><td>'.$r1[4].'</td><td>'.$r1[5].'</td>
		<td><input type="checkbox" class="toedit" ind="'.$r1[0].'" a1="'.$r1[1].'" a2="'.$r1[2].'" a3="'.$r1[3].'" a4="'.$r1[4].'" a5="'.$r1[5].'"/></td></tr>';
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Gestion de Niveles</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/dataTables.bootstrap.css" rel="stylesheet">
		<script src="js/jquery-1.10.2.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.dataTables.js"></script>
		<script src="js/dataTables.bootstrap.js"></script>
		<link href="font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="css/sb-admin.css" rel="stylesheet">
		<script type="text/javascript">
			$(document).ready(function(){
				//setTimeout(function(){ var www=$(document.getElementsByName("DataTables_Table_0_length")).change();},200);
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
				$('.datatab').dataTable({"oLanguage": {"sUrl": "css/spanish.txt"},"iDisplayLength": "50"});
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$('body').delegate('.toedit','click',function(){
					$(this).removeClass("toedit").addClass("inedit"); var obj=$(this).parent().parent();
					for(i=2;i<7;i++){var y=i-1; obj.find("td:nth-child("+i+")").html('<input type="text" class="form-control input-sm toup'+$(this).attr("ind")+'" value="'+$(this).attr("a"+y)+'">');}
				});
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$('body').delegate('.inedit','click',function(){
					$(this).removeClass("inedit").addClass("toedit"); var obj=$(this).parent().parent();
					for(i=2;i<7;i++){var y=i-1; obj.find("td:nth-child("+i+")").html($(this).attr("a"+y));}
				});
				
				$('body').delegate('#update','click',function(){
					var data=[]; var i=0;
					$('.inedit').each(function(){var ii=$(this).attr("ind");
						var sub=[]; sub.push(ii); $(".toup"+ii).each(function(){sub.push($(this).val());}); 
						data[i]=sub; i++;
					}); 
					var c=1;
					$.post('ajax/gmenu.php',{data:data,tar:c},function(fff){
						if(fff=="1"){location.href="gesmenu.php";}
					});
				});
				///////////////////////////////////////
				$('body').delegate('#saveall','click',function(){
					var nn=$('#nombre').val();
					var uu=$('#url').val();
					var id=$('#idfunc').val();
					var gg=$('#grupo').val();
					var or=$('#orden').val();
					var c=2;
					if(id==997 || id==998 || id==999){$('#idfunc').val("").focus(); return false;}
					$.post('ajax/gmenu.php',{tar:c , nombre:nn, url:uu , idfunc:id, grupo:gg, orden:or},function(fff){
						if(fff=="1"){$('#add').modal('hide'); location.href="gesmenu.php";}
						
					});
				});
				////////////////////////////////////////
			});
		</script>
	</head>
	<body>
		
		<div class="container"><br>
			<div class="row">
				<h2>Gestion de Niveles</h2>
			</div><br>
			<div class="row">
				<div class="panel panel-success">
					<div class="panel-heading">GESTION</div>                   
					<div class="panel-body">
						<div class="tabcont">
							<table class="table table-bordered datatab" >
								<thead><th>Nivel</th><th>Nombre</th><th>URL</th><th>Idfunc</th><th>Grupo</th><th>Orden</th><th>Editar</th></thead>
								<tbody><?php echo $tbl; ?></tbody>
							</table><input type="hidden" class="form-control" value="1" name="cambiar">
						</div> 
					</div>                   
				</div>
			</div>
			<div class="row"><div class="col-md-6"><button type="button" class="form-control input-sm btn btn-success" id="update">Actualizar</button></div><div class="col-md-6"><button type="button" class="form-control input-sm btn btn-info" id="butadd" data-toggle="modal" data-target="#add">Añadir</button></div></div>
		</div>
		<!-- Modal -->
		<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<div class="form-group">
							<label for="nombre">Nombre</label>
							<input type="text" class="form-control input-sm" id="nombre" placeholder="Nombre">
						</div>
						<div class="form-group">
							<label for="url">URL</label>
							<input type="text" class="form-control input-sm" id="url" placeholder="URL">
						</div>
						<div class="form-group">
							<label for="idfunc">Idfunc</label>
							<input type="text" class="form-control input-sm" id="idfunc" placeholder="Idfunc">
						</div>
						<div class="form-group">
							<label for="grupo">Grupo</label>
							<input type="text" class="form-control input-sm" id="grupo" placeholder="Grupo">
						</div>
						<div class="form-group">
							<label for="orden">Orden</label>
							<input type="text" class="form-control input-sm" id="orden" placeholder="Orden">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-primary" id="saveall">Guardar</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
	</body>
	
</html>