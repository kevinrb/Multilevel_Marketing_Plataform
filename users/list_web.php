<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	$qry = "select concat(a.nombre,' ',a.apellidos), a.login,a.direccion,a.email,a.telefono,a.celular,nivel,pass_end from registro.registers a where a.activo=1   order by iduser desc";
	$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		$tbl1.="<tr><td>$r[7]</td><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td>$r[3]</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td><td><a type='button' class='confirm btn btn-success btn-sm' href='reguserweb.php?id=$r[1]' value='confirmar'>Confirmar</a></td></tr>"; 
	}
	
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>WEB</title>
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
				<div class="panel-heading"><h3 class="panel-title">Registros Web</h3></div>
				<table class='table '>
					<thead>
						<th>Fecha</th><th>Nombre</th><th>DNI</th><th>Direccion</th><th>Correo</th><th>Telefono</th><th>Celular</th><th>Lider</th><th></th>
					</thead>
					
					<?php echo $tbl1;?>
				</table>
			</div>
			
		</body>
	</html>			