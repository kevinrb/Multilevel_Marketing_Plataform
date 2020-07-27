<?php 
	require("../sec.php");
	
	if($pais!=""){
		$res=qry("select a.idprod,TRIM(a.nombre) nombre,a.precio,a.precio1,DATE_FORMAT(b.horaini,'%Y-%m-%d') horaini,DATE_FORMAT(b.horafin,'%Y-%m-%d') horafin from productos$pais a left join kitparts$pais b on b.idkit=a.idprod  where a.promo=1 and   DATEDIFF(DATE_FORMAT(b.horafin,'%Y-%m-%d'),CURDATE())>=0 group by a.idprod");
		
		$tbody="";
		while($temp=mysql_fetch_row($res))
		{
			$tbody .= "<tr>";
			for($i=0;$i<count($temp);$i++){
				$tbody .= "<td>".$temp[$i]."</td>"; 
			}
			$tbody .= "<td><button inf='$temp[0]' data-p='$pais' class='edit btn btn-success btn-sm'><span class='glyphicon glyphicon-edit'></span></button></td></tr>";
		}
		
		$loc=qry("select local,nombre from locales;");
		$llo="<option selected disabled value='0'>--Elija--</option>";
		while($temp=mysql_fetch_row($loc))
		{ $llo .= "<option value='$temp[0]'>$temp[1]</option>"; }
		
		$qcanal=qry("select idcanal,nombre from canales where estado=1;");
		$ocanal="<option value='0' selected disabled>--Elija Un Canal--</option>";
		while($temp=mysql_fetch_row($qcanal))
		{ $ocanal .= "<option value='$temp[0]'>$temp[1]</option>"; }
	}
	
	$res=mysql_query("select idpais, pais from paises where estado=1");
	$opc="<option value='' disabled>-elije-</option>";
	while($r=mysql_fetch_row($res)){
		$opc.="<option value='$r[0]'>$r[1]</option>";
	}
	
?>
<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Creacion Promociones</title>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css"> 
		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.js"></script>
		<script>
			var idp=0;
			$(window).load(function(){
				
				$(".edit").click(function(){
					idp=$(this).attr("inf");
					var p=$(this).data("p");
					$.post("../res/promo/ges_promo.php",{a:"dp",i:$(this).attr("inf"),p:p},function(data){
						$("#cedit").html(data[0]);
						$("#listloc").html(data[1]);
						$("#listcan").html(data[2]);
						$("#myModal").modal();
					},"json");
				});
				
				$("#addloc").click(function(){
					l=$("#seloct").val(); e=0;
					$("#listloc").children().each(function(index){ if($(this).attr("inf")==l){e++;} });
					if(e==0)
					{$("#listloc").append("<span inf='"+$("#seloct").val()+"' class='badge'>"+$("#seloct").find(":selected").text()+"<span class='remove glyphicon glyphicon-remove'></span></span>");}
				});
				
				$("#addcan").click(function(){
					l=$("#selcan").val(); e=0;
					$("#listcan").children().each(function(index){ if($(this).attr("inf")==l){e++;} });
					if(e==0)
					{$("#listcan").append("<span inf='"+$("#selcan").val()+"' class='badge'>"+$("#selcan").find(":selected").text()+"<span class='remove glyphicon glyphicon-remove'></span></span>");}
				});
				
				$(document).on("click",".remove",function(){$(this).parent().remove();});
				
				$("#save").click(function(){
					loc=new Array();
					var p=$("#pais").val();
					$("#listloc").children().each(function(){ loc.push($(this).attr("inf")); });
					can=new Array();
					$("#listcan").children().each(function(){ can.push($(this).attr("inf")); });
					$.post("../res/promo/ges_promo.php",{a:"upr",fini:$("#fini").val(),ffin:$("#ffin").val(),pack:$("#pack").val(),loc:loc,i:idp,can:can,p:p},function(data){
						if(data=="1"){ location.reload(); } 
					});
				});
				
			});
			
		</script>
		<style>
			.remove{cursor:pointer;}
		</style>
	</head>
	<body>
		<div class="container">
			<?php if(!$pais!=""){?>
				<form >
					<div class="form-group">
						<label for="pais">Elije un pais</label>
						<select class="form-control" placeholder="Pais" name="pais" id="pais">
							<?php echo $opc;?>
							
						</select>
						<button >Confirmar</button>
					</div>
				</form>
				<?php }else{ ?>
				<h2 class="text-center">Lista de Promociones</h2>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead><tr><th>IDProd</th><th>Nombre</th><th>Precio</th><th>Lista1</th><th>F.Inicio</th><th>F.Fin</th><th>Editar</th></tr></thead>
						<tbody><?php echo $tbody;?></tbody>
					</table>
				</div>
				<div class="text-center">
					<a href="asigna_promociones.php" class="btn btn-success">Nueva Promocion</a>
				</div>
			</div>
			
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabel">Edicion Promocion</h4>
						</div>
						<div class="modal-body">
							<div class="row" id="cedit"></div>
							<!--	
								<div class="row" id="ledit">
								<div class="form-group col-sm-6">
								<label>Local</label>
								<select class="form-control" id="seloct">
								<?php echo $llo;?>
								</select>
								</div>
								<div  class="form-group col-sm-6">
								<label>Agregar</label>
								<div style="padding-left:0px; padding-right:0px;" class="col-sm-12"><button id="addloc" class="btn btn-success">Agregar</button></div>
								</div>
								</div>
								<div id="listloc" class="row"></div>
								
								<div class="row" id="cedit">
								<div class="form-group col-sm-6">
								<label>Canal</label>
								<select class="form-control" id="selcan">
								<?php echo $ocanal;?>
								</select>
								</div>
								<div  class="form-group col-sm-6">
								<label>Agregar</label>
								<div style="padding-left:0px; padding-right:0px;" class="col-sm-12"><button id="addcan" class="btn btn-success">Agregar</button></div>
								</div>
								</div>
								
								<div id="listcan" class="row"></div>
							-->
							
						</div>
						<div class="modal-footer">
							<button id="save" type="button" class="btn btn-primary">Guardar</button>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</body>
</html>