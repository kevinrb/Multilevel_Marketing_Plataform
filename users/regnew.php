<?php
	require_once("../sec.php");
	require_once("../ws/func.php");
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	if($pst_tar==4){
		$Xlocal="Chorrillos";
		$sql="INSERT ignore INTO usuarios_temp (login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,activo,passwd,local,nivel,meta1,edobs) 
		VALUES ('$pst_dni','$pst_dni','$pst_nombre','$pst_apellidos','$pst_email','$pst_yearnn-$pst_monnn-$pst_daynn','$pst_direc','$pst_telefono','$pst_celular',1,'123456','$Xlocal','$Xlogin','$pst_monto','$pst_vou-=-$pst_idcuenta')";
		$psql=qry($sql);
		
		$ubi=mysql_fetch_row(mysql_query("SELECT id FROM ubigeo WHERE departamento='$pst_depa' AND provincia='$pst_prov' AND distrito='$pst_dist'"));	 
		qry("insert ignore into clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal) value ('$pst_dni','$pst_nombre $pst_apellidos','$pst_direc','$pst_email','$pst_telefono',1,'$pst_celular','$pst_dni',now(),'$Xlocal','CATALOGO')");
		$qrycli="insert ignore into clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal) value ('$pst_dni','$pst_nombre $pst_apellidos','$pst_direc','$pst_email','$pst_telefono',1,'$pst_celular','$pst_dni',now(),'$Xlocal','CATALOGO')";
		
		qry("update clientes set lastupdate=now(), nombre='$pst_nombre $pst_apellidos',direccion='$pst_direc',ubigeo='$ubi[0]',email='$pst_email',fono1='$pst_telefono',tipo=1,fono2='$pst_celular',codigo1='$pst_dni',local='$Xlocal' where idcliente='$pst_dni' and canal='CATALOGO'");
		$qryupd="update clientes set lastupdate=now(), nombre='$pst_nombre $pst_apellidos',direccion='$pst_direc',ubigeo='$ubi[0]',email='$pst_email',fono1='$pst_telefono',tipo=1,fono2='$pst_celular',codigo1='$pst_dni',local='$Xlocal' where idcliente='$pst_dni' and canal='CATALOGO'";
		/////Nuevo Sistema
		/*
			mysql_query("insert ignore into erpd.clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal) value ('$pst_ruc','$pst_razon','$pst_direc','$pst_email','$pst_telefono',1,'$pst_celular','$pst_dni',now(),'$pst_local','CATALOGO')");
		mysql_query("update erpd.clientes set lastupdate=now(), nombre='$pst_razon',direccion='$pst_direc',email='$pst_email',fono1='$pst_telefono',tipo=1,fono2='$pst_celular',codigo1='$pst_dni' where idcliente='$pst_ruc'");*/
		echo $idsms=posturl(array("a"=>"new_user","qryuser"=>$sql,"qrycli"=>$qrycli,"qryupd"=>$qryupd),$XXurl_erp1);
	}
	
	
	date_default_timezone_set('America/Lima');
	$tabla_mes=array("1"=>"ENERO","2"=>"FEBRERO","3"=>"MARZO","4"=>"ABRIL","5"=>"MAYO","6"=>"JUNIO","7"=>"JULIO","8"=>"AGOSTO","9"=>"SEPTIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE", "12"=>"DICIEMBRE");
	$today = date("Y-m-d H:i:s");
	$cury = date("Y");
	$curm = date("m");
	$yint= intval($cury);
	$dia="";
	$mes="";
	$year="";
	$xx=array();
	
	$tabb=4;
	
	
	////////////////////////////////////////////////////////////////////////////////////
	$res1=mysql_query("select distinct departamento from ubigeo");
	$res2=mysql_query("select departamento,provincia,distrito from ubigeo");
	$deppro=array();
	$prodis=array();
	$artemp=array();
	$depart="";
	while($temp=mysql_fetch_row($res1)){
		if($temp[0]==$xx[21]){$at="selected";} else{$at="xx";} $depart .= "<option value='$temp[0]' $at>$temp[0]</option>";
	}
	while($temp=mysql_fetch_row($res2)){
		if(!isset($artemp[$temp[0]])){$artemp[$temp[0]]=array();}
		if(in_array($temp[1],$artemp[$temp[0]])){}
		else{$deppro[$temp[0]][]="<option value='$temp[1]'>$temp[1]</option>"; $artemp[$temp[0]][]=$temp[1];}
		$prodis[$temp[0]][$temp[1]][]="<option value='$temp[2]'>$temp[2]</option>";
	}
	$deppro=json_encode($deppro);
	$prodis=json_encode($prodis);
	////////////////////////////////////////////////////////////////////////////////////
	
	
	
	$qry1 = "select local from locales order by local";
	$res1 = mysql_query($qry1) or die("ERROR D: " . mysql_error());
	$local="<option value=''>seleccione</option>";
	while($r = mysql_fetch_row($res1)) {
		$sel="";
		//if($xx[20]==$r[0])
		if("Almacen"==$r[0])
		$sel='selected';
		$local.="<option $sel>$r[0]</option>";
	}
	
	$dist .= "</optgroup>";
	$tip="<input class='form-control'  name='tipo' id='tipo' value='$tipo' type='hidden'>";
	
	$exi=qry("SELECT EXISTS(SELECT 1 FROM permisos WHERE idfunc ='999' and login='$Xlogin' LIMIT 1)");
	$exi=mysql_fetch_row($exi);
	$exi=$exi[0];
	$a="login='$Xlogin'";
	if($exi==1)
	{
		$a="b.cod_usu_sn like '%E%'";
	}
	
	$qry = "select b.login , b.nombre from usuarios b order by nombre";
	$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
	$lid = $sel="";
	while($r = mysql_fetch_row($res)) {
		$sel="";
		if($Xlogin==$r[0])
		$sel='selected';
		$lid .= "<option value='$r[0]' $sel>$r[1]</option>";
	}
	
	$fech=explode("-",$xx[4]);
	
	$qry1 = "select idcuenta,nombre from cuentas order by nombre";
	$res1 = mysql_query($qry1) or die("ERROR D: " . mysql_error());
	$cuen="<option value=''>seleccione</option>";
	while($r = mysql_fetch_row($res1)) {
		$cuen.="<option value='$r[0]'>$r[1]</option>";
	}
	
	
	if($xx[14]==1)
	$check="checked";
	$aa='<div id="tabs">'.$tip.' 
	<div id="tabs-1" style=" overflow: auto;" class="container">
	<div class="row">
	<div class="col-sm-6">
	<legend>DATOS PERSONALES</legend>
	<div class="form-group"><label for="">DNI*</label><input  required class="form-control"  type="text" name="dni" id="dni" value="'.$xx[0].'"></div>
	<div class="form-group"><label class="lab">Nombre*</label><input required class="form-control"  type="text" name="nombre" id="nombre" value="'.$xx[1].'"></div>
	<div class="form-group"><label for="">Apellidos*</label><input required class="form-control"  type="text" name="apellidos" id="apellidos" value="'.$xx[2].'"></div>
	
	<div class="form-group"><label for="">E-Mail</label><input class="form-control"  type="text" name="email" id="email" value="'.$xx[3].'"></div>
	<div class="form-group"><label for="">Dirección*</label><input required class="form-control"  type="text" name="direc" id="direc" value="'.$xx[5].'"></div>
	<div class="form-group col-md-4">
	<label>DPTO.*</label>
	<select required class="dataloc form-control"  name="depa" id="depa"><option value="" selected disabled>--Elegir--</option>'.$depart.'</select>
	</div>
	
	<div class="form-group col-md-4">
	<label>PROV.*</label>
	<select required class="dataloc form-control" name="prov" id="prov"><option>'.$xx[22].'</option></select>
	</div>
	
	<div class="form-group col-md-4">
	<label>DIST.*</label>
	<select required class="dataloc form-control" name="dist" id="dist"><option>'.$xx[23].'</option></select>
	</div>
	<div class="form-group"><label for="">Fecha de Nacimiento</label>
	<div class="form-inline">
	<select class=" form-control" required id="daynn" name="daynn">
	</select> 
	<select  class=" form-control" required id="monnn" name="monnn">
	</select> 
	<select class=" form-control" required id="yearnn" name="yearnn">
	</select> 
	</div>
	
	</div>
	<div class="form-group">
	<label for="">Telefono</label><input  class="form-control"  type="text" name="telefono" id="telefono" value="'.$xx[6].'">
	</div>
	<div class="form-group">
	<label for="">Celular</label><input  class="form-control"  type="text" name="celular" id="celular" value="'.$xx[7].'">
	</div>
	</div>
	
	<div class="col-sm-6" >
	
	
	
	
	<legend>SANTA NATURA</legend>
	
	<div class="form-group">
	<label for="">EMPRESARIO</label>
	<select class="form-control" name="lider" id="lider" disabled><option value="">--ninguno--</option>'.$lid.'</select>
	</div>
	<div class="form-group">
	<label for="">LOCAL</label>
	<select class="form-control" name="local" id="local" disabled>'.$local.'</select>
	</div>
	<legend>DEPOSITO</legend>
	<div class="form-group">
	<label for="">Cuenta*</label>
	<select  type="text" required  class="form-control" name="idcuenta" id="idcuenta">'.$cuen.'
	
	</select>
	</div>
	<div class="form-group">
	<label for="">Nro de Voucher*</label>
	<input  type="text" required  class="form-control" name="vou" id="vou" />
	</div>
	<div class="form-group">
	<label for="">Monto*</label>
	<input type="text" required class="form-control" name="monto" id="monto" />
	</div>
	</div>
	</div>
	
	<div class="row text-center">
	
	<input  type="submit" class=" btn btn-primary" id="savecambios" value="GUARDAR">
	<a  href="list_socios.php" class="btn btn-primary" id="cancel" value="CANCELAR">CANCELAR</a>
	
	</div>
	
	</div>
	</div>';
	
	
	
	
	
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>REGISTRO DE USUARIOS</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" media="screen" href="../css/bootstrap-datetimepicker.min.css">
	</head>
	<body>
		<form method="POST" id="gadmin">
			<input type='hidden' name='tar' value="4">
			<?php echo $aa;?>
		</form>
		<script src="../js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script src='../js/bootstrap-datetimepicker.min.js'></script>
		<script src='../js/bootstrap-datetimepicker.es.js'></script>
		<script>
			/***********************************************
			* Drop Down Date select script- by JavaScriptKit.com
			* This notice MUST stay intact for use
			* Visit JavaScript Kit at http://www.javascriptkit.com/ for this script and more
			***********************************************/
			
			var monthtext=['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sept','Oct','Nov','Dec'];
			
			function populatedropdown(dayfield, monthfield, yearfield){
			var today=new Date()
			var dayfield=document.getElementById(dayfield)
			var monthfield=document.getElementById(monthfield)
			var yearfield=document.getElementById(yearfield)
			for (var i=1; i<32; i++)
			dayfield.options[i]=new Option(i, i+1)
			dayfield.options[today.getDate()]=new Option(today.getDate(), today.getDate(), true, true) //select today's day
			for (var m=0; m<12; m++)
			monthfield.options[m]=new Option(monthtext[m], m+1)
			monthfield.options[today.getMonth()]=new Option(monthtext[today.getMonth()], today.getMonth()+1, true, true) //select today's month
			var thisyear=2000;
			for (var y=0; y<50; y++){
			yearfield.options[y]=new Option(thisyear, thisyear)
			thisyear-=1
			}
			//yearfield.options[0]=new Option(today.getFullYear(), today.getFullYear(), true, true) //select today's year
			}
			
			
			$(document).ready(function(){
			populatedropdown("daynn", "monnn", "yearnn");
			var deppro=<?php echo $deppro;?>;
			var prodis=<?php echo $prodis;?>;
			$("body").delegate('#depa','change',function(){ $("#prov").html("<option selected disabled value=''>--Elija--</option>"+deppro[$(this).val()]); $("#dist").html("");});
			$("body").delegate('#prov','change',function(){ $("#dist").html("<option selected disabled value=''>--Elija--</option>"+prodis[$("#depa").val()][$(this).val()]);});
			
			
			$('body').delegate('.delatr','click',function(){
			var o1=$(this).attr('naat');
			var o2="#";
			var op=String(o2)+String(o1);
			$(op).remove();
			});
			
			
			
			$('body').delegate('#savecambios','click',function(){
			var ll=$('#login').val();
			var nn=$('#nombre').val();
			var ap=$('#apellidos').val();
			var d=$('#dni').val();
			var mail=$('#email').val();
			var dir=$('#direc').val();
			var fn=$('#ffnn').val();
			var fi=$('#ffii').val();
			var rc=$('#ruc').val();
			var rz=$('#razon').val();
			var dt=$('#dis').val();
			var tp=$('#tipo').val();
			var lid=$('#lider').val();
			var tel=$('#telefono').val();
			var cel=$('#celular').val();
			var niv=$('#niv :selected').attr('value');
			var obs=$('#obs').val();
			var ex=$('#expe').val();
			var cods=$('#codsn').val();
			var act=$('#activo').val();
			var dp=$('#depa').val();
			var local=$('#local').val();
			var vou=$('#vou').val();
			var mon=$('#monto').val();
			var idcu=$('#idcuenta').val();
			var c=<?php echo $tabb;?>;
			if(nn=="" || rc=="" || rz=="" || d=="" || ap==""  || local=="" || dir=="" || vou=="" || mon=="" || idcu=="" ){
			alert('COMPLETE TODOS LOS CAMPOS OBLIGATORIOS(*)');
			}
			else{
			
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (!filter.test(mail) && mail!="") {
			alert('Direccion de Correo Invalida');
			}
			
			else{
			$("#gadmin").submit();
			} 
			}
			});
			
			$('#cancel').click(function(){
			window.open("regvend.php",'_self');
			});
			});
		</script>
	</body>
</html>