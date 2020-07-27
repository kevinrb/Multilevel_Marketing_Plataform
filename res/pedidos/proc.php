<?php
	require_once("../../sec.php");
	require_once("../../ws/func.php");
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	
	///////////////////////////////////////////////////////////////////////////////
	if($pst_a=="newc"){
		$val="";
		$data=$pst_data;
		for($i=0;$i<count($data);$i++)
		{$val .= "'$data[$i]',";}
		$sql=qry("INSERT INTO cuentas(idcuenta,banco,nombre,descripcion,horacrea,logincrea) values($val NOW(),'$Xlogin')");
		echo "1";
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
		$idped=posturl(array("a"=>"pDepositos","idc"=>$pst_idc,"fechadep"=>$pst_fechadep,"login"=>$Xlogin,"monto"=>$pst_monto,"nrodoc"=>$pst_nrodoc,"det"=>$pst_det,"canal"=>"CATALOGO","idcliente"=>$Xidcliente),$XXurl_erp1);
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
	function arbol($n,$login,$arr){
		$res=qry("select socio from asociadas where empresa='$login'");
		while($r=mysql_fetch_row($res)){
			$arr[0][$r[0]]=$n;
			$arr[1][]=$r[0];
			if($n==1)
			$arr[2][]=$r[0];		
			$m=$n+1;
			$arr=arbol($m,$r[0],$arr);
		}
		return $arr;
	}
	
	$qry_comi=qry("select * from comision_red");
	$comi=array();
	while($r=mysql_fetch_row($qry_comi)){
		$comi[1][$r[0]]=$r[1];
		$comi[2][$r[0]]=$r[2];
		$comi[3][$r[0]]=$r[3];
		$comi[4][$r[0]]=$r[4];
		$comi[5][$r[0]]=$r[5];
	}
	if($pst_a=="det_comi2"){
		$arr=array();
		$tan="";
		FOR($i=1;$i<11;$i++){
			$tan.=",round((if(d.tipo=1 and d.nivel=$i,d.total,0)),2)";
		}
		$comision=0;
		//	if($temp!=""){
		$tbl="<table class='table'>
		<thead><th>IDSOCIO</th><th>NOMBRE</th><th>PROFUNDIDAD</th>
		</thead>";
		$qry = "select b.nombre,b.apellidos,a.total,b.iduser from detalle_volumen a, usuarios b, user_nivel c where c.iduser=b.iduser and b.iduser=a.idsocio and a.iduser ='$pst_iduser' and c.estado=1 order by total desc";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$tot=array();
		$i=0;
		while($r = mysql_fetch_row($res)) {
			$ttr="";
			if($i==0){
				$ttr="class='info'";
			}
			$tbl.="<tr $ttr><td>$r[3]</td><td>$r[0] $r[1]</td><td>$r[2]</td></tr>";
			$tot[]=$r[2];
			$i++;
		}
		
		$stot=0;
		for($i=1;$i<count($tot);$i++){
			$stot+=$tot[$i];
		}		
		$tbl.="<tfoot><tr ><td></td><td><STRONG>VOLUMEN DE CONSTRUC.</STRONG></td><td>$stot</td></tr></tfoot></TABLE>";
		
		$tbl.="<table class='table table-bordered'><thead><tr><th colspan='9'>DETALLE COMISIONES</th></tr><tr><th>fecha</th><th>nivel</th><th>profun.</th><th>login</th><th>Nombre</th><th>tipo</th><th>total</th><th>%</th><th>Comision</th></tr></thead>";
		$res2=qry("select d.fecha,e.nivel,d.nivel,d.idsocio_from,concat(a.nombre,' ',coalesce(a.apellidos,'')),concat(if(d.tipo=1,'AUTOCONSUMO',if(d.tipo=2,'ADICIONAL','')),'-',d.tipo),d.total,d.porcentaje,d.comision,round((comision),2)$tan from (usuarios a, user_nivel b,comisiones_historial_puntos d) left join niveles_red c on c.idnivel=b.nivel left join user_nivel_day e on e.fecha=d.fecha and e.iduser=b.iduser where d.iduser=b.iduser and a.iduser=d.idsocio_from and b.hora_in>'2015-01-01' and d.fecha between '$pst_fecha' and last_day('$pst_fecha') and  b.iduser='$pst_iduser'");
//		$subto=array(0,0,0,0,0,0,0,0,0,0,0);
		while($rr=mysql_fetch_row($res2)){
			$tan1="";
			FOR($i=1;$i<11;$i++){
				$tt=$i+9;
				//$subto[$i]+=$rr[$tt];
			}
			$tbl.="<tr><td>$rr[0]</td><td>$rr[1]</td><td>$rr[2]</td><td>$rr[3]</td><td>$rr[4]</td><td>$rr[5]</td><td>$rr[6]</td><td>$rr[7]</td><td>$rr[8]</td></tr>";
		}

		
		$res2=qry("select d.fecha,e.nivel,d.nivel,d.idsocio_from,concat(a.nombre,' ',coalesce(a.apellidos,'')),concat(if(d.tipo=1,'AUTOCONSUMO',if(d.tipo=2,'ADICIONAL','')),'-',d.tipo),d.total,d.porcentaje,d.comision,round((comision),2)$tan from (usuarios a, user_nivel b,comisiones_historial_puntos3 d) left join niveles_red c on c.idnivel=b.nivel left join user_nivel_day e on e.fecha=d.fecha and e.iduser=b.iduser where d.iduser=b.iduser and a.iduser=d.idsocio_from and b.hora_in>'2015-01-01' and d.fecha between '$pst_fecha' and last_day('$pst_fecha') and  b.iduser='$pst_iduser'");
		$subto=array(0,0,0,0,0,0,0,0,0,0,0);
		while($rr=mysql_fetch_row($res2)){
			$tan1="";
			FOR($i=1;$i<11;$i++){
				$tt=$i+9;
				$subto[$i]+=$rr[$tt];
			}
		}	
		
		$tbl.="</table><table class='table table-bordered'><thead><tr><th colspan='2'>ACUMULADOS</th></tr></thead>";
		FOR($i=1;$i<11;$i++){
			$tbl.="<tr><td>$i</td><td>{$subto[$i]}</td></tr>";
		}
		
		
		
		echo $tbl;
		/*}
			else{
			Echo "NO TIENE SOCIOS";
		}*/
	}
	if($pst_a=="det_comi"){
		$tbl="";
		$qry = "select a.nombre,a.login,b.nivel,b.hora_in,hora_prof from usuarios a, user_nivel_day b where b.fecha='2016-03-31' and a.login=b.login and a.login='$pst_login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$tauto=comision_auto($r[1],$r[2],$r[3],$r[4]);
			$tadic=comision_adic($r[1],$r[2],$r[3]);
			//$tot=$tauto+$tadic;
			//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		echo $tauto.$tadic;
	}
	function tree_func($login,$jj){
		$jj++;
		$tbl="";
		$qry = "select a.login,a.nombre,a.apellidos from usuarios a, asociadas b where b.empresa=a.login and  b.socio='$login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$tbl.="<tr><td>$jj</td>";
			for($i=0;$i<count($r);$i++){
				$tbl.="<td>$r[$i]</td>";
			}
			$tbl.="</tr>";
			if($r[0]!="")
			$tbl.=tree_func($r[0],$jj);
			//$tadic=comision_adic($r[1],$r[2],$r[3]);
			//$tot=$tauto+$tadic;
			//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		return $tbl;
		
	}
	if($pst_a=="tree"){
		$tbl="<table class='table table-bordered'><thead><tr><td>N</td><td>login</td><td>nombre</td><td>Apellidos</td></tr></thead>";
		$tbl.=tree_func($pst_login,0 );
		$tbl.="</table>";
		echo $tbl;
	}
	if($pst_a=="venta"){
		$tbl="<table class='table table-bordered'><thead><tr><td>ID</td><td>IDOP</td><td>LOGIN</td><td>MONTO</td><td>HORA</td><td>TIPO</td><td>ESTA.</td><td>HORA</td><td>CORTE</td></tr></thead>";
		$qry = "select * from puntos where tipo=4 and login='$pst_login' and estado=1 order by hora_confirm";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$tbl.="<tr>";
			for($i=0;$i<count($r);$i++){
				$tbl.="<td>$r[$i]</td>";
			}
			$tbl.="</tr>";
		}
		$tbl.="</table>";
		$tbl1="<table class='table table-bordered'><thead><tr><td>ID</td><td>IDOP</td><td>LOGIN</td><td>MONTO</td><td>HORA</td><td>TIPO</td><td>ESTA.</td><td>HORA</td><td>CORTE</td></tr></thead>";
		$qry = "select * from puntos where tipo=6 and login='$pst_login' and estado=1 order by hora_confirm";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$tbl1.="<tr>";
			for($i=0;$i<count($r);$i++){
				$tbl1.="<td>$r[$i]</td>";
			}
			$tbl1.="</tr>";
		}
		$tbl1.="</table>";
		echo $tbl.$tbl1;
	}
	if($pst_a=="correc"){
		$tbl="<table class='table table-bordered'><thead><tr><td>IDUSER</td><td>LOGIN</td><td>MONTO</td></tr></thead>";
		$qry = "select iduser,login,comision from comisiones_adicional where iduser='$pst_iduser' and login='$pst_login' and fecha='$pst_fecha-15' and tipo='CORRECCION'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$r = mysql_fetch_row($res);
		$tbl.="<tr><td>$pst_iduser</td><td>$pst_login</td><td><input value='$r[2]' id='moncorr' class='input-sm form-control' /></td><td><button data-login='$pst_login' data-iduser='$pst_iduser' data-fecha='$pst_fecha' type='button' class='btn btn-success btn-sm save_correc'>
			 <span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span></button></td></tr>";
		$tbl.="</table>";

		echo $tbl;
	}	
	if($pst_a=="mcorrec"){
		$qry = "insert into comisiones_adicional(iduser,login,fecha,tipo,comision) values ('$pst_iduser','$pst_login','$pst_fecha-15','CORRECCION','$pst_montcorr') on duplicate key update comision='$pst_montcorr'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		
		$tbl="<table class='table table-bordered'><thead><tr><td>IDUSER</td><td>LOGIN</td><td>MONTO</td></tr></thead>";
		$qry = "select iduser,login,comision from comisiones_adicional where iduser='$pst_iduser' and login='$pst_login' and fecha='$pst_fecha-15' and tipo='CORRECCION'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$r = mysql_fetch_row($res);
		$tbl.="<tr><td>$pst_iduser</td><td>$pst_login</td><td> $r[2]</td><td></td></tr>";
		$tbl.="</table>";

		echo $tbl;
	}		
	function comision_auto($Xlogin,$Xnivel,$Xfchcorte,$f_conf){
		$temp=0;
		$n=1;
		$tbl="<table class='table table-bordered'><thead><tr><th colspan='8'>COMISION AUTOCONSUMO($Xfchcorte) $f_conf</th></tr></thead>";
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1 and c.tipo=4,c.puntos,0)),a.telefono,a.celular,'',a.login,b.hora_in,date(min(c.hora)) from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login and date(c.hora) between  DATE_FORMAT('2016-03-31' ,'%Y-%m-01') and '2016-03-31' where b.fecha='2016-03-31' and b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$qryy=comision($r[7],$Xnivel,$n,1,$Xfchcorte,$f_conf);
			$qryy[0]=round($qryy[0],2);
			//$temp+=$qryy;
			$r[3]=round($r[3],2);
			if($r[3]>0){
				$tbl.="<tr $cla><td>$n</td><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy[1]</td><td>$qryy[0]</td><td>$r[6]</td><td>$r[9]</td></tr>";
			}
			$tbl.=hijos($r[7],"'$Xlogin'",$n,$Xnivel,$Xfchcorte,$f_conf);
			//$temp+=$tbl;
		}
		return $tbl;
	}
	function hijos($log,$pa_log,$n,$Xnivel,$Xfchcorte,$f_conf){
		$n++;
		$temp=0;
		$qry = "select a.nombre, a.idpersona,a.direccion,sum(if(c.estado=1 and c.tipo=4,c.puntos,0)),a.telefono,a.celular,'$log',a.login,b.hora_in,date(max(c.hora))  from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login and date(c.hora) between DATE_FORMAT('2016-03-31' ,'%Y-%m-01') and '2016-03-31' where b.fecha='2016-03-31' and b.login=a.login and a.login in (select socio from asociadas where empresa='$log')and a.login not in ($pa_log) group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		$tbl="";
		while($r = mysql_fetch_row($res)) {
			$pa_log.=",'$r[7]'";
			$qryy=comision($r[7],$Xnivel,$n,1,$Xfchcorte,$f_conf);
			//$temp+=$qryy;
			$qryy[0]=round($qryy[0],2);
			//$temp+=$qryy;
			$r[3]=round($r[3],2);		
			if($r[3]>0){
				$tbl.="<tr $cla><td>$n</td><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy[1]</td><td>$qryy[0]</td><td>$r[6]</td><td>$r[9]</td></tr>";
			}
			$tbl.= hijos($r[7],$pa_log,$n,$Xnivel,$Xfchcorte,$f_conf);
			//$temp+=$tbl;
		}
		return $tbl;
	}
	function comision($Xlogin,$Xnivel,$n,$igv,$Xfchcorte,$f_conf){
		if($Xnivel>0)
		{
			$descact_past=0;
			$desc=mysql_fetch_row(qry("select nivel$Xnivel from comision_red where nivel=$n"));
			if($desc[0]>0){
				$qry1="select if('$n'=1,if(a.hora_confirm between '$f_conf' and '$f_conf' + interval 6 month or a.hora_confirm between '$Xfchcorte' and '$Xfchcorte' + interval 1 month,sum(puntos*(($desc[0])/(100*$igv))),sum(puntos*((15)/(100*$igv)))),sum(puntos*(($desc[0])/(100*$igv)))),if('$n'=1,if(a.hora_confirm between '$f_conf' and '$f_conf' + interval 6 month or a.hora_confirm between '$Xfchcorte' and '$Xfchcorte' + interval 1 month,($desc[0]),(15)),($desc[0])),'$desc[0]' from puntos a where tipo=4 and login='$Xlogin' and estado=1 and date(a.hora) between concat(year('2016-03-31'),'-',month('2016-03-31'),'-01') and '2016-03-31' ";
				$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
				$prespro_past=mysql_fetch_row($res1);
				$prespro_past[]=$qry1;
			}
			return $descact_past=$prespro_past;
		}
	}
	function comision_adic($Xlogin,$Xnivel,$Xfchcorte){
		$temp=0;
		$n=1;
		$tbl="<table class='table table-bordered'><thead><tr><th colspan='8'>COMISION ADICIONAL</th></tr></thead>";
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1 and c.tipo=6,c.puntos,0)),a.telefono,a.celular,'',a.login,b.hora_in,date(min(c.hora)) from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login and c.hora between  DATE_FORMAT('2016-03-31' ,'%Y-%m-01') and '2016-03-31 23:59:59'  where b.fecha='2016-03-31' and b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$qryy=comision2($r[7],$Xnivel,1,$Xfchcorte);
			//$temp$qryy;
			$qryy[0]=round($qryy[0],2);
			//$temp+=$qryy;
			$r[3]=round($r[3],2);
			if($r[3]>0){
				$tbl.="<tr $cla><td>$n</td><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy[1]</td><td>$qryy[0]</td><td>$r[6]</td><td>$r[9]</td></tr>";
			}
			//$tbl=hijos($r[7],"'$Xlogin'",$n,$Xnivel);
			//$temp+=$tbl;
		}
		return $tbl;
	}
	function comision2($Xlogin,$Xnivel,$igv,$Xfchcorte){
		
		if($Xnivel>0)
		{
			//echo 1;
			if(1==1){
				$qry1="select sum(puntos*((5)/(100*$igv))),5 from puntos a where tipo=6 and login='$Xlogin' and estado=1 and date(a.hora)  between concat(year('2016-03-31'),'-',month('2016-03-31'),'-01') and '2016-03-31'";
				$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
				$prespro_past=mysql_fetch_row($res1);
			}
			return $descact_past=$prespro_past;
		}
	}
	
	/*
		if($pst_a=="det_comi"){
		$tbl="";
		$qry = "select a.nombre,a.login,b.nivel,b.hora_in from usuarios a, user_nivel_day b where a.login=b.login and a.login='$pst_login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		$tauto=comision_auto($r[1],$r[2],$r[3]);
		$tadic=comision_adic($r[1],$r[2],$r[3]);
		//$tot=$tauto+$tadic;
		//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		echo $tauto.$tadic;
		}
		
		function comision_auto($Xlogin,$Xnivel,$Xfchcorte){
		$temp=0;
		$n=1;
		$tbl="<table class='table table-bordered'><thead><tr><th colspan='8'>COMISION AUTOCONSUMO($Xfchcorte)</th></tr></thead>";
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1 and c.tipo=4,c.puntos,0)),a.telefono,a.celular,'',a.login,b.hora_in,date(min(c.hora)) from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login and c.hora> DATE_FORMAT(NOW() ,'%Y-%m-01')  where b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		$qryy=comision($r[7],$Xnivel,$n,1,$Xfchcorte);
		//$temp+=$qryy;
		if($r[3]>0){
		$tbl.="<tr $cla><td>$n</td><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy[1]</td><td>$qryy[0]</td><td>$r[6]</td><td>$r[9]</td></tr>";
		}
		$tbl.=hijos($r[7],"'$Xlogin'",$n,$Xnivel,$Xfchcorte);
		//$temp+=$tbl;
		}
		return $tbl;
		}
		function hijos($log,$pa_log,$n,$Xnivel,$Xfchcorte){
		$n++;
		$temp=0;
		$qry = "select a.nombre, a.idpersona,a.direccion,sum(if(c.estado=1 and c.tipo=4,c.puntos,0)),a.telefono,a.celular,'$log',a.login,b.hora_in,date(min(c.hora))  from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login and c.hora> DATE_FORMAT(NOW() ,'%Y-%m-01') where b.login=a.login and a.login in (select socio from asociadas where empresa='$log')and a.login not in ($pa_log) group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		$tbl="";
		while($r = mysql_fetch_row($res)) {
		$palog.=$pa_log.",'$r[7]'";
		$qryy=comision($r[7],$Xnivel,$n,1,$Xfchcorte);
		//$temp+=$qryy;
		if($r[3]>0){
		$tbl.="<tr $cla><td>$n</td><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy[1]</td><td>$qryy[0]</td><td>$r[6]</td><td>$r[9]</td></tr>";
		}
		$tbl.= hijos($r[7],$palog,$n,$Xnivel,$Xfchcorte);
		//$temp+=$tbl;
		}
		return $tbl;
		}
		function comision($Xlogin,$Xnivel,$n,$igv,$Xfchcorte){
		
		if($Xnivel>0)
		{
		$descact_past=0;
		$desc=mysql_fetch_row(qry("select nivel$Xnivel from comision_red where nivel=$n"));
		if($desc[0]>0){
		$qry1="select sum(puntos*(($desc[0])/(100*$igv))),'$desc[0]' from puntos a where tipo=4 and login='$Xlogin' and estado=1 and if(now()>concat(year(now()),'-',month(now()),'-',day('$Xfchcorte'),' 23:59:59'),a.hora>concat(year(now()),'-',month(now()),'-',day('$Xfchcorte'),' 23:59:59'),a.hora>concat(year(now()),'-',month(now()),'-01')) ";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		$prespro_past=mysql_fetch_row($res1);
		$prespro_past[]=$qry1;
		}
		return $descact_past=$prespro_past;
		}
		}
		function comision_adic($Xlogin,$Xnivel,$Xfchcorte){
		$temp=0;
		$n=1;
		$tbl="<table class='table table-bordered'><thead><tr><th colspan='8'>COMISION ADICIONAL</th></tr></thead>";
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1 and c.tipo=4,c.puntos,0)),a.telefono,a.celular,'',a.login,b.hora_in,date(min(c.hora)) from (usuarios a, user_nivel_day b) left join puntos c on c.login=a.login and c.hora> DATE_FORMAT(NOW() ,'%Y-%m-01')  where b.login=a.login and a.login in (select socio from asociadas where empresa='$Xlogin') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		$qryy=comision2($r[7],$Xnivel,1,$Xfchcorte);
		//$temp$qryy;
		if($r[3]>0){
		$tbl.="<tr $cla><td>$n</td><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy[1]</td><td>$qryy[0]</td><td>$r[6]</td><td>$r[9]</td></tr>";
		}
		//$tbl=hijos($r[7],"'$Xlogin'",$n,$Xnivel);
		//$temp+=$tbl;
		}
		return $tbl;
		}
		function comision2($Xlogin,$Xnivel,$igv,$Xfchcorte){
		
		if($Xnivel>0)
		{
		//echo 1;
		if(1==1){
		$qry1="select sum(puntos*((5)/(100*$igv))),5 from puntos a where tipo=4 and login='$Xlogin' and estado=1 and if(now()>concat(year(now()),'-',month(now()),'-',day('$Xfchcorte'),' 23:59:59'),a.hora>concat(year(now()),'-',month(now()),'-',day('$Xfchcorte'),' 23:59:59'),a.hora>concat(year(now()),'-',month(now()),'-01')) ";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		$prespro_past=mysql_fetch_row($res1);
		}
		return $descact_past=$prespro_past;
		}
		}
	*/
?>