<?php
	require_once("../sec.php");
	require_once("../ws/func.php");
	if($a=="sincro")
	{
		echo sincro_prod();
	}
	
	if($tipo=="upda")
	{
		$cant=count($idprod);
		for($i=0;$i<$cant;$i++)
		{
			if($prof[$i]!="")
			{
				qry("insert ignore into promo_puntos(idprod,descuento1,descuento2,descuento3,descuento4,descuento5,comision) values ($idprod[$i],'$descuento1[$i]','$descuento2[$i]','$descuento3[$i]','$descuento4[$i]','$descuento5[$i]','$comision[$i]')");
				qry("update promo_puntos set descuento1='$descuento1[$i]',descuento2='$descuento2[$i]',descuento3='$descuento3[$i]',descuento4='$descuento4[$i]',descuento5='$descuento5[$i]',comision='$comision[$i]',prof='$prof[$i]' where idprod=$idprod[$i]");	
			}
			
		}
	}
	
	$qry1="select b.idprod,b.nombre,a.descuento1,a.descuento2,a.descuento3,a.descuento4,a.descuento5,a.comision,a.prof,b.precio  from productos b left join promo_puntos a on a.idprod=b.idprod  where promo=1 and estado in (1,3) and porlocal=0 order by b.nombre";
	$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
	while($r1=mysql_fetch_row($res1)) {
		$tbl.='<tr>
		<td> <input type="hidden" name="idprod[]" value="'.$r1[0].'" />'.$r1[0].'</td><td>'.$r1[1].'</td><td>'.$r1[9].'</td><td><input class="form-control input-sm" type="text" name="descuento1[]" value="'.$r1[2].'"/></td><td><input class="form-control input-sm" type="text" name="descuento2[]" value="'.$r1[3].'"/></td><td><input class="form-control input-sm" type="text" name="descuento3[]" value="'.$r1[4].'"/></td><td><input class="form-control input-sm" type="text" name="descuento4[]" value="'.$r1[5].'"/></td><td><input class="form-control input-sm" type="text" name="descuento5[]" value="'.$r1[6].'"/></td><td><input class="form-control input-sm" type="text" name="comision[]" value="'.$r1[7].'"/></td><td><input class="form-control input-sm" type="text" name="prof[]" value="'.$r1[8].'"/></td></tr>';
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
									<thead><th>IDPROD</th><th>Nombre</th><td>PRECIO</td><th>Nivel 1(%)</th><th>Nivel 2(%)</th><th>Nivel 3(%)</th><th>Nivel 4(%)</th><th>Nivel 5(%)</th><th>Comision (%)</th><th>Prod Prof.</th></thead>
									<tbody><?php echo $tbl; ?></tbody>
								</table><input type="hidden" class="form-control" value="1" name="cambiar">
							</div> 
						</div>                   
					</div>
				</div>
				<div class="row"><div class="col-md-6 col-md-offset-3"><button type="submit" class="form-control input-sm btn btn-success" id="update">Actualizar</button></div><a href="gespromo.php?a=sincro" class="btn btn-warning btn-sm">Sincronizar</a></div>
			</form>
		</div>
		<!-- Modal -->
		
		
	</body>
	
</html>