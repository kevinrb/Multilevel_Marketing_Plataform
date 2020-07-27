<?php
	include("../sec.php");
	//echo $Xlogin;
	
	
	if($a=="change_tipo"){
		qry("update user_nivel set tipo='$tipo' where login='$login' ");
		echo "TIPO CAMBIADO";
	}
	if($a=="reset"){
		qry("update user_nivel set deuda=3 where login='$login' ");
		echo "USUARIO RESETEADO";
	}
	
	$tab2="<table class='table table-bordered'><thead><tr>";
	$res=qry("describe user_nivel ");
	while($r=mysql_fetch_row($res)){
		$tab2.="<th>$r[0]</th>";
		
	}
	$tab2.="</tr></thead><tbody>";
	$res=qry(" select * from user_nivel where login='$login'");
	$tipoo="";
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab2.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab2.="<td>$r[$i]</td>";
			$tipoo="$r[$i]";
		}
		$tab2.="</tr>";
	}
	$tab2.="</tbody></body>";
	
	$tab3="<table class='table table-bordered'><thead><tr>";
	$res=qry("describe puntos ");
	while($r=mysql_fetch_row($res)){
		$tab3.="<th>$r[0]</th>";
	}
	$tab3.="</tr></thead><tbody>";
	$res=qry("select a.*,b.idpadre2 from puntos a left join operacionesp b on b.idop=a.idop where a.login='$login'");
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab3.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab3.="<td>$r[$i]</td>";
		}
		$tab3.="</tr>";
	}
	$tab3.="</tbody></body>";
	
	
	
	
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css"> 
		
		
	</head>
	<body>
		<form method="GET" class="form-inline">
			<input name="login" type="hidden" value="<?php echo $login;?>"/>
			<input name="a" type="hidden" value="change_tipo"/>
			<label for="tipo">TIPO</label>
			<select name="tipo" id="tipo" class="form-control" ><?php echo "<option>$tipoo</option>";?><option>RED</option><option>ESPECIAL</option><option>VENTAS</option></select>
			<button type="submit" class="btn btn-info">CAMBIAR</button>
		</form>
		<form method="GET" class="form-inline"  id="form_reset">
			<input name="login" type="hidden" value="<?php echo $login;?>"/>
			<input name="a" type="hidden" value="reset"/>
			<label for="tipo">RESETEAR</label>
			<button id="btn_reset" type="button" class="btn btn-info">RESETEAR</button>
		</form>
		<?php echo $tab1."<br/>".$tab2."<br/>".$tab3."<br/>".$tab4."<br/>".$tab5."<br/>".$tab6;?>
		<script src="../js/jquery.js"></script>
		<script src="../js/alertify.js"></script>
		<script>
			$(function(){
				$("#btn_reset").click(function(){
					alertify.confirm("DESEA RESETEAR? Este proceso no es reversible", function () {
						$("#form_reset").submit();
						// user clicked "ok"
						}, function() {
						// user clicked "cancel"
					});
				});
			});
		</script>
	</body>
</html>