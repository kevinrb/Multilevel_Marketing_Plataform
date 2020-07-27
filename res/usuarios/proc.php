<?php
	require_once("../../sec.php");
	
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	
	{$tabla_mes=array("1"=>"ENERO","2"=>"FEBRERO","3"=>"MARZO","4"=>"ABRIL","5"=>"MAYO","6"=>"JUNIO","7"=>"JULIO","8"=>"AGOSTO","9"=>"SEPTIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE", "12"=>"DICIEMBRE");
		$today = date("Y-m-d H:i:s");
		$tab_niv=array("pri"=>"PRIMARIA","sec"=>"SECUNDARIA","sup"=>"SUPERIOR");
		
		$sql=qry("SELECT DISTINCT tipo FROM atributos");
		while($r=mysql_fetch_row($sql)){
			$atb.='<option value="'.$r[0].'">'.$r[0].'</option>';
		}
		
		$sql=qry("SELECT local,nombre FROM locales ORDER BY nombre");
		while($r=mysql_fetch_row($sql)){
			$loc.='<option value="'.$r[0].'">'.$r[1].'</option>';
		}
		
		$oe="";
		$opp="";
		$param[0]="skill";
		$param[1]="rol";
		$param[2]="grupo";
		$param[3]="area";
		$param[4]="cola";
		
		
		$tdiv[0]="divsk";
		$tdiv[1]="divro";
		$tdiv[2]="divgr";
		$tdiv[3]="divar";
		$tdiv[4]="divco";
		
		
		for($i=0;$i<4;$i++){
			$qx="SELECT id$param[$i],nombre FROM $param[$i]s WHERE estado='1'";
			$pqx=qry($qx);
			$ox="";
			while($ff=mysql_fetch_row($pqx)){
				$ox.='<option value="'.$ff[1].'" atid="'.$ff[0].'">'.$ff[1].'</option>';
			}
			$sel[$i]='<select class="form-control input-sm" name="'.$param[$i].'" id="sel'.$param[$i].'"><option value="" selected disabled>--Elegir--</option>'.$ox.'</select>';
		}
		
		
		$qe="SELECT cola,nombre FROM colas ";
		$pqe=qry($qe);
		while($ff=mysql_fetch_row($pqe)){
			$oe.='<option value="'.$ff[1].'" atid="'.$ff[0].'">'.$ff[1].'</option>';
		}
		$qp="SELECT perfil,login,nombre FROM perfil";
		$pqp=qry($qp);
		while($ff=mysql_fetch_row($pqp)){
			$opp.='<option value="'.$ff[1].'" peid="'.$ff[0].'">'.$ff[1].'</option>';
		}
		
		$sele='<select class="form-control input-sm" name="cola" id="selcola"><option value="" selected disabled>--Elegir--</option>'.$oe.'</select>';
		$selp='<select class="form-control input-sm" name="perfil" id="perfil"><option value="" selected disabled>--Elegir--</option>'.$opp.'</select>';
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($pst_tar==1){
		
		if($pst_limit==0){
			$sql="SELECT login,nombre FROM usuarios WHERE  login LIKE '%$pst_patron%' OR nombre LIKE '%$pst_patron%' 
			OR apellidos LIKE '%$pst_patron%' OR email LIKE '%$pst_patron%' ORDER BY nombre";
		}
		
		else{
			$sql="SELECT login,nombre FROM usuarios WHERE  login LIKE '%$pst_patron%' OR nombre LIKE '%$pst_patron%' 
			OR apellidos LIKE '%$pst_patron%' OR email LIKE '%$pst_patron%' ORDER BY nombre LIMIT 10;";
		}
		
		$psql=qry($sql);
		$cc=mysql_num_rows($psql);	  
		if($cc!=0){
			$res1="";
			
			while($tem=mysql_fetch_row($psql)){
				
				$sqi="SELECT a.idarea,a.nombre ,b.login from areas a, userarea b where b.login='$tem[0]' and a.idarea=b.idarea;";
				$psqi=qry($sqi);
				$ccc=mysql_num_rows($psqi);
				
				if($ccc!=0){
					$tem2=mysql_fetch_row($psqi);
					$cont.='<tr>
					<td class="col-sm-8"><span id="'.$tem[0].'" name="'.$tem[0].'" class="btn btn-active usuarios">'.$tem[1].'</span></td>
					<td class="col-sm-2"><div id="'.$tem2[0].'" name="'.$tem2[0].'" class="userarea">'.$tem2[1].'</div></td>
					</tr>';
				}
				
				else{
					$cont.='<tr>
					<td class="col-sm-8"><span id="'.$tem[0].'" name="'.$tem[0].'" class="btn btn-active usuarios">'.$tem[1].'</span></td>
					<td class="col-sm-2"><div id="'.$tem2[0].'" name="'.$tem2[0].'" class="userarea">S/A</div></td>
					</tr>';
				}
				
			}
			
			$res1='<table class="table">'.$cont.'</table>'.'<input type="hidden" class="tipoatributo" id="tipouser" name="user">';
		}
		
		else{
			$res1="NO SE ENCONTRARON USUARIOS";
		}
		
		echo json_encode(array("dat1"=>$res1, "dat2"=>$sql));
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	else if($pst_tar==2){
		
		$sql=qry("SELECT local,tipo FROM atributos WHERE valor='$pst_login'");
		while($r=mysql_fetch_row($sql)){
			$atcont.='<div class="col-md-12 ">
			<div class="col-md-5"><select class="form-control"><option value="'.$r[1].'" selected disabled>'.$r[1].'</option></select></div>
			<div class="col-md-5"><select class="form-control"><option value="'.$r[0].'" selected disabled>'.$r[0].'</option></select></div>
			<div class="col-md-2"><button class="btn btn-default addatb" disabled><span class="glyphicon glyphicon-plus"></span></button></div>		
			</div>';
		}
		
		
		
		$sql="SELECT idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,nivel,edobs,ffii,experiencia,foto,local,activo
		FROM usuarios WHERE login='$pst_login'";
		$psql=qry($sql); $cc=mysql_num_rows($psql);	$xx=mysql_fetch_row($psql);
		$qql=qry("select local,nombre FROM locales WHERE local!='$xx[13]' ORDER BY nombre");
		
		while($res=mysql_fetch_row($qql)){$ll.='<option value="'.$res[0].'">'.$res[1].'</option>';}
		if($xx[13]!=""){$tql=qry("SELECT nombre from locales WHERE local='$xx[13]' ORDER BY nombre"); $nn=mysql_fetch_row($tql); $nom=$nn[0];
		$seloc='<select class="form-control" name="locales" id="locales"><option value="'.$xx[13].'" selected>'.$nom.'</option>'.$ll.'</select>';}
		else{$seloc='<select class="form-control" name="locales" id="locales">'.$ll.'</select>';}
		
		$arres=array('0'=>'INACTIVO','1'=>'ACTIVO');
		if($xx[14]=="1"){$utx="0";} else{$utx="1";}
		$estuser='<select class="form-control" id="estuser"><option value="'.$xx[14].'" selected>'.$arres[$xx[14]].'</option><option value="'.$utx.'">'.$arres[$utx].'</option></select>';
		
		{
			for($i=0;$i<4;$i++){
				$qq="SELECT a.nombre,a.id$param[$i],b.login FROM $param[$i]s a, user$param[$i] b WHERE a.id$param[$i]=b.id$param[$i] AND b.login='$pst_login' ";
				$pqr=qry($qq);
				$c1=mysql_num_rows($pqr);
				if($c1!=0){
					while($ff1=mysql_fetch_row($pqr)){
						//$ss.='<div id="'.$ff1[0].'" atid="'.$ff1[1].'" class="divsk">'.$ff1[0].'<input type="button" id="del'.$ff1[0].'" naat="'.$ff1[0].'" class="delatr" value="X"></div>';
						$div[$i].='<div class="input-group '.$tdiv[$i].'" id="'.$ff1[0].'" atid="'.$ff1[1].'"><input class="form-control input-sm" type="text"  value="'.$ff1[0].'"><span class="input-group-btn"><button class="btn btn-default input-sm delatr" id="del'.$ff1[0].'" naat="'.$ff1[0].'" type="button"><span class="glyphicon glyphicon-remove"></span></button></span></div>';
					}
				}
				else{
				}
			}
			
		}
		
		$aa='
		<div id="tabs">
		
		<ul class="nav nav-pills nav-justified">
		<li class="active"><a href="#tabs-1" data-toggle="tab">Datos Personales</a></li>
		<li><a href="#tabs-2" data-toggle="tab">Otros</a></li>
		<li><a href="#tabs-3" data-toggle="tab">Atrib.</a></li>
		</ul> 
		
		</div>
		
		
		<div class="tab-content">
		
		<div class="tab-pane active" id="tabs-1"><br>
		<div class="row" id="">
		<div class="col-md-6">
		<div class="col-md-12 datos"><legend>Datos Personales</legend></div>
		<div class="col-md-12 datos"><label>Login</label><br><div class="input-group"><input class="form-control" type="text" name="login" id="login" value="'.$pst_login.'" readonly><span class="input-group-btn"><button class="btn btn-default" type="button" id="veri"> <span class="glyphicon glyphicon-search"></span></button></span></div></div>
		<div class="col-md-12 datos"><label>Nombre</label><input class="form-control" type="text" name="nombre" id="nombre" value="'.$xx[1].'"></div>
		<div class="col-md-12 datos"><label>Apellidos</label><input class="form-control" type="text" name="apellidos" id="apellidos" value="'.$xx[2].'"></div>
		<div class="col-md-12 datos"><label>DNI</label><input class="form-control" type="text" name="dni" id="dni" value="'.$xx[0].'"></div>
		<div class=" col-md-12 datos"><label>E-Mail</label><input class="form-control" type="text" name="mail" id="mail" value="'.$xx[3].'"></div>
		
		<div class="col-md-12 datos"><label>Dirección</label><input class="form-control" type="text" name="direc" id="direc" value="'.$xx[5].'"></div>
		<div class="col-md-12 datos"><label>Fecha de Nacimiento</label><input class="form-control" type="text" id="ffnn" value="'.$xx[4].'" /></div>
		<div class="col-md-12 datos"><label>Telefono</label><input class="form-control" type="text" name="telefono" id="telefono" value="'.$xx[6].'"></div>
		<div class="col-md-12 datos"><label>Celular</label><input class="form-control" type="text" name="celular" id="celular" value="'.$xx[7].'"></div>
		<div class="col-md-12 datos"><label>&nbsp;&nbsp;&nbsp;</label><button type="button" class="btn btn-success col-md-12" id="savecambios">GUARDAR</button></div>
		</div>
		
		<div class="col-md-6">
		<div class="col-md-12 datos"><legend>Educacion</legend></div>
		<div class="col-md-12 datos"><label>Nivel de Educacion</label>			
		<select class="form-control input-sm" name="niv" id="niv">
		<option value="'.$xx[8].'" selected>'.$tab_niv[$xx[8]].'</option>
		<option value="pri">PRIMARIA</option>
		<option value="sec">SECUNDARIA</option>
		<option value="sup">SUPERIOR</option>
		</select>
		</div>
		<div class="col-md-12 datos"><label>Local</label>'.$seloc.'</div>
		<div class="col-md-12 datos"><label>Estado</label>'.$estuser.'</div>
		<div class="col-md-12 datos"><button class="form-control btn-sm btn-warning resetpass" d1="'.$pst_login.'">RESET PASSWORD</button></div>
		<div class="col-md-12 datos"><label>Fecha Ingreso</label><input class="form-control" type="text" id="ffii" value="'.$xx[10].'" /></div>
		<div class="col-md-12 datos"><label>Educacion(Otros)</label><textarea class="form-control" name="obs" id="obs" cols="40" rows="5">'.$xx[9].'</textarea></div>
		<div class="col-md-12 datos"><label>Eperiencia</label><textarea class="form-control" name="epe" id="epe" cols="40" rows="5">'.$xx[11].'</textarea></div>	
		
		</div>			
		</div>
		</div>
		
		<div class="tab-pane" id="tabs-2">
		
		<table class="table">
		
		<tr>
		<td class="col-md-6">
		<div class="attr">
		<div class="form-group"><legend>Skills</legend></div>
		'.$sel[0].'
		<div class="listas" id="listsk" >'.$div[0].'</div>
		</div> 
		</td>
		
		<td class="col-md-6">
		<div class="attr">
		<div class="form-group"><legend>Roles</legend></div>
		'.$sel[1].'
		<div class="listas" id="listro">'.$div[1].'</div>
		</div> 
		</td>
		</tr>
		
		<tr>
		<td class="col-md-6">
		<div class="attr">
		<div class="form-group"><legend>Grupos</legend></div>
		'.$sel[2].'
		<div class="listas" id="listgr">'.$div[2].'</div>
		</div>
		</td>
		
		<td class="col-md-6">
		<div class="attr">
		<div class="form-group"><legend>Areas</legend></div>
		'.$sel[3].'
		<div class="listas" id="listareas">'.$div[3].'</div>
		</div>
		</td>
		</tr>
		
		<tr>
		<td class="col-md-6">
		<div class="attr">
		<div class="form-group"><legend>Colas</legend></div>
		'.$sele.'
		<div class="listas" id="listco">'.$div[4].'</div>
		</div>
		</td>
		
		<td class="col-md-6">
		<div class="perfil attr">
		<div class="form-group"><legend>Perfil</legend></div>
		<div class="input-group">
		'.$selp.'
		<span class="input-group-btn">
		<button class="btn btn-default input-sm" id="aplicar" type="button">
		<span class="glyphicon glyphicon-thumbs-up"></span>
		</button>
		</span>
		</div>
		</div>				
		</td>
		</tr>
		</table>
		
		</div>
		
		<div class="tab-pane" id="tabs-3"><br>
		
		<div class="row" id="in-tab-3">
		<div class="col-md-12">
		<div class="col-md-5"><legend>Atributo</legend></div><div class="col-md-7"><legend>Local</legend></div>
		</div>
		'.$atcont.'
		<div class="col-md-12 attrx">
		<div class="col-md-5"><select class="form-control atbvl"><option value="0" selected disabled>--Elegir--</option>'.$atb.'</select></div>
		<div class="col-md-5"><select class="form-control atblc"><option value="0" selected disabled>--Elegir--</option>'.$loc.'</select></div>
		<div class="col-md-2"><button class="btn btn-default addatb"><span class="glyphicon glyphicon-plus"></span></button></div>		
		</div>
		</div>
		</div>	
		
		</div>
		';
		echo $aa;	
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	else if($pst_tar==3){
		$ffnn="$pst_year-$pst_mes-$pst_dia";
		$sql="INSERT INTO usuarios (login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,nivel,edobs,ffii,experiencia,passwd,activo,local,pass_end) 
		VALUES ('$pst_login','$pst_dni','$pst_nombre','$pst_apellidos','$pst_email','$pst_ffnn','$pst_direc','$pst_telefono','$pst_celular','$pst_nivel','$pst_edobs','$pst_ffii','$pst_exp','123456',1,'$pst_loc',NOW())";
		$psql=qry($sql);
		
		for($ii=0;$ii<$pst_css;$ii++){
			$qr1="INSERT INTO userskill (idskill,login,hora) VALUES ('$pst_sk[$ii]','$pst_login',NOW())";
			$pq1=qry($qr1);
		}
		
		for($yy=0;$yy<$pst_crr;$yy++){$qr2="INSERT INTO userrol (idrol,login,hora) VALUES ('$pst_ro[$yy]','$pst_login',NOW())";
			$pq2=qry($qr2);
			qry("insert ignore into permisos select '$pst_login',idfunc from rolfunc where idrol='$pst_ro[$yy]'");
		}
		
		for($zz=0;$zz<$pst_cgg;$zz++){
			$qr3="INSERT INTO usergrupo (idgrupo,login,hora) VALUES ('$pst_gr[$zz]','$pst_login',NOW())";
			$pq3=qry($qr3);
		}	 
	}
	
	else if($pst_tar==4){
		$ffnn="$pst_year-$pst_mes-$pst_dia";
		$sql="UPDATE usuarios SET idpersona='$pst_dni',nombre='$pst_nombre',apellidos='$pst_apellidos',email='$pst_email',
		direccion='$pst_direc',telefono='$pst_telefono',celular='$pst_celular',ffnn='$pst_ffnn',
		nivel='$pst_nivel',edobs='$pst_edobs',ffii='$pst_ffii',experiencia='$pst_exp',local='$pst_local',activo='$pst_estu' WHERE login='$pst_login' ";
		$psql=qry($sql);
		
		$qr11="DELETE FROM userskill WHERE login='$pst_login'";
		$pq11=qry($qr11);
		for($ii=0;$ii<$pst_css;$ii++){
			//$qr1="INSERT INTO userskill (idskill,login,hora) SELECT * FROM (SELECT '$pst_sk[$ii]','$pst_login',NOW()) AS tmp WHERE NOT EXISTS (SELECT idskill,login,hora FROM userskill WHERE idskill='$pst_sk[$ii]' AND login='$pst_login' and idfunc='$pst_select_func2[$i]')";
			$qr1="INSERT INTO userskill (idskill,login,hora) VALUES ('$pst_sk[$ii]','$pst_login',NOW())";
			$pq1=qry($qr1);
		}
		
		$qr22="DELETE FROM userrol WHERE login='$pst_login'";
		qry("delete from permisos where login='$pst_login'");
		$pq22=qry($qr22);
		for($yy=0;$yy<$pst_crr;$yy++){
			//$qr1="INSERT INTO userskill (idskill,login,hora) SELECT * FROM (SELECT '$pst_sk[$ii]','$pst_login',NOW()) AS tmp WHERE NOT EXISTS (SELECT idskill,login,hora FROM userskill WHERE idskill='$pst_sk[$ii]' AND login='$pst_login' and idfunc='$pst_select_func2[$i]')";
			qry("insert ignore into permisos select '$pst_login',idfunc from rolfunc where idrol='$pst_ro[$yy]'");
			$qr2="INSERT INTO userrol (idrol,login,hora) VALUES ('$pst_ro[$yy]','$pst_login',NOW())";
			$pq2=qry($qr2);
		}
		
		$qr33="DELETE FROM usergrupo WHERE login='$pst_login'";
		$pq33=qry($qr33);
		for($zz=0;$zz<$pst_cgg;$zz++){
			//$qr1="INSERT INTO userskill (idskill,login,hora) SELECT * FROM (SELECT '$pst_sk[$ii]','$pst_login',NOW()) AS tmp WHERE NOT EXISTS (SELECT idskill,login,hora FROM userskill WHERE idskill='$pst_sk[$ii]' AND login='$pst_login' and idfunc='$pst_select_func2[$i]')";
			$qr3="INSERT INTO usergrupo (idgrupo,login,hora) VALUES ('$pst_gr[$zz]','$pst_login',NOW())";
			$pq3=qry($qr3);
		}
		
		$qr44="DELETE FROM userarea WHERE login='$pst_login'";
		$pq44=qry($qr44);
		for($zz=0;$zz<$pst_caa;$zz++){
			//$qr1="INSERT INTO userskill (idskill,login,hora) SELECT * FROM (SELECT '$pst_sk[$ii]','$pst_login',NOW()) AS tmp WHERE NOT EXISTS (SELECT idskill,login,hora FROM userskill WHERE idskill='$pst_sk[$ii]' AND login='$pst_login' and idfunc='$pst_select_func2[$i]')";
			$qr4="INSERT INTO userarea (idarea,login,hora) VALUES ('$pst_ar[$zz]','$pst_login',NOW())";
			$pq4=qry($qr4);
		}
		
		/*
			$qr55="DELETE FROM usercola WHERE login='$pst_login'";
			$pq5=qry($qr55);
			for($zz=0;$zz<$pst_ccc;$zz++){
			//$qr1="INSERT INTO userskill (idskill,login,hora) SELECT * FROM (SELECT '$pst_sk[$ii]','$pst_login',NOW()) AS tmp WHERE NOT EXISTS (SELECT idskill,login,hora FROM userskill WHERE idskill='$pst_sk[$ii]' AND login='$pst_login' and idfunc='$pst_select_func2[$i]')";
			$qr4="INSERT INTO usercola (cola,login,hora) VALUES ('$pst_co[$zz]','$pst_login',NOW())";
			$pq4=qry($qr4);
		}*/
		
		for($i=0;$i<count($pst_ats);$i++){
			$vv=$pst_ats[$i][0]; $ll=$pst_ats[$i][1];
			$sql="INSERT INTO atributos (local,tipo,valor)
			SELECT * FROM (SELECT '$ll', '$vv', '$pst_login') AS tmp
			WHERE NOT EXISTS (SELECT local,tipo,valor FROM  atributos WHERE local='$ll' AND tipo='$vv' AND valor='$pst_login')";
			$psql=qry($sql);
		}
		
		echo $sql;
	}
	
	else if($pst_tar==5){
		
		for($i=0;$i<3;$i++){
			$sql="SELECT a.id$param[$i],b.login,c.nombre FROM user$param[$i] a, perfil b, $param[$i]s c WHERE b.login='$pst_perfil' AND a.login=b.login AND a.id$param[$i]=c.id$param[$i];";
			$psql=qry($sql);	
			while($fff=mysql_fetch_row($psq1)){
				//$cont[$i].='<div id="'.$fff[2].'" atid="'.$fff[0].'"  class="divsk">'.$fff[2].'<input type="button" id="del'.$fff[2].'" naat="'.$fff[2].'" class="delatr" value="X"></div>';
				$cont[$i].='<div class="input-group '.$tdiv[$i].'" id="'.$fff[2].'" atid="'.$fff[0].'"><input class="form-control input-sm" type="text"  value="'.$fff[2].'"><span class="input-group-btn"><button class="btn btn-default input-sm delatr" id="del'.$fff[2].'" naat="'.$fff[2].'" type="button"><span class="glyphicon glyphicon-remove"></span></button></span></div>';
			}
		}
		echo json_encode(array("sk"=>$cont[0], "ro"=>$cont[1], "gr"=>$cont[2]));
	}
	
	
	else if($pst_tar==9){
		for($i=0;$i<$pst_aa;$i++){
			$qr1="UPDATE $pst_table SET estado='1' WHERE $pst_id='$pst_act[$i]'";
			$pqr1=qry($qr1);
		}
		for($y=0;$y<$pst_dd;$y++){
			$qr2="UPDATE $pst_table SET estado='0' WHERE $pst_id='$pst_des[$y]'";
			$pqr2=qry($qr2);
		}
	}
	
	else if($pst_tar==10){
		
		$qr1="INSERT INTO $pst_table (nombre,obs,estado) VALUES ('$pst_name','$pst_obsv','1')";
		$pqr1=qry($qr1);
		$id=mysql_insert_id();
		echo $id;
	}
	
	else if($pst_tar==12){
		$id="a.id$pst_tipo";
		$idb="b.id$pst_tipo";
		$tab="user$pst_tipo";
		$tb=$pst_tipo."s";
		
		$sql="SELECT b.login,c.nombre FROM $tb a, user$pst_tipo b, usuarios c WHERE b.login=c.login AND a.id$pst_tipo=b.id$pst_tipo AND a.id$pst_tipo='$pst_id';";
		
		if($pst_tipo=="cola")
		{$sql="select b.login from $pst_table a, $tab b where a.nombre='$pst_nombre' and a.$pst_tipo=b.$pst_tipo ";}
		
		$psql=qry($sql);
		$cc=mysql_num_rows($psql);	  
		
		if($cc!=0){
			$res="";
			while($tem=mysql_fetch_row($psql)){
				if($tem[1]==""){
					$tem[1]=$tem[0];
				}
				
				$res.='<tr><td id="'.$tem[0].'" name="'.$tem[0].'" class="userattr">'.$tem[1].'</td>
				<td>'.$tem[0].'</td>
				</tr>';
			}
			$tab='<table class="table table-bordered">
			<tr><td  class="warning">Nombre</td><td class="warning">Login</td></tr>
			'.$res.'</table>';
			echo $tab;
		}
		
		else{echo "NO HAY USUARIOS";}
	}
	
	else if($pst_tar==13){
		
		$result=qry("SELECT C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,C11,C12,C13,C14,C15,C16,C17,C18,C19,C20
		FROM listas WHERE id='$pst_id'");
		$fields=mysql_num_fields($result);
		$camp="";
		$res=mysql_fetch_row($result);
		for ($i=0; $i < $fields; $i++) {
			
			$name= mysql_field_name($result, $i);
			$y=$i+1;
			if($res[$i]!=""){
				$camp.='<option name="'.$res[$i].'" value="'.$name.'" campref="C'.$y.'">'.$res[$i].'</option>';
				
			}   
			else{}
		}
		$selcm='<select name="selcamp" id="selcamp" class="form-control">'.$camp.'</select>';
		echo $selcm;
	}
	
	else if($pst_tar==14){
		$sql="INSERT INTO nodos (tipo,tespera,idsonido,nombre,idlista,campolista,play,nroesperas,tsla,tcorto,tlargo,hora,login) VALUES 
		('c','$pst_tesp','0','$pst_nombre','$pst_idlista','$pst_clista','$pst_play','$pst_nesp','$pst_tsla','$pst_tcorto','$pst_tlargo',NOW(),'$pst_login')";
		$psql=qry($sql);
		
	}
	
	else if($pst_tar==15){
		$sql="UPDATE nodos SET tespera='$pst_tesp',nombre='$pst_nombre',idlista='$pst_idlista',campolista='$pst_clista',
		play='$pst_play',nroesperas='$pst_nesp',tsla='$pst_tsla',tcorto='$pst_tcorto',tlargo='$pst_tlargo',hora=NOW(),login='$pst_login' WHERE idnodo='$pst_idnodo'";
		$psql=qry($sql);
		
	}
	
	else if($pst_tar==16){
		$hoy = date("Y-m-d H:i:s");  
		for($i=0;$i<$pst_nl;$i++){
			$log=$pst_logs[$i];
			$sql="INSERT INTO usernodo (login,hora,idnodo)
			SELECT * FROM (SELECT '$log', '$hoy', '$pst_idnodo') AS tmp
			WHERE NOT EXISTS (SELECT login FROM  usernodo WHERE login='$log' AND idnodo='$pst_idnodo')";
			$psql=qry($sql);
		}
	}
	
	
	
	else if($pst_tar==17){
		$tab=$pst_tipo."s";
		$qr1="UPDATE $tab SET estado='$pst_est' WHERE id$pst_tipo='$pst_id'";
		$pqr1=qry($qr1);
		echo $qr1;
	}
	
	
	else if($pst_tar==18){
		$hoy = date("Y-m-d"); 
		for($i=0;$i<$pst_cc;$i++){
			//$sql=qry("INSERT INTO user$pst_tipo (id$pst_tipo,login,hora) values ('$pst_id','$pst_user[$i]',NOW())");
			$sql="INSERT INTO user$pst_tipo (id$pst_tipo,login,hora)
			SELECT * FROM (SELECT '$pst_id','$pst_user[$i]','$hoy' ) AS tmp
			WHERE NOT EXISTS (SELECT login FROM user$pst_tipo WHERE login='$pst_user[$i]' AND id$pst_tipo='$pst_id')";
			$psql=qry($sql);
			echo $sql;
		}
	}
	
	
	else if($pst_tar==19){
		$sql=qry("SELECT login FROM usuarios WHERE login='$pst_log' ");
		$cc=mysql_num_rows($sql);
		echo $cc;
	}
	
	else if($pst_tar==20){
		$sql=mysql_query("UPDATE usuarios set passwd='123456' WHERE login='$pst_login'");
	}
?>