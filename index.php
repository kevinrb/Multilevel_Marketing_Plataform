<?php
	include "global.php";
	require("cnx.php");
	$res=mysql_query("select idpais, pais from paises where estado=1");
	$opc="";
	while($r=mysql_fetch_row($res)){
		$opc.="<option value='$r[0]'>$r[1]</option>";
	}
	
?>
<!DOCTYPE html>
<html>  
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" href="img/favicon.ico" type="image/x-icon">
		<title>Acceso al Sistema</title>
		
		<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
		<style type="text/css">      
			td {
			border: 1px solid #aaa;
			}
			td:first-child {
			font-weight: bold;
			width: 100px;
			padding-left: 5px;
			}      
		</style>
		<script src="js/jquery-1.10.1.min.js" type="text/javascript"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			function ini() {
				document.getElementById("usuario").focus();
				document.getElementById("w000").value = screen.width;
				document.getElementById("h000").value = screen.height;
			}
			$(function(){
				$("#enter").click(function(){
					$("form").submit();
					
				});
				
			});
		</script>
	</head>
	<body onload="ini()">
		
		<div class="container">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					<div class="login-panel panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Iniciar Sesión</h3>
							
						</div>
						<div class="panel-body">
							<div class="row">
								<input type="image" class="col-md-12" name="imageField" id="imageField" src="img/logo.png" alt="logo"/>
								</div>    <form role="form" name="form1" action="auth1.php" method="post">
								
								<input type='hidden' name='w000' id="w000" value='0'/>
								<input type='hidden' name='h000' id="h000" value='0'/>
								EN MANTENIMIENTO
								
								<fieldset>
									<br />
									<div class="form-group">
										<select class="form-control" placeholder="Pais" name="pais" id="pais">
											
											<option value="604">PERU</option>
											<?php echo $opc;?>
											
										</select>
									</div>							
									<div class="form-group">
										<input class="form-control" placeholder="Usuario" name="usuario" id="usuario" type="text" autofocus>
									</div>
									<div class="form-group">
										<input class="form-control" placeholder="Password" name="passwd" id="passwd" type="password" value="">
									</div>
									
									<!-- Change this to a button or input when using this as a form -->
									<button id="enter"  class="btn btn-lg btn-success btn-block" >Ingresar</button>
								</fieldset>
								
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
		
	</body>
</html>
