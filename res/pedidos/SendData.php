<?php
	require_once("../../sec.php");
	//require_once("../../ws/func.php");
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	
	///////////////////////////////////////////////////////////////////////////////
	if($pst_a=="prod"){
		$val="";
		$data=$pst_busq;
		$res=qry("select a.idprod,a.precio,TRIM(UPPER(CONCAT(a.nombre,' ',a.nombre1,' ',a.nombre2,' ',a.nombre3,' ',a.nombre4))) nombre from productos a where isnull(a.nombre)!=1 and a.nombre!='' and porlocal=0 and a.estado=1 and if(a.porcanal=1,if(a.idprod in (select idprod from prodcanal where canal='9'),1,0),1) and a.categoria=1 and a.insumo=0 and a.nombre like '%$data%' order by a.nombre;");
		while($r=mysql_fetch_row($res)){
			$tan.="<option value='$r[0]' label='$r[2]'></option>";
		}
		echo $tan;
	}
	///////////////////////////////////////////////////////////////////////////////
	if($pst_a=="newmov"){
		$data=$pst_data;
		/*if($data[0]=="tx"){
			//$sql=qry("INSERT INTO cuentasmove(idcuenta,hora,fecha,monto,login,nrodoc,tipo,detalles) values('$data[1]',NOW(),'$data[3]','$data[4]','$data[6]','$data[2]','$data[0]','$data[5]')");
			$sql=qry("INSERT INTO cuentasmove(idcuenta,hora,fecha,monto,login,nrodoc,tipo,detalles,local,loginuso,horauso,idcliente) select '$data[1]',NOW(),'$data[3]','$data[4]','$data[7]','$data[2]','$data[0]','$data[5]','$data[6]','$Xlogin',NOW(),idcliente from locales where local='$data[6]'");
			}
		else{*/
		//$sql=qry("INSERT INTO cuentasmove(idcuenta,tipo,nrodoc,fecha,monto,detalles,login,hora) values('111111111','$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]',NOW())");
		
		$sql=qry("INSERT INTO cuentasmove(idcuenta,hora,fecha,login,monto,nrodoc,tipo,idcliente,estado,detalles,tipocuenta,canal) select $pst_idc,now(),'$pst_fechadep','$Xlogin','$pst_monto','$pst_nrodoc','1',idcliente,0,'$pst_det',2,'CATALOGO' from usuarios where login='$Xlogin'");
		$id=mysql_insert_id();
		$idped=posturl(array("a"=>"pDepositos","idc"=>$pst_idc,"fechadep"=>$pst_fechadep,"login"=>$Xlogin,"monto"=>$pst_monto,"nrodoc"=>$pst_nrodoc,"det"=>$pst_det,"canal"=>"CATALOGO","idcliente"=>$Xidcliente),"http://perushop.pe/santa2/webservices/pedidos.php");
		qry("update cuentasmove set idpadre=$idped where idmove=$id");
		//echo "INSERT INTO cuentasmove(idcuenta,hora,fecha,login,monto,nrodoc,tipo,idcliente,estado,detalles,tipocuenta,canal) select $pst_idc,now(),'$pst_fechadep','$Xlogin','$pst_monto','$pst_nrodoc','1',idcliente,0,'$pst_det',2,'CATALOGO' from usuarios where login='$Xlogin'";
		//}
		
		echo "1";
	}
	///////////////////////////////////////////////////////////////////////////////
	if($pst_a=="loadinfo"){
		$sql=qry("SELECT idcuenta,nombre from cuentas where idcuenta='$pst_idcuenta'");
		$ff=mysql_fetch_row($sql);
		$res='<div class="form-group col-xs-6">
		<label>NRO. De CUENTA</label>
		<input class="form-control" value="'.$ff[0].'" readonly>
		</div>
		
		<div class="form-group col-xs-5">
		<label>BANCO</label>
		<input class="form-control" value="'.$ff[1].'" readonly>
		</div>';
		echo $res;
	}
	///////////////////////////////////////////////////////////////////////////////
	if($pst_a=="valida"){
		$res=valnumero($pst_data);
		if($res=="valido"){echo "1";}
		else{echo "0";}
	}
	
	///////////////////////////////////////////////////////////////////////////////
	function valnumero($data){
		$int_options = array("options"=>array("min_range"=>1, "max_range"=>1000000000));
		if(filter_var($data, FILTER_VALIDATE_INT, $int_options)===false){return "invalido";}
		else{return "valido";}
	}
	
	
?>