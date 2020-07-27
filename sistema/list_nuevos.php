<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	$qry = "select concat(a.nombre,' ',a.apellidos), a.idpersona,a.direccion,a.email,a.telefono,a.celular,nivel,a.login,a.meta1,a.edobs from usuarios_temp a where a.activo=1   order by nombre";
	$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		
		$rem=explode("-=-",$r[9]);
		$tbl1.="<tr><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td><td>$rem[1]</td><td>$rem[0]</td><td>$r[8]</td><td><input type='button' class='confirm btn btn-success btn-sm' data-login='$r[1]' value='confirmar' /></td></tr>";
		
	}
	
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Mis Socios</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" media="screen" href="../css/bootstrap-datetimepicker.min.css">
		<script src="../js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script type="text/javascript">
			$(function(){
				$(".confirm").click(function(){
					$.post("ajax.php",{a:"confuser",login:$(this).data('login')},function(){
						location.reload();
						
					});
					
				});
				
				
			})
		</script>
	</head>
	<body>
		<div class="container">
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Socios por Confirmar</h3></div>
				<table class='table '>
					<thead>
						<th>Nombre</th><th>DNI</th><th>Direccion</th><th>Telefono</th><th>Celular</th><th>Lider</th><th>Cuenta</th><th>Nro Dep.</th><th>Monto</th>
					</thead>
					
					<?php echo $tbl1;?>
				</table>
			</div>
			
		</body>
	</html>	