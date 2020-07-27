<?php
	require_once("../sec.php");
	if($tipo=="upda")
	{
		$cant=count($idprod);
		for($i=0;$i<$cant;$i++)
		{
			qry("update producto_puntos set puntos=$puntos[$i] where idprod=$idprod[$i]");
		}
	}
	
	$qry1="select a.idprod,b.nombre,a.puntos  from producto_puntos a, productos b where a.idprod=b.idprod and promo=0";
	$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
	while($r1=mysql_fetch_row($res1)) {
		$tbl.='<tr>
		<td> <input type="hidden" name="idprod[]" value="'.$r1[0].'" />'.$r1[0].'</td><td>'.$r1[1].'</td><td><input type="text" name="puntos[]" value="'.$r1[2].'"/></td></tr>';
	}
	
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Gestion de Niveles</title>
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
						<div class="panel-heading">Gestion de Puntos</div>                   
						<div class="panel-body">
							<div class="tabcont">
								<input type="hidden" name="tipo" value="upda" />
								<table class="table table-bordered datatab" >
									<thead><th>IDPROD</th><th>Nombre</th><th>Puntos</th></thead>
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