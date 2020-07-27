<?php
	require_once("../sec.php");
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	if($pst_tar==4){
		$ubi=mysql_fetch_row(qry("SELECT id FROM ubigeo WHERE departamento='$pst_depa' AND provincia='$pst_prov' AND distrito='$pst_dist'"));
		$sql="UPDATE usuarios SET nombre='$pst_nombre',apellidos='$pst_apellidos',email='$pst_email',ffnn='$pst_yearnn-$pst_monnn-$pst_daynn',telefono='$pst_telefono',celular='$pst_celular',ubigeo='$ubi[0]',sexo='$pst_sex' WHERE login='$Xlogin' ";
		$psql=qry($sql);
		/*
			if($Xdisabled==0 and $Xnivelestado==1){
			qry("delete from asociadas where socio='$Xlogin'");
			qry("insert ignore into asociadas(socio,empresa,idsocio) values ('$Xlogin','$pst_lider','$Xiduser')");
			qry("update user_nivel set hora_disabled=null where login='$Xlogin'");
			echo "<script>alert('Nuevo Patrocinador asignado!');</script>";
			header("Location: reguser.php");
			}
		*/
		qry("insert into user_detrac(iduser,cuenta) values ('$Xiduser','$pst_detrac') on duplicate key update cuenta='$pst_detrac'");
		
		//echo "SELECT id FROM ubigeo WHERE departamento='$pst_depa' AND provincia='$pst_prov' AND distrito='$pst_dist'";
		qry("insert ignore into clientes (idcliente,nombre,direccion,ubigeo,email,fono1,tipo,fono2,codigo1,lastupdate,canal) value ('$pst_ruc','$pst_razon','$pst_direc','$ubi[0]','$pst_email','$pst_telefono',1,'$pst_celular','$Xlogin',now(),'RED')") or die(mysql_error());
		qry("update clientes set lastupdate=now(),nombre='$pst_razon',direccion='$pst_direc',ubigeo='$ubi[0]',email='$pst_email',fono1='$pst_telefono',tipo=1,fono2='$pst_celular' where idcliente='$Xlogin'") or die(mysql_error());
		
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
	$sql="SELECT a.idpersona,a.nombre,a.apellidos,a.email,a.ffnn,a.direccion,a.telefono,a.celular,a.nivel,a.edobs,a.ffii,a.experiencia,a.foto,a.cod_usu_sn,a.activo,'','','',c.empresa,a.direccion,a.local, d.departamento,d.provincia,d.distrito,a.sexo
	FROM usuarios a left join asociadas c  on c.socio=a.login left join ubigeo d on d.id=a.ubigeo WHERE a.login='$Xlogin'";
	//echo $sql;
	$psql=mysql_query($sql);
	$cc=mysql_num_rows($psql); 
	$xx=mysql_fetch_row($psql);
	
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
		//if("Almacen"==$r[0])
		if($xx[20]==$r[0])
		$sel='selected';
		$local.="<option $sel>$r[0]</option>";
	}
	
	$marcs="";
	$res=qry("select idbanco,nombre from bancos where estado=1 and idpais='$Xpais' order by nombre");
	$arr_marc=array();
	while($r=mysql_fetch_row($res))
	{
		$arr_marc[]=$r;
		$marcs.="<option value='$r[0]'>$r[1]</option>";
	}
	$a_marc=json_encode($arr_marc);
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
	
	$qry = "select b.login ,concat(b.nombre,' ',coalesce(b.apellidos,''),'(',b.login,')') from usuarios b,user_nivel a where a.login=b.login and b.activo=1 and a.deuda=0 and a.estado=1 order by nombre";
	$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
	$lid = $sel="";
	while($r = mysql_fetch_row($res)) {
		$sel="";
		if($xx[18]==$r[0])
		$sel='selected';
		$lid .= "<option value='$r[0]' $sel>$r[1]</option>";
	}
	
	if($Xdisabled==0 and $Xnivelestado==1){
		$sel_patr="<select class='form-control' name='lider' id='lider'><option value=''></option>$lid</select>";
		$js_p="$('#lider').busc({limit: 10});";
		}else{
		$sel_patr="<select class='form-control' name='lider' id='lider' disabled><option value=''>--ninguno--</option>$lid</select>";
		$js_p="";
	}
	
	$qry="select b.nombre,a.cuenta,a.cuenta_int,a.id from user_cuentas a left join bancos b on b.idbanco=a.idbanco where a.iduser='$Xiduser'";
	$res=qry($qry);
	$tbl="";
	$tbl1="";
	$arrmarc=array();
	while($r=mysql_fetch_row($res)){
		$tbl.="<tr><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td><button type='button' class='btn btn-danger btn-sm del_prov' data-id=$r[3]>
		<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
		</button></td></tr>";
		$tbl1.="<tr><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td></tr>";
	}
	
	$ren=mysql_fetch_row(qry("select cuenta from user_detrac where iduser='$Xiduser'"));
	$detrac=$ren[0];
	
	$fech=explode("-",$xx[4]);
	
	if($xx[14]==1)
	$check="checked";
	$aa='<div id="tabs">'.$tip.' 
	<div id="tabs-1" style="" class="container">
	<div class="row">
	<div class="col-sm-6">
	<legend>DATOS PERSONALES</legend>
	<div class="form-group"><label class="lab">Nombre*</label><input required class="form-control"  type="text" name="nombre" id="nombre" value="'.$xx[1].'"></div>
	<div class="form-group"><label for="">Apellidos*</label><input required class="form-control"  type="text" name="apellidos" id="apellidos" value="'.$xx[2].'"></div>
	<div class="form-group"><label for="">DNI*</label><input readonly required class="form-control"  type="text" name="dni" id="dni" value="'.$xx[0].'"></div>
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
	<div class="form-group col-md-8"><label for="">Fecha de Nacimiento</label>
	<div class="form-inline">
	<select class=" form-control" required id="daynn" name="daynn">
	</select> 
	<select  class=" form-control" required id="monnn" name="monnn">
	</select> 
	<select class=" form-control" required id="yearnn" name="yearnn">
	</select> 
	</div>
	</div>
	<div class="form-group col-md-4">
	<label>SEXO*</label>
	<select required class="form-control" name="sex" id="sex"><option>---</option><option value="M">MASCULINO</option><option value="F">FEMENINO</option></select>
	</div>
	<div class="form-group">
	<label for="">Telefono</label><input  class="form-control"  type="text" name="telefono" id="telefono" value="'.$xx[6].'">
	</div>
	<div class="form-group">
	<label for="">Celular</label><input  class="form-control"  type="text" name="celular" id="celular" value="'.$xx[7].'">
	</div>
	</div>
	
	<div class="col-sm-6" >
	
	<!--
	<legend>DATOS PARA EL COMPROBANTE</legend>
	<div class="form-group"><label for="">RUC o DNI*</label><input required class="form-control"  type="text" name="ruc" id="ruc" value="'.$xx[15].'"></div>
	<div class="form-group"><label for="">Razon Social*</label><input  required class="form-control"  type="text" name="razon" id="razon" value="'.$xx[16].'"></div>
	
	<div class="divedu">
	
	
	</div>-->
	
	<legend>SANTA NATURA</legend>
	
	<div class="form-group">
	<label for="">PATROCINADOR</label>
	'.$sel_patr.'
	</div>
	<div class="form-group">
	<label for="">LOCAL DE PEDIDOS</label>
	<select class="form-control" name="local" id="local" disabled>'.$local.'</select>
	</div>
	<a class="btn btn-primary btn-sm changename" data-toggle="modal" href="#modalname" data-id="8" title="Ajustes">CUENTAS</a>
	
	<table class="table table-bordered">
	<thead><tr><th>BANCO</th><th>CUENTA</th><th>CUENTA INTERBANCARIA</th></tr></thead>
	<tbody id="">
	'.$tbl1.'
	
	</tbody>
	</table>
	
	<div class="form-group"><label for="">Cta. DETRACCION*</label><input required class="form-control"  type="text" name="detrac" id="detrac" value="'.$detrac.'"></div>
	</div>
	</div>
	
	<div class="row text-center">
	
	<input  type="button" class=" btn btn-success" id="savecambios" value="GUARDAR">
	<!-- <a href="" type="button" class="btn btn-warning" id="cancel" >CANCELAR</a> -->
	
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
		
		
		<div class="modal fade" id="modalname" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">MIS CUENTAS</h4>
					</div>
					<div class="modal-body form-horizontal">
						
						<table class="table table-bordered">
							<thead><tr><th>BANCO</th><th>CUENTA</th><th>CUENTA INTERBANCARIA</th><th></th></tr></thead>
							<tbody id="t_prov">
								<?php echo $tbl;?>
								<tr><td><select class="form-control" id="idbanco"><option value="" selected>--seleccione marcacion--</option><?php echo $marcs;?></select></td><td><INPUT class="form-control" id="cuenta" type='text'/></td><td><INPUT class="form-control" id="cuenta_int" type='text'/></td><td><button type="button" class="btn btn-success btn-sm save_prov">
									<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
								</button></td></tr>
								
							</tbody>
						</table>
						<div class='form-group text-center'>
							<!--<input class="btn btn-primary" type='button' value='Guardar' id='savenew' data-dismiss="modal"/>
							<input type="hidden" id="idnew">-->
						</div> 	
					</div>
				</div>
			</div>
		</div>		
		<script src="../js/jquery-1.10.1.min.js"></script>
		<script src="../js/busq_select.js"></script>
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
			
			$("#depa").change();
			$("#prov").val("<?php echo $xx[22];?>");
			$("#prov").change();
			$("#dist").val("<?php echo $xx[23];?>");
			
			$("#daynn").val("<?php echo $fech[2]+0;?>");
			$("#monnn").val("<?php echo $fech[1]+0;?>");
			$("#yearnn").val("<?php echo $fech[0];?>");
			
			$("#sex").val("<?php echo $xx[24];?>");
			
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
			var detrac=$('#detrac').val();
			var c=<?php echo $tabb;?>;
			if(nn=="" || rc=="" || rz=="" || d=="" || ap==""  || dir==""){
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
			
			
			$(document).on("click",".save_prov",function(){
			var idbanco=$("#idbanco").val();
			var idc=$("#cuenta").val();
			var idcint=$("#cuenta_int").val();
			$.post("../res/users/proc.php",{a:"save_banco",idbanco:idbanco,idc:idc,idcint:idcint},function(data){
			$("#t_prov").html(data.tabla);
			location.reload();
			},"json")
			});
			
			$(document).on("click",".del_prov",function(){
			var id=$(this).data("id");
			$.post("../res/users/proc.php",{a:"del_banco",id:id},function(data){
			$("#t_prov").html(data.tabla);
			location.reload();
			},"json")
			});						
			});
			
			$(window).load(function(){
			<?php echo $js_p;?>
			
			});
		</script>
	</body>
</html>		