<?php
	require("../../sec.php");
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
		$promo=tienepromo($p,$c,$lp);
		$lp=$_POST["lp"];
		$loc=$_POST["loc"];
		
		if($f=="1"){$fr=$cfr;}
		else{$fr="0";}
		
		if($promo=="0")
		{
			if($de[0]=="1")
			{
				qry("insert into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,fraccion,canal,lista,localdst) values('$i','$p',now(),'$c','".$de[1]."','$Xlogin','$Xlocal','$fr','$can','$lp','$loc');");
			}
			else
			{
				qry("insert into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,fraccion,canal,lista,localdst) values('$i','$p',now(),'$c','".precioprod($p,$lp)."','$Xlogin','$Xlocal','$fr','$can','$lp','$loc');");
			}
		}
		else
		{
			for($j=0;$j<count($promo);$j++){ 
				qry("insert into bolscanal(idbolsa,idprod,hora,cantidad,precio,login,local,promo,fraccion,canal,lista,localdst) values('$i','".$promo[$j][0]."',now(),'".$promo[$j][2]."','".$promo[$j][1]."','$Xlogin','$Xlocal','$p','0','$can','$lp','$loc');");
			}
		}
		
		
		echo listabolsa($i,$Xlocal,$Xlogin);
	}
	
	if($a=="plus_prod")
	{
		$p=$_POST["p"];
		$i=$_POST["i"];
		
		$m=$_POST["m"];
		$promo=tienepromo($m,1,'');	
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
		
		echo listabolsa($i,$Xlocal,$Xlogin);
	}
	
	if($a=="less_prod")
	{
		$p=$_POST["p"];
		$i=$_POST["i"];
		
		$m=$_POST["m"];
		$promo=tienepromo($m,1,'');
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
		
		echo listabolsa($i,$Xlocal,$Xlogin);
	}
	
	
	if($a=="qp")
	{
		$p=$_POST["p"];
		$i=$_POST["i"];
		
		$m=$_POST["m"];
		if($m=="0")
		{qry("delete from bolscanal where idprod='$p' and idbolsa='$i' and login='$Xlogin' and local='$Xlocal';");}
		else
		{qry("delete from bolscanal where promo='$m' and idbolsa='$i' and login='$Xlogin' and local='$Xlocal';");}
		
		echo listabolsa($i,$Xlocal,$Xlogin);
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
		$rst=qry("select distinct  idbolsa from bolscanal where login='$Xlogin' and local='$Xlocal' and estado=1 and idbolsa!=0;");
		$html="";
		if(mysql_num_rows($rst)>0)
		{ while($temp=mysql_fetch_row($rst)){ $html .= "<button inf='$temp[0]' class='btn btn-success  ped'>Pedido $temp[0]</button>"; } }
		echo $html;
	}
	
	if($a=="descuento")
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
		//$idc=$idc[0];
		
		////////Calculo de puntos////
		$puns=puntos_regural_general($Xlogin,$Xlocal,$i,$Xlogin);
		$rpun[0]=$puns["rpun"];	
		
		
		///////////////////////////
		if($puns["rpuna"]!="" && $puns["rpuna"]!=NULL){$rpuna[0]=$puns["rpuna"];}else{$rpuna[0]=0;}
		$rpuna[0]=$puns["rpuna"];
		///////////////////////////
		
		
		
		$puntos=$rpun[0]+$rpuna[0];
		////////
		$peso=$puns["peso"]/1000;
		$arr['peso']=$peso;
		if($rpun[0]>=200)
		{
			$r2=array(0,0,0,0);
			$cal_pre=calculo_precio($r2[0],$r2[1],$r2[2],$Xlogin,$Xlocal,$i,$idc,$r2[3]);
			$descact_past=$cal_pre["descact_past"];
			$nroped=$cal_pre["nroped"];
			$descact=$cal_pre["descact"];
			$descant=$cal_pre["descant"];
			//$ret1=capitalizate($Xlogin,$rpun[0],$puntos);
			//$ret=$ret1["options"];
			//$capti_prod=$ret1["capti_prod"];
			$tab=tabla_prod($Xlogin,$Xlocal,$i,$r2[0],$r2[1],$r2[2],$r2[3]);
			$tped=$tab["tped"];
			$tbody=$tab["tbody"];
			
			$tped=$tped+5;		
			
			if(($cal_pre["descact_past"]+$cal_pre["descant"])>($tped-$cal_pre["descact"]))
			$descantneto=round($tped-$descact,3);
			else
			$descantneto=round($cal_pre["descact_past"]+$cal_pre["descant"],3);
			
			/////No considera la perdida anterior
			if($descantneto<0){
				$descantneto=0;
			}
			////
			
			$adic="<tr><td></td><td>COSTO DE EMBALAJE</td><td>0</td><td>5</td><td>5</td></tr>";
			
			
			$linea="<h3>Numero de Pedido:".($cal_pre["nroped"]+1)."</h3><table class='table table-bordered'><thead><th class='tabla1'>Cod. Prod.</th><th class='tabla1'>Nombre</th><th class='tabla1'>Puntos</th><th class='tabla1'>Monto</th><th class='tabla1'>Total</th></thead>$tbody $adic
			<tr class='tabla2'><th></th><th>Actual</th><td class='tabla2'>$rpun[0]</td><td class='tabla2'>".($tped)."</td><td class='tabla2'>".($tped-$descact)."</td></tr>
			<tr class='tabla2'><th></th><th>Acumulado</th><td class='tabla2'>$rpuna[0]</td><td class='tabla2'>0</td><td class='tabla2'>".($descantneto)."</td></tr>
			<tr class='tabla2'><th colspan='2'>TOTAL</th><td class='tabla2'>".($rpuna[0]+$rpun[0])."</td><td class='tabla2'>".($tped)."</td><th class='tabla2'>".(($tped)-( $descantneto+$descact))."</th></tr></table>";
			//echo $linea;
			$ttro= microtime(true)-$ttime;
			$ttt=$ttro;
			
			$arr['capti_prod']=$capti_prod;
			$arr['capitalizate']=$ret;
			$arr['total']=($tped)-( $descantneto+$descact);
			$arr['text']=$linea;
			$arr['est']=1;
		}
		else{
			$arr['text']="Se Necesita un pedido minimo  de 200 Puntos";
			$arr['est']=0;
		}
		echo json_encode($arr);
	}
	
	
	/////Descuento General
	if($a=="descuento_general")
	{
		$ttime=microtime(true);
		$i=$_POST["i"];
		$login=$_POST["socio"];
		$idcq=qry("select idcliente,nombre from clientes where codigo1='$login'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
			$opcli.="<option value='$idc1[0]'>$idc1[1]($idc1[0])</option>";
		}
		$idc=implode(",",$ids);
		//$idc=$idc[0];
		/*
			$idped=posturl(array("a"=>"list_","canal"=>$cana,"prod"=>$prod,"idc"=>$login,"local"=>$Xlocal,"log"=>$Xlogin,"not"=>$nota,"qrydest"=>$qrydest,"fpago"=>$fpago,"ruc"=>$ruc),$XXurl_erp1);	
		*/
		
		////////Calculo de puntos////
		$puns=validacion_general($Xlogin,$Xlocal,$i,$login);
		$rpun[0]=$puns["rpun"];	
		///////////////////////////
		if($puns["rpuna"]!="" && $puns["rpuna"]!=NULL){$rpuna[0]=$puns["rpuna"];}else{$rpuna[0]=0;}
		$rpuna[0]=$puns["rpuna"];
		///////////////////////////
		$puntos=$rpun[0]+$rpuna[0];
		////////
		$peso=$puns["peso"];
		$arr['peso']=$peso/1000;
		
		if(($puns["totbolsa"]>=$Xlimit and $puns["est"]==1) or ($puns["est"]==2 and $puns["totbolsa"]>=$puns["minnivel"] ) or ($puns["est"]==3))
		{
			$r2=array(0,0,0,0);
			if($puns["est"]==3){
				$qry1="select descuento1,descuento2,descuento3,idnivel from niveles where idnivel={$puns['nivel']}";
				$res1 = qry($qry1);
				$r2=mysql_fetch_row($res1);
			}
			
			$cal_pre=calculo_precio($r2[0],$r2[1],$r2[2],$Xlogin,$Xlocal,$i,$idc,$r2[3]);
			$descact_past=$cal_pre["descact_past"];
			$nroped=$cal_pre["nroped"];
			$descact=$cal_pre["descact"];
			$descant=$cal_pre["descant"];
			//$ret1=capitalizate($login,$rpun[0],$puntos);
			//$ret=$ret1["options"];
			//$capti_prod=$ret1["capti_prod"];
			$tab=tabla_prod($Xlogin,$Xlocal,$i,$r2[0],$r2[1],$r2[2],$r2[3]);
			$tped=$tab["tped"];
			$tbody=$tab["tbody"];
			
			$tped=$tped+5;		
			
			
			$adic="<tr><td></td><td>COSTO DE EMBALAJE</td><td>0</td><td>5</td><td>5</td></tr>";
			
			
			$linea="<h3>Numero de Pedido:".($cal_pre["nroped"]+1)."</h3><table class='table table-bordered'><thead><th class='tabla1'>Cod. Prod.</th><th class='tabla1'>Nombre</th><th class='tabla1'>Puntos</th><th class='tabla1'>Monto</th><th class='tabla1'>Total</th></thead>$tbody $adic
			<tr class='tabla2'><th></th><th>Actual</th><td class='tabla2'>$rpun[0]</td><td class='tabla2'>".($tped)."</td><td class='tabla2'>".($tped-$descact)."</td></tr>
			<tr class='tabla2'><th></th><th>Acumulado</th><td class='tabla2'>$rpuna[0]</td><td class='tabla2'>0</td><td class='tabla2'>".($descantneto)."</td></tr>
			<tr class='tabla2'><th colspan='2'>TOTAL</th><td class='tabla2'>".($rpuna[0]+$rpun[0])."</td><td class='tabla2'>".($tped)."</td><th class='tabla2'>".(($tped)-( $descantneto+$descact))."</th></tr></table>";
			//echo $linea;
			$ttro= microtime(true)-$ttime;
			$ttt=$ttro;
			
			//$arr['capti_prod']=$capti_prod;
			//$arr['capitalizate']=$ret;
			$arr['total']=($tped)-( $descantneto+$descact);
			$arr['text']=$linea;
			$arr['est']=1;
			
			
		}
		else{
			$arr['text']=$puns["msg"];
			$arr['est']=0;
		}
		$arr['opcli']=$opcli;
		echo json_encode($arr);
	}
	
	/////////General TRANSPORTE
	
	if($a=="descuento_transporte_general")
	{
		$ttime=microtime(true);
		$i=$_POST["i"];
		$login=$_POST["socio"];	
		$idcq=qry("select idcliente from clientes where codigo1='$login'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		////////Calculo de puntos////
		$puns=puntos_tot_general($Xlogin,$Xlocal,$i,$login);	
		$rpun[0]=$puns["rpun"];	
		///////////////////////////
		if($puns["rpuna"]!="" && $puns["rpuna"]!=NULL){$rpuna[0]=$puns["rpuna"];}else{$rpuna[0]=0;}
		$rpuna[0]=$puns["rpuna"];
		///////////////////////////
		$puntos=$rpun[0]+$rpuna[0];
		////////
		$peso=$puns["peso"];
		$arr['peso']=$peso;
		
		if($rpun[0]>=200)
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
				
				/////No considera la perdida anterior
				if($descantneto<0){
					$descantneto=0;
				}
				////
				
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
				$arr['text']="Se Necesita un pedido mayor a 200 Puntos";
				$arr['est']=0;
			}
		}
		else{
			$arr['text']="Se Necesita un pedido minimo  de 200 Puntos";
			$arr['est']=0;
		}
		echo json_encode($arr);
	}
	
	//////////////Venta General
	
	if($a=="sv_transporte")
	{
		$idbol=$_POST["idbol"];
		$ruc=$_POST["ruc"];
		$infex=$_POST["infex"];
		$login=$_POST["socio"];
		$nota =$_POST["nota"];
		$nopc =$_POST["nopc"];
		$trans =$_POST["trans"];
		$capital =$_POST["capital"];
		$ruc =$_POST["ruc"];
		//////////////////////////////////////////////////////////////	
		if(is_array($capital) && count($capital)>0){
			$qry="select sum(p.unidades) from capital c left join productos p on c.idprod=p.idprod where (";
			$fil="";
			for($i=0;$i<count($capital);$i++){$idpr=explode("-",$capital[$i]); $idpr=$idpr[1]; $fil.=" c.idprod='".$idpr."' or  "; }
			$fil=trim($fil," or ");
			$qry.=$fil.");";
			$qry=mysql_fetch_row(mysql_query($qry));
			$pex=$qry[0]/1000;
		}
		else{$cextr=0;}
		//////////////////////////////////////////////////////////////
		
		$pes=peso_ped($Xlogin,$Xlocal,$idbol);
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
		$qry = "insert into operacionesp(hora,total,idcliente,login,vendedor1,notas,local,tipo,nameopc,estado,localdst,canal,tipopago1,ruc) values(now(),'$tota','$login','$Xlogin','$Xlogin','$nota','$local','PE','$nopc','105','$ldes','$cana','$fpago','$ruc')";
		qry($qry);
		$idopv=mysql_insert_id();
		if($cana=="CATALOGO")
		{
			$idcq=qry("select idcliente from clientes where codigo1='$login'");
			$ids=array();
			while($idc1=mysql_fetch_row($idcq))
			{
				$ids[]=$idc1[0];
			}
			$idc=implode(",",$ids);
			////////Calculo de puntos////
			$puns=puntos_regural_general($Xlogin,$Xlocal,$idbol,$login);	
			$rpun[0]=$puns["rpun"];	
			$rpuna[0]=$puns["rpuna"];
			$puntos=$rpun[0]+$rpuna[0];
			////////
			$peso=$puns["peso"];
			$pex+=($peso/1000);	
			if($pex!="")
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
			{ qry("insert into stockmovesp(idprod,hora,cantidad,precio,login,idop,local,promo,fraccion) values('".$prod[$i][0]."',now(),'".$prod[$i][1]."','".$prod[$i][2]."','$Xlogin','$idopv','$local','".$prod[$i][3]."','".$prod[$i][4]."');"); }
			qry("insert ignore into puntos(idop,login,puntos,hora,estado,tipo) values ('$idopv','$login','$rpun[0]',now(),3,2) ");
			save_detallado($Xlogin,$Xlocal,$idbol,$r2[0],$r2[1],$r2[2],$r2[3],$idopv);
			if($descact>0)
			{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop) values ('254','1',now(),'$Xlogin','$local','-$descact','$idopv') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(254,1,-$descact,0);
				//echo $qry;
			}
			//////Descuento Neto Anterior
			if($descantneto>0)
			{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop) values ('1598','1',now(),'$Xlogin','$local','-$descantneto','$idopv') ";
				$res = qry($qry) or die("4--".mysql_error());
				$prod[]=array(1598,1,-$descantneto,0);
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',1598,1,-$descantneto,-$descantneto,0,0,0,0,-$descantneto)");
				//echo $qry;
			}
			/*if(($nroped+0)==0)
				{
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('458','1',now(),'$Xlogin','$local','5','$idopv','BOLSA') ";
				$res = qry($qry) or die("4--".mysql_error());
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',458,1,5,5,0,0,0,0,5)");
				$prod[]=array(458,1,5,0);
				}
			ELSE{*/
			$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('1910','1',now(),'$Xlogin','$local','5','$idopv','EMBALAJE') ";
			qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',1910,1,5,5,0,0,0,0,5)");
			$res = qry($qry) or die("4--".mysql_error());
			$prod[]=array(1910,1,5,0);				
			//}
			if($trans==1){
				$qry="insert ignore into stockmovesp (idprod,cantidad,hora,login,local,precio,idop,nota2) values ('264','1',now(),'$Xlogin','$local','".$cextr."','$idopv','MOVILIDAD') ";
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
				$qrydest="insert into destinos(idcliente,direccion,referencia,ubigeo) values('".$idcli."','".$dir."','".$ref."','".$ubi."');";
				mysql_query("insert into destinos(idcliente,direccion,referencia,ubigeo) values('".$idcli."','".$dir."','".$ref."','".$ubi."');");
                $idenv=mysql_insert_id();
			    mysql_query("insert into ped_destino(idop,iddestino) values('".$idopv."','".$idenv."');");
			}
			$cantC=count($capital);
			for($i=0;$i<$cantC;$i++)
			{
				$tran=explode("-",$capital[$i]);
				$res=qry("select idprod,precio,nroped from capital where nroped='$tran[0]' and idprod='$tran[1]'");
				$r=mysql_fetch_row($res);
				qry("insert into stockmovesp(idprod,hora,cantidad,precio,login,idop,local,nota2,promo) values('".$r[0]."',now(),1,'".$r[1]."','$Xlogin','$idopv','$local','CAPITAL-$r[2]','2118')");
				$prod[]=array($r[0],1,$r[1],2118);
				qry("insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo,total) values ('$idopv',$r[0],1,$r[1],$r[1],0,0,0,2118,$r[1])");
			}
		}
		
		$prod=json_encode($prod);	
		
		$idped=posturl(array("a"=>"pCatalogo","canal"=>$cana,"prod"=>$prod,"idc"=>$login,"local"=>$Xlocal,"log"=>$Xlogin,"not"=>$nota,"qrydest"=>$qrydest,"fpago"=>$fpago,"ruc"=>$ruc),$XXurl_erp1);
		
		qry("update operacionesp set idpadre2='$idped', total=(select sum(precio*cantidad) from stockmovesp where idop='$idopv') where idop='$idopv'");
		qry("delete from bolscanal where idbolsa='$idbol';");
		
		echo "1";
	}
	
	
	/////////////
	
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
		$qry = "insert into operacionesp(hora,total,idcliente,login,vendedor1,notas,local,tipo,nameopc,estado,localdst,canal,tipopago1,ruc) values(now(),'$tota','$Xidcliente','$Xlogin','$Xlogin','$nota','','PE','$nopc','105','$ldes','$cana','$fpago','$ruc')";
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
			{ qry("insert into stockmovesp(idprod,hora,cantidad,precio,login,idop,local,promo,fraccion) values('".$prod[$i][0]."',now(),'".$prod[$i][1]."','".$prod[$i][2]."','$Xlogin','$idopv','$Xlocal','".$prod[$i][3]."','".$prod[$i][4]."');"); }
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
		
		$idped=posturl(array("a"=>"pCatalogo","canal"=>$cana,"prod"=>$prod,"idc"=>$Xidcliente,"local"=>$Xlocal,"log"=>$Xlogin,"not"=>$nota,"qrydest"=>$qrydest,"fpago"=>$fpago,"ruc"=>$ruc),$XXurl_erp1);
		
		qry("update operacionesp set idpadre2='$idped', total=(select sum(precio*cantidad) from stockmovesp where idop='$idopv') where idop='$idopv'");
		qry("delete from bolscanal where idbolsa='$idbol';");
		
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
	
	if($a=="calcenv")
	{
		$dir=$_POST[""];
		$ref=$_POST["rdiref"];
		$dep=$_POST["dep"];
		$pro=$_POST["pro"];
		$dis=$_POST["dis"];
		$val=$_POST["val"];
		$idb=$_POST["i"];
		$tip=$_POST["tip"];
		if($tip=="g"){$idc=$_POST["idc"];
			$log=qry("select codigo1 from clientes where idcliente='$idc'");
			$login=mysql_fetch_row($log);
			$login=$login[0];
			}elseif($tip=="p"){
			$cliq=qry("select idcliente,(select local from usuarios where login='$Xlogin') from clientes where codigo1='$Xlogin' order by lastupdate desc limit 1");
			$idc2=mysql_fetch_row($cliq);
			$idc=$idc2[0];
			$local=$idc2[1];
		}else{}
		
		if(is_array($val) && count($val)>0){
			$qry="select sum(p.unidades),sum(c.precio) from capital c left join productos p on c.idprod=p.idprod where (";
			$fil="";
			for($i=0;$i<count($val);$i++){$idpr=explode("-",$val[$i]); $idpr=$idpr[1]; $fil.=" c.idprod='".$idpr."' or  "; }
			$fil=trim($fil," or ");
			$qry.=$fil.");";
			$qry=mysql_fetch_row(qry($qry));
			$pex=$qry[0]/1000;
			$ccc=$qry[1];
		}
		else{$pex=0; $ccc=0;}
		$res=mysql_query("");
		
		
		//////////////////////////////////////////////////////////////////////////
		if($tip=="g"){
			$puns=puntos_regural_general($Xlogin,$Xlocal,$idb,$login);	
			}elseif($tip=="p"){
			$puns=puntos_regural_general($Xlogin,$Xlocal,$idb,$Xlogin);	
		}
		
		
		
		$rpun[0]=$puns["rpun"];	
		$rpuna[0]=$puns["rpuna"];
		$puntos=$rpun[0]+$rpuna[0];
		$peso=$puns["peso"];
		$arr['peso']=$peso;
		
		$pex+=($peso/1000);	
		if($pex!="")
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
		
		
		if($rpun[0]>=200)
		{
			$r2=array(0,0,0,0);
			$cal_pre=calculo_precio($r2[0],$r2[1],$r2[2],$Xlogin,$Xlocal,$idb,$idc,$r2[3]);
			$descact_past=$cal_pre["descact_past"];
			$nroped=$cal_pre["nroped"];
			$descact=$cal_pre["descact"];
			$descant=$cal_pre["descant"];		
			/////Lista de Productos
			$tab=tabla_prod($Xlogin,$Xlocal,$idb,$r2[0],$r2[1],$r2[2],$r2[3]);
			$tped=$tab["tped"];
			$tbody=$tab["tbody"];
			$tped=$tped+5+$cextr+$ccc;						
			
			if(($descact_past+$descant)>($tped-$descact))
			$descantneto=round($tped-$descact,3);
			else
			$descantneto=round($descact_past+$descant,3);
			
			/////No considera la perdida anterior
			if($descantneto<0){
				$descantneto=0;
			}
			////
			$arr['text']=$linea;
			$arr['total']=($tped)-( $descantneto+$descact);
			$arr['est']=1;
			
		}
		else{
			$arr['text']="Se Necesita un pedido mayor a 200 Puntos";
			$arr['est']=0;
		}
		///////////////////////////////////////////////////////////////////////////////
		
		
		echo json_encode(array($arr['total'],$cextr,$pex));	
	}
	
	function precioprod($idprod,$lp){
		if($lp==0)
		$lp="";
		$r=qry("select precio$lp from productos where idprod='$idprod';");
		if(mysql_num_rows($r)>0){$r=mysql_fetch_row($r); return $r[0];}
		else{return 0;}
	}
	
	function listabolsa($idop,$local,$login)
	{
		$tbody="";
		$ct=1;
		$formp=formap($login,$local);
		//$res=qry("select a.idprod,TRIM(UPPER(CONCAT(b.nombre,' ',b.nombre1,' ',b.nombre2,' ',b.nombre3,' ',b.nombre4))) nombre,a.cantidad,if(a.promo=0,a.precio,c.precio),(a.cantidad+(a.fraccion/b.unidades))*if(a.promo=0,a.precio,c.precio) total,a.promo,if(a.fraccion>0,CONCAT(a.fraccion,b.unmedida),''),UPPER(a.cantidad+(a.fraccion/b.unidades)),FLOOR(a.cantidad/b.pack) packs,b.pack from bolscanal a,productos b,kitparts c where b.idprod=a.idprod and a.login='$login' and a.local='$local' and a.estado=1 and a.idbolsa='$idop' and if(a.promo=0,1,if(a.promo=c.idkit and a.idprod=c.idprod,1,0)) group by a.idprod,a.promo order by a.promo,nombre");
		$res=qry("select a.idprod,TRIM(UPPER(CONCAT(b.nombre,' ',b.nombre1,' ',b.nombre2,' ',b.nombre3,' ',b.nombre4))) nombre,a.cantidad,if(a.promo=0,a.precio,c.precio),(a.cantidad+(a.fraccion/b.unidades))*if(a.promo=0,a.precio,c.precio) total,a.promo,if(a.fraccion>0,CONCAT(a.fraccion,b.unmedida),''),UPPER(a.cantidad+(a.fraccion/b.unidades)),FLOOR(a.cantidad/b.pack) packs,b.pack from (bolscanal a,productos b) left join kitparts c on a.promo=c.idkit and a.idprod=c.idprod where b.idprod=a.idprod and a.login='$login' and a.local='$local' and a.estado=1 and a.idbolsa='$idop' group by a.idprod,a.promo order by a.promo,nombre");
		//echo "select a.idprod,TRIM(UPPER(CONCAT(b.nombre,' ',b.nombre1,' ',b.nombre2,' ',b.nombre3,' ',b.nombre4))) nombre,a.cantidad,if(a.promo=0,a.precio,c.precio),(a.cantidad+(a.fraccion/b.unidades))*if(a.promo=0,a.precio,c.precio) total,a.promo,if(a.fraccion>0,CONCAT(a.fraccion,b.unmedida),''),UPPER(a.cantidad+(a.fraccion/b.unidades)),FLOOR(a.cantidad/b.pack) packs,b.pack from bolscanal a,productos b,kitparts c where b.idprod=a.idprod and a.login='$login' and a.local='$local' and a.estado=1 and a.idbolsa='$idop' and if(a.promo=0,1,if(a.promo=c.idkit and a.idprod=c.idprod,1,0)) group by a.idprod,a.promo order by a.promo,nombre";
		while($temp=mysql_fetch_row($res))
		{
			$mas="";
			if($temp[5]!="0"){
				$ex="info";
				$mas="<td class='text-center'>$temp[2]</td>";
			}
			else{
				
			}
			if($temp[2]==1)
			{
				$mas="<td class='text-center'>$temp[2]<button type='button' class=' pull-right plus btn btn-xs' ><span class='glyphicon glyphicon-plus'></span></button></td>";		
				}else{
				$mas="<td class='text-center'><button type='button' class='less btn btn-xs pull-left' ><span class='glyphicon glyphicon-minus'></span></button>$temp[2]<button type='button' class=' pull-right plus btn btn-xs' ><span class='glyphicon glyphicon-plus'></span></button></td>";		
			}	
			
			if($formp=="P"){ $mas="<td>$temp[8]</td><td>$temp[9]</td><td>$temp[2]</td>"; } $tbody .= "<tr $ex promo='$temp[5]' unit='$temp[3]' cant='$temp[7]' class='list $ex' idp='$temp[0]'><td>$ct</td><td>$temp[1]</td>".$mas."<td>$temp[6]</td><td>$temp[3]</td><td>$temp[4]</td><td><button class='btn btn-warning remove btn-xs'><span class='glyphicon glyphicon-remove'></span></button></td></tr>";  $ct++;
		}
		return $tbody;
	}
	
	function listaprod($idbolsa,$local,$login){
		$res=qry("select bolscanal.idprod,bolscanal.cantidad,bolscanal.precio,bolscanal.promo,bolscanal.fraccion from bolscanal where  bolscanal.login='$login' and bolscanal.local='$local' and bolscanal.idbolsa='$idbolsa';");
		$prod=array();
		while($temp=mysql_fetch_row($res))
		{ $prod[]=$temp; }
		$res=mysql_fetch_row(qry("select sum(bolscanal.precio*(bolscanal.cantidad+bolscanal.fraccion/productos.unidades)) cant,bolscanal.localdst,bolscanal.lista,canales.nombre from bolscanal,productos,canales where bolscanal.idprod=productos.idprod and  bolscanal.login='$login' and bolscanal.local='$local' and bolscanal.idbolsa='$idbolsa' and bolscanal.canal=canales.idcanal;"));
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
	
	function tienepromo($idprod,$c,$lp){
		$prod=array();
		if($lp==0)
		$lp="";
		$res=qry("select kitparts.idprod,kitparts.precio$lp,kitparts.cantidad  from kitparts,productos where  productos.idprod=kitparts.idkit and  productos.promo=1 and idkit='$idprod';");
		if(mysql_num_rows($res)>0)
		{while($temp=mysql_fetch_row($res)){$prod[]=array($temp[0],$temp[1],$temp[2]*$c);} return $prod;}
		else{return "0";}
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
	
	function posturl($data,$url){
		$postdata = http_build_query($data);
		$opts = array('http' => array('method'  => 'POST','header'  => 'Content-type: application/x-www-form-urlencoded','content' => $postdata));
		$context = stream_context_create($opts);
		$result  = file_get_contents($url,false,$context);
		return $result;
	}
	
	
	function calculo_precio($n1,$n2,$n3,$Xlogin,$Xlocal,$i,$idc,$nivel){
		$arr=array();
		$qry0="select sum(if((a.promo=0 and c.categoria=1), a.precio*a.cantidad,0))*($n1/100),sum(if((c.categoria=2 and a.promo=0),a.precio*a.cantidad,0))*($n2/100),sum(if((a.promo=0 and c.categoria=4),a.precio*a.cantidad,0))*($n3/100) from bolscanal a,productos c where c.idprod=a.idprod and a.login='$Xlogin' and a.local='$Xlocal' and canal=9 and idbolsa='$i'";
		$res0 = qry($qry0) or die("1--".mysql_error());
		$prespro=mysql_fetch_row($res0);
		//Precio con promo
		if($nivel>0){
			$qry0="select sum(a.precio*a.cantidad*(c.descuento$nivel/100)) from bolscanal a,promo_puntos c where a.promo=c.idprod and a.login='$Xlogin' and a.local='$Xlocal' and a.promo>0 and canal=9 and idbolsa='$i'";
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
		$res0=qry("select sum(if(a.promo=0,a.precio*a.cantidad*b.puntos,a.precio*a.cantidad)) from bolscanal a,producto_puntos b where a.idprod=b.idprod and a.login='$Xlogin' and a.local='$Xlocal'   and canal=9 and idbolsa='$i' ");
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
	
	function puntos_regural_general($Xlogin,$Xlocal,$i,$login){
		$arr=array();
		$res0=qry("select sum(a.precio*a.cantidad) from bolscanal a, productos b where a.idprod=b.idprod and b.categoria=1 and a.login='$Xlogin' and a.local='$Xlocal'  and canal=9 and idbolsa='$i' ");
		$rpun=mysql_fetch_row($res0);	
		$qry1="select sum(puntos) from puntos a, usuarios b where a.login=b.login and  a.login='$login' and a.hora between b.ffii and date_add(b.ffii, interval 3 month) and a.estado=1 and a.tipo=2";
		//$qry1="select sum(puntos) from puntos a where login='$login' and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and estado=1 and tipo=2";
		$res1 = qry($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		
		$peso=peso_ped($Xlogin,$Xlocal,$i);
		
		$arr["rpun"]=$rpun[0];
		$arr["rpuna"]=$rpuna[0];
		$arr["peso"]=$peso;
		return $arr;
	}
	
	function validacion_general($Xlogin,$Xlocal,$i,$login){
		$arr=array();
		$res0=qry("select sum(a.precio*a.cantidad) from bolscanal a, productos b where a.idprod=b.idprod and b.categoria=1 and a.login='$Xlogin' and a.local='$Xlocal'  and canal=9 and idbolsa='$i' ");
		$totbolsa=mysql_fetch_row($res0);
		
		$res=qry("select estado,nivel from user_nivel where login='$login'");
		$data=mysql_fetch_row($res);
		if($data[0]>0){
			if($data[1]>0)
			{
				$qry1="select count(*) from puntos  a, usuarios b where a.login=b.login and  a.login='$login' and a.estado=1 and a.tipo=3 and a.hora>concat('2015-',month(now()),'-01')";
				$res1 = qry($qry1);
				$nped=mysql_fetch_row($res1);
				if($nped[0]>1){
					$arr["est"]=3;
					}else{	
					$qry1="select minimo from niveles where idnivel=$data[1]";
					$res1 = qry($qry1);
					$minnivel=mysql_fetch_row($res1);
					$arr["minnivel"]=$minnivel[0];
					$arr["msg"]="Falta para su Auto Consumo Personal";
					$arr["est"]=2;
				}
				$arr["nped"]=$nped[0];
			}
			else{
				$arr["msg"]="Empresario Sin Nivel, Ingrese su primer Pedido";
				$arr["est"]=1;
			}
		}
		else{
			$arr["msg"]="Empresario Desactivado, Ingrese Nuevamente su Pedido inicial";
			$arr["est"]=1;
		}
		
		$peso=peso_ped($Xlogin,$Xlocal,$i);
		$arr["totbolsa"]=$totbolsa[0];	
		$arr["peso"]=$peso;
		$arr["nivel"]=$data[1];
		return $arr;
	}
	
	function tabla_prod($Xlogin,$Xlocal,$i,$n1,$n2,$n3,$nivel){
		/////Lista de Productos
		$arr=array();
		$qrybp="select codigo2,c.nombre,if((a.promo=0 and c.categoria=1), (a.precio*a.cantidad*d.puntos),if((c.categoria=2 and a.promo=0),(a.precio*a.cantidad*d.puntos),if((a.promo=0 and c.categoria=4),(a.precio*a.cantidad*d.puntos),if(a.promo>0,a.precio*a.cantidad,0)))),a.precio*a.cantidad,0,0,if((a.promo=0 and c.categoria!=2), 1,if((c.categoria=2 and a.promo=0),2,if(a.promo!=0,3,0))) from (bolscanal a,productos c) left join promo_puntos b on a.promo=b.idprod left join producto_puntos d on d.idprod=a.idprod where c.idprod=a.idprod and a.login='$Xlogin' and a.local='$Xlocal'   and canal=9 and idbolsa='$i'";
		$tbody="";
		$qq1=qry($qrybp);
		$tped=0;
		while($qq=mysql_fetch_row($qq1))
		{
			$tped+=$qq[3];
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
	
	function peso_ped($Xlogin,$Xlocal,$i){
		$arr=array();
		$res0=qry("select sum(a.cantidad*b.unidades) from bolscanal a,productos b where a.idprod=b.idprod and a.login='$Xlogin' and a.local='$Xlocal'   and canal=9 and idbolsa='$i' ");
		$rpun=mysql_fetch_row($res0);		
		$rpun[0];
		
		return $rpun[0];
	}
	
	function ubigeo($dep,$pro,$dis){
		$res=mysql_query("select id from ubigeo where departamento='".$dep."' and provincia='".$pro."' and distrito='".$dis."';");
		if(mysql_num_rows($res)>0)
		{$res=mysql_fetch_row($res); return $res[0];}
		else
		{return false;}
	}
	
	function save_detallado($Xlogin,$Xlocal,$i,$n1,$n2,$n3,$nivel,$idop){
		/////Lista de Productos
		
		$qrybp="insert into pedido_detalle(idop,idprod,cantidad,precio,monto,puntos,porcentaje,descuento,promo) select $idop,c.idprod,a.cantidad,a.precio,a.precio*a.cantidad,if((a.promo=0 and c.categoria=1), (a.precio*a.cantidad*d.puntos),if((c.categoria=2 and a.promo=0),(a.precio*a.cantidad*d.puntos),if((a.promo=0 and c.categoria=4),(a.precio*a.cantidad*d.puntos),if(a.promo>0,a.precio*a.cantidad,0)))),if((a.promo=0 and c.categoria=1),$n1,if((c.categoria=2 and a.promo=0),$n2,if((a.promo=0 and c.categoria=4),$n3,if(a.promo>0,0 ,0)))),if((a.promo=0 and c.categoria=1), (a.precio*a.cantidad)*($n1/100),if((c.categoria=2 and a.promo=0),(a.precio*a.cantidad)*($n2/100),if((a.promo=0 and c.categoria=4),(a.precio*a.cantidad)*($n3/100),if(a.promo>0,0 ,0)))), a.promo from (bolscanal a,productos c) left join promo_puntos b on a.promo=b.idprod left join producto_puntos d on d.idprod=a.idprod where c.idprod=a.idprod and a.login='$Xlogin' and a.local='$Xlocal'   and canal=9 and idbolsa='$i'";
		$qq1=qry($qrybp);
		qry("update pedido_detalle set total=monto-descuento where idop=$idop");
		return $qq1;
	}
	
?>
