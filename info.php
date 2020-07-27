<?php
	include("sec.php");
	function tree_func($login,$jj,$fec,$cons){
		$jj++;
		$tbl="";
		$qry = "select a.login,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.empresa=a.login and  b.socio='$login' and b.fecha='$fec'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			
			$tbl="insert ignore into cuenta_platino_day(fecha,empresa,socio,nivel) values ('$fec','$r[0]','$cons',$jj)";
			qry($tbl);
			if($r[0]!="")
			tree_func($r[0],$jj,$fec,$cons);
		}
		return 1;
	}
	
	//////REgula platinos
	/*
		qry("truncate table cuenta_platino_day");
		$res=qry("select login,fecha from user_nivel_day where fecha> '2016-07-01'  and nivel>3");
		while($r=mysql_fetch_row($res)){
		//echo "<br />$r[0]<br />";
		$tbl="insert ignore into cuenta_platino_day(fecha,empresa,socio,nivel) values ('$r[1]','$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func($r[0],0,$r[1],$r[0]);
		
	}*/
	
	/////contador de platinos
	//$res=qry("update user_nivel_day a,(select a.fecha,a.login,count(distinct c.empresa) npla from user_nivel_day a, asociadas_day b,cuenta_platino_day c where c.fecha=a.fecha and b.socio=c.empresa and a.fecha=b.fecha and a.login=b.empresa and nivel_1>9 group by a.fecha,a.login) b set a.plati_mas=b.npla where a.login=b.login and a.fecha=b.fecha") ;
	
	/////// contador DIAMANTES
	
	function tree_func_d($login,$jj,$fec,$cons){
		$jj++;
		$tbl="";
		$qry = "select a.login,a.nombre,a.apellidos from usuarios a, asociadas_day b where b.empresa=a.login and  b.socio='$login' and b.fecha='$fec'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			
			$tbl="insert ignore into cuenta_diamante_day(fecha,empresa,socio,nivel) values ('$fec','$r[0]','$cons',$jj)";
			qry($tbl);
			if($r[0]!="")
			tree_func_d($r[0],$jj,$fec,$cons);
		}
		return 1;
	}
	
	qry("truncate table cuenta_diamante_day");
	$res=qry("select login,fecha from user_nivel_day where fecha> '2016-07-01'  and nivel>5");
	while($r=mysql_fetch_row($res)){
		//echo "<br />$r[0]<br />";
		$tbl="insert ignore into cuenta_diamante_day(fecha,empresa,socio,nivel) values ('$r[1]','$r[0]','$r[0]',0)";
		qry($tbl);	
		tree_func_d($r[0],0,$r[1],$r[0]);
		
	}
	/////contador de platinos
	//$res=qry("update user_nivel_day a,(select a.fecha,a.login,count(distinct c.empresa) npla from user_nivel_day a, asociadas_day b,cuenta_diamante_day c where c.fecha=a.fecha and b.socio=c.empresa and a.fecha=b.fecha and a.login=b.empresa and nivel_1>9 group by a.fecha,a.login) b set a.diaman_mas=b.npla where a.login=b.login and a.fecha=b.fecha") ;
	
	
	//CALCULADOR DE COMISIONES
	
	/////funcion comi
	function tree_comi($login,$jj,$fec,$monto,$idop,$log){
		$jj++;
		$tbl="";
		//$qry = "select a.login,if(a.tipo='ESPECIAL' and a.nivel<5,5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or '$fec' between date(hora_in) and hora_in + interval 1 month,1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)) from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.empresa=a.login and  b.socio='$login' and b.fecha='$fec'";
		$qry = "select a.login,if(a.tipo='ESPECIAL' and a.nivel<5 and (select 1 from especiales where fecha=a.fecha and login=a.login),5,a.nivel),if(('$fec' between hora_prof and hora_prof + interval 6 month and tprof=1) or ('$fec' between date(hora_in) and hora_in + interval 1 month and hora_in<'2016-11-07') or ('$fec' between hora_prof and hora_prof + interval 9 month and tprof=3),1,if('$fec' between hora_prof and hora_prof + interval 3 month and tprof=2,2,0)) from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.empresa=a.login and  b.socio='$login' and b.fecha='$fec'";	
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$tcom=0;
			if($r[1]>0){
				$rte=mysql_fetch_row(qry("select round((nivel$r[1]*$monto)/100,3),nivel$r[1] from comision_red where nivel=$jj"));
				if($rte[0]>0){
					if($jj==1 and $r[2]==1){
						$tcom=30;
					}
					if($jj==1 and $r[2]==2){
						$tcom=40;
					}
					if($tcom>0){
						$rte[0]=round($tcom*$monto/100,3);
						$rte[1]=$tcom;
					}
					echo "$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
					$tbl="insert ignore into comisiones_historial(fecha,login,socio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],1,$monto)";
					qry($tbl);	
					
					}else{
					$tbl="insert ignore into comisiones_historial(fecha,login,socio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto)";
					qry($tbl);				
				}
				}else{
				$tbl="insert ignore into comisiones_historial(fecha,login,socio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,0,0,1,$monto)";
				qry($tbl);
			}
			if($r[0]!="")
			tree_comi($r[0],$jj,$fec,$monto,$idop,$log);
		}
		return 1;
	}
	
	function tree_comi_adi($login,$jj,$fec,$monto,$idop,$log){
		$jj++;
		$tbl="";
		$qry = "select a.login,a.nivel from user_nivel_day a, asociadas_day b where a.fecha=b.fecha and b.empresa=a.login and  b.socio='$login' and b.fecha='$fec'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$tcom=0;
			if($r[1]>0){			
				$rte=mysql_fetch_row(qry("select round((5*$monto)/100,3),5 from comision_red where nivel=$jj"));
				if($rte[0]>0){
					echo "$r[1]-$jj($rte[1])-$r[0]--$rte[0]<BR />";
					$tbl="insert ignore into comisiones_historial(fecha,login,socio_from,idop,nivel,porcentaje,comision,tipo,total) values ('$fec','$r[0]','$log',$idop,$jj,$rte[1],$rte[0],2,$monto)";
					qry($tbl);
				}
			}
		}
		return 1;
	}
	
	/*
		qry("truncate table comisiones_historial");
		//////genera COMISIONES
		$res=qry("select date(hora_confirm),login,puntos,idop from puntos where tipo=4 and estado=1 and hora_confirm>='2016-05-01'");
		while($r=mysql_fetch_row($res)){
		echo "<br />$r[0]----$r[2]<br />";
		tree_comi($r[1],0,$r[0],$r[2],$r[3],$r[1]);
		
		}
		
		$res=qry("select date(hora_confirm),login,puntos,idop from puntos where tipo=6 and estado=1 and hora_confirm>='2016-05-01' and hora_confirm<'2016-11-04'");
		while($r=mysql_fetch_row($res)){
		echo "<br />$r[0]----$r[2]<br />";	
		tree_comi_adi($r[1],0,$r[0],$r[2],$r[3],$r[1]);
		
		}
	*/
	
	
	/////CALCULADOR UTILIDADES
	/*
		qry("truncate table comisiones_adicional");
		$arrcomi=array();
		$res=qry("select ye,mon,round(tp*0.15*0.01/sum(if(tt=6,1,0)),2),sum(if(tt=6,1,0)),round(tp*0.15*0.02/sum(if(tt=7,1,0)),2),sum(if(tt=7,1,0)),round(tp*0.15*0.04/sum(if(tt=8,1,0)),2),sum(if(tt=8,1,0)) from (select year(fecha) ye,month(fecha) mon,login,max(nivel) tt from user_nivel_day where nivel>5 group by year(fecha),month(fecha),login) a left join (select year(hora_confirm) yy,month(hora_confirm) mm,sum(puntos) tp from puntos where tipo in (4,6) and estado=1 group by year(hora_confirm),month(hora_confirm)) b on a.ye=b.yy and a.mon=b.mm group by ye,mon");
		while($r=mysql_fetch_row($res)){
		$arrcomi[$r[0]][$r[1]][6]=$r[2];
		$arrcomi[$r[0]][$r[1]][7]=$r[4];
		$arrcomi[$r[0]][$r[1]][8]=$r[6];
		//echo "<br />$r[0]<br />";
		}
		
		$res=qry("select year(fecha) ye,month(fecha) mon,max(nivel),login tt from user_nivel_day where nivel>5 group by year(fecha),month(fecha),login");
		while($r=mysql_fetch_row($res)){
		$com=$arrcomi[$r[0]][$r[1]][$r[2]];
		if($com>0){
		qry("insert into comisiones_adicional(fecha,login,tipo,comision) values ('$r[0]-$r[1]-15','$r[3]','UTILIDADES',$com)");
		}
		}
	*/
?>