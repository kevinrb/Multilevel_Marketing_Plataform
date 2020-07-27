<?php
	require("../../sec.php");
	require_once("../../ws/func.php");
	$a=$_POST["a"];
	
	if($a=="newcl")
	{
		$data=$_POST["data"];
		$canal=$_POST["canal"];
		$sql=qry("INSERT INTO clientes(idcliente,nombre,nombre2,direccion,ubigeo,email,fono1,fono2,lastupdate,local,canal) values('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]',NOW(),'$Xlocal','$canal')");
		echo "1";
	}
	
	if($a=="ap")
	{
		$i=$_POST["i"];
		$c=$_POST["c"];
		$p=$_POST["p"];
		$f=$_POST["fr"];
		$cfr=$_POST["cfr"];
		$can=$_POST["can"];
		$de=$_POST["de"];
		if($p>0){
			$promo=tienepromo($p,$c,$Xpais);
			$lp=$_POST["lp"];
			$loc=$_POST["loc"];
			
			if($f=="1"){$fr=$cfr;}
			else{$fr="0";}
			
			if($promo=="0")
			{
				if($de[0]=="1")
				{
					qry("insert ignore into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,fraccion,canal,lista,localdst) values('$i','$p',now(),'$c','".$de[1]."','$Xlogin','$Xlocal','$fr','$can','$lp','$loc');");
				}
				else
				{
					
					$pre=precioprod($p,$Xpais,$lp);
					$promo1=0;
					
					qry("insert ignore into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,fraccion,canal,lista,localdst,promo,codigo1) values('$i','$p',now(),'$c','$pre[0]','$Xlogin','$Xlocal','$fr','$can','$lp','$loc',$promo1,$pre[1]);");				
					//qry("insert ignore into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,fraccion,canal,lista,localdst) values('$i','$p',now(),'$c','".precioprod($p,$lp)."','$Xlogin','$Xlocal','$fr','$can','$lp','$loc');");
				}
			}
			else
			{
				for($j=0;$j<count($promo);$j++){ 
					qry("insert ignore into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,promo,fraccion,canal,lista,localdst,codigo1) values('$i','".$promo[$j][0]."',now(),'".$promo[$j][2]."','".$promo[$j][1]."','$Xlogin','$Xlocal','$p','0','$can','$lp','$loc','{$promo[$j][3]}');");
				}
			}
		}
		echo listabolsa($i,$Xlocal,$Xlogin,$lp);
	}
	
	if($a=="plus_prod")
	{
		$p=$_POST["p"];
		$i=$_POST["i"];
		$lp=$_POST["lp"];
		$m=$_POST["m"];
		$promo=tienepromo($m,1,$Xpais);	
		if($m=="0")
		{
			qry("update bolscanal set cantidad=cantidad+1 where idprod='$p' and idbolsa='$i' and login='$Xlogin' and local='$Xlocal';");
		}
		else
		{
			for($j=0;$j<count($promo);$j++){
				qry("update bolscanal set cantidad=cantidad+".$promo[$j][2]." where idprod='".$promo[$j][0]."' and idbolsa='$i' and login='$Xlogin' and local='$Xlocal' and promo='$m'");
			}	
		}
		
		///////3x2//
		$can=14;
		if($can==0 or $can==""){
			$q_can="";
			}else{
			$q_can=" and canal='$can'";
		}
		$qryy="select floor((sum(cantidad)/3)) from bolscanal where idbolsa='$i' and local='$Xlocal' and login='$Xlogin' and promo in (0,7879) and idprod in (296,305,303,301,304,302) ";
		$condi=mysql_fetch_row(qry($qryy));
		if($condi[0]>0){
			$desc=0;
			$res=qry("select cantidad,precio,idprod from bolscanal where idbolsa=$i and local='$Xlocal' and login='$Xlogin' and promo in (0,7879) and idprod in (296,305,303,301,304,302) order by precio");
			while($r=mysql_fetch_row($res)){
				if($r[0]>=$condi[0]){
					$desc+=$r[1]*$condi[0];
					$condi[0]=0;
					}else{
					$desc+=$r[1]*$r[0];
					$condi[0]=$condi[0]-$r[0];
				}
			}
		}
		qry("delete from bolscanal where promo='7879' and idprod='7879' and login='$Xlogin'");		
		if($desc>0){
			//qry("insert into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,promo,fraccion,canal,lista,localdst) values('$i','7879',now(),'1','-$desc','$Xlogin','$Xlocal','7879','0','$can','0','');");
			//$res=qry("update bolscanal set promo=7879 where idbolsa=$i and local='$Xlocal' and login='$Xlogin' and promo in (0) and idprod in (296,305,303,301,304,302)");
		}	
		
		echo listabolsa($i,$Xlocal,$Xlogin,$lp);
	}
	
	if($a=="less_prod")
	{
		$p=$_POST["p"];
		$i=$_POST["i"];
		$lp=$_POST["lp"];
		$m=$_POST["m"];
		$promo=tienepromo($m,1,$Xpais);
		if($m=="0")
		{
			qry("update bolscanal set cantidad=cantidad-1 where idprod='$p' and idbolsa='$i' and login='$Xlogin' and local='$Xlocal';");
		}
		else
		{
			for($j=0;$j<count($promo);$j++){
				qry("update bolscanal set cantidad=cantidad-".$promo[$j][2]." where idprod='".$promo[$j][0]."' and idbolsa='$i' and login='$Xlogin' and local='$Xlocal' and promo='$m'");
			}	
		}
		///////3x2//
		$can=14;
		if($can==0 or $can==""){
			$q_can="";
			}else{
			$q_can=" and canal='$can'";
		}		
		$qryy="select floor((sum(cantidad)/3)) from bolscanal where idbolsa='$i' and local='$Xlocal' and login='$Xlogin' and promo in (0,7879) and idprod in (296,305,303,301,304,302) ";
		$condi=mysql_fetch_row(qry($qryy));
		if($condi[0]>0){
			$desc=0;
			$res=qry("select cantidad,precio,idprod from bolscanal where idbolsa=$i and local='$Xlocal' and login='$Xlogin' and promo in (0,7879) and idprod in (296,305,303,301,304,302) order by precio");
			while($r=mysql_fetch_row($res)){
				if($r[0]>=$condi[0]){
					$desc+=$r[1]*$condi[0];
					$condi[0]=0;
					}else{
					$desc+=$r[1]*$r[0];
					$condi[0]=$condi[0]-$r[0];
				}
			}
		}
		qry("delete from bolscanal where promo='7879' and idprod='7879' and login='$Xlogin'");		
		if($desc>0){
		//	qry("insert into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,promo,fraccion,canal,lista,localdst) values('$i','7879',now(),'1','-$desc','$Xlogin','$Xlocal','7879','0','$can','0','');");
		//	$res=qry("update bolscanal set promo=7879 where idbolsa=$i and local='$Xlocal' and login='$Xlogin' and promo in (0) and idprod in (296,305,303,301,304,302)");
		}
		echo listabolsa($i,$Xlocal,$Xlogin,$lp);
	}
	
	if($a=="qp")
	{
		$p=$_POST["p"];
		$i=$_POST["i"];
		$lp=$_POST["lp"];
		$m=$_POST["m"];
		if($m=="0")
		{
			qry("delete from bolscanal where idprod='$p' and idbolsa='$i' and login='$Xlogin' and local='$Xlocal' and promo=0");
			}else{
		qry("delete from bolscanal where promo='$m' and idbolsa='$i' and login='$Xlogin' and local='$Xlocal';");}
		///////3x2//
		$can=14;
		if($can==0 or $can==""){
			$q_can="";
			}else{
			$q_can=" and canal='$can'";
		}		
		$qryy="select floor((sum(cantidad)/3)) from bolscanal where idbolsa='$i' and local='$Xlocal' and login='$Xlogin' and promo in (0,7879) and idprod in (296,305,303,301,304,302) ";
		$condi=mysql_fetch_row(qry($qryy));
		if($condi[0]>0){
			$desc=0;
			$res=qry("select cantidad,precio,idprod from bolscanal where idbolsa=$i and local='$Xlocal' and login='$Xlogin' and promo in (0,7879) and idprod in (296,305,303,301,304,302) order by precio");
			while($r=mysql_fetch_row($res)){
				if($r[0]>=$condi[0]){
					$desc+=$r[1]*$condi[0];
					$condi[0]=0;
					}else{
					$desc+=$r[1]*$r[0];
					$condi[0]=$condi[0]-$r[0];
				}
			}
		}
		qry("delete from bolscanal where promo='7879' and idprod='7879' and login='$Xlogin'");		
		if($desc>0){
			//qry("insert into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,promo,fraccion,canal,lista,localdst) values('$i','7879',now(),'1','-$desc','$Xlogin','$Xlocal','7879','0','$can','0','');");
			//$res=qry("update bolscanal set promo=7879 where idbolsa=$i and local='$Xlocal' and login='$Xlogin' and promo in (0) and idprod in (296,305,303,301,304,302)");
		}	
		echo listabolsa($i,$Xlocal,$Xlogin,$lp);
	}
	
	if($a=="sb")
	{
		$idb=rand(1000,9999);
		$res="1";
		$tmp=mysql_num_rows(qry("select idbolsa from bolscanal where login='$Xlogin' and local='$Xlocal';"));
		
		if($tmp==6)
		{$res="0";}
		qry("update bolscanal set idbolsa='$idb' where login='$Xlogin' and local='$Xlocal' and idbolsa=0;");
		
		$rst=qry("select distinct  idbolsa from bolscanal where login='$Xlogin' and local='$Xlocal' and estado=1 and idbolsa!=0;");
		$html="";
		if(mysql_num_rows($rst)>0)
		{ while($temp=mysql_fetch_row($rst)){ $html .= "<button inf='$temp[0]' class='btn btn-success  ped'>Pedido $temp[0]</button>"; } }
		
		echo json_encode(array($res,$idb,$html));
	}
	
	if($a=="db")
	{
		$i=$_POST["i"];
		echo listabolsa($i,$Xlocal,$Xlogin);
	}
	
	if($a=="bb")
	{
		$i=$_POST["i"];
		qry("delete from bolscanal where login='$Xlogin' and local='$Xlocal' and idbolsa='$i';");
		$rst=qry("select idbolsa from bolscanal where login='$Xlogin' and local='$Xlocal' and idbolsa>0 group by idbolsa");
		$html="";
		if(mysql_num_rows($rst)>0){
			while($temp=mysql_fetch_row($rst)){ $html .= "<button inf='$temp[0]' class='btn btn-success  ped'>Pedido $temp[0]</button>"; } 
		}
		echo $html;
	}
	
	
	////////////
	
	if($a=="descuento_transporte")
	{
		$ttime=microtime(true);
		$i=$_POST["i"];
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		////////Calculo de puntos////
		$puns=puntos_tot($Xlogin,$Xlocal,$i);	
		$rpun[0]=$puns["rpun"];	
		$rpuna[0]=$puns["rpuna"];
		$puntos=$rpun[0]+$rpuna[0];
		////////
		$peso=$puns["peso"];
		$arr['peso']=$peso;
		
		if($rpun[0]>=150)
		{
			$re=qry("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");
			
			$r2=mysql_fetch_row($re);
			if($r2[0]>0)
			{
				$cal_pre=calculo_precio($r2[0],$r2[1],$r2[2],$Xlogin,$Xlocal,$i,$idc,$r2[3]);
				$descact_past=$cal_pre["descact_past"];
				$nroped=$cal_pre["nroped"];
				$descact=$cal_pre["descact"];
				$descant=$cal_pre["descant"];		
				/////Lista de Productos
				$tab=tabla_prod($Xlogin,$Xlocal,$i,$r2[0],$r2[1],$r2[2],$r2[3]);
				$tped=$tab["tped"];
				$tbody=$tab["tbody"];
				$tped=$tped+5+12;						
				
				if(($descact_past+$descant)>($tped-$descact))
				$descantneto=round($tped-$descact,3);
				else
				$descantneto=round($descact_past+$descant,3);
				
				/////No considera la perdida anterior
				if($descantneto<0){
					$descantneto=0;
				}
				////			
				
				if(($nroped+0)==0)
				{
					$adic="<tr><td></td><td>SOBRE DE CAMPAÑA</td><td>0</td><td>5</td><td>0</td><td>0</td><td>0</td></tr>";
				}
				ELSE{
					$adic="<tr><td></td><td>COSTO DE EMBALAJE</td><td>0</td><td>5</td><td>0</td><td>0</td><td>0</td></tr>";					
				}
				$adic.="<tr><td></td><td>TRANSPORTE</td><td>0</td><td>12</td><td>0</td><td>0</td><td>0</td></tr>";
				
				
				$linea="<h3>Numero de Pedido:".($nroped+1)."</h3><table class='table table-bordered'><thead><th class='tabla1'>Cod. Prod.</th><th class='tabla1'>Nombre</th><th class='tabla1'>Puntos</th><th class='tabla1'>Monto</th><th class='tabla1'>%</th><th class='tabla1'>Descuento</th><th class='tabla1'>Total</th></thead>$tbody $adic
				<tr class='tabla2'><th></th><th>Actual</th><td class='tabla2'>$rpun[0]</td><td class='tabla2'>".($tped)."</td><td class='tabla2'>0</td><td class='tabla2'>$descact</td><td class='tabla2'>".($tped-$descact)."</td></tr>
				<tr class='tabla2'><th></th><th>Acumulado</th><td class='tabla2'>$rpuna[0]</td><td class='tabla2'>0</td><td class='tabla2'>0</td><td class='tabla2'>$descantneto</td><td class='tabla2'>".($descantneto)."</td></tr>
				<tr class='tabla2'><th colspan='2'>TOTAL</th><td class='tabla2'>".($rpuna[0]+$rpun[0])."</td><td class='tabla2'>".($tped)."</td><td class='tabla2'>0</td><td class='tabla2'>".( $descantneto+$descact)."</td><th class='tabla2'>".(($tped)-( $descantneto+$descact))."</th></tr></table>";
				//echo $linea;
				$ttro= microtime(true)-$ttime;
				$ttt=$ttro;
				
				$arr['text']=$linea;
				$arr['total']=($tped)-( $descantneto+$descact);
				$arr['est']=1;
			}
			else{
				$arr['text']="Se Necesita un pedido mayor a 240 Puntos";
				$arr['est']=0;
			}
		}
		else{
			$arr['text']="Se Necesita un pedido minimo  de 150 Puntos";
			$arr['est']=0;
		}
		echo json_encode($arr);
	}
	
	if($a=="sp")
	{
		$s=$_POST["s"];
		$c=$_POST["c"];
		$l=5;
		$r=qry("select idcliente,nombre,nombre2,direccion,ubigeo,email,if(fono1!=0,fono1,''),if(fono2!=0,fono2,'') from clientes where ((nombre like '%".$s."%') or (nombre2 like '%".$s."%') or (idcliente='$s')) and canal='$c' limit $l;");
		$a=array();	
		if(mysql_num_rows($r)>0){
		while($temp=mysql_fetch_row($r)){$a[]=$temp;}}
		echo json_encode($a);
	}
	
	if($a=="sv")
	{
		$idbol=$_POST["idbol"];
		$ruc=$_POST["ruc"];
		$infex=$_POST["infex"];
		$nota =$_POST["nota"];
		$fpago =$_POST["fpago"];
		$nopc =$_POST["nopc"];
		$trans =$_POST["trans"];
		$capital =$_POST["capital"];
		if($idcli!="0"){$nopc=namecli($idcli);}
		
		$res=listaprod($idbol,$Xlocal,$Xlogin);
		$prod=$res[0];
		$tota=$res[1];
		$ldes=$res[2];
		$lpre=$res[3];
		$cana=$res[4];
		
		
		//////////////////////////////////////////////////////////////	
		/*if(is_array($capital) && count($capital)>0){ 
			$qry="select sum(p.unidades),sum(precio) from capital c left join productos p on c.idprod=p.idprod where (";
			$fil="";
			for($i=0;$i<count($capital);$i++){$idpr=explode("-",$capital[$i]); $idpr=$idpr[1]; $fil.=" c.idprod='".$idpr."' or  "; }
			$fil=trim($fil," or ");
			$qry.=$fil.");";
			$qry=mysql_fetch_row(mysql_query($qry));
			$pex=$qry[0]/1000;
			$ccc=$qry[1];
			}
		else{$cextr=0;}*/
		//////////////////////////////////////////////////////////////	
		//qry("update clientes set lista='$lpre' where idcliente='$idcli';");
		$pag = "";
		$qry = "insert into operacionesp(hora,total,idcliente,login,vendedor1,notas,local,tipo,nameopc,estado,localdst,canal,tipopago1,ruc,moneda) values(now(),'$tota','$Xidcliente','$Xlogin','$Xlogin','$nota','','PE','$nopc','105','','$cana','$fpago','$ruc','$Xmoneda')";
		qry($qry);
		$idopv=mysql_insert_id();
		if($cana=="CATALOGO")
		{
			$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
			$ids=array();
			while($idc1=mysql_fetch_row($idcq))
			{
				$ids[]=$idc1[0];
			}
			$idc=implode(",",$ids);
			////////Calculo de puntos////
			$puns=puntos_regural_general($Xlogin,$Xlocal,$idbol,$Xlogin);	
			$rpun[0]=$puns["rpun"];	
			$rpuna[0]=$puns["rpuna"];
			$puntos=$rpun[0]+$rpuna[0];
			////////
			$peso=$puns["peso"];
			$pex+=($peso/1000);	
			if($pex!="")
			{
				if($dep=="LIMA" && $pro=="LIMA")
				{$cextr=17;}
				else
				{
					if($pex>=1)
					{
						$cextr=12;
						$difp=$pex-1;
						$difp=ceil($difp);
						$cextr+=$difp*(2.5);
					}
					else
					{$cextr=12;}
				}
			}
			$arr['peso']=$pex;	
			if($rpun[0]>=200)
			{
				$r2=array(0,0,0,0);
				$cal_pre=calculo_precio($r2[0],$r2[1],$r2[2],$Xlogin,$Xlocal,$idbol,$idc,$r2[3]);
				$descact_past=$cal_pre["descact_past"];
				$nroped=$cal_pre["nroped"];
				$descact=$cal_pre["descact"];
				$descant=$cal_pre["descant"];
				
				if(($descact_past+$descant)>($tota-$descact))
				$descantneto=round($tota-$descact,3);
				else
				$descantneto=round($descact_past+$descant,3);	
				
			}
			
			$cantP=count($prod);
			for($i=0;$i<$cantP;$i++)
			{ qry("insert into stockmovesp(idprod,hora,cantidad,precio,login,idop,local,promo,fraccion,codigo1) values('".$prod[$i][0]."',now(),'".$prod[$i][1]."','".$prod[$i][2]."','$Xlogin','$idopv','$Xlocal','".$prod[$i][3]."','".$prod[$i][4]."','{$prod[$i][5]}');"); }
			qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo) values ('$idopv','$Xlogin','$rpun[0]',now(),3,2) ");
			save_detallado($Xlogin,$Xlocal,$idbol,$r2[0],$r2[1],$r2[2],$r2[3],$idopv);
			if($descact>0)
			{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop) values ('254','1',now(),'$Xlogin','$Xlocal','-$descact','$idopv') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(254,1,-$descact,0);
				//echo $qry;
			}
			//////Descuento Neto Anterior
			if($descantneto>0)
			{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop) values ('1598','1',now(),'$Xlogin','$Xlocal','-$descantneto','$idopv') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(1598,1,-$descantneto,0);
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',1598,1,-$descantneto,-$descantneto,0,0,0,0,-$descantneto)");
				//echo $qry;
			}
			/*if(($nroped+0)==0)
				{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('458','1',now(),'$Xlogin','$Xlocal','5','$idopv','BOLSA') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(458,1,5,0);
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',458,1,5,5,0,0,0,0,5)");
				}
			ELSE{*/
			$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('1910','1',now(),'$Xlogin','$Xlocal','5','$idopv','EMBALAJE') ";
			$res = qry($qry) or die("4--".mysql_error());
			$prod[]=array(1910,1,5,0);
			qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',1910,1,5,5,0,0,0,0,5)");
			//}
			if($trans==1){
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('264','1',now(),'$Xlogin','$Xlocal','".$cextr."','$idopv','MOVILIDAD') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(264,1,$cextr,0);	
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',264,1,$cextr,$cextr,0,0,0,0,$cextr)");
				
				$dir=$_POST["dir"];
	            $ref=$_POST["ref"];
            	$dep=$_POST["dep"];
              	$pro=$_POST["pro"];
	            $dis=$_POST["dis"];
            	$ubi=ubigeo($dep,$pro,$dis);
             	if($ubi!=false){}
            	else
             	{exit;}
				$qrydest="insert into destinos(idcliente,direccion,referencia,ubigeo) values('".$Xidcliente."','".$dir."','".$ref."','".$ubi."');";
				mysql_query("insert into destinos(idcliente,direccion,referencia,ubigeo) values('".$Xidcliente."','".$dir."','".$ref."','".$ubi."');");
                $idenv=mysql_insert_id();
			    mysql_query("insert into ped_destino(idop,iddestino) values('".$idopv."','".$idenv."');");
			}
			/*	
				$cantC=count($capital);
				for($i=0;$i<$cantC;$i++)
				{
				$tran=explode("-",$capital[$i]);
				$res=qry("select idprod,precio,nroped from capital where nroped='$tran[0]' and idprod='$tran[1]'");
				$r=mysql_fetch_row($res);
				qry("insert into stockmovesp(idprod,hora,cantidad,precio,login,idop,local,nota2) values('".$r[0]."',now(),1,'".$r[1]."','$Xlogin','$idopv','$Xlocal','CAPITAL-$r[2]')");
				$prod[]=array($r[0],1,$r[1],2118);
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',$r[0],1,$r[1],$r[1],0,0,0,2118,$r[1])");
			}*/
		}
		
		$prod=json_encode($prod);	
		
		$idped=posturl(array("a"=>"pCatalogo","tipo"=>"2","canal"=>$cana,"prod"=>$prod,"idc"=>$Xidcliente,"local"=>$Xlocal,"log"=>$Xlogin,"not"=>$nota,"qrydest"=>$qrydest,"fpago"=>$fpago,"ruc"=>$ruc,"moneda"=>$Xmoneda,"moneda"=>$Xmoneda),$XXurl_erp1);
		
		qry("update operacionesp set idpadre2='$idped', total=(select sum(precio*cantidad) from stockmovesp where idop='$idopv') where idop='$idopv'");
		qry("delete from bolscanal where idbolsa='$idbol' and login='$Xlogin'");
		if($idped>0){
			qry("insert ignore into opsv(idop,n) values ($idped,0)");
		}	
		echo "1";
	}
	
	if($a=="rgser")
	{
		/* idpro,nupro,sepro,promo,precio */
		$idop=$_POST["idop"];
		
		$res=mysql_fetch_row(qry("select  operacionesp.idcliente,operacionesp.tipodoc,operacionesp.tipopago1,operacionesp.monto1,operacionesp.tipopago2,operacionesp.monto2,operacionesp.tipopago3,operacionesp.monto3,operacionesp.login,operacionesp.vendedor1,operacionesp.vendedor2,operacionesp.local,puntoemision.lineas,operacionesp.total,operacionesp.nrodoc,operacionesp.nameopc from operacionesp,puntoemision where operacionesp.tipodoc=puntoemision.tipodoc and operacionesp.local=puntoemision.local and operacionesp.idop='$idop';"));
		
		
		$lst=qry("select stockmovesp.idprod,stockmovesp.cantidad,stockmovesp.precio,stockmovesp.promo,((stockmovesp.cantidad+(stockmovesp.fraccion/productos.unidades))*stockmovesp.precio) cant,stockmovesp.fraccion from stockmovesp,operacionesp,productos where stockmovesp.idprod=productos.idprod and operacionesp.idop=stockmovesp.idop and operacionesp.idop='$idop' order by stockmovesp.promo;");
		
		$lstprod=array(); $lstnfac=array();
		while($temp=mysql_fetch_row($lst))
		{ $lstprod[]=$temp; }
		
		$gng="0";
		
		if($res[1]!="TI")
		{$lineas=$res[12]; $nrodoc="0";}
		else
		{$lineas=count($lstprod); $gng="1"; $nrodoc=$res[14];}
		
		
		$listfac=array(); $f=0;
		for($i=0;$i<count($lstprod);$i++)
		{
			if($i%$lineas==0 && $i!=0){$f++;}
			$listfac[$f][0]=$listfac[$f][0]+$lstprod[$i][4];
			$listfac[$f][1][]=$lstprod[$i];
		}
		
		for($i=0;$i<count($listfac);$i++)
		{
			$porc=$listfac[$i][0]/$res[13];
			qry("insert into operacionesv(hora,total,idcliente,tipodoc,tipopago1,monto1,tipopago2,monto2,tipopago3,monto3,login,vendedor1,vendedor2,local,idpadre,nrodoc,nameopc) values(now(),'".$listfac[$i][0]."','".$res[0]."','".$res[1]."','".$res[2]."','".round($porc*$res[3],2)."','".$res[4]."','".round($porc*$res[5],2)."','".$res[6]."','".round($porc*$res[7],2)."','".$res[8]."','".$res[9]."','".$res[10]."','".$res[11]."','$idop','$nrodoc','".$res[15]."');");
			$idv=mysql_insert_id(); $lstnfac[]=$idv;
			for($j=0;$j<count($listfac[$i][1]);$j++)
			{qry("insert into stockmovesv(idprod,hora,cantidad,precio,login,idop,local,promo,fraccion) values('".$listfac[$i][1][$j][0]."',now(),'".$listfac[$i][1][$j][1]."','".$listfac[$i][1][$j][2]."','".$res[8]."','$idv','".$res[11]."','".$listfac[$i][1][$j][3]."','".$listfac[$i][1][$j][5]."');");}
		}
		
		
		$data=$_POST["data"];
		qry("insert into operaciones2(total,idcliente,tipodoc,tipopago1,monto1,tipopago2,monto2,tipopago3,monto3,login,vendedor1,vendedor2,notas,local,hora,idpadre,origen) select total,idcliente,tipodoc,tipopago1,monto1,tipopago2,monto2,tipopago3,monto3,login,vendedor1,vendedor2,notas,local,now(),'$idop','VE' from operacionesp where idop='$idop';");
		$idop2=mysql_insert_id();
		$prod=precioprodv($data);
		for($i=0;$i<count($prod);$i++)
		{ qry(" insert into stockmoves2(hora,idprod,serie,cantidad,login,idop,local,precio,tipo,promo,fraccion) values(now(),'".$prod[$i][0]."','".$prod[$i][2]."','".$prod[$i][1]."','$Xlogin','$idop2','$Xlocal','".$prod[$i][4]."','-1','".$prod[$i][3]."','".$prod[$i][5]."'); "); }
		
		echo json_encode(array($idop,$gng));
	}
	
	if($a=="vs")
	{
		$data =$_POST["data"];
		$idp  =$_POST["idp"];
		
		if(servicio($idp))
		{echo "1";}
		else{
			$res=qry("select * from (select sum(cantidad*tipo) cantidad from stockmoves2 where idprod='$idp' and serie='$data' and local='$Xlocal') as A where A.cantidad>0;");
			if(mysql_num_rows($res)>0)
			{echo "1";}
			else
			{echo "0";}
		}
	}
	
	if($a=="and")
	{
		$idop=$_POST["idop"];
		$data=$_POST["data"];
		for($i=0;$i<count($data);$i++)
		{ qry(" update operacionesv set  nrodoc='".$data[$i][1]."' where idop='".$data[$i][0]."' and idpadre='".$idop."';"); }
		echo "1";
	}
	
	if($a=="updcl")
	{
		$idc=$_POST["idc"];
		$nom=$_POST["nom"];
		$ape=$_POST["ape"];
		$dir=$_POST["dir"];
		$dis=$_POST["dis"];
		$cor=$_POST["cor"];
		$cel=$_POST["cel"];
		$tel=$_POST["tel"];
		$canal=$_POST["canal"];
		$r=qry("update clientes set nombre='$nom',nombre2='$ape',direccion='$dir',ubigeo='$dis',email='$cor',fono1='$cel',fono2='$tel' where idcliente='$idc' and canal='$canal';");
		if($r)
		{echo "1";}
		else
		{echo "0";} 
	}
	
	if($a=="filprod")
	{
		$data=$_POST["data"];
		$lp  =$_POST["lp"];
		//$res=qry("select a.idprod,a.precio$lp,TRIM(UPPER(CONCAT(a.nombre,' ',a.nombre1,' ',a.nombre2,' ',a.nombre3,' ',a.nombre4))) nombre,a.pack,if(isnull(a.barras)=1,'',a.barras) barras,sum(if(b.idprod=a.idprod,b.cantidad*b.tipo,0)) stock,a.promo,a.precvar,a.fraccion,a.unmedida,a.unidades from productos a,stockmoves2 where isnull(a.nombre)!=1 and a.nombre!=''  and if(a.porlocal=1,if(a.idprod in (select idprod from productolocal where local='$Xlocal'),1,0),1) and if(a.porcanal=1,if(a.idprod in (select idprod from prodcanal where canal='$data'),1,0),1) and a.estado=1 GROUP BY a.idprod order by a.nombre;");
		$res=qry("select a.idprod,a.precio$lp,TRIM(UPPER(CONCAT(a.nombre,' ',a.nombre1,' ',a.nombre2,' ',a.nombre3,' ',a.nombre4))) nombre,a.pack,if(isnull(a.barras)=1,'',a.barras) barras,sum(if(b.idprod=a.idprod,b.cantidad*b.tipo,0)) stock,a.promo,a.precvar,a.fraccion,a.unmedida,a.unidades from productos a,stockmoves2 b where isnull(a.nombre)!=1 and a.nombre!=''  and  a.porlocal=0 and if(a.porcanal=1,if(a.idprod in (select idprod from prodcanal where canal='$data'),1,0),1) and a.estado=1 GROUP BY a.idprod order by a.nombre;");
		$prod=array();
		while($temp=mysql_fetch_row($res)){ $prod[]=$temp; }
		echo json_encode($prod);
	}
	
	if($a=="detalle_pedido")
	{
		/////Lista de Productos
		$arr=array();
		$qrybp="select c.codigo2,c.nombre,a.cantidad,a.precio,a.monto,a.puntos,a.porcentaje,a.descuento,a.total,if((a.promo=0 and c.categoria!=2), 1,if((c.categoria=2 and a.promo=0),2,if(a.promo!=0,3,0))) from pedido_detalle a,productos c where c.idprod=a.idprod  and a.idop='$idop'";
		$tbody="";
		$qq1=qry($qrybp);
		$tped=0;
		while($qq=mysql_fetch_row($qq1))
		{
			$temp=count($qq)-1;
			$tped+=$qq[($temp-1)];
			$color="";
			if($qq[$temp]==3)
			{
				$color="class='info'";
			}
			elseif($qq[$temp]==2){
				$color="class='success'";		
			}
			else{
				$color="class=''";
			}
			$tbody.="<tr $color>";
			
			for($i=0;$i<$temp;$i++)
			{
				$tbody.="<td>$qq[$i]</td>";
			}
			$tbody.="</tr>";
		}
		$tbody="<table class='table table-bordered'><thead><tr><th>COD.</th><th>NOM.</th><th>CANT.</th><th>PRE.</th><th>MON.</th><th>PUNT.</th><th>%</th><th>DESC.</th><th>TOT.</th></tr></thead>".$tbody."<tr><td></td><td><strong>TOTAL</strong></td><td></td><td></td><td></td><td></td><td></td><td></td><td>$tped</td></tr></table>";
		$arr["tped"]=$tped;
		
		echo $tbody;
		
	}
	
	////////OK/////////
	if($a=="descuento_unico")
	{
		if(isset($tipov)){
			$tipov=$_POST["tipov"];
			}else{
			$tipov="";
		}
		$ttime=microtime(true);
		$i=$_POST["i"];
		if(isset($_POST["login"])){
			$login=$_POST["login"];
			if(isset($_POST["pais"])){
				$pais=$_POST["pais"];
				}else{
				$pais=604;
			}
			$rest=mysql_fetch_row(qry("select sum(acumu),(hora_disabled is null),iduser from user_nivel where login='$login' and idpais='$pais'"));
			$disable=$rest[1];
			$Xacum=$rest[0];
			$iduser=$rest[2];
			}else{
			$pais=$Xpais;
			$iduser=$Xiduser;
			$login=$Xlogin;
			$disable=$Xdisabled;
		}
		if(isset($_POST["tipo"]) && $_POST["tipo"]=="g"){
			////////////Nuevo Direccion///
			$selcli="<option disabled selected value=''>--seleccine--</option>";
			$arrcli=getrucs($login);
			$r=json_decode($arrcli);
			$n=count($r);
			for($ii=0;$ii<$n;$ii++)
			{
				$selcli.="<option value='{$r[$ii][0]}'>{$r[$ii][2]}({$r[$ii][1]})</option>";
			}
			$a_cli=$r;
			$arr['selcli']=$selcli;
			$arr['a_cli']=$a_cli;
			///////////Envio///
			
			$arrclit=getenvios($login);
			$rt=json_decode($arrclit);
			$seldest1="<option disabled selected value=''>--seleccine--</option>";
			$seldest2="";
			$n=count($rt);
			for($ii=0;$ii<$n;$ii++)
			{
				$seldest2.="<option value='{$rt[$ii][0]}' $selec>{$rt[$ii][1]}</option>";
			}
			$seldest=$seldest1.$seldest2;
			$a_dest=$rt;	
			$arr['seldest']=$seldest;
			$arr['a_dest']=$a_dest;
		}
		$idcq=qry("select idcliente,nombre,direccion,referencia,b.departamento,b.provincia,b.distrito from clientes a left join ubigeo b on b.id=a.ubigeo where codigo1='$login' and idpais='$pais' order by lastupdate limit 1");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq)){
			//$ids[]=$idc1[0];
			$opcli.="<option value='$idc1[0]'>$idc1[1]($idc1[0])</option>";
			$arr['dir']=$idc1[2];
			$arr['ref']=$idc1[3];
			$arr['dep']=$idc1[4];
			$arr['prov']=$idc1[5];
			$arr['dist']=$idc1[6];
		}
		$puns=validacion_general($Xlogin,$Xlocal,$i,$login,$pais,$iduser,$lp);
		$peso=$puns["peso"];
		//echo $puns["qry"];
		$arr['qry']=$puns["qry"];
		$arr['peso']=$peso/1000;
		
		if(!isset($tipov)){
			$tipov="";
		}
		
		if(($puns["puntos"]>=$Xlimitr and $puns["est"]==1 and $tipov=="") or ($puns["est"]==2 and $puns["puntos"]>=$Xlimitr and $tipov=="") or ($puns["est"]==3 and $puns["puntos"]>=$Xlimitr and $tipov=="") or ($puns["puntos"]>0 and $tipov=="up" and $puns["est"]==5) or ($puns["est"]==5 and $puns["puntos"]>=$Xlimitr )){
			$r2=array(0,0,0,0);
			/*if($puns["est"]==3){
				$qry1="select descuento1,descuento2,descuento3,idnivel from niveles where idnivel={$puns['nivel']}";
				$res1 = qry($qry1);
				$r2=mysql_fetch_row($res1);
			}*/
			if($puns["est"]==2){
				$proms=mysql_fetch_row(qry("select count(*) from bolscanal a, productos$pais b where a.promo=b.idprod and prof in (5) and idbolsa='$i' and login='$Xlogin' and local='$Xlocal'"));
				if($proms[0]>0 and $disable==0){
					
					}else{
					$idkini=mysql_fetch_row(qry("select idprod from productos$pais where prof=6 and estado=0 limit 1 "));
					if($idkini[0]>0){
						$promo=tienepromo($idkini[0],1,$pais);
						for($j=0;$j<count($promo);$j++){
							qry("insert ignore into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,promo,fraccion,canal,lista,localdst,codigo1) values('$i','".$promo[$j][0]."',now(),'".$promo[$j][2]."','".$promo[$j][1]."','$Xlogin','$Xlocal','$idkini[0]','0','14','0','$Xlocal',{$promo[$j][3]});");
						}
					}			
				}
				
			}
			
			$tab=tabla_prod($Xlogin,$Xlocal,$i,$r2[0],$r2[1],$r2[2],$pais,$lp);
			$tped=$tab["tped"];
			$tped1=$tab["tped1"];
			$tbody=$tab["tbody"];	
			$adic="";
			$linea="<h3>Numero de Pedido:".($cal_pre["nroped"]+1)."</h3><table class='table table-bordered'><thead><th class='tabla1'>Cod. Prod.</th><th class='tabla1'>Nombre</th><th class='tabla1'>Puntos</th><th class='tabla1'>Monto</th><th class='tabla1'>Total</th></thead>$tbody $adic <tr class='tabla2'><th colspan='2'>TOTAL</th><td class='tabla2'>".($tped1)."</td><td class='tabla2'>".($tped)."</td><th class='tabla2'>".($tped)."</th></tr></table>";
			$ttro= microtime(true)-$ttime;
			$ttt=$ttro;
			$arr['total']=($tped);
			$arr['text']="<strong>".$puns["msg"]."</strong>".$linea;
			$arr['est']=1;
			}
		else{
			if($tipov=="up"){
				$arr['text']="<strong style='color:red;'>Primero debe ingresar el Autoconsumo del Mes</strong>";
				}elseif($puns["puntos"]>=$Xlimitr){
				$arr['text']="<strong style='color:red;'>".$puns["msg"].$puns["puntos"]."</strong>";
				}else{
				$arr['text']="<strong style='color:red;'>Necesita un pedido mayor a $Xlimitr(Tiene solo ".$puns['puntos']." Puntos)</strong>";
			}
			$arr['est']=0;
		}
		$arr['opcli']=$opcli;
		echo json_encode($arr);
	}
	
	////////OK///
	if($a=="sv_transporte")
	{
		if(isset($_POST["tipov"])){
			$tipov=$_POST["tipov"];
		}
		if(isset($_POST["login"])){
			$login=$_POST["login"];
			$pais=$_POST["pais"];
			$rest=mysql_fetch_row(qry("select acumu,hora_update,deuda,iduser from user_nivel where login='$login' and idpais='$pais'"));
			$Xacum=$rest[0];
			$Xfchcorte=$rest[1];
			$Xdeuda=$rest[2];
			$iduser=$rest[3];
			}else{
			$login=$Xlogin;		
			$pais=$Xpais;
			$iduser=$Xiduser;
		}
		$lp=$_POST["lp"];
		$idbol=$_POST["idbol"];
		$idruc=$_POST["idruc"];
		if($idruc>0)
		{
			
		}
		else{
			$ruc=$_POST["ruc"]; 
			$razon=$_POST["razon"]; 
			$dir_ruc=$_POST["dir_ruc"]; 
			$idruc=addruc($login,$ruc,$razon,$dir_ruc);
		}
		$transp=$_POST["transp"];
		if($transp==1)
		{
			if($iddest>0)
			{
				$dep=$_POST["depa"];
				$pro=$_POST["prov"];
				$dist=$_POST["dist"]; 
			}
			else{
				$dir=$_POST["dir"];
				$ref=$_POST["ref"];
				$dep=$_POST["depa"];
				$pro=$_POST["prov"];
				$dist=$_POST["dist"]; 
				$ubi=ubigeo($dep,$pro,$dist);
				$iddest=adddest($login,$dir,$ref,$ubi);
				/*$qryfunc[]="insert ignore into direccion_cliente(idcliente,direccion,referencia,ubigeo) select idcliente,'$dir','$ref','$ubi' from operacionesp where idop=$i";
				$idfunc[]="iddest";*/
			}
		}
		//////////////////////////////////////////////////////////////
		if($idcli!="0"){$nopc=namecli($idcli);}
		$res=listaprod($idbol,$Xlocal,$Xlogin,$lp);
		$prod=$res[0];
		$tota=$res[1];
		$ldes=$res[2];
		$lpre=$res[3];
		$cana=$res[4];
		$cliq=qry("select idcliente,local from usuarios where login='$login'");
		$idc2=mysql_fetch_row($cliq);
		$idcliente=$idc2[0];
		$local=$idc2[1];
		///////
		//qry("update clientes set lista='$lpre' where idcliente='$idcli';");
		$pag = "";
		$qry = "insert into operacionesp(hora,total,idcliente,login,vendedor1,notas,local,tipo,nameopc,estado,localdst,canal,tipopago1,idruccli,iddircli,moneda) values(now(),'$tota','$login','$Xlogin','$Xlogin','$nota','$local','PE','$nopc','105','','$cana','$fpago','$idruc','$iddest','$Xmoneda')";
		qry($qry);
		$idopv=mysql_insert_id();
		if($cana=="RED")
		{
			$idcq=qry("select idcliente from clientes where codigo1='$login'");
			$ids=array();
			while($idc1=mysql_fetch_row($idcq))
			{
				$ids[]=$idc1[0];
			}
			$idc=implode(",",$ids);
			////////Calculo de puntos////
			$puns=puntos_regural_general($Xlogin,$Xlocal,$idbol,$login,$pais,$lp);	
			$rpun[0]=$puns["rpun"];	
			$regular=$puns["regular"];
			$puntos2=$puns["puntos"];
			//$rpuna[0]=$puns["rpuna"];
			$puntos=$rpun[0];//+$rpuna[0];
			////////
			$peso=$puns["peso"];
			$pex+=($peso/1000);	
			
			$cextr=17;
			if($pex>10)
			{
				$cextr+=ceil($pex-10)*1.5;
			}		
			/*
				if(($dep=="LIMA" && $pro=="LIMA") or $dep=="CALLAO"){
				$cextr=15;
				}else{
				$cextr=10;
			}*/		
			$arr['peso']=$pex;
			$r2=array(0,0,0,0);		
			$cantP=count($prod);
			for($i=0;$i<$cantP;$i++)
			{ qry("insert into stockmovesp(idprod,hora,cantidad,precio,login,idop,local,promo,fraccion,codigo1) values('".$prod[$i][0]."',now(),'".$prod[$i][1]."','".$prod[$i][2]."','$Xlogin','$idopv','$local','".$prod[$i][3]."','".$prod[$i][4]."','{$prod[$i][5]}');"); }
			if($tipov=="up" and $deuda==0){
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser,fecha_venc) values ('$idopv','$login',$regular,now(),3,3,'$iduser',concat(year('$Xfchcorte'),'-',month(curdate()),'-',day('$Xfchcorte')))");
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser,fecha_venc) values ('$idopv','$login',$puntos,now(),3,4,'$iduser',concat(year('$Xfchcorte'),'-',month(curdate()),'-',day('$Xfchcorte')))");	
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser,fecha_venc) values ('$idopv','$login',$puntos2,now(),3,9,'$iduser',concat(year('$Xfchcorte'),'-',month(curdate()),'-',day('$Xfchcorte')))");
				}elseif($Xfchcorte!="" and $Xdeuda==0){
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,fecha_venc,iduser) values ('$idopv','$login',$regular,now(),3,3,(concat(date('$Xfchcorte'),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date('$Xfchcorte'),' 23:59:59'),now()) + 1 - $Xdeuda) month),'$iduser') ");
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,fecha_venc,iduser) values ('$idopv','$login',$puntos,now(),3,4,(concat(date('$Xfchcorte'),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date('$Xfchcorte'),' 23:59:59'),now()) + 1 - $Xdeuda) month),'$iduser') ");
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,fecha_venc,iduser) values ('$idopv','$login',$puntos2,now(),3,9,(concat(date('$Xfchcorte'),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date('$Xfchcorte'),' 23:59:59'),now()) + 1 - $Xdeuda) month),'$iduser') ");
				}else{
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser) values ('$idopv','$login',$regular,now(),3,3,'$iduser')");
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser) values ('$idopv','$login',$puntos,now(),3,4,'$iduser')");	
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser) values ('$idopv','$login',$puntos2,now(),3,9,'$iduser')");	
			}
			
			
			save_detallado($Xlogin,$Xlocal,$idbol,$r2[0],$r2[1],$r2[2],$r2[3],$idopv);
			
			/*if(($nroped+0)==0)
				{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('458','1',now(),'$Xlogin','$local','5','$idopv','BOLSA') ";
				$res = qry($qry) or die("4--".mysql_error());
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',458,1,5,5,0,0,0,0,5)");
				$prod[]=array(458,1,5,0);
				}
				ELSE{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('1910','1',now(),'$Xlogin','$local','5','$idopv','EMBALAJE') ";
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',1910,1,5,5,0,0,0,0,5)");
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(1910,1,5,0);				
			//}*/
			if($transp==1){
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2,codigo1) values ('264','1',now(),'$Xlogin','$local','".$cextr."','$idopv','MOVILIDAD',264) ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(264,1,$cextr,0,0,264);	
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',264,1,$cextr,$cextr,0,0,0,0,$cextr)");
			}
			
		}
		
		$prod=json_encode($prod);	
		
		$idped=posturl(array("a"=>"pCatalogo","tipo"=>"2","canal"=>$cana,"prod"=>$prod,"idc"=>$login,"local"=>$Xlocal,"log"=>$Xlogin,"not"=>$nota,"qrydest"=>$qrydest,"fpago"=>$fpago,"ruc"=>$ruc,"idruc"=>$idruc,"iddest"=>$iddest,"moneda"=>$Xmoneda),$XXurl_erp1);
		//qry("update user_nivel set hora_in=now() where login='$login' and hora_in is null");
		qry("update operacionesp set idpadre2='$idped', total=(select sum(precio*cantidad) from stockmovesp where idop='$idopv') where idop='$idopv'");
		qry("delete from bolscanal where idbolsa='$idbol' and login='$Xlogin'");
		if($idped>0){
			qry("insert ignore into opsv(idop,n) values ($idped,0)");
		}	
		echo "1";
	}
	
	
	////OK////
	if($a=="calcenv")
	{
		$dir=$_POST["dir"];
		$ref=$_POST["ref"];
		$dep=$_POST["dep"];
		$pro=$_POST["pro"];
		$dis=$_POST["dis"];
		$idb=$_POST["i"];
		$lp=$_POST["lp"];
		$tip=$_POST["tip"];
		
		if($tip=="g"){$idc=$_POST["idc"];
			$pais=$_POST["pais"];
			$log=qry("select codigo1 from clientes where idcliente='$idc'");
			$login=mysql_fetch_row($log);
			$login=$login[0];
			}elseif($tip=="p"){
			$cliq=qry("select idcliente,(select local from usuarios where login='$Xlogin') from clientes where codigo1='$Xlogin' order by lastupdate desc limit 1");
			$idc2=mysql_fetch_row($cliq);
			$idc=$idc2[0];
			$local=$idc2[1];
			$pais=$Xpais;
		}else{$pais=$Xpais;}
		$res=mysql_query("");
		
		
		/*if($tip=="g"){
			$puns=puntos_regural_general($Xlogin,$Xlocal,$idb,$login);	
			}elseif($tip=="p"){
			$puns=puntos_regural_general($Xlogin,$Xlocal,$idb,$Xlogin);	
			}
			
			
			
			$rpun[0]=$puns["rpun"];	
			$rpuna[0]=$puns["rpuna"];
		$puntos=$rpun[0]+$rpuna[0];*/
		$peso=peso_ped($Xlogin,$Xlocal,$idb,$pais,$lp);
		$arr['peso']=$peso[0];
		
		$pex+=($peso[0]/1000);	
		/*if($pex!="")
			{
			if($dep=="LIMA" && $pro=="LIMA")
			{$cextr=15;}
			else
			{
			if($pex>=1)
			{
			$cextr=12;
			$difp=$pex-1;
			$difp=ceil($difp);
			$cextr+=$difp*(2.5);
			}
			else
			{$cextr=12;}
			}
			}
		*/
		$cextr=17;
		if($pex>10)
		{
			$cextr+=ceil($pex-10)*1.5;
		}
		/*
			if(($dep=="LIMA" && $pro=="LIMA") or $dep=="CALLAO"){
			$cextr=15;
			}else{
			$cextr=10;
		}*/
		
		//if($rpun[0]>=200)
		//{
		$r2=array(0,0,0,0);
		/*$cal_pre=calculo_precio($r2[0],$r2[1],$r2[2],$Xlogin,$Xlocal,$idb,$idc,$r2[3]);
			$descact_past=$cal_pre["descact_past"];
			$nroped=$cal_pre["nroped"];
			$descact=$cal_pre["descact"];
		$descant=$cal_pre["descant"];		*/
		/////Lista de Productos
		
		$tab=tabla_prod($Xlogin,$Xlocal,$idb,$r2[0],$r2[1],$r2[2],$pais,$lp);
		$tped=$tab["tped"];
		$tbody=$tab["tbody"];					
		////
		$arr['text']=$linea;
		$arr['total']=($tped + $cextr);
		$arr['est']=1;
		/*	}
			else{
			$arr['text']="Se Necesita un pedido mayor a 200 Puntos";
			$arr['est']=0;
		}*/
		///////////////////////////////////////////////////////////////////////////////
		echo json_encode(array($arr['total'],$cextr,$pex));	
	}
	
	/////////////////Calc_acumulado///
	
	////OK////
	if($a=="descuento_acumulado")
	{
		$ttime=microtime(true);
		$i=$_POST["i"];
		$lp=$_POST["lp"];
		if(isset($_POST["login"])){
			$login=$_POST["login"];
			$pais=$_POST["pais"];
			$rest=mysql_fetch_row(qry("select acumu,nivel,tipo,idpais,monto from user_nivel where login='$login' and idpais='$pais'"));
			$Xacum=$rest[0];
			$nivel=$rest[1];
			$tipo_n=$rest[2];
			$monto=$rest[3];
			}else{
			$login=$Xlogin;
			$nivel=$Xnivel;
			$tipo_n=$Xtipo_n;
			$pais=$Xpais;
			$monto=$Xmonto;
		}
		if(isset($_POST["tipo"]) && $_POST["tipo"]=="g"){
			////////////Nuevo Direccion///
			$selcli="<option disabled selected value=''>--seleccine--</option>";
			$arrcli=getrucs($login);
			$r=json_decode($arrcli);
			$n=count($r);
			for($ii=0;$ii<$n;$ii++)
			{
				$selcli.="<option value='{$r[$ii][0]}'>{$r[$ii][2]}({$r[$ii][1]})</option>";
			}
			$a_cli=$r;
			$arr['selcli']=$selcli;
			$arr['a_cli']=$a_cli;
			///////////Envio///
			
			$arrclit=getenvios($login);
			$rt=json_decode($arrclit);
			$seldest1="<option disabled selected value=''>--seleccine--</option>";
			$seldest2="";
			$n=count($rt);
			for($ii=0;$ii<$n;$ii++)
			{
				$seldest2.="<option value='{$rt[$ii][0]}' $selec>{$rt[$ii][1]}</option>";
			}
			$seldest=$seldest1.$seldest2;
			$a_dest=$rt;	
			$arr['seldest']=$seldest;
			$arr['a_dest']=$a_dest;
		}
		
		$idcq=qry("select idcliente,nombre,direccion,referencia,b.departamento,b.provincia,b.distrito from clientes a left join ubigeo b on b.id=a.ubigeo where codigo1='$login'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq)){
			$ids[]=$idc1[0];
			$opcli.="<option value='$idc1[0]'>$idc1[1]($idc1[0])</option>";
			$arr['dir']=$idc1[2];
			$arr['ref']=$idc1[3];
			$arr['dep']=$idc1[4];
			$arr['prov']=$idc1[5];
			$arr['dist']=$idc1[6];
		}
		$idc=implode(",",$ids);	
		////////Calculo de puntos////
		//$puns=validacion_acumulado($Xlogin,$Xlocal,$i,$login);	
		$peso=peso_ped($Xlogin,$Xlocal,$i,$pais,$lp);
		$arr['peso']=$peso[0]/1000;
		$acum_temp=$Xacum+$peso[1];
		$arr["acum"]=$Xacum;
		if($monto>0)
		{
			$r2=array(0,0,0,0);
			if($tipo_n=="ESPECIAL"){
				$r2=array(40,15,5,0);
				}else{
				$qry1="select descuento1,descuento2,descuento3,idnivel from niveles_acum where $acum_temp between limite1 and limite2 and $acum_temp<limite2";
				$arr["qry"]=$qry1;
				$res1 = qry($qry1);
				if(mysql_num_rows($res1)>0){
					$r2=mysql_fetch_row($res1);
				}			
			}	
			$tab=tabla_prod_desc($Xlogin,$Xlocal,$i,$r2[0],$r2[1],$r2[2],$r2[3],$pais,$lp);
			$tped=$tab["tped"];
			$tdesc=$tab["tdesc"];
			$puntos=$tab["puntos"];
			$tbody=$tab["tbody"];	
			$adic="";
			$linea="<h3>Numero de Pedido:".($cal_pre["nroped"]+1)."</h3><div class='table-responsive'><table class='table table-bordered'><thead><th class='tabla1'>Cod. Prod.</th><th class='tabla1'>Nombre</th><th class='tabla1'>Puntos</th><th class='tabla1'>Monto</th><th class='tabla1'>%</th><th class='tabla1'>Descuento</th><th class='tabla1'>Total</th></thead>$tbody $adic <tr class='tabla2'><th colspan='2'>TOTAL</th><td class='tabla2'>".$puntos."</td><td class='tabla2'>".($tped)."</td><td class='tabla2'></td><td class='tabla2'>".($tdesc)."</td><th class='tabla2'>".($tped-$tdesc)."</th></tr></table></div>";
			$ttro= microtime(true)-$ttime;
			$ttt=$ttro;
			$arr['total']=($tped-$tdesc);
			$arr['text']=$puns["msg"]."<br />".$linea.$tabpromo;
			$arr['est']=1;
			}else{
			$arr['text']="Tu Primera Compra del Mes debe ser mayor a 56 puntos";
			$arr['est']=0;
		}
		$arr['opcli']=$opcli;
		echo json_encode($arr);
	}
	///////TNIKUY
	////OK////
	if($a=="descuento_tinkuy")
	{
		$ttime=microtime(true);
		$i=$_POST["i"];
		if(isset($_POST["login"])){
			$login=$_POST["login"];
			$rest=mysql_fetch_row(qry("select tin_acumu,nivel,tinkuy from user_nivel where login='$login'"));
			$Xtin_acumu=$rest[0];
			$nivel=$rest[1];
			$Xtinkuy=$rest[2];
			}else{
			$login=$Xlogin;
			$nivel=$Xnivel;
			//$tipo_n=$Xtipo_n;
		}
		if(isset($_POST["tipo"]) && $_POST["tipo"]=="g"){
			////////////Nuevo Direccion///
			$selcli="<option disabled selected value=''>--seleccine--</option>";
			$arrcli=getrucs($login);
			$r=json_decode($arrcli);
			$n=count($r);
			for($ii=0;$ii<$n;$ii++)
			{
				$selcli.="<option value='{$r[$ii][0]}'>{$r[$ii][2]}({$r[$ii][1]})</option>";
			}
			$a_cli=$r;
			$arr['selcli']=$selcli;
			$arr['a_cli']=$a_cli;
			///////////Envio///
			
			$arrclit=getenvios($login);
			$rt=json_decode($arrclit);
			$seldest1="<option disabled selected value=''>--seleccine--</option>";
			$seldest2="";
			$n=count($rt);
			for($ii=0;$ii<$n;$ii++)
			{
				$seldest2.="<option value='{$rt[$ii][0]}' $selec>{$rt[$ii][1]}</option>";
			}
			$seldest=$seldest1.$seldest2;
			$a_dest=$rt;	
			$arr['seldest']=$seldest;
			$arr['a_dest']=$a_dest;
		}
		
		$idcq=qry("select idcliente,nombre,direccion,referencia,b.departamento,b.provincia,b.distrito from clientes a left join ubigeo b on b.id=a.ubigeo where codigo1='$login'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
			$opcli.="<option value='$idc1[0]'>$idc1[1]($idc1[0])</option>";
			$arr['dir']=$idc1[2];
			$arr['ref']=$idc1[3];
			$arr['dep']=$idc1[4];
			$arr['prov']=$idc1[5];
			$arr['dist']=$idc1[6];
		}
		$idc=implode(",",$ids);	
		////////Calculo de puntos////
		//$puns=validacion_acumulado($Xlogin,$Xlocal,$i,$login);	
		$peso=peso_ped($Xlogin,$Xlocal,$i);
		$arr['peso']=$peso[0]/1000;
		$acum_temp=$Xtin_acumu+$peso[1];
		$arr["acum"]=$Xtin_acumu;
		if($Xtinkuy!=3){
			if($acum_temp>=2500 or $nivel>0)
			{
				$r2=array(0,0,0,0);
				if($Xtinkuy==0){
					$r2=array(0,0,0,0);
					}else{
					$qry1="select descuento1,descuento2,descuento3,idnivel from niveles_tinkuy where $acum_temp between limite1 and limite2 and $acum_temp<limite2";
					$res1 = qry($qry1);
					if(mysql_num_rows($res1)>0){
						$r2=mysql_fetch_row($res1);
					}			
				}
				$tab=tabla_prod_desc($Xlogin,$Xlocal,$i,$r2[0],$r2[1],$r2[2],$r2[3]);
				$tped=$tab["tped"];
				$tdesc=$tab["tdesc"];
				$tbody=$tab["tbody"];	
				$adic="";
				$linea="<h3>Numero de Pedido:".($cal_pre["nroped"]+1)."</h3><div class='table-responsive'><table class='table table-bordered'><thead><th class='tabla1'>Cod. Prod.</th><th class='tabla1'>Nombre</th><th class='tabla1'>Puntos</th><th class='tabla1'>Monto</th><th class='tabla1'>%</th><th class='tabla1'>Descuento</th><th class='tabla1'>Total</th></thead>$tbody $adic <tr class='tabla2'><th colspan='2'>TOTAL</th><td class='tabla2'>".($rpuna[0]+$rpun[0])."</td><td class='tabla2'>".($tped)."</td><td class='tabla2'></td><td class='tabla2'>".($tdesc)."</td><th class='tabla2'>".($tped-$tdesc)."</th></tr></table></div>";
				$ttro= microtime(true)-$ttime;
				$ttt=$ttro;
				$arr['total']=($tped-$tdesc);
				$arr['text']=$puns["msg"]."<br />".$linea;
				$arr['est']=1;
			}
			else{
				$arr['text']="<strong>Tu Primera Compra TINKUY del Mes debe ser mayor a S/.2500</strong>.";
				$arr['est']=0;
			}}else{
			$arr['text']="<strong>Espera la Aprobacion de tu Afiliacion.</strong>";
			$arr['est']=0;		
		}
		$arr['opcli']=$opcli;
		echo json_encode($arr);
	}
	/////////
	
	////OK////
	if($a=="calcenv_acum")
	{
		$dir=$_POST["dir"];
		$ref=$_POST["ref"];
		$dep=$_POST["dep"];
		$pro=$_POST["pro"];
		$dis=$_POST["dis"];
		$idb=$_POST["i"];
		$lp=$_POST["lp"];
		$tip=$_POST["tip"];
		
		if(isset($_POST["login"])){
			$pais=$_POST["pais"];
			$login=$_POST["login"];
			$rest=mysql_fetch_row(qry("select acumu,nivel,tipo from user_nivel where login='$login' and idpais='$pais'"));
			$Xacum=$rest[0];
			$nivel=$rest[1];
			$tipo_n=$rest[2];
			}else{
			$pais=$Xpais;
			$login=$Xlogin;
			$nivel=$Xnivel;
			$tipo_n=$Xtipo_n;
		}	
		
		
		if(isset($_POST["tipo"]) && $_POST["tipo"]=="g"){
			
		}
		
		$res=mysql_query("");
		$peso=peso_ped($Xlogin,$Xlocal,$idb,$pais,$lp);
		//$arr['peso']=$peso;
		$pex=($peso[0]/1000);
		$cextr=17;
		if($pex>10){
			$cextr+=ceil($pex-10)*1.5;
		}	
		/*
			if(($dep=="LIMA" && $pro=="LIMA") or $dep=="CALLAO"){
			$cextr=15;
			}else{
			$cextr=10;
		}*/
		$acum_temp=$Xacum+$peso[1];
		$r2=array(0,0,0,0);
		if($tipo_n=="ESPECIAL"){
			$r2=array(40,15,5,0);
			}else{
			$qry1="select descuento1,descuento2,descuento3,idnivel from niveles_acum where $acum_temp between limite1 and limite2 and $acum_temp<limite2";
			$res1 = qry($qry1);
			if(mysql_num_rows($res1)>0){
				$r2=mysql_fetch_row($res1);
			}		
		}
		/////Lista de Productos
		$tab=tabla_prod_desc($Xlogin,$Xlocal,$idb,$r2[0],$r2[1],$r2[2],$r2[3],$pais,$lp);
		$tped=$tab["tped"];
		$tdesc=$tab["tdesc"];
		$tbody=$tab["tbody"];					
		////
		$arr['text']=$linea;
		$arr['total']=($tped - $tdesc + $cextr);
		$arr['est']=1;		
		///////////////////////////////////////////////////////////////////////////////
		echo json_encode(array($arr['total'],$cextr,$pex,$arr['desc']));	
	}
	
	////OK///
	if($a=="sv_transporte_acum")
	{
		if(isset($_POST["login"])){
			$login=$_POST["login"];
			$pais=$_POST["pais"];
			$rest=mysql_fetch_row(qry("select acumu,nivel,tipo,idpais,iduser from user_nivel where login='$login' and idpais='$pais'"));
			$Xacum=$rest[0];
			$nivel=$rest[1];
			$tipo_n=$rest[2];
			$iduser=$rest[4];
			}else{
			$pais=$Xpais;
			$login=$Xlogin;
			$nivel=$Xnivel;
			$tipo_n=$Xtipo_n;
			$iduser=$Xiduser;
		}
		$idbol=$_POST["idbol"];
		$lp=$_POST["lp"];
		$idruc=$_POST["idruc"];
		if($idruc>0)
		{
			
		}
		else{
			$ruc=$_POST["ruc"]; 
			$razon=$_POST["razon"]; 
			$dir_ruc=$_POST["dir_ruc"]; 
			$idruc=addruc($login,$ruc,$razon,$dir_ruc);	
			/*$qryfunc[]="insert ignore into ruc_cliente (idcliente,ruc,razon_social,direccion)  select idcliente,'$ruc','$razon','$dir_ruc' from operacionesp where idop=$i";
			$idfunc[]="idruc";*/
		}
		$transp=$_POST["transp"];
		if($transp==1)
		{
			if($iddest>0)
			{
				$dep=$_POST["depa"];
				$pro=$_POST["prov"];
				$dist=$_POST["dist"]; 
			}
			else{
				$dir=$_POST["dir"];
				$ref=$_POST["ref"];
				$dep=$_POST["depa"];
				$pro=$_POST["prov"];
				$dist=$_POST["dist"]; 
				$ubi=ubigeo($dep,$pro,$dist);
				$iddest=adddest($login,$dir,$ref,$ubi);
				/*$qryfunc[]="insert ignore into direccion_cliente(idcliente,direccion,referencia,ubigeo) select idcliente,'$dir','$ref','$ubi' from operacionesp where idop=$i";
				$idfunc[]="iddest";*/
			}
		}
		//////////////////////////////////////////////////////////////
		if($idcli!="0"){$nopc=namecli($idcli);}	
		$res=listaprod($idbol,$Xlocal,$Xlogin,$lp);
		$prod=$res[0];
		$tota=$res[1];
		$ldes=$res[2];
		$lpre=$res[3];
		$cana=$res[4];
		$cliq=qry("select idcliente,local from usuarios where login='$login'");
		$idc2=mysql_fetch_row($cliq);
		$idcliente=$idc2[0];
		$local=$idc2[1];
		///////
		//qry("update clientes set lista='$lpre' where idcliente='$idcli';");
		$pag = "";
		$qry = "insert into operacionesp(hora,total,idcliente,login,vendedor1,notas,local,tipo,nameopc,estado,localdst,canal,tipopago1,idruccli,iddircli,moneda) values(now(),'$tota','$login','$Xlogin','$Xlogin','$nota','$local','PE','$nopc','105','','$cana','$fpago','$idruc','$iddest','$Xmoneda')";
		qry($qry);
		$idopv=mysql_insert_id();
		if($cana=="RED")
		{
			$idcq=qry("select idcliente from clientes where codigo1='$login'");
			$ids=array();
			while($idc1=mysql_fetch_row($idcq))
			{
				$ids[]=$idc1[0];
			}
			$idc=implode(",",$ids);
			
			$peso=peso_ped($Xlogin,$Xlocal,$idbol,$pais,$lp);
			$pex=($peso[0]/1000);	
			
			$cextr=17;
			if($pex>10)
			{
				$cextr+=ceil($pex-10)*1.5;
			}
			/*
				if(($dep=="LIMA" && $pro=="LIMA") or $dep=="CALLAO"){
				$cextr=15;
				}else{
				$cextr=10;
			}*/
			$acum_temp=$Xacum+$peso[1];
			$arr['peso']=$pex;
			////////Calculo de puntos////
			$r2=array(0,0,0,0);
			if($tipo_n=="ESPECIAL"){
				$r2=array(40,15,5,0);
				}else{
				$qry1="select descuento1,descuento2,descuento3,idnivel from niveles_acum where $acum_temp between limite1 and limite2 and $acum_temp<limite2";
				$res1 = qry($qry1);
				if(mysql_num_rows($res1)>0){
					$r2=mysql_fetch_row($res1);
				}		
			}
			
			
			////////			
			$tab=tabla_prod_desc($Xlogin,$Xlocal,$idbol,$r2[0],$r2[1],$r2[2],$r2[3],$pais,$lp);
			$tped=$tab["tped"];
			$tdesc=$tab["tdesc"];
			$tbody=$tab["tbody"];
			$puntos=$tab["puntos"];
			$totpuntos=$tped-$tdesc-$desc;
			////////
			//$r2=array(0,0,0,0);		
			$cantP=count($prod);
			for($i=0;$i<$cantP;$i++)
			{ qry("insert into stockmovesp(idprod,hora,cantidad,precio,login,idop,local,promo,fraccion,codigo1) values('".$prod[$i][0]."',now(),'".$prod[$i][1]."','".$prod[$i][2]."','$Xlogin','$idopv','$local','".$prod[$i][3]."','".$prod[$i][4]."','{$prod[$i][5]}');"); }
			qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser) values ('$idopv','$login',$tped,now(),3,5,'$iduser') ");
			qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser) values ('$idopv','$login',$totpuntos,now(),3,6,'$iduser') ");
			qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo,iduser) values ('$idopv','$login',$puntos,now(),3,10,'$iduser') ");
			save_detallado($Xlogin,$Xlocal,$idbol,$r2[0],$r2[1],$r2[2],$r2[3],$idopv);
			if($tdesc>0)
			{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,codigo1) values ('254','1',now(),'$Xlogin','$local','-$tdesc','$idopv','254') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(254,1,-$tdesc,0,0,254);
				//echo $qry;
			}
			
			/////Descuento Necesario
			/*
				if($desc>0)
				{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop) values ('7879','1',now(),'$Xlogin','$local','-$desc','$idopv') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(7879,1,-$desc,0);
				//echo $qry;
				}
			*/
			/*if(($nroped+0)==0)
				{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('458','1',now(),'$Xlogin','$local','5','$idopv','BOLSA') ";
				$res = qry($qry) or die("4--".mysql_error());
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',458,1,5,5,0,0,0,0,5)");
				$prod[]=array(458,1,5,0);
				}
				ELSE{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('1910','1',now(),'$Xlogin','$local','5','$idopv','EMBALAJE') ";
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',1910,1,5,5,0,0,0,0,5)");
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(1910,1,5,0);				
			//}*/
			if($transp==1){
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2,codigo1) values ('264','1',now(),'$Xlogin','$local','".$cextr."','$idopv','MOVILIDAD','264') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(264,1,$cextr,0,0,264);	
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',264,1,$cextr,$cextr,0,0,0,0,$cextr)");
				/*
					$ubi=ubigeo($dep,$pro,$dis);
					if($ubi!=false){}
					else
					{exit;}
					$qrydest="insert into destinos(idcliente,direccion,referencia,ubigeo) values('".$idcli."','".$dir."','".$ref."','".$ubi."');";
					mysql_query("insert into destinos(idcliente,direccion,referencia,ubigeo) values('".$idcli."','".$dir."','".$ref."','".$ubi."');");
					$idenv=mysql_insert_id();
				mysql_query("insert into ped_destino(idop,iddestino) values('".$idopv."','".$idenv."');");*/
			}
			
		}
		
		$prod=json_encode($prod);	
		
		$idped=posturl(array("a"=>"pCatalogo","tipo"=>"2","canal"=>$cana,"prod"=>$prod,"idc"=>$login,"local"=>$Xlocal,"log"=>$Xlogin,"not"=>$nota,"qrydest"=>$qrydest,"fpago"=>$fpago,"ruc"=>$ruc,"idruc"=>$idruc,"iddest"=>$iddest,"moneda"=>$Xmoneda),$XXurl_erp1);
		//qry("update user_nivel set hora_in=now() where login='$login' and hora_in is null");
		qry("update operacionesp set idpadre2='$idped', total=(select sum(precio*cantidad) from stockmovesp where idop='$idopv') where idop='$idopv'");
		qry("delete from bolscanal where idbolsa='$idbol' and login='$Xlogin'");
		if($idped>0){
			qry("insert ignore into opsv(idop,n) values ($idped,0)");
		}
		echo "1";
	}
	//////Movilidad Tinkuy
	////OK////
	if($a=="calcenv_tinkuy")
	{
		$dir=$_POST["dir"];
		$ref=$_POST["ref"];
		$dep=$_POST["dep"];
		$pro=$_POST["pro"];
		$dis=$_POST["dis"];
		$idb=$_POST["i"];
		$tip=$_POST["tip"];
		
		if(isset($_POST["login"])){
			$login=$_POST["login"];
			$rest=mysql_fetch_row(qry("select tin_acumu,nivel,tinkuy from user_nivel where login='$login'"));
			$Xtin_acumu=$rest[0];
			$nivel=$rest[1];
			$Xtinkuy=$rest[2];
			}else{
			$login=$Xlogin;
			$nivel=$Xnivel;
			//$tipo_n=$Xtipo_n;
		}	
		
		if(isset($_POST["tipo"]) && $_POST["tipo"]=="g"){
			
		}
		
		$res=mysql_query("");
		$peso=peso_ped($Xlogin,$Xlocal,$idb);
		//$arr['peso']=$peso;
		$pex=($peso[0]/1000);
		$cextr=17;
		/*
			if(($dep=="LIMA" && $pro=="LIMA") or $dep=="CALLAO"){
			$cextr=15;
			}else{
			$cextr=10;
		}*/
		$acum_temp=$Xtin_acumu+$peso[1];
		$r2=array(0,0,0,0);
		if($Xtinkuy==0){
			$r2=array(0,0,0,0);
			}else{
			$qry1="select descuento1,descuento2,descuento3,idnivel from niveles_tinkuy where $acum_temp between limite1 and limite2 and $acum_temp<limite2";
			$res1 = qry($qry1);
			if(mysql_num_rows($res1)>0){
				$r2=mysql_fetch_row($res1);
			}		
		}
		/////Lista de Productos
		$tab=tabla_prod_desc($Xlogin,$Xlocal,$idb,$r2[0],$r2[1],$r2[2],$r2[3]);
		$tped=$tab["tped"];
		$tdesc=$tab["tdesc"];
		$tbody=$tab["tbody"];					
		////
		$arr['text']=$linea;
		$arr['total']=($tped - $tdesc + $cextr);
		$arr['est']=1;		
		///////////////////////////////////////////////////////////////////////////////
		echo json_encode(array($arr['total'],$cextr,$pex));	
	}
	
	//////venta final tinkuy
	////OK///
	if($a=="sv_transporte_tinkuy")
	{
		
		if(isset($_POST["login"])){
			$login=$_POST["login"];
			$rest=mysql_fetch_row(qry("select tin_acumu,nivel,tinkuy from user_nivel where login='$login'"));
			$Xtin_acumu=$rest[0];
			$nivel=$rest[1];
			$Xtinkuy=$rest[2];
			}else{
			$login=$Xlogin;
			$nivel=$Xnivel;
			//$tipo_n=$Xtipo_n;
		}	
		$idbol=$_POST["idbol"];
		$idruc=$_POST["idruc"];
		if($idruc>0)
		{
			
		}
		else{
			$ruc=$_POST["ruc"]; 
			$razon=$_POST["razon"]; 
			$dir_ruc=$_POST["dir_ruc"]; 
			$idruc=addruc($login,$ruc,$razon,$dir_ruc);	
			/*$qryfunc[]="insert ignore into ruc_cliente (idcliente,ruc,razon_social,direccion)  select idcliente,'$ruc','$razon','$dir_ruc' from operacionesp where idop=$i";
			$idfunc[]="idruc";*/
		}
		$transp=$_POST["transp"];
		if($transp==1)
		{
			if($iddest>0)
			{
				$dep=$_POST["depa"];
				$pro=$_POST["prov"];
				$dist=$_POST["dist"]; 
			}
			else{
				$dir=$_POST["dir"];
				$ref=$_POST["ref"];
				$dep=$_POST["depa"];
				$pro=$_POST["prov"];
				$dist=$_POST["dist"]; 
				$ubi=ubigeo($dep,$pro,$dist);
				$iddest=adddest($login,$dir,$ref,$ubi);
				/*$qryfunc[]="insert ignore into direccion_cliente(idcliente,direccion,referencia,ubigeo) select idcliente,'$dir','$ref','$ubi' from operacionesp where idop=$i";
				$idfunc[]="iddest";*/
			}
		}
		//////////////////////////////////////////////////////////////
		if($idcli!="0"){$nopc=namecli($idcli);}	
		$res=listaprod($idbol,$Xlocal,$Xlogin);
		$prod=$res[0];
		$tota=$res[1];
		$ldes=$res[2];
		$lpre=$res[3];
		$cana=$res[4];
		$cliq=qry("select idcliente,local from usuarios where login='$login'");
		$idc2=mysql_fetch_row($cliq);
		$idcliente=$idc2[0];
		$local=$idc2[1];
		///////
		//qry("update clientes set lista='$lpre' where idcliente='$idcli';");
		$pag = "";
		if($Xtinkuy==0){
			$qry = "insert into operacionesp(hora,total,idcliente,login,vendedor1,notas,local,tipo,nameopc,estado,localdst,canal,tipopago1,idruccli,iddircli,moneda) values(now(),'$tota','$login','$Xlogin','$Xlogin','$nota','$local','TK','$nopc','105','','$cana','$fpago','$idruc','$iddest','$Xmoneda')";
			}else{
			$qry = "insert into operacionesp(hora,total,idcliente,login,vendedor1,notas,local,tipo,nameopc,estado,localdst,canal,tipopago1,idruccli,iddircli,moneda) values(now(),'$tota','$login','$Xlogin','$Xlogin','$nota','$local','PE','$nopc','105','','$cana','$fpago','$idruc','$iddest','$Xmoneda')";
		}
		qry($qry);
		$idopv=mysql_insert_id();
		if($cana=="RED")
		{
			$idcq=qry("select idcliente from clientes where codigo1='$login'");
			$ids=array();
			while($idc1=mysql_fetch_row($idcq))
			{
				$ids[]=$idc1[0];
			}
			$idc=implode(",",$ids);
			
			$peso=peso_ped($Xlogin,$Xlocal,$idbol);
			$pex=($peso[0]/1000);	
			
			$cextr=17;
			if($pex>10)
			{
				$cextr+=ceil($pex-10)*1.5;
			}
			/*
				if(($dep=="LIMA" && $pro=="LIMA") or $dep=="CALLAO"){
				$cextr=15;
				}else{
				$cextr=10;
			}*/
			$acum_temp=$Xtin_acumu+$peso[1];
			$arr['peso']=$pex;
			////////Calculo de puntos////
			$r2=array(0,0,0,0);
			if($Xtinkuy==0){
				$r2=array(0,0,0,0);
				}else{
				$qry1="select descuento1,descuento2,descuento3,idnivel from niveles_tinkuy where $acum_temp between limite1 and limite2 and $acum_temp<limite2";
				$res1 = qry($qry1);
				if(mysql_num_rows($res1)>0){
					$r2=mysql_fetch_row($res1);
				}		
			}
			$tab=tabla_prod_desc($Xlogin,$Xlocal,$idbol,$r2[0],$r2[1],$r2[2],$r2[3]);
			$tped=$tab["tped"];
			$tdesc=$tab["tdesc"];
			$tbody=$tab["tbody"];
			$totpuntos=$tped-$tdesc;
			////////
			//$r2=array(0,0,0,0);		
			$cantP=count($prod);
			for($i=0;$i<$cantP;$i++)
			{ qry("insert into stockmovesp(idprod,hora,cantidad,precio,login,idop,local,promo,fraccion,codigo1) values('".$prod[$i][0]."',now(),'".$prod[$i][1]."','".$prod[$i][2]."','$Xlogin','$idopv','$local','".$prod[$i][3]."','".$prod[$i][4]."','{$prod[$i][5]}');"); }
			if($Xtinkuy==0){
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo) values ('$idopv','$login',$tped,now(),3,1) ");
				qry("update user_nivel set tinkuy=3 where login='$login' ");
				}ELSE{
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo) values ('$idopv','$login',$tped,now(),3,7) ");
				qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo) values ('$idopv','$login',$totpuntos,now(),3,8)");				
			}
			save_detallado($Xlogin,$Xlocal,$idbol,$r2[0],$r2[1],$r2[2],$r2[3],$idopv);
			if($tdesc>0)
			{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop) values ('254','1',now(),'$Xlogin','$local','-$tdesc','$idopv') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(254,1,-$tdesc,0);
				//echo $qry;
			}
			/*if(($nroped+0)==0)
				{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('458','1',now(),'$Xlogin','$local','5','$idopv','BOLSA') ";
				$res = qry($qry) or die("4--".mysql_error());
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',458,1,5,5,0,0,0,0,5)");
				$prod[]=array(458,1,5,0);
				}
				ELSE{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('1910','1',now(),'$Xlogin','$local','5','$idopv','EMBALAJE') ";
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',1910,1,5,5,0,0,0,0,5)");
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(1910,1,5,0);				
			//}*/
			if($transp==1){
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('264','1',now(),'$Xlogin','$local','".$cextr."','$idopv','MOVILIDAD') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(264,1,$cextr,0);	
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',264,1,$cextr,$cextr,0,0,0,0,$cextr)");
				/*
					$ubi=ubigeo($dep,$pro,$dis);
					if($ubi!=false){}
					else
					{exit;}
					$qrydest="insert into destinos(idcliente,direccion,referencia,ubigeo) values('".$idcli."','".$dir."','".$ref."','".$ubi."');";
					mysql_query("insert into destinos(idcliente,direccion,referencia,ubigeo) values('".$idcli."','".$dir."','".$ref."','".$ubi."');");
					$idenv=mysql_insert_id();
				mysql_query("insert into ped_destino(idop,iddestino) values('".$idopv."','".$idenv."');");*/
			}
			
		}
		
		$prod=json_encode($prod);	
		
		$idped=posturl(array("a"=>"pCatalogo","tipo"=>"2","canal"=>$cana,"prod"=>$prod,"idc"=>$login,"local"=>$Xlocal,"log"=>$Xlogin,"not"=>$nota,"qrydest"=>$qrydest,"fpago"=>$fpago,"ruc"=>$ruc,"idruc"=>$idruc,"iddest"=>$iddest,"moneda"=>$Xmoneda),$XXurl_erp1);
		//qry("update user_nivel set hora_in=now() where login='$login' and hora_in is null");
		qry("update operacionesp set idpadre2='$idped', total=(select sum(precio*cantidad) from stockmovesp where idop='$idopv') where idop='$idopv'");
		qry("delete from bolscanal where idbolsa='$idbol' and login='$Xlogin'");
		if($idped>0){
			qry("insert ignore into opsv(idop,n) values ($idped,0)");
		}
		echo "1";
	}
	////////
	
	
	function precioprod($idprod,$pais,$lp=0){
		if($lp==0)
		$lp="";
		$r=qry("select precio$lp,codigo1 from productos$pais where idprod='$idprod';");
		if(mysql_num_rows($r)>0){$r=mysql_fetch_row($r); return $r;}
		else{return array(0,0);}
	}
	
	function listabolsa($idop,$local,$login,$lp)
	{
		$tbody="";
		$ct=1;
		$formp=formap($login,$local);
		$res=qry("select a.idprod,TRIM(UPPER(CONCAT(b.nombre,' ',b.nombre1,' ',b.nombre2,' ',b.nombre3,' ',b.nombre4))) nombre,a.cantidad,round(if(a.promo=0,a.precio,a.precio),2),round((a.cantidad+(a.fraccion/b.unidades))*if(a.promo=0,a.precio,a.precio),2) total,a.promo,if(a.fraccion>0,CONCAT(a.fraccion,b.unmedida),''),UPPER(a.cantidad+(a.fraccion/b.unidades)),FLOOR(a.cantidad/b.pack) packs,b.pack from bolscanal a left join productos b on  b.idprod=a.codigo1 where a.login='$login' and a.local='$local' and a.idbolsa='$idop' and a.lista='$lp' group by a.idprod,a.promo order by a.promo,nombre");
		while($temp=mysql_fetch_row($res))
		{
			$mas="";
			if($temp[5]!="0"){
				$ex="info";
				$mas="<td class='text-center'>$temp[2]</td>";
			}
			else{
				if($temp[2]==1)
				{
					$mas="<td class='text-center'>$temp[2]<button type='button' class=' pull-right plus btn btn-xs' ><span class='glyphicon glyphicon-plus'></span></button></td>";		
					}else{
					$mas="<td class='text-center'><button type='button' class='less btn btn-xs pull-left' ><span class='glyphicon glyphicon-minus'></span></button>$temp[2]<button type='button' class=' pull-right plus btn btn-xs' ><span class='glyphicon glyphicon-plus'></span></button></td>";		
				}	
			}
			
			
			if($formp=="P"){ $mas="<td>$temp[8]</td><td>$temp[9]</td><td>$temp[2]</td>"; }
			$tbody .= "<tr $ex promo='$temp[5]' unit='$temp[3]' cant='$temp[7]' class='list $ex' idp='$temp[0]'><td>$ct</td><td>$temp[1]</td>".$mas."<td>$temp[6]</td><td>$temp[3]</td><td>$temp[4]</td><td><button class='btn btn-warning remove btn-xs'><span class='glyphicon glyphicon-remove'></span></button></td></tr>";  $ct++;
		}
		return $tbody;
	}
	
	function listaprod($idbolsa,$local,$login,$lp=0){
		$res=qry("select a.idprod,a.cantidad,a.precio,a.promo,a.fraccion,a.codigo1 from bolscanal a where  a.login='$login' and a.local='$local' and a.idbolsa='$idbolsa' and a.lista='$lp';");
		$prod=array();
		while($temp=mysql_fetch_row($res))
		{ $prod[]=$temp; }
		$res=mysql_fetch_row(qry("select sum(bolscanal.precio*(bolscanal.cantidad+bolscanal.fraccion/productos.unidades)) cant,bolscanal.localdst,bolscanal.lista,canales.nombre from bolscanal,productos,canales where bolscanal.codigo1=productos.idprod and  bolscanal.login='$login' and bolscanal.local='$local' and bolscanal.idbolsa='$idbolsa' and bolscanal.canal=canales.idcanal and bolscanal.lista='$lp';"));
		return array($prod,$res[0],$res[1],$res[2],$res[3]);
	}
	
	function precioprodv($prod){
		$qry="select idprod,precio,promo from stockmovesp where ";
		for($i=0;$i<count($prod);$i++)
		{ $qry .= " (idprod='".$prod[$i][0]."' and promo='".$prod[$i][3]."') or "; }
		$qry=trim($qry," or ");
		$r=qry($qry);
		$d=array();
		while($temp=mysql_fetch_row($r))
		{ $d[$temp[0]][$temp[2]]=$temp[1]; }
		for($i=0;$i<count($prod);$i++)
		{ array_push($prod[$i],$d[$prod[$i][0]][$prod[$i][3]]); }
		return $prod;
	}
	
	function tienepromo($idprod,$c,$Xpais){
		$prod=array();
		if($lp==0)
		$lp="";
		$prom=mysql_fetch_row(qry("select promo from productos$Xpais where idprod=$idprod"));
		if($prom[0]==1){
			$res=qry("select a.idprod,a.precio,a.cantidad,b.codigo1  from kitparts$Xpais a left join productos$Xpais b on a.idprod=b.idprod where a.idkit='$idprod';");
			if(mysql_num_rows($res)>0)
			{while($temp=mysql_fetch_row($res)){$prod[]=array($temp[0],$temp[1],$temp[2]*$c,$temp[3]);} return $prod;}
			else{
				echo "Promocion mal cargada, Reportalo!";
			}
			}else{
			return "0";
		}
	}
	
	function servicio($idprod)
	{
		$r=qry("select idprod from productos where servicio=1 and idprod='$idprod';");
		if(mysql_num_rows($r)>0)
		{return true;}else{return false;}
	}
	
	
	function namecli($idcli)
	{ $r=mysql_fetch_row(qry("select CONCAT(nombre,' ',nombre2) from clientes where idcliente='$idcli';")); return $r[0];}
	
	function unidades($i){
		$res=qry("select unidades from productos where idprod='$i';");
		if(mysql_num_rows($res)>0)
		{$res=mysql_fetch_row($res); return $res[0];}
		else{return "1";}
	}
	
	function formap($login,$local){
		$r=mysql_fetch_row(qry("select locales.formap from bolscanal,locales  where bolscanal.localdst=locales.local and  bolscanal.login='$login' and bolscanal.local='$local' limit 1")); return $r[0];
	}
	
	
	function calculo_precio($n1,$n2,$n3,$Xlogin,$Xlocal,$i,$idc,$nivel){
		$arr=array();
		$qry0="select sum(if((a.promo=0 and c.categoria=1), a.precio*a.cantidad,0))*($n1/100),sum(if((c.categoria=2 and a.promo=0),a.precio*a.cantidad,0))*($n2/100),sum(if((a.promo=0 and c.categoria=4),a.precio*a.cantidad,0))*($n3/100) from bolscanal a,productos c where c.idprod=a.idprod and a.login='$Xlogin' and a.local='$Xlocal' and canal=14 and idbolsa='$i'";
		$res0 = qry($qry0) or die("1--".mysql_error());
		$prespro=mysql_fetch_row($res0);
		//Precio con promo
		if($nivel>0){
			$qry0="select sum(a.precio*a.cantidad*(c.descuento$nivel/100)) from bolscanal a,promo_puntos c where a.promo=c.idprod and a.login='$Xlogin' and a.local='$Xlocal' and a.promo>0 and canal=14 and idbolsa='$i'";
			$res0 = qry($qry0) or die("1--".mysql_error());
			$precpro=mysql_fetch_row($res0);
		}
		/////////			
		//////Anterior
		/////sin promo
		$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*($n1/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*($n2/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*($n3/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
		$res1 = qry($qry1) or die("2--".mysql_error());
		$prespro_past=mysql_fetch_row($res1);
		$nroped=$prespro_past[3];
		//////con promo
		if($nivel>0){
			$qry1="select sum(b.precio*b.cantidad*(c.descuento$nivel/100)) from operacionesp a, stockmovesp b,promo_puntos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = qry($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);
		}			
		///////////			
		///Calculo del descuento Anterior//
		$qrydsc=qry("select sum(b.cantidad* b.precio) from stockmovesp b, operacionesp a where a.idop=b.idop and a.canal='CATALOGO' and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and b.idprod in (254,1598) and idcliente in ($idc) and b.nota2='' and a.estado=107");
		$descant=mysql_fetch_row($qrydsc);
		$descant=$descant[0];
		/////
		$descact=$prespro[0]+$prespro[1]+$prespro[2]+$precpro[0];
		$descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];			
		
		if(($descact_past+$descant)>($tota-$descact))
		$descantneto=round($tota-$descact,3);
		else
		$descantneto=round($descact_past+$descant,3);
		
		$arr["descact_past"]=$descact_past;
		$arr["nroped"]=$nroped;
		$arr["descact"]=$descact;
		$arr["descant"]=$descant;
		return $arr;
	}
	function puntos_tot($Xlogin,$Xlocal,$i){
		$arr=array();
		$res0=qry("select sum(if(a.promo=0,a.precio*a.cantidad*b.puntos,a.precio*a.cantidad)) from bolscanal a,producto_puntos b where a.idprod=b.idprod and a.login='$Xlogin' and a.local='$Xlocal'   and canal=14 and idbolsa='$i' ");
		$rpun=mysql_fetch_row($res0);	
		
		$qry1="select sum(puntos) from puntos a where login='$Xlogin' and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and estado=1";
		$res1 = qry($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		
		$peso=peso_ped($Xlogin,$Xlocal,$i);
		
		$arr["rpun"]=$rpun[0];
		$arr["rpuna"]=$rpuna[0];
		$arr["peso"]=$peso;
		return $arr;
	}
	
	function puntos_regural_general($Xlogin,$Xlocal,$i,$login,$pais=604,$lp=0){
		$res=qry("select id from categorias where idpadre=2");
		$arr_id=array();
		while($r=mysql_fetch_row($res)){
			$arr_id[]=$r[0];
		}
		$cats=implode(",",$arr_id);
		if($cats==""){
			$cats="99999";
		}
		$arr=array();
		
		$res0=qry("select sum(a.precio*a.cantidad),sum(if(d.prof in (1,2,3,4,5,6,7) and b.categoria>0 and b.categoria not in (0) ,a.precio*a.cantidad,if(a.promo=0 and b.categoria not in (0),a.precio*a.cantidad,0))),sum(if(a.promo=0,coalesce(c.puntos,0)*a.cantidad ,if(a.promo>0 and d.prof in (1,2,3,4,5,6,7),coalesce(c.puntos,0)*a.cantidad,0))) from (bolscanal a, productos$pais b) left join productos$pais d on d.idprod=a.promo left join producto_puntos c on c.idprod=a.codigo1 where a.idprod=b.idprod and a.login='$Xlogin' and a.local='$Xlocal' and idbolsa='$i' and a.lista='$lp'");
		$rpun=mysql_fetch_row($res0);
		
		
		$peso=peso_ped($Xlogin,$Xlocal,$i);
		
		$arr["rpun"]=$rpun[0];
		$arr["regular"]=$rpun[1];
		$arr["puntos"]=$rpun[2];
		$arr["peso"]=$peso[0];
		$arr["qry"]="select sum(a.precio*a.cantidad),sum(if(d.prof in (1,2,3,4,5,6,7) and b.categoria>0 and b.categoria not in (0) ,a.precio*a.cantidad,if(a.promo=0 and b.categoria not in (0),a.precio*a.cantidad,0))),sum(if(a.promo=0,coalesce(c.puntos,0)*a.cantidad ,if(a.promo>0 and d.prof in (1,2,3,4,5,7),coalesce(c.puntos,0)*a.cantidad,0))) from (bolscanal a, productos$pais b) left join productos$pais d on d.idprod=a.promo left join producto_puntos c on c.idprod=a.codigo1 where a.idprod=b.idprod and a.login='$Xlogin' and a.local='$Xlocal' and idbolsa='$i' and a.lista='$lp'";
		return $arr;
	}
	
	function validacion_general($Xlogin,$Xlocal,$i,$login,$pais,$iduser,$lp=0){
		$arr=array();
		$gdata=puntos_regural_general($Xlogin,$Xlocal,$i,$login,$pais,$lp);
		$regular=$gdata["regular"];
		$puntos=$gdata["puntos"];
		$totbolsa=$gdata["rpun"];
		$qryyyy=$gdata["qry"];
		$arr["totbolsa"]=$totbolsa;	
		$arr["regular"]=$regular;
		$arr["puntos"]=$puntos;
		//$arr["qry"]=$qryyyy;
		$arr["peso"]=$gdata["peso"];
		$res=qry("select estado,nivel,hora_in,(concat(date(hora_update),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_update),' 23:59:59'),now())) month),deuda from user_nivel where iduser='$iduser'");
		$data=mysql_fetch_row($res);
		$arr["est2"]=$data[0];
		if($data[0]>0){
			if($data[2]!=""){
				if($data[4]==0){
					$qry1="select sum(if(a.estado=1,1,0)),sum(if(a.estado=3,1,0)) from puntos  a, user_nivel b where a.iduser=b.iduser and  a.iduser='$iduser' and a.tipo=3 and a.fecha_venc>'$data[3]'";
					$res1 = qry($qry1);
					$nped=mysql_fetch_row($res1);
					if($nped[0]>0){
						$arr["msg"]="Ya Ingreso Pedido Del Mes";
						$arr["est"]=4;
						}elseif($nped[1]>0){
						$arr["msg"]="Ya Ingreso Pedido Del Mes, a la Espera de la confirmación";
						$arr["est"]=4;
						}elseif($nped[0]==0){
						$arr["est"]=3;
					}
					$arr["nped"]=$nped[0];
					
					$qryup=mysql_fetch_row(qry("select count(*) from puntos where iduser='$iduser' and SUBSTRING(curdate(),1,7)=SUBSTRING(fecha_venc,1,7) and tipo=9 and estado=1"));
					if($qryup[0]>0){
						$arr["est"]=5;
					} 					
				}else{
					$qry1="select sum(if(a.estado=1,1,0)),sum(if(a.estado=3,1,0)) from puntos  a, user_nivel b where a.iduser=b.iduser and  a.iduser='$iduser' and a.tipo=3 and a.hora>'$data[3]' and date('$data[3]')=a.fecha_venc";
					$res1 = qry($qry1);
					$nped=mysql_fetch_row($res1);
					if($nped[0]>0){
						$arr["msg"]="Ya Ingreso Pedido Del Mes";
						$arr["est"]=4;
						}elseif($nped[1]>0){
						$arr["msg"]="Ya Ingreso Pedido Del Mes, a la Espera de la confirmación";
						$arr["est"]=4;
						}elseif($nped[0]==0){
						$arr["msg"]="No ha ingresado el Autoconsumo de este mes";
						$arr["est"]=3;						
					}
				}
				}else{
				$qry1="select sum(if(a.estado=1,1,0)),sum(if(a.estado=3,1,0)) from puntos  a, usuarios b where  a.iduser=b.iduser and  a.iduser='$iduser' and a.tipo=3";
				$res1 = qry($qry1);
				$nped=mysql_fetch_row($res1);
				if($nped[1]>0){
					$arr["msg"]="Ya se Ingreso su primer Pedido, Esperando Validacion";
					$arr["est"]=4;
					}else{
					$arr["msg"]="Empresario Sin Nivel, Ingrese su primer Pedido";
					$arr["est"]=2;					
				}
			}
		}
		else{
			$arr["msg"]="Empresario Desactivado, Ingrese Nuevamente su Pedido inicial";
			$arr["est"]=2;
		}
		
		$arr["nivel"]=$data[1];
		return $arr;
	}
	
	function tabla_prod($Xlogin,$Xlocal,$i,$n1,$n2,$n3,$pais=604,$lp=0){
		
		/////Lista de Productos
		$arr=array();
		$qrybp="select a.codigo1,c.nombre,if(a.promo=0,coalesce(d.puntos,0)*a.cantidad ,if(a.promo>0 and b.prof in (1,2,3,4,5,6,7),coalesce(d.puntos,0)*a.cantidad,0)),a.precio*a.cantidad,0,0,if((a.promo=0 and c.categoria!=2), 1,if((c.categoria=2 and a.promo=0),2,if(a.promo!=0,3,0))) from (bolscanal a,productos$pais c) left join productos$pais b on b.idprod=a.promo  left join producto_puntos d on d.idprod=c.codigo1 where c.idprod=a.idprod and a.login='$Xlogin' and a.local='$Xlocal' and idbolsa='$i' and a.lista='$lp'";
		$tbody="";
		$qq1=qry($qrybp);
		$tped=0;
		$tped1=0;
		while($qq=mysql_fetch_row($qq1))
		{
			$tped+=$qq[3];
			$tped1+=$qq[2];
			$color="";
			if($qq[6]==3)
			{
				$color="class='info'";
			}
			elseif($qq[6]==2){
				$color="class='success'";		
			}
			else{
				$color="class=''";
			}
			$tbody.="<tr $color>";
			$temp=count($qq)-3;
			for($i=0;$i<$temp;$i++)
			{
				$tbody.="<td>$qq[$i]</td>";
			}
			$tbody.="<td>".($qq[3]-$qq[5])."</td>";
			$tbody.="</tr>";
		}
		$arr["tbody"]=$tbody;
		$arr["tped"]=$tped;
		$arr["tped1"]=$tped1;
		return $arr;
	}
	
	
	
	function capitalizate($Xlogin,$puntos,$puntost){
		$prop[2]=0;
		$res=qry("select if(now() between min(hora) and concat(year(date_add(min(hora),interval 2 month)),'-',month(date_add(min(hora),interval 2 month)),'-05') or count(*)=0,1,0),count(*),sum(puntos) from puntos where login='$Xlogin' and estado=1");
		$prop=mysql_fetch_row($res);
		$arr=array();
		$capti_prod=array();
		if($prop[0]==1 and $puntos>=400){
			if($prop[2]>0)
			$prop[2]=$prop[2];
			else
			$prop[2]=0;
			// if( 1==1){
			$res=qry("select if(count(*)=sum(if(puntos>=400,1,0)) or count(*)=0,1,0),count(*) from puntos where login='$Xlogin'  and estado=1");
			$res=qry("select count(*)   from puntos where login='$Xlogin'  and estado=1 and puntos >=400");
			$ped=mysql_fetch_row($res);
			$tor=floor($puntos/400);
			/*if($ped[0]>=0)
			{*/	
			$options="<label for='capital'>CAPITALIZATE: $prop[1] $prop[2] $puntos </label>";
			$res=qry("select a.idprod,a.precio,a.nroped,b.nombre,b.unidades from capital a, productos b where a.idprod=b.idprod and nroped between ($prop[1]+1) and (floor(($puntos+$prop[2])/400)) and a.idprod not in (select idprod from stockmovesp a, operacionesp b where a.idop=b.idop and  b.idcliente='$Xlogin' and nota2=concat('CAPITAL-',nroped) and a.estado=1) order by nroped limit $tor");
			//echo "select a.idprod,a.precio,a.nroped,b.nombre,b.unidades from capital a, productos b where a.idprod=b.idprod and nroped between ($ped[1]+1) and ($ped[1]+floor($puntos/400)) and a.idprod not in (select idprod from stockmovesp a, operacionesp b where a.idop=b.idop and  b.idcliente='$Xlogin' and nota2=concat('CAPITAL-',nroped) and a.estado=1)";
			$i=0;
			while($r=mysql_fetch_row($res))
			{
				$capti_prod[$i]=$r;
				
				$options.="<div class='input-group'>
				<span class='input-group-addon'>
				<input type='checkbox' class='capital' data-ped='$i' value='$r[2]-$r[0]' />
				
				</span>
				<input type='text' value='$r[3] x S/.$r[1]' disabled class='form-control'>
				</div>";
				//$options.="<option value='$i'>$r[3] x S/.$r[1](Pedido:$r[2])</option>";
				$i++;
			}
			$options.="";
			$arr["options"]=$options;
			$arr["capti_prod"]=json_encode($capti_prod);
			return $arr;
			/*}
				else 
				{
				return 0;
			}*/
			
		}
		else{
			return 0;
		}
	}
	
	function peso_ped($Xlogin,$Xlocal,$i,$pais=604,$lp=0){
		$arr=array();
		$res0=qry("select sum(a.cantidad*coalesce(e.unidades,0)),sum(if(a.promo=0,coalesce(d.puntos,0)*a.cantidad ,if(a.promo>0 and b.prof in (1,2,3,4,5,6,7),coalesce(d.puntos,0)*a.cantidad,0))),sum(a.cantidad*a.precio) from (bolscanal a,productos$pais c) left join productos$pais b on b.idprod=a.promo  left join producto_puntos d on d.idprod=c.codigo1 left join productos e on c.codigo1=e.idprod where a.idprod=c.idprod and  a.login='$Xlogin' and a.local='$Xlocal' and a.lista='$lp' and idbolsa='$i'");
		$rpun=mysql_fetch_row($res0);		
		//$rpun[0];
		return $rpun;
	}
	
	function ubigeo($dep,$pro,$dis){
		$res=mysql_query("select id from ubigeo where departamento='".$dep."' and provincia='".$pro."' and distrito='".$dis."';");
		if(mysql_num_rows($res)>0)
		{$res=mysql_fetch_row($res); return $res[0];}
		else
		{return false;}
	}
	
	function save_detallado($Xlogin,$Xlocal,$i,$n1,$n2,$n3,$nivel,$idop){
		$res=qry("select id from categorias where idpadre=2");
		$arr_id=array();
		while($r=mysql_fetch_row($res)){
			$arr_id[]=$r[0];
		}
		$cats=implode(",",$arr_id);
		if($cats==""){
			$cats="99999";
		}	
		/////Lista de Productos
		
		$qrybp="insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo) select $idop,c.idprod,a.cantidad,a.precio,a.precio*a.cantidad,if((a.promo=0 and c.categoria not in ($cats)), (a.precio*a.cantidad),if((c.categoria in ($cats) and a.promo=0),(a.precio*a.cantidad),if((a.promo=0 and c.categoria=100),(a.precio*a.cantidad),if(a.promo>0,a.precio*a.cantidad,0)))),if((a.promo=0 and c.categoria not in ($cats)),$n1,if((c.categoria in ($cats) and a.promo=0),$n2,if(a.promo>0,$n3,0))),if((a.promo=0 and c.categoria not in ($cats)), (a.precio*a.cantidad)*($n1/100),if((c.categoria in ($cats) and a.promo=0),(a.precio*a.cantidad)*($n2/100),if((a.promo>0),(a.precio*a.cantidad)*($n3/100),if(a.promo>0,0 ,0)))), a.promo from (bolscanal a,productos c) left join promo_puntos b on a.promo=b.idprod left join producto_puntos d on d.idprod=a.idprod where c.idprod=a.codigo1 and a.login='$Xlogin' and a.local='$Xlocal' and idbolsa='$i'";
		$qq1=qry($qrybp);
		qry("update pedido_detalle set total=monto-descuento where idop=$idop");
		return $qq1;
	}
	function tabla_prod_desc($Xlogin,$Xlocal,$i,$n1,$n2,$n3,$nivel,$pais=604,$lp=0){
		/////Lista de Productos
		$res=qry("select id from categorias where idpadre=2");
		$arr_id=array();
		while($r=mysql_fetch_row($res)){
			$arr_id[]=$r[0];
		}
		$cats=implode(",",$arr_id);
		if($cats==""){
			$cats="99999";
		}
		$arr=array();
		$qrybp="select codigo2,c.nombre,a.cantidad*coalesce(if(a.promo=0,d.puntos,0),0),a.precio*a.cantidad,if((a.promo=0 and c.categoria not in ($cats)),$n1,if((c.categoria in ($cats) and a.promo=0),$n2,if(a.promo>0 ,$n3,0))),if((a.promo=0 and c.categoria not in ($cats)), (a.precio*a.cantidad)*($n1/100),if((c.categoria in ($cats) and a.promo=0),(a.precio*a.cantidad)*($n2/100),if(a.promo>0 ,(a.precio*a.cantidad)*($n3/100),0))),if((a.promo=0 and c.categoria!=2), 1,if((c.categoria=2 and a.promo=0),2,if(a.promo>0,3,0))) from (bolscanal a,productos c) left join producto_puntos d on c.idprod=d.idprod  where c.idprod=a.codigo1 and a.login='$Xlogin' and a.local='$Xlocal'   and canal=14 and a.lista='$lp' and idbolsa='$i'";
		$tbody="";
		$qq1=qry($qrybp);
		$tped=0;
		$tdesc=0;
		$tpuntos=0;
		while($qq=mysql_fetch_row($qq1))
		{
			$tped+=$qq[3];
			$tpuntos+=$qq[2];
			$color="";
			if($qq[6]==3)
			{
				$color="class='info'";
			}
			elseif($qq[6]==2){
				$color="class='success'";		
			}
			else{
				$color="class=''";
			}
			$tbody.="<tr $color>";
			$temp=count($qq)-1;
			for($i=0;$i<$temp;$i++)
			{
				$tbody.="<td>$qq[$i]</td>";
			}
			$tbody.="<td>".($qq[3]-$qq[5])."</td>";
			$tbody.="</tr>";
			$tdesc+=$qq[5];
		}
		$arr["tbody"]=$tbody;
		$arr["tped"]=$tped;
		$arr["tdesc"]=$tdesc;
		$arr["puntos"]=$tpuntos;
		
		return $arr;
	}
	
	
?>
