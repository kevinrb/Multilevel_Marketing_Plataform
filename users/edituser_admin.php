<?php
	require_once("../sec.php");
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	extract ($_GET, EXTR_PREFIX_ALL, "gt");
	if($pst_tar==4){
		$ubi=mysql_fetch_row(mysql_query("SELECT id FROM ubigeo WHERE departamento='$pst_depa' AND provincia='$pst_prov' AND distrito='$pst_dist'"));
		$sql="UPDATE usuarios SET nombre='$pst_nombre',apellidos='$pst_apellidos',email='$pst_email',ffnn='$pst_yearnn-$pst_monnn-$pst_daynn',telefono='$pst_telefono',celular='$pst_celular',local='$pst_local',direccion='$pst_direc',ubigeo='$ubi[0]',sexo='$pst_sex' WHERE login='$pst_login' ";
		$psql=mysql_query($sql);
		//mysql_query("insert ignore into clientes (idcliente,nombre,direccion,ubigeo,email,fono1,tipo,fono2,codigo1,lastupdate,canal) value ('$pst_ruc','$pst_razon','$pst_direc','$ubi[0]','$pst_email','$pst_telefono',1,'$pst_celular','$pst_login',now(),'CATALOGO')") or die(mysql_error());
		
		qry("update asociadas set empresa='$pst_lider' where socio='$pst_login'");
		
		mysql_query("update clientes set email='$pst_email',fono1='$pst_telefono',fono2='$pst_celular',local='$pst_local' where codigo1='$pst_login'") or die(mysql_error());
		
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
	$clien=array();
	$selcli="<option disabled selected value=''>--seleccine--</option>";
	$qcli=qry("select idcliente,nombre,direccion from clientes where codigo1='$gt_login'");
	while($r=mysql_fetch_row($qcli))
	{
		$clien[]=$r;
		$selcli.="<option value='$r[0]'>$r[1]($r[0])</option>";
	}
	$a_cli=json_encode($clien);
	$tabb=4;
	$sql="SELECT a.idpersona,a.nombre,a.apellidos,a.email,a.ffnn,a.direccion,a.telefono,a.celular,a.nivel,a.edobs,a.ffii,a.experiencia,a.foto,a.cod_usu_sn,a.activo,'','','',c.empresa,a.direccion,a.local, d.departamento,d.provincia,d.distrito,a.sexo
	FROM usuarios a left join asociadas c  on c.socio=a.login left join ubigeo d on d.id=a.ubigeo WHERE login='$gt_login'";
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
	
	$dist .= "</optgroup>";
	$tip="<input class='form-control'  name='tipo' id='tipo' value='$tipo' type='hidden'><input  name='login' id='login' value='$gt_login' type='hidden'>";
	
	$exi=qry("SELECT EXISTS(SELECT 1 FROM permisos WHERE idfunc ='999' and login='$gt_login' LIMIT 1)");
	$exi=mysql_fetch_row($exi);
	$exi=$exi[0];
	$a="login='$gt_login'";
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
	
	$fech=explode("-",$xx[4]);
	
	$qry="select b.nombre,a.cuenta,a.cuenta_int,a.id from user_cuentas a left join bancos b on b.idbanco=a.idbanco where a.iduser='$gt_iduser'";
	$res=qry($qry);
	$tbl="";
	$arrmarc=array();
	while($r=mysql_fetch_row($res)){
		$tbl.="<tr><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td></tr>";
	}	
	$ren=mysql_fetch_row(qry("select cuenta from user_detrac where iduser='$gt_iduser'"));
	$detrac=$ren[0];
	
	
	
	if($xx[14]==1)
	$check="checked";
	$aa=''.$tip.' 
	<div  class="container">
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
	<div class="form-group  col-md-8"><label for="">Fecha de Nacimiento</label>
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
	<div class="form-group"><label for="">Elija:*</label>
	<div class="input-group">	
	<select class="form-control"  name="idruc" id="idruc">'.$selcli.'</select>
	<span class="input-group-btn">
	<button data-toggle="tooltip" data-placement="left" title="Nueva Razon Social" class="btn btn-info calendar" type="button" id="add_ruc" ><span class="glyphicon glyphicon-plus"></span></button>
	</span>	
	</div>
	</div>
	<div class="form-group"><label for="">RUC o DNI*</label><input required class="form-control"  type="text" name="ruc" id="ruc" value=""></div>
	<div class="form-group"><label for="">Razon Social*</label><input  required class="form-control"  type="text" name="razon" id="razon" value=""></div>
	<div class="form-group"><label for="">Direccion*</label><input  required class="form-control"  type="text" name="dir_ruc" id="dir_ruc" value=""></div>
	<div class="text-center">
	
	<input  type="button" class=" btn btm-sm btn-primary" data-loading-text="Agregando..." id="add_cli" value="Agregar" />
	<input  type="button" class="btn btm-sm btn-primary" data-loading-text="Actualizando..." id="save_cli" value="Actualizar" />
	</div> -->
	
	<legend>SANTA NATURA</legend>
	
	<div class="form-group">
	<label for="">EMPRESARIO</label>
	<select class="form-control" name="lider" id="lider" readonly><option value="">--ninguno--</option>'.$lid.'</select>
	</div>
	<div class="form-group">
	<label for="">LOCAL</label>
	<select class="form-control" name="local" id="local" readonly>'.$local.'</select>
	</div>
	<table class="table table-bordered">
	<thead><tr><th>BANCO</th><th>CUENTA</th><th>CUENTA INTERBANCARIA</th></tr></thead>
	<tbody id="t_prov">
	'.$tbl.'
	</tbody>
	</table>
	<div class="form-group"><label for="">Cta. DETRACCION*</label><input  class="form-control"  type="text" name="detrac" id="detrac" value="'.$detrac.'"></div>
	<div class="form-group">
	<input class="form-control btn btn-sm btn-warning" id="reset" type="button" value="RESETEAR PASSWORD" />
	</div>
	</div>
	</div>
	
	<div class="row text-center">
	
	<input  type="submit" class=" btn btn-primary" id="savecambios" value="GUARDAR">
	<a href="../proyecto_usuarios/usuarios_gestion.php" type="button" class="btn btn-primary" id="cancel" >CANCELAR</a>
	
	</div>
	
	</div>
	';
	
	
	
	
	
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
			
			var a_cli= <?php echo $a_cli;?>;
			$(document).ready(function(){
			$('#add_ruc').tooltip();
			populatedropdown("daynn", "monnn", "yearnn");
			var deppro=<?php echo $deppro;?>;
			var prodis=<?php echo $prodis;?>;
			$("body").delegate('#depa','change',function(){ $("#prov").html("<option selected disabled value=''>--Elija--</option>"+deppro[$(this).val()]); $("#dist").html("");});
			$("body").delegate('#prov','change',function(){ $("#dist").html("<option selected disabled value=''>--Elija--</option>"+prodis[$("#depa").val()][$(this).val()]);});
			
			$("#depa").change();
			$("#prov").val("<?php echo $xx[22];?>");
			$("#prov").change();
			$("#dist").val("<?php echo $xx[23];?>");
			
			$("#daynn").val("<?php echo $fech[2];?>");
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
			var c=<?php echo $tabb;?>;
			if(nn==""   || d=="" || ap==""  || local=="" || dir==""){
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
			
			
			$("body").on("change","#idruc", function(){
			var valu=$(this).val();
			var opcs="<option value='' selected disabled style='display: none;'>-seleccione-</option>";
			for(i=0;i<a_cli.length;i++){
			if(valu==a_cli[i][0])
			{
			$("#ruc").val(a_cli[i][0]);
			$("#razon").val(a_cli[i][1]);
			$("#dir_ruc").val(a_cli[i][2]);
			}
			
			}
			$("#ruc").prop("readonly",true);
			$("#add_cli").hide();
			$("#save_cli").show();
			});
			
			$('#add_ruc').click(function(){
			$("#ruc").prop("readonly",false);
			$("#ruc").val('');
			$("#razon").val('');
			$("#dir_ruc").val('');
			$("#add_cli").show();
			$("#save_cli").hide();
			$("#idruc").val('');
			});
			
			dvr=[];
			vnum = new RegExp("(\\D|\\s)");
			vtxt = new RegExp("^[a-zA-ZñÑ ]*$");
			vflo = new RegExp("^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$");
			vema = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
			
			$('#save_cli,#add_cli').hide();
			$('#add_cli').click(function(){
			var ruc=$("#ruc").val(); dvr.push([1,$("#ruc"),"",0]);
			var razon=$("#razon").val(); dvr.push([1,$("#razon"),"",0]);
			var dir_ruc=$("#dir_ruc").val(); dvr.push([1,$("#dir_ruc"),"",0]);
			var login=$("#login").val();
			var btn = $(this);
			btn.button('loading');
			for(i=0;i<dvr.length;i++)
			{dvr[i][1].parent().removeClass("has-error");}
			
			for(i=0;i<dvr.length;i++)
			{
			fn=dvr[i][0];
			ob=dvr[i][1];
			fl=dvr[i][2];
			cn=dvr[i][3];
			if(fn==1 || (fn==0 && ob.val()!="" && ob.val()!=null)){
			if(ob.val()!="" && ob.val()!=null)
			{
			if(fl!="")
			{
			if(fl=="num"){if(!vnum.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
			if(fl=="let"){if(vtxt.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
			if(fl=="ema"){if(String(ob.val()).search(vema)!=-1 && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
			if(fl=="flo"){if(vflo.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
			}
			}
			else
			{
			ob.parent().addClass("has-error"); ob.focus(); return false;}
			}
			}
			$.post("../res/users/proc.php", {a:"add_cli",ruc:ruc,razon:razon,dir_ruc:dir_ruc,login:login}, function(data){
			btn.button('reset');
			if(data.estado=="1")
			{
			alert("Nueva Razon Social guardada.");
			$("#idruc").html(data.selcli);
			a_cli=eval("("+data.a_cli+")");
			$("#ruc").val('');
			$("#razon").val('');
			$("#dir_ruc").val('');
			$("#idruc").val('');
			$('#save_cli,#add_cli').hide();
			}
			},"json");
			});
			
			
			$('#save_cli').click(function(){
			
			var ruc=$("#ruc").val(); dvr.push([1,$("#ruc"),"",0]);
			var razon=$("#razon").val(); dvr.push([1,$("#razon"),"",0]);
			var dir_ruc=$("#dir_ruc").val(); dvr.push([1,$("#dir_ruc"),"",0]);
			var login=$("#login").val(); 
			var btn = $(this);
			btn.button('loading');
			
			for(i=0;i<dvr.length;i++)
			{dvr[i][1].parent().removeClass("has-error");}
			
			for(i=0;i<dvr.length;i++)
			{
			fn=dvr[i][0];
			ob=dvr[i][1];
			fl=dvr[i][2];
			cn=dvr[i][3];
			if(fn==1 || (fn==0 && ob.val()!="" && ob.val()!=null)){
			if(ob.val()!="" && ob.val()!=null)
			{
			if(fl!="")
			{
			if(fl=="num"){if(!vnum.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
			if(fl=="let"){if(vtxt.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
			if(fl=="ema"){if(String(ob.val()).search(vema)!=-1 && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
			if(fl=="flo"){if(vflo.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
			}
			}
			else
			{
			ob.parent().addClass("has-error"); ob.focus(); return false;}
			}
			}
			
			
			$.post("../res/users/proc.php", {a:"save_cli",ruc:ruc,razon:razon,dir_ruc:dir_ruc,login:login}, function(data){
			btn.button('reset');
			if(data.estado=="1")
			{
			alert("Razon Social actualizada.");
			$("#idruc").html(data.selcli);
			a_cli=eval("("+data.a_cli+")");
			$("#ruc").val('');
			$("#razon").val('');
			$("#dir_ruc").val('');
			$("#idruc").val('');
			$('#save_cli,#add_cli').hide();
			}
			},"json");
			
			
			
			});
			
			$("#reset").click(function(){
			alert(1);
			$.post("../res/users/proc.php",{a:"reset",login:"<?php echo $login;?>"},function(data){
			if(data==1){
			alert("Contraseña Reseteada");
			}else{
			alert("YA esta reseteado");
			}
			
			});
			});
			
			$('#cancel').click(function(){
			window.open("list_global.php",'_self');
			});
			});
		</script>
	</body>
</html>