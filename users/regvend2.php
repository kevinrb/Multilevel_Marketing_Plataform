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
	$tab_niv=array("pri"=>"PRIMARIA","sec"=>"SECUNDARIA","sup"=>"SUPERIOR");
	$xx=array();
	$tabb=3;
	if($_GET["id"]>0)
	{
		$tabb=4;
		$sql="SELECT a.idpersona,a.nombre,a.apellidos,a.email,a.ffnn,b.direccion,a.telefono,a.celular,a.nivel,a.edobs,a.ffii,a.experiencia,a.foto,a.cod_usu_sn,a.activo,b.idcliente,b.nombre,b.distrito,b.loginpadre,a.direccion,a.local
		FROM usuarios a, clientes b WHERE a.cod_usu_sn=b.cod_usu_sn and login='$id'";
		$psql=mysql_query($sql);
		$cc=mysql_num_rows($psql); 
		$xx=mysql_fetch_row($psql);
	}
	
	$qry1 = "select local from locales order by local";
	$res1 = mysql_query($qry1) or die("ERROR D: " . mysql_error());
	$local="<option value=''>seleccione</option>";
	while($r = mysql_fetch_row($res1)) {
		$sel="";
		if($xx[20]==$r[0])
		$sel='selected';
		$local.="<option $sel>$r[0]</option>";
	}
	/*
		$qry1 = "select * from vlciudades order by ciudad";
		$res1 = mysql_query($qry1) or die("ERROR D: " . mysql_error());
		$ciud="<option value=''>seleccione</option>";
		while($r = mysql_fetch_row($res1)) {
		$sel="";
		if($xx[19]==$r[0])
		$sel='selected';
		
		$ciud.="<option $sel>$r[0]</option>";
		}
	*/
	/*
		$qry = "select * from distritos where nombre != 'DELIVERYPROVINCIAS' and provincia!='OTROS' order by provincia desc,nombre";
		$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
		$ctrl = $dist = $sel="";
		$c = 0;
		while($r = mysql_fetch_row($res)) {
		$sel="";
		if($xx[17]==$r[1])
		$sel='selected';
		if($ctrl != $r[2]) {
		if($c > 0)
		$dist .= "</optgroup>";
		$dist .= "<optgroup label='Distritos de $r[2]'>";
		$ctrl = "$r[2]";
		$c++;
		}
		$dist .= "<option value='$r[1]' $sel>$r[1]</option>";
		}
		
	*/
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
	$aa='
	<div id="tabs">'.$tip.' 
	<div id="tabs-1" style=" overflow: auto;" class="container">
	<div class="row">
	<div class="col-sm-6">
	<legend>DATOS PERSONALES</legend>
	<div class="datos"><div class="lab">Login</div><input class="form-control"  type="text" name="login" id="login" value="'.$id.'" readonly></div>
	<div class="datos"><div class="lab">Activo</div><input class="form-control"  type="checkbox" name="activo" id="activo" value=1 '.$check.' ></div>
	<div class="datos"><div class="lab">Nombre*</div><input class="form-control"  type="text" name="nombre" id="nombre" value="'.$xx[1].'"></div>
	<div class="datos"><div class="lab">Apellidos*</div><input class="form-control"  type="text" name="apellidos" id="apellidos" value="'.$xx[2].'"></div>
	<div class="datos"><div class="lab">DNI*</div><input class="form-control"  type="text" name="dni" id="dni" value="'.$xx[0].'"></div>
	<div class="datos"><div class="lab">E-Mail</div><input class="form-control"  type="text" name="mail" id="mail" value="'.$xx[3].'"></div>
	<div class="datos"><div class="lab">Dirección</div><input class="form-control"  type="text" name="direc" id="direc" value="'.$xx[5].'"></div>
	<div class="form-group col-md-4">
	<label>DPTO.</label>
	<select class="dataloc form-control" id="depa"><option value="" selected disabled>--Elegir--</option>'.$depart.'</select>
	</div>
	
	<div class="form-group col-md-4">
	<label>PROV.</label>
	<select class="dataloc form-control" id="prov"></select>
	</div>
	
	<div class="form-group col-md-4">
	<label>DIST.</label>
	<select class="dataloc form-control" id="dist"></select>
	</div>
	<div class="datos"><div class="lab">Fecha de Nacimiento</div>
	<div id="datetimepicker1" class="input-append">
	<input class="form-control"  data-format="yyyy-MM-dd" type="text" id="ffnn" value="'.$xx[4].'" style="font-size:13px !important;"></input>
	<span class="add-on">
	<i data-time-icon="icon-time" data-date-icon="icon-calendar">
	</i>
	</span>
	</div>
	</div>
	<div class="datos">
	<div class="lab">Telefono</div><input class="form-control"  type="text" name="telefono" id="telefono" value="'.$xx[6].'">
	</div>
	<div class="datos">
	<div class="lab">Celular</div><input class="form-control"  type="text" name="celular" id="celular" value="'.$xx[7].'">
	</div>
	</div>
	
	<div class="col-sm-6" >
	
	
	<legend>DATOS PARA EL COMPROBANTE</legend>
	<div class="datos"><div class="lab">RUC o DNI*</div><input class="form-control"  type="text" name="ruc" id="ruc" value="'.$xx[15].'"></div>
	<div class="datos"><div class="lab">Razon Social*</div><input class="form-control"  type="text" name="razon" id="razon" value="'.$xx[16].'"></div>
	
	<div class="divedu">
	<div class="datos">
	<div class="lab">Fecha Ingreso</div>
	<div id="datetimepicker2" class="input-append">
	<input class="form-control"  data-format="yyyy-MM-dd" type="text" id="ffii" value="'.$xx[10].'" style="font-size:13px !important;" ></input>
	<span class="add-on">
	<i data-time-icon="icon-time" data-date-icon="icon-calendar">
	</i>
	</span>
	</div>
	</div>
	
	</div>
	
	<legend>SANTA NATURA</legend>
	
	<div class="datos">
	<div class="lab">EMPRESARIO</div>
	<select class="form-control" name="lider" id="lider"><option value="">--ninguno--</option>'.$lid.'</select>
	</div>
	<div class="datos">
	<div class="lab">LOCAL</div>
	<select class="form-control" name="local" id="local" readonly>'.$local.'</select>
	</div>
	<div class="datos">
	<div class="lab">Codigo SN</div>
	<input class="form-control"  type="text" name="codsn" id="codsn" value="'.$xx[13].'" readonly>
	
	</div> 
	
	</div>
	</div>
	
	<div class="row">
	<input  type="button" class="btn btn-primary" id="savecambios" value="GUARDAR">
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
		<script src="../js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script src='../js/bootstrap-datetimepicker.min.js'></script>
		<script src='../js/bootstrap-datetimepicker.es.js'></script>
		<script>
			$(document).ready(function(){
			var deppro=<?php echo $deppro;?>;
			var prodis=<?php echo $prodis;?>;
			$("body").delegate('#depa','change',function(){ $("#prov").html("<option selected disabled value=''>--Elija--</option>"+deppro[$(this).val()]); $("#dist").html("");});
			$("body").delegate('#prov','change',function(){ $("#dist").html("<option selected disabled value=''>--Elija--</option>"+prodis[$("#depa").val()][$(this).val()]);});
			
			$("#ffnn").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
			pickerPosition: 'top-left',
			autoclose: true,
			language:'es',minView:2,
			startDate:'-40y',
			endDate:'-10y'
			});
			
			$("#ffii").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
			pickerPosition: 'top-left',
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
			if(nn=="" || rc=="" || rz=="" || d=="" || ap==""  || local==""){
			alert('COMPLETE TODOS LOS CAMPOS OBLIGATORIOS(*)');
			}
			else{
			
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (!filter.test(mail) && mail!="") {
			alert('Direccion de Correo Invalida');
			}
			
			else{
			
			$.ajax({
			type: 'POST',
			url: 'uservl/proc.php',
			data: {login:ll, nombre: nn, apellidos:ap, dni:d, email:mail, direc:dir, ffnn:fn, telefono:tel, celular:cel , nivel:niv , edobs:obs, exp:ex, ffii:fi, tar:c, codsn:cods,activo:act,ruc:rc,razon:rz,dis:dt,lider:lid,tipo:tp,depa:dp,local:local},
			success: function(data){
			window.open("regvend<?php echo $tipo?>.php",'_self');
			
			}
			});
			} 
			}
			});
			
			$('#cancel').click(function(){
			window.open("regvend.php",'_self');
			});
			});
		</script>
	</head>
	<body>
		<?php echo $aa;?>
	</body>
</html>