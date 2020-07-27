<?php
	include "sec.php";
	function puntos($Xlogin,$time_start=0,$time_end=0){
		$qrytime="if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now()))";
		if($time_start!=0)
		{
			$qrytime=" a.hora between '$time_start' and '$time_end' ";
		}
		$qry1="select sum(puntos) from puntos a where login='$Xlogin' and $qrytime and estado=1";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		return $rpuna;
	}
	
	function comision($Xlogin,$igv,$mes){
		
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin,"2014-$mes-05","2014-".($mes+1)."-05");
		$puntos=$puntos[0];
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r2[0]>0)
		{
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*((100-$r2[0])/(100*$igv))*($r3[0]/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*((100-$r2[1])/(100*$igv))*($r3[1]/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*((100-$r2[2])/(100*$igv))*($r3[2]/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c where idcliente in ($idc) and a.hora between concat(year(now()),'-$mes-5') and concat(year(now()),'-',($mes+1),'-5')  and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(comision/100)) from operacionesp a, stockmovesp b,promo_puntos c where idcliente in ($idc) and a.hora between concat(year(now()),'-$mes-5') and concat(year(now()),'-',($mes+1),'-5') and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function comision_nivel2($Xlogin,$igv,$mes){
		
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin,"2014-$mes-05","2014-".($mes+1)."-05");
		$puntos=$puntos[0];
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r2[0]>0)
		{
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*((100-$r2[0])/(100*$igv))*(1/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*((100-$r2[1])/(100*$igv))*(1/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*((100-$r2[2])/(100*$igv))*(1/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c where idcliente in ($idc) and a.hora between concat(year(now()),'-$mes-5') and concat(year(now()),'-',($mes+1),'-5') and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(1/100)) from operacionesp a, stockmovesp b,promo_puntos c where idcliente in ($idc) and a.hora between concat(year(now()),'-$mes-5') and concat(year(now()),'-',($mes+1),'-5') and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function global_comision($log,$mes){
		$comi_global=0;
		$comi_temp=0;
		$comi_tot=0;
		$tep="";
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$cla="";
			if($r[3]=="")
			{
				$cla="class='warning'";
			}
			$comi_temp=comision($r[7],1.18,$mes);
			//$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			$comi_tot=hijos($r[7],"'$log'",$mes);
			$tep.="$r[1]:$comi_temp -- Hijos:$comi_tot <br />";
			$comi_global=$comi_global+$comi_tot+$comi_temp;
		}
		return $comi_global."<BR />".$tep;
	}
	function hijos($log,$pa_log,$mes){
		$comi=0;
		$qry = "select a.nombre, a.idpersona,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'$log',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log') and a.login not in ($pa_log) group by a.login  order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$palog.=$pa_log.",'$r[7]'";
			$comi=$comi+comision_nivel2($r[7],1.18,$mes);
			//	$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			//$tbl.= hijos($r[7],$palog);
		}
		return $comi;
	}
	echo "---------Diciembre:";
	echo $qryy=global_comision("08273555",12);
	echo "---------Noviembre:";
	echo $qryy=global_comision("08273555",11);
	echo "---------Octubre:";
	echo $qryy=global_comision("08273555",10);
	echo "---------Setiembre:";
	echo $qryy=global_comision("08273555",9);
	echo "---------Agosto:";
	echo $qryy=global_comision("08273555",8);
	echo "---------Julio:";
	echo $qryy=global_comision("08273555",7);
	
?>