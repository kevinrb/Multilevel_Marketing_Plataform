<?php
	require_once("../../sec.php");
	date_default_timezone_set('America/Lima');
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	
	
	
	if($pst_tar==3){
		$qry= "select concat((select codigo from vlciudades where ciudad='$pst_depa'),if(grupolocal='SANTA','1','2'),LPAD(idlocal,3,'000'),'$pst_tipo','$pst_dni') from locales where local='$pst_local'";
		$pst_codsn1=mysql_fetch_row(mysql_query($qry));
		echo $pst_codsn=$pst_codsn1[0];
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
	
	elseif($pst_tar==4){
		
		$sql="UPDATE usuarios SET idpersona='$pst_dni',nombre='$pst_nombre',apellidos='$pst_apellidos',email='$pst_email',
		direccion='$pst_depa',telefono='$pst_telefono',celular='$pst_celular',ffnn='$pst_ffnn' ,
		nivel='$pst_nivel',edobs='$pst_edobs',ffii='$pst_ffii',experiencia='$pst_exp',cod_usu_sn='$pst_codsn',activo='$pst_activo' WHERE cod_usu_sn='$pst_codsn' ";
		$psql=mysql_query($sql);
		mysql_query("insert into permisos values ('$pst_dni',605)");
		mysql_query("insert ignore into clientes (idcliente,nombre,direccion,distrito,email,fono,tipo,movil,loginpadre,cod_usu_sn,lastupdate) value ('$pst_ruc','$pst_razon','$pst_direc','$pst_dis','$pst_email','$pst_telefono',1,'$pst_celular','$pst_lider','$pst_codsn',now())") or die(mysql_error());
		
		mysql_query("update clientes set lastupdate=now(),nombre='$pst_razon',direccion='$pst_direc',distrito='$pst_dis',email='$pst_email',fono='$pst_telefono',tipo=1,movil='$pst_celular',loginpadre='$pst_lider',cod_usu_sn='$pst_codsn' where idcliente='$pst_ruc'") or die(mysql_error());
		
		/////nuevo Sistema
		mysql_query("insert ignore into erpd.clientes (idcliente,nombre,direccion,email,fono1,tipo,fono2,codigo1,lastupdate,local,canal) value ('$pst_ruc','$pst_razon','$pst_direc','$pst_email','$pst_telefono',1,'$pst_celular','$pst_dni',now(),'$pst_local','CATALOGO')");
		mysql_query("update erpd.clientes set lastupdate=now(), nombre='$pst_razon',direccion='$pst_direc',email='$pst_email',fono1='$pst_telefono',tipo=1,fono2='$pst_celular',codigo1='$pst_dni' where idcliente='$pst_ruc'");
		//mysql_query("update clientes set nombre='$pst_razon',idcliente='$pst_ruc',direccion='$pst_direc',distrito='$pst_dis',email='$pst_email',fono='$pst_telefono',tipo=1,movil='$pst_celular',loginpadre='$pst_lider' where cod_usu_sn='$pst_codsn'") or die(mysql_error());
		
	}
	
	if($pst_a=="add_cli"){
		$arr=array();
		$qry="insert ignore into clientes (idcliente,nombre,direccion,ubigeo,email,fono1,tipo,fono2,codigo1,lastupdate,canal,local) select '$pst_ruc','$pst_razon','$pst_dir_ruc',ubigeo,email,fono1,1,fono2,'$pst_login',now(),'CATALOGO',local from clientes where codigo1='$pst_login' limit 1";
		mysql_query("insert ignore into clientes (idcliente,nombre,direccion,ubigeo,email,fono1,tipo,fono2,codigo1,lastupdate,canal,local) select '$pst_ruc','$pst_razon','$pst_dir_ruc',ubigeo,email,fono1,1,fono2,'$pst_login',now(),'CATALOGO',local from clientes where codigo1='$pst_login' limit 1") or die(mysql_error());
		$idsms=posturl(array("a"=>"new_idcli","qrycli"=>$qry),$XXurl_erp1);
		
		$arr["estado"]=$idsms;
		
		$selcli="<option disabled selected value=''>--seleccine--</option>";
		$qcli=qry("select idcliente,nombre,direccion from clientes where codigo1='$pst_login'");
		while($r=mysql_fetch_row($qcli))
		{
			$clien[]=$r;
			$selcli.="<option value='$r[0]'>$r[1]($r[0])</option>";
		}
		$a_cli=json_encode($clien);
		$arr["a_cli"]=$a_cli;
		$arr["selcli"]=$selcli;
		$arr1=json_encode($arr);
		echo $arr1;
		}elseif($pst_a=="reset"){
		$temp=$_SERVER['HTTP_REFERER'];
		//if($temp=="http://www.redsantanatura.com/users/edituser_admin.php?login=".$pst_login){
		qry("update usuarios set passwd=login,pass_end=null where login='$pst_login'");
		if(mysql_affected_rows()>0){
			echo 1;
		}
		else{
			echo 0;
		}
		//}
	}
	
	if($pst_a=="save_cli"){
		$arr=array();
		$qry="update clientes set nombre='$pst_razon',direccion='$pst_dir_ruc',lastupdate=now() where codigo1='$pst_login' and idcliente='$pst_ruc' ";
		mysql_query("update clientes set nombre='$pst_razon',direccion='$pst_dir_ruc',lastupdate=now() where codigo1='$pst_login' and idcliente='$pst_ruc'") or die(mysql_error());
		$idsms=posturl(array("a"=>"save_cli","qrycli"=>$qry),$XXurl_erp1);
		
		$arr["estado"]=$idsms;
		
		$selcli="<option disabled selected value=''>--seleccine--</option>";
		$qcli=qry("select idcliente,nombre,direccion from clientes where codigo1='$pst_login'");
		while($r=mysql_fetch_row($qcli))
		{
			$clien[]=$r;
			$selcli.="<option value='$r[0]'>$r[1]($r[0])</option>";
		}
		$a_cli=json_encode($clien);
		$arr["a_cli"]=$a_cli;
		$arr["selcli"]=$selcli;
		$arr1=json_encode($arr);
		echo $arr1;
	}
	
	if($pst_a=="save_banco"){
		$idbanco=$_POST['idbanco'];
		$idc=$_POST['idc'];
		$idcint=$_POST['idcint'];
		if($idbanco>0 and $idc>0){
			qry("insert ignore into user_cuentas(iduser,idbanco,cuenta,cuenta_int) values ('$Xiduser','$idbanco','$idc','$idcint') ");
		}
		$tbl=tabla_dial($Xiduser,$Xpais);
		$arr=array();
		$arr["tabla"]=$tbl;	
		echo json_encode($arr);
	}
	if($pst_a=="del_banco"){
		$id=$_POST['id'];
		if($id>0){
			qry("delete from user_cuentas where id='$id' and iduser='$Xiduser' ");
		}
		$tbl=tabla_dial($Xiduser,$Xpais);
		$arr=array();
		$arr["tabla"]=$tbl;	
		echo json_encode($arr);
	}
	
	function tabla_dial($id,$Xpais){
		$qry="select b.nombre,a.cuenta,a.cuenta_int,a.id from user_cuentas a left join bancos b on b.idbanco=a.idbanco where a.iduser='$id'";
		$res=qry($qry);
		$tbl="";
		$arrmarc=array();
		while($r=mysql_fetch_row($res)){
			$tbl.="<tr><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td><button type='button' class='btn btn-danger btn-sm del_prov' data-id=$r[3]>
			<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
			</button></td></tr>";
		}
		
		$marcs="";
		$res=qry("select idbanco,nombre from bancos where estado=1 and idpais='$Xpais' order by nombre");
		$arr_marc=array();
		while($r=mysql_fetch_row($res))
		{
			$arr_marc[]=$r;
			$marcs.="<option value='$r[0]'>$r[1]</option>";
		}
		
		$tbl.=' <tr><td><select class="form-control" id="idbanco"><option value="" selected>--seleccione marcacion--</option>'.$marcs.'</select></td><td><INPUT class="form-control" id="cuenta" type="text"/></td><td><INPUT class="form-control" id="cuenta_int" type="text"/></td><td><button type="button" class="btn btn-default btn-sm save_prov">
		<span class="glyphicon glyphicon-save" aria-hidden="true"></span>
		</button></td></tr>';
		
		return $tbl;	
	}
	
	
	function posturl($data,$url)
	{$postdata = http_build_query($data);
		$opts = array('http' => array('method'  => 'POST','header'  => 'Content-type: application/x-www-form-urlencoded','content' => $postdata));
		$context = stream_context_create($opts);
		$result  = file_get_contents($url,false,$context);
	return $result;}
	
?>