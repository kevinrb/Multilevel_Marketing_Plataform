<?php
	require_once("../sec.php");
	////////////////////////////////////////////////////////////////////////////////////
	$res1=mysql_query("select distinct departamento from ubigeo");
	$res2=mysql_query("select departamento,provincia,distrito from ubigeo");
	$deppro=array();
	$prodis=array();
	$artemp=array();
	$depart="";
	while($temp=mysql_fetch_row($res1)){
		if($temp[0]==$inf[0]){$at="selected";} else{$at="xx";} $depart .= "<option value='$temp[0]' $at>$temp[0]</option>";
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
	$tabb=3;
	
	if($pst_tar==3){
		$sql="INSERT ignore INTO usuarios (login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,nivel,edobs,ffii,experiencia,cod_usu_sn,activo,passwd,local) 
		VALUES ('$pst_dni','$pst_dni','$pst_nombre','$pst_apellidos','$pst_email','$pst_ffnn','$pst_depa','$pst_telefono','$pst_celular','$pst_nivel','$pst_edobs','$pst_ffii','$pst_exp','$pst_codsn','$pst_activo','123456','$pst_local')";
		$psql=mysql_query($sql) or die(mysql_error());
		
		
		mysql_query("insert into permisos values ('$pst_dni',605)");
		//$sql="UPDATE usuarios SET nombre='$pst_nombre',apellidos='$pst_apellidos',email='$pst_email',      direccion='$pst_direc',telefono='$pst_telefono',celular='$pst_celular',ffnn='$pst_ffnn',	  nivel='$pst_nivel',edobs='$pst_edobs',ffii='$pst_ffii',experiencia='$pst_exp',cod_usu_sn='$pst_codsn',activo='$pst_activo' WHERE idpersona='$pst_dni' ";
		//$psql=mysql_query($sql);
		
		
		mysql_query("insert ignore into clientes (idcliente,nombre,direccion,distrito,email,fono,tipo,movil,loginpadre,cod_usu_sn,lastupdate,local) value ('$pst_ruc','$pst_razon','$pst_direc','$pst_dis','$pst_email','$pst_telefono',1,'$pst_celular','$pst_lider','$pst_codsn',now(),'$pst_local')") or die(mysql_error());
		
		$qrycli="insert ignore into clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal) value ('$pst_ruc','$pst_razon','$pst_direc','$pst_email','$pst_telefono',1,'$pst_celular','$pst_dni',now(),'$pst_local','CATALOGO')";
		
		mysql_query("update clientes set lastupdate=now(), nombre='$pst_razon',direccion='$pst_direc',distrito='$pst_dis',email='$pst_email',fono='$pst_telefono',tipo=1,movil='$pst_celular',loginpadre='$pst_lider',cod_usu_sn='$pst_codsn' where idcliente='$pst_ruc'") or die(mysql_error());
		
		$qryupd="update clientes set lastupdate=now(), nombre='$pst_razon',direccion='$pst_direc',email='$pst_email',fono1='$pst_telefono',tipo=1,fono2='$pst_celular',codigo1='$pst_dni' where idcliente='$pst_ruc'";
		
		/////Nuevo Sistema
		mysql_query("insert ignore into erpd.clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal) value ('$pst_ruc','$pst_razon','$pst_direc','$pst_email','$pst_telefono',1,'$pst_celular','$pst_dni',now(),'$pst_local','CATALOGO')");
		mysql_query("update erpd.clientes set lastupdate=now(), nombre='$pst_razon',direccion='$pst_direc',email='$pst_email',fono1='$pst_telefono',tipo=1,fono2='$pst_celular',codigo1='$pst_dni' where idcliente='$pst_ruc'");
		$idsms=posturl(array("a"=>"users","qryuser"=>$sql,"qrycli"=>$qrycli,"lider"=>$pst_lider,"qryupd"=>$qryupd,"login"=>$pst_dni),$XXurl_erp4);
	}
	
	
	
	if($_GET["id"]>0)
	{
		$tabb=4;
		$sql="SELECT a.idpersona,a.nombre,a.apellidos,a.email,a.ffnn,b.direccion,a.telefono,a.celular,a.nivel,a.edobs,a.ffii,a.experiencia,a.foto,a.cod_usu_sn,a.activo,b.idcliente,b.nombre,'',c.empresa,a.direccion,a.local
		FROM (usuarios a, clientes b) left join asociadas c  on c.socio=a.login WHERE a.login=b.codigo1 and login='$id'";
		echo $sql;
		$psql=mysql_query($sql);
		$cc=mysql_num_rows($psql); 
		$xx=mysql_fetch_row($psql);
	}
	
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
		if($xx[18]==$r[0])
		$sel='selected';
		$lid .= "<option value='$r[0]' $sel>$r[1]</option>";
	}
	
	if($xx[14]==1)
	$check="checked";
	$aa='<div id="tabs">'.$tip.' 
	<div id="tabs-1" style=" overflow: auto;" class="container">
	<div class="row">
	<div class="col-sm-6">
	<legend>DATOS PERSONALES</legend>
	<div class="form-group"><label class="lab">Nombre*</label><input required class="form-control"  type="text" name="nombre" id="nombre" value="'.$xx[1].'"></div>
	<div class="form-group"><label for="">Apellidos*</label><input required class="form-control"  type="text" name="apellidos" id="apellidos" value="'.$xx[2].'"></div>
	<div class="form-group"><label for="">DNI*</label><input required class="form-control"  type="text" name="dni" id="dni" value="'.$xx[0].'"></div>
	<div class="form-group"><label for="">E-Mail</label><input class="form-control"  type="text" name="mail" id="mail" value="'.$xx[3].'"></div>
	<div class="form-group"><label for="">Dirección*</label><input required class="form-control"  type="text" name="direc" id="direc" value="'.$xx[5].'"></div>
	<div class="form-group col-md-4">
	<label>DPTO.*</label>
	<select required class="dataloc form-control"  name="depa" id="depa"><option value="" selected disabled>--Elegir--</option>'.$depart.'</select>
	</div>
	
	<div class="form-group col-md-4">
	<label>PROV.*</label>
	<select required class="dataloc form-control" name="prov" id="prov"></select>
	</div>
	
	<div class="form-group col-md-4">
	<label>DIST.*</label>
	<select required class="dataloc form-control" name="dist" id="dist"></select>
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
	
	
	<legend>DATOS PARA EL COMPROBANTE</legend>
	<div class="form-group"><label for="">RUC o DNI*</label><input required class="form-control"  type="text" name="ruc" id="ruc" value="'.$xx[15].'"></div>
	<div class="form-group"><label for="">Razon Social*</label><input  required class="form-control"  type="text" name="razon" id="razon" value="'.$xx[16].'"></div>
	
	<div class="divedu">
	
	
	</div>
	
	<legend>SANTA NATURA</legend>
	
	<div class="form-group">
	<label for="">EMPRESARIO</label>
	<select class="form-control" name="lider" id="lider"><option value="">--ninguno--</option>'.$lid.'</select>
	</div>
	<div class="form-group">
	<label for="">LOCAL</label>
	<select class="form-control" name="local" id="local" disabled>'.$local.'</select>
	</div>
	</div>
	</div>
	
	<div class="row text-center">
	
	<input  type="submit" class=" btn btn-primary" id="savecambios" value="GUARDAR">
	<input  type="button" class="btn btn-primary" id="cancel" value="CANCELAR">
	
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
			monthfield.options[m]=new Option(monthtext[m], monthtext[m])
			monthfield.options[today.getMonth()]=new Option(monthtext[today.getMonth()], monthtext[today.getMonth()], true, true) //select today's month
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
			
			$("#ffnn").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
			
			autoclose: true,
			language:'es',minView:2,
			startDate:'-40y',
			endDate:'-10y'
			});
			
			$("#ffii").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
			
			autoclose: true,
			language:'es',minView:2,
			});
			
			
			
			
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
			var mail=$('#mail').val();
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
			var c=<?php echo $tabb;?>;
			if(nn=="" || rc=="" || rz=="" || d=="" || ap==""  || local=="" || dir==""){
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