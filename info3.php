<?php
	include "global.php";
	include("cnx.php");
	
	function qry($a){
		$b=mysql_query($a) or die(mysql_error()."-----".$a) ;
		return $b;
	}
	
	$fec="2017-12";
	
	/*
		//Corrector General deuda
		
		qry("update puntos a,user_nivel_day b set b.deuda=0,b.idop=a.idop,b.monto=a.puntos where a.iduser=b.iduser and b.fecha between a.fecha_venc and a.fecha_venc + interval 1 month and b.fecha<a.fecha_venc + interval 1 month  and a.fecha_venc>='2017-04-01' and a.tipo=9 and a.estado=1 and fecha_venc>=date(hora_confirm) and b.deuda>0");
		
		//Corrector General
		
		qry("update puntos a,user_nivel_day b set b.monto=a.puntos,b.idop=a.idop  where a.iduser=b.iduser and b.fecha between a.fecha_venc and a.fecha_venc + interval 1 month and b.fecha<a.fecha_venc + interval 1 month  and a.fecha_venc>='2017-04-01' and a.tipo=9 and a.estado=1 and fecha_venc>=date(hora_confirm) and b.monto<a.puntos");
		
		//REGULA los monto respecto a un dia
		
		qry("update user_nivel_day a,(select iduser,sum(puntos) tot from puntos where substr(fecha_venc,1,7)='$fec' and estado=1 and tipo=9 group by iduser) b set a.monto=tot where a.iduser=b.iduser and a.fecha=last_day('$fec-01') and tot!=a.monto");
		
		//////////NIVEL_1
		
		function tree_func_n($login,$jj,$cons,$fec){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.fecha=last_day('$fec-01') and b.idpatro=a.iduser and  b.idsocio='$login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		//echo "'$r[0]','$cons' \n";
		$tbl="insert ignore into cuenta_nivel_1(idempresa,idsocio,nivel) values ('$r[0]','$cons',$jj)";
		qry($tbl);
		if($r[0]!="")
		tree_func_n($r[0],$jj,$cons,$fec);
		//$tadic=comision_adic($r[1],$r[2],$r[3]);
		//$tot=$tauto+$tadic;
		//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		return 1;
		
		}
		
		//////REgula NIVEL_1
		qry("truncate table cuenta_nivel_1");
		$res=qry("select iduser from user_nivel_day where  monto>0 and fecha=last_day('$fec-01')");
		while($r=mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_nivel_1(idempresa,idsocio,nivel) values ('$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func_n($r[0],0,$r[0],$fec);
		
		}
		
		
		//qry("update user_nivel_day a left join (select a.iduser,count(distinct c.idempresa) npla from user_nivel_day a, asociadas_day b,cuenta_nivel_1 c,asociadas_day b2 where a.fecha=last_day('$fec-01') and b.fecha=last_day('$fec-01') and b.idsocio=c.idempresa and a.iduser=b.idempresa and c.idsocio=b2.idsocio and a.iduser=b2.idpatro AND b2.fecha=last_day('$fec-01') group by a.iduser) b on a.iduser=b.iduser set a.nivel_1=coalesce(b.npla,0) where a.fecha=last_day('$fec-01')");
		qry("update user_nivel_day a left join (select a.iduser,count(distinct c.idempresa) npla from user_nivel_day a, asociadas_day b,cuenta_nivel_1 c,asociadas_day b2 where a.fecha=last_day('$fec-01') and b.fecha=last_day('$fec-01') and b.idsocio=c.idempresa and a.iduser=b.idpatro and c.idsocio=b2.idsocio and a.iduser=b2.idpatro AND b2.fecha=last_day('$fec-01') group by a.iduser) b on a.iduser=b.iduser set a.nivel_1=coalesce(b.npla,0) where a.fecha=last_day('$fec-01')");
		qry("update user_nivel_day a left join (select a.iduser,a.nivel,coalesce(max(b.idnivel),0) nivel1 from user_nivel_day a left join niveles_red b on b.limite1<=a.monto and b.minimo<=a.nivel_1 and b.min_vol<=a.volumen and b.idnivel<6 where monto>0 and a.fecha=last_day('$fec-01') group by a.iduser) b on a.iduser=b.iduser set a.nivel=coalesce(b.nivel1,0) where a.fecha=last_day('$fec-01')");
		///////////CALCULO de VOLUMEN
		
		///////COMISION DIARIO
		/////funcion comi
		function tree_comi_v($login,$jj,$fec,$monto,$idop,$log){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in>'2016-12-01') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3) or ('$fec' between hora_prof and hora_prof + interval 5 month and tprof=4),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)),tprof,hora_prof from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.idempresa=a.iduser and  b.idsocio='$login' and b.fecha='$fec'";
		
		$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		$tcom=0;
		if($r[1]>0){
		$rte=mysql_fetch_row(mysql_query("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red where nivel=$jj"));
		if($rte[0]>0){
		if($jj==1 and $r[2]==1){
		$tcom=30;
		if($r[3]==4)
		{
		$rte1=mysql_fetch_row(mysql_query("select if(hora_in>='$r[4]',1,0) from user_nivel_day where fecha='$fec' and iduser='$login' "));
		if($rte1[0]>0){
		$tcom=30;
		}else{
		$tcom=0;
		}
		}
		}
		if($jj==1 and $r[2]==2){
		$tcom=40;
		}
		if($tcom>0){
		$rte[0]=round($tcom*$monto/100,3);
		$rte[1]=$tcom;
		}
		//echo "$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
		$tbl="insert ignore into comisiones_historial_puntos2(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],1,$monto)";
		mysql_query($tbl);	
		
		}else{
		$tbl="insert ignore into comisiones_historial_puntos2(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto)";
		qry($tbl);				
		}
		}else{
		$tbl="insert ignore into comisiones_historial_puntos2(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto)";
		qry($tbl);
		}
		if($r[0]!="")
		tree_comi_v($r[0],$jj,$fec,$monto,$idop,$log);
		}
		return 1;
		}
		///////COMISION DIARIO
		/////funcion comi
		function tree_comi2_v($login,$jj,$fec,$monto,$idop,$log){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in>'2016-12-01') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3) or ('$fec' between hora_prof and hora_prof + interval 5 month and tprof=4),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)),tprof,hora_prof from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.idempresa=a.iduser and  b.idsocio='$login' and b.fecha='$fec'";
		
		$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)){
		$tcom=0;
		if($r[1]>0){
		$rte=mysql_fetch_row(mysql_query("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red_acumu where nivel=$jj"));
		if($rte[0]>0){
		//echo "$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
		$tbl="insert ignore into comisiones_historial_puntos2(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],2,$monto)";
		mysql_query($tbl);	
		
		}else{
		$tbl="insert ignore into comisiones_historial_puntos2(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,2,$monto)";
		qry($tbl);				
		}
		}else{
		$tbl="insert ignore into comisiones_historial_puntos2(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,2,$monto)";
		qry($tbl);
		}
		if($r[0]!="")
		tree_comi2_v($r[0],$jj,$fec,$monto,$idop,$log);
		}
		return 1;
		}
		
		
		qry("delete from comisiones_historial_puntos2 where fecha between '$fec-01' and last_day('$fec-01') and fecha>'2017-05-02'");
		//////genera COMISIONES
		//echo "select date(hora_confirm),iduser,puntos,idop from puntos where tipo=9 and estado=1 and date(hora_confirm) between '$fec-01' and last_day('$fec-01') and hora_confirm>='2017-05-03'";
		$res=qry("select date(hora_confirm),iduser,puntos,idop from puntos where tipo=9 and estado=1 and date(hora_confirm) between '$fec-01' and last_day('$fec-01') and hora_confirm>='2017-05-03'");
		while($r=mysql_fetch_row($res)){
		//"<br />$r[0]----$r[2]---$r[1]<br />";
		tree_comi_v($r[1],0,$r[0],$r[2],$r[3],$r[1]);
		$tbl="insert ignore into comisiones_historial_puntos2(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,1,'$r[2]')";
		$res2 = mysql_query($tbl) or die(mysql_error());
		}
		////genera comisiones adicionales
		//echo "select date(hora_confirm),iduser,puntos,idop from puntos where tipo=10 and estado=1 and date(hora_confirm) between '$fec-01' and last_day('$fec-01') and hora_confirm>'2017-05-03'";
		$res=qry("select date(hora_confirm),iduser,puntos,idop from puntos where tipo=10 and estado=1 and date(hora_confirm) between '$fec-01' and last_day('$fec-01') and hora_confirm>'2017-05-03'");
		while($r=mysql_fetch_row($res)){
		//echo "<br />$r[0]----$r[2]---$r[1]<br />";
		tree_comi2_v($r[1],0,$r[0],$r[2],$r[3],$r[1]);
		$tbl="insert ignore into comisiones_historial_puntos2(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,2,'$r[2]')";
		$res2 = mysql_query($tbl) or die(mysql_error());
		}
		
		qry("delete from detalle_volumen");
		qry("insert into detalle_volumen select b.idempresa,b.idsocio,sum(total) from comisiones_historial_puntos2 a, asociadas_day b where b.fecha=last_day('$fec-01') and  a.iduser=b.idsocio and a.fecha>='2017-05-03' and '$fec'=substr(a.fecha,1,7) group by b.idempresa,b.idsocio;");
		
		
		qry("update user_nivel_day set volumen=0 where volumen >0 and fecha=last_day('$fec-01')");
		$res1=qry(" select iduser from user_nivel_day where nivel_1>1 and fecha=last_day('$fec-01')");
		while($rr=mysql_fetch_row($res1)){
		$Xiduser=$rr[0];
		$qry = "select b.nombre,b.apellidos,a.total,b.iduser from detalle_volumen a, usuarios b, user_nivel_day c where c.fecha=last_day('$fec-01') and c.iduser=b.iduser and b.iduser=a.idsocio and a.iduser ='$Xiduser' and c.estado=1 order by total desc";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$tot=array();
		$i=0;
		while($r = mysql_fetch_row($res)) {
		$tot[]=$r[2];
		$i++;
		}
		
		$stot=0;
		for($i=1;$i<count($tot);$i++){
		$stot+=$tot[$i];
		}
		qry("update user_nivel_day set volumen='$stot' where iduser='$Xiduser' and fecha=last_day('$fec-01')");
		}
		
		
		///////////
		
		///niveles
		qry("update user_nivel_day a left join (select a.iduser,a.nivel,coalesce(max(b.idnivel),0) nivel1 from user_nivel_day a left join niveles_red b on b.limite1<=a.monto and b.minimo<=a.nivel_1 and b.min_vol<=a.volumen and b.idnivel<6 where monto>0 and a.fecha=last_day('$fec-01') group by a.iduser) b on a.iduser=b.iduser set a.nivel=coalesce(b.nivel1,0) where a.fecha=last_day('$fec-01')");
		
		//////////ORO
		
		function tree_func_o($login,$jj,$cons,$fec){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.fecha=last_day('$fec-01') and b.idempresa=a.iduser and  b.idsocio='$login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		
		$tbl="insert ignore into cuenta_oro(idempresa,idsocio,nivel) values ('$r[0]','$cons',$jj)";
		qry($tbl);
		if($r[0]!="")
		tree_func_o($r[0],$jj,$cons,$fec);
		//$tadic=comision_adic($r[1],$r[2],$r[3]);
		//$tot=$tauto+$tadic;
		//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		return 1;
		}
		
		//////REgula ORO
		qry("truncate table cuenta_oro");
		$res=qry("select iduser from user_nivel_day where  nivel>2 and fecha=last_day('$fec-01')");
		while($r=mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_oro(idempresa,idsocio,nivel) values ('$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func_o($r[0],0,$r[0],$fec);
		
		}
		
		$res=qry("update user_nivel_day a left join (select a.iduser,count(distinct c.idempresa) npla from user_nivel_day a, asociadas b,cuenta_oro c where  b.idsocio=c.idempresa and a.login=b.empresa and nivel_1>3 and a.estado=1 and a.fecha=last_day('$fec-01') group by a.login) b on a.iduser=b.iduser set a.oro_mas=coalesce(b.npla,0) where a.fecha=last_day('$fec-01')");
		
		
		/////nivels con oro
		qry("update user_nivel_day a left join (select a.iduser,a.nivel,coalesce(max(b.idnivel),0) nivel1 from user_nivel_day a left join niveles_red b on b.limite1<=a.monto and b.minimo<=a.nivel_1 and b.min_vol<=a.volumen and b.idnivel<7 and min_oro<=a.oro_mas where monto>0 and a.fecha=last_day('$fec-01') group by a.iduser) b on a.iduser=b.iduser set a.nivel=coalesce(b.nivel1,0) where a.fecha=last_day('$fec-01')");
		
		///////PLATINO
		
		function tree_func_p($login,$jj,$cons,$fec){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.fecha=last_day('$fec-01') and b.idempresa=a.iduser and  b.idsocio='$login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_platino(idempresa,idsocio,nivel) values ('$r[0]','$cons',$jj)";
		qry($tbl);
		if($r[0]!="")
		tree_func_p($r[0],$jj,$cons,$fec);
		//$tadic=comision_adic($r[1],$r[2],$r[3]);
		//$tot=$tauto+$tadic;
		//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		return 1;
		
		}
		
		//////REgula platinos
		qry("truncate table cuenta_platino");
		$res=qry("select iduser from user_nivel_day where  nivel>3 and fecha=last_day('$fec-01')");
		while($r=mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_platino(idempresa,idsocio,nivel) values ('$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func_p($r[0],0,$r[0],$fec);
		
		}
		
		$res=qry("update user_nivel_day a left join (select a.iduser,count(distinct c.idempresa) npla from user_nivel_day a, asociadas b,cuenta_platino c where  b.idsocio=c.idempresa and a.login=b.empresa and nivel_1>=3 and a.estado=1 and a.fecha=last_day('$fec-01') group by a.login) b on a.iduser=b.iduser set a.plati_mas=coalesce(b.npla,0) where a.fecha=last_day('$fec-01')");
		
		qry("update user_nivel_day a left join (select a.iduser,a.nivel,coalesce(max(b.idnivel),0) nivel1 from user_nivel_day a left join niveles_red b on b.limite1<=a.monto and b.minimo<=a.nivel_1 and b.min_vol<=a.volumen and b.idnivel<8 and min_plati<=a.plati_mas where monto>0 and a.fecha=last_day('$fec-01') group by a.iduser) b on a.iduser=b.iduser set a.nivel=coalesce(b.nivel1,0) where a.fecha=last_day('$fec-01')");
		
		
		///////ZAFIRO
		
		function tree_func_z($login,$jj,$cons,$fec){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.fecha=last_day('$fec-01') and b.idempresa=a.iduser and  b.idsocio='$login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_zafiro(idempresa,idsocio,nivel) values ('$r[0]','$cons',$jj)";
		qry($tbl);
		if($r[0]!="")
		tree_func_z($r[0],$jj,$cons,$fec);
		//$tadic=comision_adic($r[1],$r[2],$r[3]);
		//$tot=$tauto+$tadic;
		//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		return 1;
		
		}
		
		//////REgula ZAFIRO
		qry("truncate table cuenta_zafiro");
		$res=qry("select iduser from user_nivel_day where  nivel>4 and fecha=last_day('$fec-01')");
		while($r=mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_zafiro(idempresa,idsocio,nivel) values ('$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func_z($r[0],0,$r[0],$fec);
		
		}
		
		$res=qry("update user_nivel_day a left join (select a.iduser,count(distinct c.idempresa) npla from user_nivel_day a, asociadas b,cuenta_zafiro c where  b.idsocio=c.idempresa and a.login=b.empresa and nivel_1>=3 and a.estado=1 and a.fecha=last_day('$fec-01') group by a.login) b on a.iduser=b.iduser set a.zafi_mas=coalesce(b.npla,0) where a.fecha=last_day('$fec-01')");
		
		
		
		
		//qry("update user_nivel_day a left join (select a.iduser,a.nivel,coalesce(max(b.idnivel),0) nivel1 from user_nivel_day a left join niveles_red b on b.limite1<=a.monto and b.minimo<=a.nivel_1 and b.min_vol<=a.volumen and b.idnivel<8 and min_plati<=a.plati_mas where monto>0 and a.fecha=last_day('$fec-01') group by a.iduser) b on a.iduser=b.iduser set a.nivel=coalesce(b.nivel1,0) where a.fecha=last_day('$fec-01')");
		
		
		
		/*
		
		A pasado
		update user_nivel_day set nivel=0 where fecha between '2017-12-01' and '2017-12-30';
		update user_nivel_day a, user_nivel_day b  set b.nivel=a.nivel where a.iduser=b.iduser and a.fecha='2017-12-31' and b.fecha between '2017-12-01' and '2017-12-30' and a.nivel!=b.nivel and b.monto>0;
		
		A futuro
		update user_nivel_day set nivel=0 where fecha between '2018-01-01' and '2018-01-30';
		update user_nivel_day a, user_nivel_day b  set b.nivel=a.nivel where a.iduser=b.iduser and a.fecha='2017-12-31' and b.fecha between '2018-01-01' and '2018-01-30' and a.nivel!=b.nivel;
		
		HOY 
		update user_nivel set nivel=0 ;
		update user_nivel_day a, user_nivel b  set b.nivel=a.nivel where a.iduser=b.iduser and a.fecha='2017-12-31'  and a.nivel!=b.nivel;
		
	*/
	/*
		
		
		/////CALCULO DE COMISIONES
		
		function tree_comi($login,$jj,$fec,$monto,$idop,$log,$t){
		$jj++;
		$tbl="";
		if($t=="R"){
		$amar="b.idpatro=a.iduser";
		}else{
		$amar="b.idempresa=a.iduser";
		}
		
		$qry = "select a.iduser,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in>'2016-12-01') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3) or ('$fec' between hora_prof and hora_prof + interval 5 month and tprof=4),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)),tprof,hora_prof,if('$fec' between date(hora_in) and date(hora_in) + interval 1 month,'R','A') from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and $amar and  b.idsocio='$login' and b.fecha='$fec'";
		
		
		$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		$tcom=0;
		if($r[1]>0){
		//echo "select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red where nivel=$jj";
		$rte=mysql_fetch_row(mysql_query("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red where nivel=$jj"));
		if($rte[0]>0){
		if($jj==1 and $r[2]==1){
		$tcom=30;
		if($r[3]==4)
		{
		$rte1=mysql_fetch_row(mysql_query("select if(hora_in>='$r[4]',1,0) from user_nivel_day where fecha='$fec' and iduser='$login' "));
		if($rte1[0]>0){
		$tcom=30;
		}else{
		$tcom=0;
		}
		}
		}
		if($jj==1 and $r[2]==2){
		$tcom=40;
		}
		if($tcom>0){
		$rte[0]=round($tcom*$monto/100,3);
		$rte[1]=$tcom;
		}
		echo "HHH1-$jj---$r[1]-$jj($rte[1])-$r[0]--$rte[0]--($r[2])<BR />";
		$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],1,$monto,'$r[5]')";
		mysql_query($tbl);	
		
		}else{
		$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto,'$r[5]')";
		qry($tbl);				
		}
		}else{
		$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto,'$r[5]')";
		qry($tbl);
		}
		if($jj>30){
		echo "BREAK-$jj---$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
		break;
		}
		if($r[0]!="")
		tree_comi($r[0],$jj,$fec,$monto,$idop,$log,$r[5]);
		}
		return 1;
		}
		
		///////COMISION DIARIO
		/////funcion comi
		function tree_comi2($login,$jj,$fec,$monto,$idop,$log){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in<'2016-12-01') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3) or ('$fec' between hora_prof and hora_prof + interval 5 month and tprof=4),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)),tprof,hora_prof from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.idempresa=a.iduser and  b.idsocio='$login' and b.fecha='$fec'";
		
		$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		$tcom=0;
		if($r[1]>0){
		$rte=mysql_fetch_row(mysql_query("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red_acumu where nivel=$jj"));
		if($rte[0]>0){
		echo "HHH2-$jj---$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
		$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],2,$monto)";
		mysql_query($tbl);	
		
		}else{
		$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,2,$monto)";
		qry($tbl);				
		}
		}else{
		$tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,2,$monto)";
		qry($tbl);
		}
		
		if($jj>30){
		echo "BREAK-$jj---$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
		break;
		}
		if($r[0]!="")
		tree_comi2($r[0],$jj,$fec,$monto,$idop,$log);
		}
		return 1;
		}
		function tree_comi_adi($login,$jj,$fec,$monto,$idop,$log,$desc){
		$jj++;
		$tbl="";
		$qry = "select a.login,a.nivel from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.empresa=a.login and  b.socio='$login' and b.fecha='$fec'";
		$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		$tcom=0;
		if($r[1]>0){			
		$rte=mysql_fetch_row(mysql_query("select round(($desc*$monto)/100,3),$desc from comision_red where nivel=$jj"));
		if($rte[0]>0){
		echo "---$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
		$tbl="insert ignore into comisiones_historial(fecha,login,socio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],2,$monto)";
		mysql_query($tbl);
		}
		}
		}
		return 1;
		}
		
		qry("delete from comisiones_historial_puntos where fecha>curdate() - interval 2 month and fecha between '$fec-01' and concat(last_day('$fec-01'),' 23:59:59')");
		//////genera COMISIONES
		$res=qry("select date(hora_confirm),a.iduser,a.puntos,a.idop,if(date(b.hora_in)=date(fecha_venc),'R','A') from puntos a left join  user_nivel_day b  on date(a.hora_confirm)=b.fecha and a.iduser=b.iduser where a.tipo=9 and a.estado=1 and hora_confirm>=curdate() - interval 2 month and hora_confirm between '$fec-01' and concat(last_day('$fec-01'),' 23:59:59')
		");
		while($r=mysql_fetch_row($res)){
		echo "<br />CCCCC1----$r[0]----$r[2]---C1-$r[1]<br />";
		tree_comi($r[1],0,$r[0],$r[2],$r[3],$r[1],$r[4]);
		echo $tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,1,'$r[2]','$r[4]')";
		$res2 = mysql_query($tbl) or die(mysql_error());
		}
		
		
		////genera comisiones adicionales
		$res=qry("select date(hora_confirm),iduser,puntos,idop from puntos where tipo=10 and estado=1 and hora_confirm>=curdate() - interval 2 month and hora_confirm between '$fec-01' and concat(last_day('$fec-01'),' 23:59:59')");
		while($r=mysql_fetch_row($res)){
		echo "<br />CCCCC2--$r[0]----$r[2]---C2-$r[1]<br />";
		tree_comi2($r[1],0,$r[0],$r[2],$r[3],$r[1]);
		echo $tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,2,'$r[2]')";
		$res2 = mysql_query($tbl) or die(mysql_error());
		}
		
		
		
		///BONOS
		
		
		///OLDD $res=qry("insert ignore into comisiones_adicional(fecha,login,tipo,comision,iduser) select concat(min(fecy),'-',min(fecm),'-15'),login,'BONO',if(tt=4,50,if(tt=5,90,if(tt=6,170,if(tt=7,400,0)))),iduser from (select year(fecha) fecy,month(fecha) fecm,login,iduser,max(nivel) tt from user_nivel_day where nivel>3 and fecha>='2017-04-01' group by year(fecha),month(fecha),iduser ) a group by iduser,tt order by iduser,min(fecm)");
		
		
		///FIRST neeww insert ignore into comisiones_adicional(fecha,login,tipo,comision,iduser,dato) select fech,a.login,'BONO',mnt,a.iduser,tt from (select min(STR_TO_DATE(concat(fecy,'-',fecm,'-15'),'%Y-%m-%d')) fech,login,'BONO',if(tt=4,50,if(tt=5,90,if(tt=6,170,if(tt=7,400,if(tt=8,1600,if(tt=9,2400,0)))))) mnt,iduser,tt from (select year(fecha) fecy,month(fecha) fecm,login,iduser,max(nivel) tt from user_nivel_day where nivel>3 and fecha>='2017-04-01' group by year(fecha),month(fecha),iduser ) a group by iduser,tt) a left join comisiones_adicional b on b.login=a.login and tt=dato and fech>fecha where b.fecha is null;
		
		
		//insert ignore into comisiones_adicional(fecha,login,tipo,comision,iduser,dato,comentario) select a.fecha,a.login,'BONO',if(tn=4,50,if(tn=5,90,if(tn=6,170,if(tn=7,400,0)))),a.iduser,tn,'ANTICIPO' from (select a.fecha,a.login,a.iduser,a.nivel-1 tn from user_nivel_day a left join comisiones_adicional b on b.iduser=a.iduser and b.fecha='2017-06-15' and b.tipo='BONO' where nivel>4 and a.fecha='2017-06-30' and b.fecha is null order by nivel) a left join comisiones_adicional b on b.iduser=a.iduser and b.dato=a.tn;
		
		
		
		/// cambio de fecha y resta de nivel
		///PRIMER OLA insert ignore into comisiones_adicional(fecha,login,tipo,comision,iduser,dato,comentario) select a.fecha,a.login,'BONO',if(tn=4,50,if(tn=5,90,if(tn=6,170,if(tn=7,400,0)))),a.iduser,tn,'ANTICIPO' from ( select a.fecha,a.login,a.iduser,a.nivel-1 tn from user_nivel_day a left join comisiones_adicional b on b.iduser=a.iduser and b.fecha in ('2017-12-15',last_day('2017-12-15')) and b.tipo='BONO' where nivel>4 and a.fecha='2017-12-31' and b.fecha is null order by nivel ) a left join comisiones_adicional b on b.iduser=a.iduser and b.dato=a.tn where b.login is null and tn>3;
		
		
		
		///SEGUNDO OLA insert ignore into comisiones_adicional(fecha,login,tipo,comision,iduser,dato,comentario) select a.fecha,a.login,'BONO',if(tn=4,50,if(tn=5,90,if(tn=6,170,if(tn=7,400,0)))),a.iduser,tn,'ANTICIPO' from ( select a.fecha,a.login,a.iduser,a.nivel-2 tn from user_nivel_day a left join comisiones_adicional b on b.iduser=a.iduser and b.fecha in ('2017-09-15',last_day('2017-09-15')) and b.tipo='BONO' where nivel>4 and a.fecha='2017-09-30' and b.fecha is null order by nivel ) a left join comisiones_adicional b on b.iduser=a.iduser and b.dato=a.tn where b.login is null and tn>3; 
		
		///TERCER OLA insert ignore into comisiones_adicional(fecha,login,tipo,comision,iduser,dato,comentario) select a.fecha,a.login,'BONO',if(tn=4,50,if(tn=5,90,if(tn=6,170,if(tn=7,400,0)))),a.iduser,tn,'ANTICIPO' from ( select a.fecha,a.login,a.iduser,a.nivel-3 tn from user_nivel_day a left join comisiones_adicional b on b.iduser=a.iduser and b.fecha in ('2017-09-15',last_day('2017-09-15')) and b.tipo='BONO' where nivel>4 and a.fecha='2017-09-30' and b.fecha is null order by nivel ) a left join comisiones_adicional b on b.iduser=a.iduser and b.dato=a.tn where b.login is null and tn>3; 
		/*
		A pasado
		update user_nivel_day set nivel=0 where fecha between '2017-08-01' and '2017-08-30';
		update user_nivel_day a, user_nivel_day b  set b.nivel=a.nivel where a.iduser=b.iduser and a.fecha='2017-08-31' and b.fecha between '2017-08-01' and '2017-08-30' and a.nivel!=b.nivel and b.monto>0;
		
		A futuro
		update user_nivel_day set nivel=0 where fecha between '2017-09-01' and '2017-09-30';
		update user_nivel_day a, user_nivel_day b  set b.nivel=a.nivel where a.iduser=b.iduser and a.fecha='2017-08-31' and b.fecha between '2017-09-01' and '2017-09-30' and a.nivel!=b.nivel;
		
		HOY 
		update user_nivel set nivel=0 ;
		update user_nivel_day a, user_nivel b  set b.nivel=a.nivel where a.iduser=b.iduser and a.fecha='2017-08-31'  and a.nivel!=b.nivel;
	*/
	
	/*
		update zkasistencia a , (select id_usuario,date(FROM_UNIXTIME(fecha)) dat,(max(fecha)),(min(fecha)) mini,count(*) tt from zkmarcaciones group by id_usuario,date(FROM_UNIXTIME(fecha)) having tt>1) b  set a.hora_ini=b.mini, a.origen='H', a.hora_inic=b.mini where a.id_usuario=b.id_usuario and a.hora_ini!=b.mini and a.hora_dia=b.dat
		
		update zkasistencia a , (select id_usuario,date(FROM_UNIXTIME(fecha)) dat,(max(fecha))minf,(min(fecha)) mini,count(*) tt from zkmarcaciones group by id_usuario,date(FROM_UNIXTIME(fecha)) having tt>1) b  set a.hora_fin=b.minf where a.id_usuario=b.id_usuario and a.hora_fin!=b.minf and a.hora_dia=b.dat
		
		update zkasistencia set hora_fin=null,hora_finc=null where hora_fin<hora_ini
	*/
	
	//////////////////
	
	//////////COMISIONES
	///////COMISION DIARIO
	
	/////funcion comi
	function tree_comi($login,$jj,$fec,$monto,$idop,$log){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in>'2016-12-01') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3) or ('$fec' between hora_prof and hora_prof + interval 5 month and tprof=4),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)),tprof,hora_prof from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.idpatro=a.iduser and  b.idsocio='$login' and b.fecha='$fec'";
		
		$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$tcom=0;
			if($r[1]>0){
				$rte=mysql_fetch_row(mysql_query("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red where nivel=$jj"));
				if($rte[0]>0){
					if($jj==1 and $r[2]==1){
						$tcom=30;
						if($r[3]==4)
						{
							$rte1=mysql_fetch_row(mysql_query("select if(hora_in>='$r[4]',1,0) from user_nivel_day where fecha='$fec' and iduser='$login' "));
							if($rte1[0]>0){
								$tcom=30;
								}else{
								$tcom=0;
							}
						}
					}
					if($jj==1 and $r[2]==2){
						$tcom=40;
					}
					if($tcom>0){
						$rte[0]=round($tcom*$monto/100,3);
						$rte[1]=$tcom;
					}
					//echo "$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
					$tbl="insert ignore into comisiones_historial_puntos3(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],1,$monto)";
					mysql_query($tbl);	
					
					}else{
					$tbl="insert ignore into comisiones_historial_puntos3(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto)";
					qry($tbl);				
				}
				}else{
				$tbl="insert ignore into comisiones_historial_puntos3(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto)";
				qry($tbl);
			}
			if($r[0]!="")
			tree_comi($r[0],$jj,$fec,$monto,$idop,$log);
		}
		return 1;
	}
	///////COMISION DIARIO
	/////funcion comi
	function tree_comi2($login,$jj,$fec,$monto,$idop,$log){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in>'2016-12-01') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3) or ('$fec' between hora_prof and hora_prof + interval 5 month and tprof=4),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)),tprof,hora_prof from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.idpatro=a.iduser and  b.idsocio='$login' and b.fecha='$fec'";
		
		$res = mysql_query($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)){
			$tcom=0;
			if($r[1]>0){
				$rte=mysql_fetch_row(mysql_query("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red_acumu where nivel=$jj"));
				if($rte[0]>0){
					//echo "$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
					$tbl="insert ignore into comisiones_historial_puntos3(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],2,$monto)";
					mysql_query($tbl);	
					
					}else{
					$tbl="insert ignore into comisiones_historial_puntos3(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,2,$monto)";
					qry($tbl);				
				}
				}else{
				$tbl="insert ignore into comisiones_historial_puntos3(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,2,$monto)";
				qry($tbl);
			}
			if($r[0]!="")
			tree_comi2($r[0],$jj,$fec,$monto,$idop,$log);
		}
		return 1;
	}
	
	
	qry("delete from comisiones_historial_puntos3 where fecha>curdate() - interval 2 month and fecha>'2017-05-02'");
	//////genera COMISIONES
	$res=qry("select date(hora_confirm),iduser,puntos,idop from puntos where tipo=9 and estado=1 and hora_confirm>=curdate() - interval 2 month and hora_confirm>'2017-05-03'");
	while($r=mysql_fetch_row($res)){
		//"<br />$r[0]----$r[2]---$r[1]<br />";
		tree_comi($r[1],0,$r[0],$r[2],$r[3],$r[1]);
		$tbl="insert ignore into comisiones_historial_puntos3(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,1,'$r[2]')";
		$res2 = mysql_query($tbl) or die(mysql_error());
	}
	////genera comisiones adicionales
	$res=qry("select date(hora_confirm),iduser,puntos,idop from puntos where tipo=10 and estado=1 and hora_confirm>=curdate() - interval 2 month and hora_confirm>'2017-05-03'");
	while($r=mysql_fetch_row($res)){
		//echo "<br />$r[0]----$r[2]---$r[1]<br />";
		tree_comi2($r[1],0,$r[0],$r[2],$r[3],$r[1]);
		$tbl="insert ignore into comisiones_historial_puntos3(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,2,'$r[2]')";
		$res2 = mysql_query($tbl) or die(mysql_error());
	}
	
	/*
		
	*/
?>