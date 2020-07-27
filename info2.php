<?php
	include("sec.php");
	
	//qry("insert into detalle_volumen select b.idempresa,b.idsocio,sum(total) from comisiones_historial_puntos a, asociadas b where a.iduser=b.idsocio group by b.idempresa,b.idsocio");
	//COMISION
	/*
		//////////COMISIONES
		///////COMISION DIARIO
		/////funcion comi
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
		
		qry("delete from comisiones_historial_puntos where fecha>curdate() - interval 2 month and fecha between '2017-06-01' and '2017-06-30'");
		//////genera COMISIONES
		$res=qry("select date(hora_confirm),a.iduser,a.puntos,a.idop,if(date(b.hora_in)=date(fecha_venc),'R','A') from puntos a left join  user_nivel_day b  on date(a.hora_confirm)=b.fecha and a.iduser=b.iduser where a.tipo=9 and a.estado=1 and hora_confirm>=curdate() - interval 2 month and hora_confirm between '2017-06-01' and '2017-06-30 23:59:59'");
		while($r=mysql_fetch_row($res)){
		echo "<br />CCCCC1----$r[0]----$r[2]---C1-$r[1]<br />";
		tree_comi($r[1],0,$r[0],$r[2],$r[3],$r[1],$r[4]);
		echo $tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total,t) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,1,'$r[2]','$r[4]')";
		$res2 = mysql_query($tbl) or die(mysql_error());
		}
		
		////genera comisiones adicionales
		$res=qry("select date(hora_confirm),iduser,puntos,idop from puntos where tipo=10 and estado=1 and hora_confirm>=curdate() - interval 2 month and hora_confirm between '2017-06-01' and '2017-06-30 23:59:59'");
		while($r=mysql_fetch_row($res)){
		echo "<br />CCCCC2--$r[0]----$r[2]---C2-$r[1]<br />";
		tree_comi2($r[1],0,$r[0],$r[2],$r[3],$r[1]);
		echo $tbl="insert ignore into comisiones_historial_puntos(fecha,iduser,idsocio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$r[0]','$r[1]','$r[1]',$r[3],0,0,0,2,'$r[2]')";
		$res2 = mysql_query($tbl) or die(mysql_error());
		}
	*/
	//qry("update comisiones_historial_puntos set comision=0");
	//qry("update comisiones_historial_puntos set comision=0");
	
	
	
	/*
		$res1=qry(" select iduser,volumen from user_nivel_day where monto>20 and fecha='2017-05-31' and nivel_1>=0");
		while($rr=mysql_fetch_row($res1)){
		$Xiduser=$rr[0];
		$qry = "select b.nombre,b.apellidos,a.total,b.iduser from detalle_volumen_temp a, usuarios b, user_nivel_day c where c.fecha='2017-05-31' and c.iduser=b.iduser and b.iduser=a.idsocio and a.iduser ='$Xiduser' and c.estado=1 order by total desc";
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
		qry("update user_nivel_day set volumen='$stot' where fecha='2017-05-31' and iduser='$Xiduser'");
		}
		
	*/
	/*
		//////////ORO
		
		function tree_func($login,$jj,$cons){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.fecha='2017-06-30' and b.idempresa=a.iduser and  b.idsocio='$login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		
		$tbl="insert ignore into cuenta_oro(idempresa,idsocio,nivel) values ('$r[0]','$cons',$jj)";
		qry($tbl);
		if($r[0]!="")
		tree_func($r[0],$jj,$cons);
		//$tadic=comision_adic($r[1],$r[2],$r[3]);
		//$tot=$tauto+$tadic;
		//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		return 1;
		
		}
		
		//////REgula ORO
		qry("truncate table cuenta_oro");
		$res=qry("select iduser from user_nivel_day where  nivel>2 and fecha='2017-06-30'");
		while($r=mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_oro(idempresa,idsocio,nivel) values ('$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func($r[0],0,$r[0]);
		
		}
		
		$res=qry("update user_nivel_day a left join (select a.iduser,count(distinct c.idempresa) npla from user_nivel a, asociadas b,cuenta_oro c where  b.idsocio=c.idempresa and a.login=b.empresa and nivel_1>3 and a.estado=1 group by a.login) b on a.iduser=b.iduser set a.oro_mas=coalesce(b.npla,0) where a.fecha='2017-06-30'");
	*/
	
	
	/*
		
		///////PLATINO
		
		function tree_func1($login,$jj,$cons){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.fecha='2017-06-30' and b.idempresa=a.iduser and  b.idsocio='$login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		
		$tbl="insert ignore into cuenta_platino(idempresa,idsocio,nivel) values ('$r[0]','$cons',$jj)";
		qry($tbl);
		if($r[0]!="")
		tree_func1($r[0],$jj,$cons);
		//$tadic=comision_adic($r[1],$r[2],$r[3]);
		//$tot=$tauto+$tadic;
		//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		return 1;
		
		}
		
		//////REgula platinos
		qry("truncate table cuenta_platino");
		$res=qry("select iduser from user_nivel_day where  nivel>3 and fecha='2017-06-30'");
		while($r=mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_platino(idempresa,idsocio,nivel) values ('$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func1($r[0],0,$r[0]);
		
		}
		
		$res=qry("update user_nivel_day a left join (select a.iduser,count(distinct c.idempresa) npla from user_nivel a, asociadas b,cuenta_platino c where  b.idsocio=c.idempresa and a.login=b.empresa and nivel_1>3 and a.estado=1 group by a.login) b on a.iduser=b.iduser set a.plati_mas=coalesce(b.npla,0) where a.fecha='2017-06-30'");
		
	*/
	/*
		//////////NIVEL_1
		
		function tree_func($login,$jj,$cons){
		$jj++;
		$tbl="";
		$qry = "select a.iduser,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.fecha='2017-06-30' and b.idempresa=a.iduser and  b.idsocio='$login'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
		
		$tbl="insert ignore into cuenta_nivel_1(idempresa,idsocio,nivel) values ('$r[0]','$cons',$jj)";
		qry($tbl);
		if($r[0]!="")
		tree_func($r[0],$jj,$cons);
		//$tadic=comision_adic($r[1],$r[2],$r[3]);
		//$tot=$tauto+$tadic;
		//$tbl.="<tr><td></td><td>$r[0]</td><td>$r[1]</td><td>$tauto</td><td>$tadic</td><td>$tot</td><td><input type='button' class='det btn btn-sm' data-login='$r[1]' value='Detalle'></td></tr>";
		}
		return 1;
		
		}
		
		//////REgula NIVEL_1
		qry("truncate table cuenta_nivel_1");
		$res=qry("select iduser from user_nivel_day where  monto>0 and fecha='2017-06-30'");
		while($r=mysql_fetch_row($res)){
		$tbl="insert ignore into cuenta_nivel_1(idempresa,idsocio,nivel) values ('$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func($r[0],0,$r[0]);
		
		}
		
		$res=qry("update user_nivel_day a left join (select a.iduser,count(distinct c.idempresa) npla from user_nivel_day a, asociadas_day b,cuenta_nivel_1 c,asociadas_day b2 where a.fecha='2017-06-30' and b.fecha='2017-06-30' and b.idsocio=c.idempresa and a.iduser=b.idempresa and c.idsocio=b2.idsocio and a.iduser=b2.idpatro AND b2.fecha='2017-06-30' group by a.iduser) b on a.iduser=b.iduser set a.nivel_1=coalesce(b.npla,0) where a.fecha='2017-06-30'");
		
	*/
	
	
	
	
	$tbl3="<table>";
	$qry = "select b.idempresa,a.nombre,a.apellidos,sum(if(b.monto>100,1,0)),tttt,count(*) tt from (select a.idempresa,c.nombre,c.apellidos,coalesce(sum(b1.estado),0) tett,datediff(curdate(),b.hora_in),b.monto,sum(if(b1.monto>100,1,0)) tttt from (asociadas a,user_nivel b,usuarios c) left join asociadas a1 on a1.idpatro=a.idsocio left join user_nivel b1 on a1.idsocio=b1.iduser and b1.hora_in between b.hora_in and b.hora_in + interval 10 day where a.idsocio=b.iduser and a.idsocio=c.iduser and b.hora_in between '2017-05-01' and '2017-06-01' group by  b.iduser having tett>0) b,usuarios  a where a.iduser=b.idempresa group by b.idempresa having tt>0";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		//if($r[3]>0){
		$ttr="";
		if($r[3]>1){
			$ttr="class='success'";
		}
		
		$tbl3.="<tr $ttr><td>$r[0]</td><td>$r[1] $r[2]</td><td>$r[3]</td><td>$r[4]</td><td>$r[5]</td></tr>";
		//}
	} 
	$tbl3.="</table>";
	echo $tbl3;
	echo "<br /><br />";
	
	$tbl3="<table>";
	$qry = "select a.idempresa,c.nombre,c.apellidos,coalesce(sum(b1.estado),0) tett,datediff(curdate(),b.hora_in) from (asociadas a,user_nivel b,usuarios c) left join asociadas a1 on a1.idpatro=a.idsocio left join user_nivel b1 on a1.idsocio=b1.iduser and b1.hora_in between b.hora_in and b.hora_in + interval 10 day where a.idsocio=b.iduser and a.idsocio=c.iduser and b.hora_in between '2017-05-01' and '2017-06-01' and a.idpatro=3522 group by  b.iduser ";
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		//if($r[3]>0){
		$ttr="";
		if($r[3]>1){
			$ttr="class='success'";
		}
		
		$tbl3.="<tr $ttr><td>$r[0]</td><td>$r[1] $r[2]</td><td>$r[3]</td><td>$r[4]</td></tr>";
		//}
	} 
	$tbl3.="</table>";
	
?>