<?php
	
	
	$usuario = $opc = "";
	$cambio = false;
	foreach($_REQUEST as $k => $v) $$k = $v;
	
	if(isset($pais) and $pais>0){
		include "global.php";
		include "cnx.php";
		}else{
		include "sec.php";
		$pais=$Xpais;
	}
	if($opc == "cambiar_pass") {
		$res = mysql_query("select iduser from usuarios where login='$usuario' and passwd='$oldpass' and activo=1 and idpais='$pais'");
		if(mysql_num_rows($res) > 0) {
			if(strlen($pass1) > 5) {
				if($pass1 != $oldpass) {
					if($pass1 == $pass2) {
						mysql_query("update usuarios set passwd='$pass1',pass_end=adddate(now(),interval 5 month) 
                        where login='$usuario' and passwd='$oldpass' and activo=1 and idpais='$pais'");
						if(mysql_affected_rows() > 0) {
							$mnsj = "Se cambio el password correctamente, en breve sera redireccionado.";
							$cambio = true;
						}
						else 
						$mnsj = "Se produjo un error al actulizar su password";          
					}
					else $mnsj ="ERROR: \"Nuevo Password\" y \"Repetir Password\" no son iguales";
				}
				else $mnsj = "ERROR: \"Nuevo Passowrd\" debe ser diferente que \"Antiguo Password\"";
			}
			else $mnsj = "ERROR: \"Nuevo Password\" debe tener minimo 6 caracteres";
		}
		else $mnsj = "ERROR: password antiguo incorrecto";    
	}
	elseif($opc == "act_pass") {
		$mnsj = "Su password a caducado, porfavor cree un nuevo (diferente) password";
		$opc = "cambiar_pass";
	}
	elseif(isset($_COOKIE["idsess"])) {
		$id = $_COOKIE["idsess"];
		$r = mysql_fetch_row(mysql_query("select login from usuarios where id='$id'"));
		$usuario = $r[0];
		$mnsj = "Cambio de password";
		$opc = "cambiar_pass";
	}
	else
    include "sec.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Punto de Venta</title>
		<script type="text/javascript">
			function ini() {
				setTimeout(function(){
					window.location = "index2.php";
				},3000);
			}
		</script>
		<style type="text/css">
			.tabla1 {
			font-family: Verdana, Geneva, sans-serif;
			font-size: 10px;
			font-style: normal;
			font-weight: bold;
			background-color: #6C0;
			}
			td {
			padding: 0 4px;
			}
			form {
			width: 500px;
			margin: 100px auto;
			padding: 10px;
			border: 2px solid green;
			}
		</style>
	</head>
	<body <?php if($cambio) echo "onload='ini()'"; ?>>    
		<form id="form3" name="form3" method="post" action="nuevopass.php" autocomplete="off">      
			<input type="hidden" name="opc" value="<?php echo $opc ?>"/>
			<input type="hidden" name="usuario" value="<?php echo $usuario ?>"/>
			<input type="hidden" name="pais" value="<?php echo $pais ?>"/>
			<table>
				<?php if($opc == "cambiar_pass") { ?>
					<tr><th colspan="4"><?php echo $mnsj; ?></th></tr>
					<tr><td colspan="4"></td></tr>
					<tr>
						<td class="tabla1">Password Actusl</td>
						<td><input name="oldpass" type="password" id="oldpass" value="" size="20" /></td>
						<td class="tabla1">Usuario</td>
						<th><?php echo $usuario; ?></th>
					</tr>
				<?php } ?>
				<tr>
					<td class="tabla1">Nuevo Password *</td>
					<td><input name="pass1" type="password" id="pass1" value="" size="20" /></td>
					<td class="tabla1">Repetir Password</td>
					<td><input name="pass2" type="password" id="pass2" value="" size="20" /></td>
				</tr>
				<tr><td colspan="4">* 6 caracteres o mas</td></tr>          
				<tr><th colspan="4"><input type="submit" value="Aplicar Nuevo Password"/></th></tr>
			</table>
		</form>    
	</body>
</html>

