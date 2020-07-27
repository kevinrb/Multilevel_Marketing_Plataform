<?php
	include("../sec.php");
	//echo $Xlogin;
	if($Xlogin=="kevin"){
		$tab1="<table class='table table-bordered'><thead><tr>";
		$res=qry("describe usuarios ");
		while($r=mysql_fetch_row($res)){
			$tab1.="<th>$r[0]</th>";
		}
		$tab1.="</tr></thead><tbody>";
		$res=qry(" select * from usuarios where iduser='$iduser'");
		while($r=mysql_fetch_row($res)){
			$n=count($r);
			$tab1.="<tr>";
			for($i=0;$i<$n;$i++){
				$tab1.="<td>$r[$i]</td>";
			}
			$tab1.="</tr>";
		}
		$tab1.="</tbody></body>";
	}
	
	if($Xpermiso==999 and $a=="change_tipo"){
		tracechange($Xlogin,"user_nivel","iduser",$iduser,"tipo",1,$tipo);
		qry("update user_nivel set tipo='$tipo' where login='$login' ");
		echo "TIPO CAMBIADO";
	}
	if($Xpermiso==999 and $a=="change_master" and $master>0){
		tracechange($Xlogin,"asociadas","idsocio",$iduser,"idempresa",1,$master);
		//echo "insert into asociados (idempresa,idsocio,socio,empresa) select u.iduser,'$iduser','$login',u.login from usuarios u where iduser='$master' on duplicate key update empresa=u.login,idempresa=u.iduser";
		if($master!=$iduser){
			qry("insert into asociadas (idempresa,idsocio,socio,empresa,idpatro) select u.iduser,'$iduser','$login',u.login,a.idpatro from usuarios u left join asociadas a on a.idsocio='$iduser' where iduser='$master' on duplicate key update empresa=u.login,idempresa=u.iduser");
			echo "insert into asociadas (idempresa,idsocio,socio,empresa,idpatro) select u.iduser,'$iduser','$login',u.login,a.idpatro from usuarios u left join asociadas a on a.idsocio='$iduser' where iduser='$master' on duplicate key update empresa=u.login,idempresa=u.iduser";
			qry("update asociadas_day a, usuarios u set a.empresa=u.login, a.idempresa=u.iduser where substr(curdate(),1,7)=substr(a.fecha,1,7) and a.idsocio='$iduser' and u.iduser='$master' and fecha>='2017-05-03'");
			echo "UPLINE CAMBIADO";	
		}
		
	}
	if($Xpermiso==999 and $a=="change_patro" and $master>0){
		tracechange($Xlogin,"asociadas","idsocio",$iduser,"idpatro",1,$master);
		//echo "insert into asociados (idempresa,idsocio,socio,empresa) select u.iduser,'$iduser','$login',u.login from usuarios u where iduser='$master' on duplicate key update empresa=u.login,idempresa=u.iduser";
		if($master!=$iduser){
			//	qry("insert into asociadas (idempresa,idsocio,socio,empresa) select u.iduser,'$iduser','$login',u.login from usuarios u where iduser='$master' on duplicate key update empresa=u.login,idempresa=u.iduser");
			//echo "update asociadas_day a, usuarios u set a.empresa=u.login, a.idempresa=u.iduser where substr(curdate(),1,7)=substr(a.fecha,1,7) and a.idsocio='$iduser' and u.iduser='$master' and fecha>='2017-05-03'";
			qry("update asociadas a, usuarios u set  a.idpatro=u.iduser where a.idsocio='$iduser' and u.iduser='$master' ");
			qry("update asociadas_day a, usuarios u set  a.idpatro=u.iduser where substr(curdate(),1,7)=substr(a.fecha,1,7) and a.idsocio='$iduser' and u.iduser='$master' and fecha>='2017-05-03'");
			echo "PATRO CAMBIADO";	
		}
		
	}	
	if($Xpermiso==999 and $a=="reset"){
		tracechange($Xlogin,"user_nivel","iduser",$iduser,"deuda",1,3);	
		qry("update user_nivel set deuda=3 where iduser='$iduser' ");
		echo "USUARIO RESETEADO";
	}
	
	$tab2="<table class='table table-bordered'><thead><tr>";
	$res=qry("describe user_nivel ");
	while($r=mysql_fetch_row($res)){
		$tab2.="<th>$r[0]</th>";
		
	}
	$tab2.="</tr></thead><tbody>";
	$res=qry(" select * from user_nivel where iduser='$iduser'");
	$tipoo="";
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab2.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab2.="<td>$r[$i]</td>";
			$tipoo="$r[$i]";
		}
		$tipoo=$r[($i-5)];
		$tab2.="</tr>";
	}
	$tab2.="</tbody></body>";
	
	$tab3="<table class='table table-bordered'><thead><tr>";
	$res=qry("describe puntos ");
	while($r=mysql_fetch_row($res)){
		$tab3.="<th>$r[0]</th>";
	}
	$tab3.="</tr></thead><tbody>";
	$res=qry("select a.*,b.idpadre2 from puntos a left join operacionesp b on b.idop=a.idop where a.iduser='$iduser'");
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab3.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab3.="<td>$r[$i]</td>";
		}
		$tab3.="</tr>";
	}
	$tab3.="</tbody></body>";
	
	
	$tab4="<table class='table table-bordered'><thead><tr><th>PAPA</th></tr><tr><th>Nombre</th><th>Apellidos</th>";
	$res=qry("describe asociadas ");
	while($r=mysql_fetch_row($res)){
		$tab4.="<th>$r[0]</th>";
	}
	$tab4.="</tr></thead><tbody>";
	$res=qry("select b.nombre,b.apellidos,a.* from asociadas a left join usuarios b on b.iduser=a.idempresa where idsocio='$iduser'");
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab4.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab4.="<td>$r[$i]</td>";
		}
		$tab4.="</tr>";
	}
	$tab4.="</tbody></body>";
	
	$tab7="<table class='table table-bordered'><thead><tr><th>UPLINE</th></tr><tr><th>Nombre</th><th>Apellidos</th>";
	$res=qry("describe asociadas ");
	while($r=mysql_fetch_row($res)){
		$tab7.="<th>$r[0]</th>";
	}
	$tab7.="</tr></thead><tbody>";
	$res=qry("select b.nombre,b.apellidos,a.* from asociadas a left join usuarios b on a.idsocio=b.iduser where idempresa='$iduser'");
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab7.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab7.="<td>$r[$i]</td>";
		}
		$tab7.="</tr>";
	}
	$tab7.="</tbody></body>";
	
	
	$tab10="<table class='table table-bordered'><thead><tr><th>PATROCINAR</th></tr><tr><th>Nombre</th><th>Apellidos</th>";
	$res=qry("describe asociadas ");
	while($r=mysql_fetch_row($res)){
		$tab10.="<th>$r[0]</th>";
	}
	$tab10.="</tr></thead><tbody>";
	$res=qry("select b.nombre,b.apellidos,a.* from asociadas a left join usuarios b on a.idsocio=b.iduser where idpatro='$iduser'");
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab10.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab10.="<td>$r[$i]</td>";
		}
		$tab10.="</tr>";
	}
	$tab10.="</tbody></body>";
	
	
	$tab5="<table class='table table-bordered'><thead><tr><th>HIJOS</th></tr><tr>";
	$res=qry("describe asociadas ");
	while($r=mysql_fetch_row($res)){
		$tab5.="<th>$r[0]</th>";
	}
	$tab5.="</tr></thead><tbody>";
	$res=qry("select * from asociadas where idempresa='$iduser'");
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab5.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab5.="<td>$r[$i]</td>";
		}
		$tab5.="</tr>";
	}
	$tab5.="</tbody></body>";
	
	$tab5="<table class='table table-bordered'><thead><tr><th>CICLO</th></tr><tr>";
	$res=qry("describe user_nivel_day ");
	while($r=mysql_fetch_row($res)){
		$tab5.="<th>$r[0]</th>";
	}
	$tab6.="</tr></thead><tbody>";
	$res=qry("select * from user_nivel_day where iduser='$iduser'");
	while($r=mysql_fetch_row($res)){
		$n=count($r);
		$tab6.="<tr>";
		for($i=0;$i<$n;$i++){
			$tab6.="<td>$r[$i]</td>";
		}
		$tab6.="</tr>";
	}
	$tab6.="</tbody></body>";
	
	///
	$res=qry("select a.iduser,UPPER(CONCAT(apellidos,' ',nombre,' (',a.login,')')) from usuarios a,user_nivel b where a.activo=1 and a.idpersona>0 and a.iduser=b.iduser and b.estado=1 order by apellidos;");
	$lstu="";
	while($temp=mysql_fetch_row($res)){ $lstu.="<option value='".$temp[0]."'>".$temp[1]."</option>"; }
	
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
		
		<form method="GET" class="form-inline">
			<input name="login" type="hidden" value="<?php echo $login;?>"/>
			<input name="iduser" type="hidden" value="<?php echo $iduser;?>"/>
			<input name="a" type="hidden" value="change_master"/>
			<label for="tipo">UPLINE</label>
			<select id="usr" name="master" class="form-control">
				<?php echo $lstu;?>
			</select>
			<button type="submit" class="btn btn-info">CAMBIAR</button>
		</form>
		<form method="GET" class="form-inline">
			<input name="login" type="hidden" value="<?php echo $login;?>"/>
			<input name="iduser" type="hidden" value="<?php echo $iduser;?>"/>
			<input name="a" type="hidden" value="change_patro"/>
			<label for="tipo">PATROCINADOR</label>
			<select id="usr1" name="master" class="form-control">
				<?php echo $lstu;?>
			</select>
			<button type="submit" class="btn btn-info">CAMBIAR</button>
		</form>
		<?php echo $tab1."<br/>".$tab2."<br/>".$tab3."<br/>".$tab4."<br/>".$tab7."<br/>".$tab10."<br/>".$tab5."<br/>".$tab6;?>
		<script src="../js/jquery.js"></script>
		<script src="../js/alertify.js"></script>
		<script src="../js/busq_select.js"></script>		
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
			$("#usr").busc({limit: 10});
			$("#usr1").busc({limit: 10});
			});
		</script>
	</body>
</html>	