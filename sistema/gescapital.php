<?php
	require_once("../sec.php");
	if($tipo=="upda")
	{
		$cant=count($idprod);
		for($i=0;$i<$cant;$i++)
		{
			
			//qry("insert ignore into promo_puntos(idprod,descuento,comision) values ($idprod[$i],'$descuento[$i]','$comision[$i]')");
			qry("update capital set precio='$precio[$i]',idprod='$idprod[$i]' where nroped=$nroped[$i]");	
			
		}
	}
	
	$res=qry("select idprod,nombre from productos where promo=0 and  nombre!='' ORDER BY nombre");
	$opt="";
	while($r=mysql_fetch_row($res))
	{
		$opt.="<option value='$r[0]'>$r[1]</option>";
	}
	
	
	$qry1="select a.nroped,b.nombre,a.precio,a.idprod  from capital a left join productos b on a.idprod=b.idprod  where a.estado=1";
	$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
	while($r1=mysql_fetch_row($res1)) {
		$tbl.='<tr>
		<td> <input type="hidden" name="nroped[]" value="'.$r1[0].'" />'.$r1[0].'</td><td><select class="form-control" name="idprod[]"><option value="'.$r1[3].'">'.$r1[1].'</option>'.$opt.'</select></td><td><input class="form-control" type="text" name="precio[]" value="'.$r1[2].'"/></td></tr>';
	}
	
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Gestion de Promocion</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dataTables.bootstrap.css" rel="stylesheet">
		<script src="../js/jquery-1.10.2.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/jquery.dataTables.js"></script>
		<script src="../js/dataTables.bootstrap.js"></script>
		<link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="../css/sb-admin.css" rel="stylesheet">
		<script type="text/javascript">
			$(document).ready(function(){
				
			});
		</script>
	</head>
	<body>
		
		<div class="container"><br>
			<form method="POST">
				<div class="row">
				</div>
				<div class="row">
					<div class="panel panel-success">
						<div class="panel-heading">Gestion de Promocion</div>                   
						<div class="panel-body">
							<div class="tabcont">
								<input type="hidden" name="tipo" value="upda" />
								<table class="table table-bordered datatab" >
									<thead><th>Nro de Pedido</th><th>Nombre</th><th>Precio</th></thead>
									<tbody><?php echo $tbl; ?></tbody>
								</table><input type="hidden" class="form-control" value="1" name="cambiar">
							</div> 
						</div>                   
					</div>
				</div>
				<div class="row"><div class="col-md-6 col-md-offset-3"><button type="submit" class="form-control input-sm btn btn-success" id="update">Actualizar</button></div></div>
			</form>
		</div>
		<!-- Modal -->
		
		
	</body>
	
</html>