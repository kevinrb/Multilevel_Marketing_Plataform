<?php
	
	function posturl($data,$url)
	{$postdata = http_build_query($data);
		$opts = array('http' => array('method'  => 'POST','header'  => 'Content-type: application/x-www-form-urlencoded','content' => $postdata));
		$context = stream_context_create($opts);
		$result  = file_get_contents($url,false,$context);
	return $result;}
	
	function upda_ped(){
		////
		$result=posturl(array("a"=>"update_ped","canal"=>"RED"),$GLOBALS["XXurl_erp1"]);
		$result;
		$resul=json_decode($result);
		$tgt=0;
		foreach($resul as $a=>$b )
		{
			print "$tgt \n";
			$tgt++;
			mysql_query("update operacionesp set estado=$b where idpadre2=$a");
			if(mysql_affected_rows()>0){
				$idop=mysql_fetch_row(mysql_query("select idop from operacionesp where idpadre2=$a"));
				$idp=$idop[0];
				if($b==2)
				{
					mysql_query("update operacionesp set horaanul=now() where idpadre2=$a");
					mysql_query("update stockmovesp a, operacionesp b set a.estado=b.estado where a.idop=b.idop and idpadre2=$a");
					mysql_query("update puntos set estado=2 where idop='$idp'");
					if(mysql_affected_rows()>0){
						mysql_query("update (puntos a, user_nivel b) left join user_nivel_day c on c.fecha=date(b.hora_confirm - interval 1 day) and b.iduser=c.iduser set b.deuda=coalesce(c.deuda,1),b.hora_update=c.hora_update where  a.iduser=b.iduser and a.tipo=3 and now()>(concat(date(hora_update),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_update),' 23:59:59'),now())) month) and date(concat(date(hora_update),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_update),' 23:59:59'),now())) month)=fecha_venc and a.estado=2 and hora_confirm>'2017-04-01' and a.idop='$idp'");
						mysql_query("update user_nivel a,puntos b set a.hora_in=null,a.idop=0,a.monto=0,a.nivel=null,a.hora_update=null,deuda=0 where a.iduser=b.iduser and b.idop='$idp' and b.tipo=3 and b.fecha_venc=date(a.hora_in) and b.fecha_venc is not null and a.hora_in is not null");		
					}
				}
				if($b==107)
				{
					mysql_query("update puntos set estado=1,hora_confirm=now() where idop='$idp' and estado!=1");
					if(mysql_affected_rows()>0){
						mysql_query("update user_nivel a,puntos b set a.monto=b.puntos, a.idop=b.idop,a.hora_update=now(),a.hora_in=now(),b.fecha_venc=curdate() where a.iduser=b.iduser and b.idop='$idp' and a.hora_in is null and b.tipo in (3,4,9) and b.estado=1 and a.estado=1");
						mysql_query("update user_nivel a,puntos b set a.monto=b.puntos, a.idop=b.idop,a.hora_update=now(),a.hora_in=now(),b.fecha_venc=curdate(),a.estado=1 where a.iduser=b.iduser and b.idop='$idp' and a.hora_in is null and b.tipo im (3,4,9) and b.estado=1 and a.estado=0");	
						mysql_query("update user_nivel a,puntos b set a.deuda=0,a.hora_update=now(),b.fecha_venc=curdate() where a.iduser=b.iduser and b.idop='$idp' and b.tipo in (3,4,9) and b.estado=1 and a.deuda>0");			
					}
					
				}
			}
		}
		////
		$result=posturl(array("a"=>"getopsv","canal"=>"RED"),$GLOBALS["XXurl_erp1"]);
		$result;
		$resul=json_decode($result);
		foreach($resul as $a=>$b )
		{
			$bb=explode("----",$b);
			mysql_query("update opsv set n=$bb[0] where idop=$a");
			if(mysql_affected_rows()>0){
				$idop=mysql_fetch_row(mysql_query("select idop from operacionesp where idpadre2=$a"));
				$idp=$idop[0];
				if($bb[0]==0)
				{
					mysql_query("update operacionesp set horaanul=now() where idpadre2=$a");
					mysql_query("update stockmovesp a, operacionesp b set a.estado=b.estado where a.idop=b.idop and idpadre2=$a");
					mysql_query("update puntos set estado=2 where idop='$idp'");
					if(mysql_affected_rows()>0){
						mysql_query("update (puntos a, user_nivel b) left join user_nivel_day c on c.fecha=date(b.hora_confirm - interval 1 day) and b.iduser=c.iduser set b.deuda=coalesce(c.deuda,1),b.hora_update=c.hora_update where  a.iduser=b.iduser and a.tipo=3 and now()>(concat(date(hora_update),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_update),' 23:59:59'),now())) month) and date(concat(date(hora_update),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_update),' 23:59:59'),now())) month)=fecha_venc and a.estado=2 and hora_confirm>'2017-04-01' and a.idop='$idp'");
						mysql_query("update user_nivel a,puntos b set a.hora_in=null,a.idop=0,a.monto=0,a.nivel=null,a.hora_update=null,deuda=0 where a.iduser=b.iduser and b.idop='$idp' and b.tipo=3 and b.fecha_venc=date(a.hora_in)");
					}
				}
				if($bb[0]>0)
				{
					mysql_query("update opsv set hora='$bb[1]' where idop=$a");
					mysql_query("update puntos set estado=1,hora_confirm=now() where idop='$idp' and estado!=1");
					if(mysql_affected_rows()>0){
						mysql_query("update user_nivel a,puntos b set a.monto=b.puntos, a.idop=b.idop,a.hora_update=now(),a.hora_in=now(),b.fecha_venc=curdate() where a.iduser=b.iduser and b.idop='$idp' and a.hora_in is null and b.tipo in (3,4,9) and b.estado=1 and a.estado=1");
						mysql_query("update user_nivel a,puntos b set a.monto=b.puntos, a.idop=b.idop,a.hora_update=now(),a.hora_in=now(),b.fecha_venc=curdate(),a.estado=1 where a.iduser=b.iduser and b.idop='$idp' and a.hora_in is null and b.tipo in (3,4,9) and b.estado=1 and a.estado=0");	
						mysql_query("update user_nivel a,puntos b set a.deuda=0,a.hora_update=now(),b.fecha_venc=curdate() where a.iduser=b.iduser and b.idop='$idp' and b.tipo in (3,4,9) and b.estado=1 and a.deuda>0");
					}
				}
			}		
		}
	}
	
	
	function upda_depo(){
		$result=posturl(array("a"=>"update_depo","canal"=>"CATALOGO"),$GLOBALS["XXurl_erp1"]);
		$resul=json_decode($result);
		$cantdep=count($resul);
		//echo $result;
		for($i=0;$i<$cantdep;$i++)
		{
			mysql_query("update cuentasmove set estado='".$resul[$i][1]."',loginvalido='".$resul[$i][2]."',horavalido='".$resul[$i][3]."',loginliq='".$resul[$i][4]."',horaliq='".$resul[$i][5]."',idliq='".$resul[$i][6]."',loginanula='".$resul[$i][7]."',horaanula='".$resul[$i][8]."' where idpadre='".$resul[$i][0]."'") or die();
			//echo "update cuentasmove set estado='".$resul[$i][1]."',loginvalido='".$resul[$i][2]."',horavalido='".$resul[$i][3]."',loginliq='".$resul[$i][4]."',horaliq='".$resul[$i][5]."',idliq='".$resul[$i][6]."',loginanula='".$resul[$i][7]."',horaanula='".$resul[$i][8]."' where idpadre='".$resul[$i][0]."'";
			
		}
		$result=posturl(array("a"=>"update_depo","canal"=>"RED"),$GLOBALS["XXurl_erp1"]);
		$resul=json_decode($result);
		$cantdep=count($resul);
		//echo $result;
		for($i=0;$i<$cantdep;$i++)
		{
			mysql_query("update cuentasmove set estado='".$resul[$i][1]."',loginvalido='".$resul[$i][2]."',horavalido='".$resul[$i][3]."',loginliq='".$resul[$i][4]."',horaliq='".$resul[$i][5]."',idliq='".$resul[$i][6]."',loginanula='".$resul[$i][7]."',horaanula='".$resul[$i][8]."' where idpadre='".$resul[$i][0]."'") or die();
			//echo "update cuentasmove set estado='".$resul[$i][1]."',loginvalido='".$resul[$i][2]."',horavalido='".$resul[$i][3]."',loginliq='".$resul[$i][4]."',horaliq='".$resul[$i][5]."',idliq='".$resul[$i][6]."',loginanula='".$resul[$i][7]."',horaanula='".$resul[$i][8]."' where idpadre='".$resul[$i][0]."'";
			
		}
		
	}
	
	function sincro_prod(){
		$result=posturl(array("a"=>"all_prod"),$GLOBALS["XXurl_erp1"]);
		$rrr=json_decode($result, true);
		$imp_prod=array();
		$cprod=count($rrr["prod"]);
		qry("truncate table productos");
		for($i=0;$i<$cprod;$i++)
		{
			$imp_prod[$i]="('";
			$imp_prod[$i].=implode("','",$rrr["prod"][$i]);
			$imp_prod[$i].="')";
			qry("insert into productos values $imp_prod[$i]") or die(mysql_error());
		}
		//$prods=implode(",",$imp_prod);
		
		
		
		
		$imp_kit=array();
		$ckit=count($rrr["kit"]);
		qry("truncate table kitparts");
		for($i=0;$i<$ckit;$i++)
		{
			$imp_kit[$i]="('";
			$imp_kit[$i].=implode("','",$rrr["kit"][$i]);
			$imp_kit[$i].="')";
			qry("insert into kitparts values $imp_kit[$i]");
		}
		$kits=implode(",",$imp_kit);
		
		//qry("insert into kitparts values $kits");
		
		$imp_canal=array();
		$ccanal=count($rrr["canal"]);
		for($i=0;$i<$ccanal;$i++)
		{
			$imp_canal[$i]="('";
			$imp_canal[$i].=implode("','",$rrr["canal"][$i]);
			$imp_canal[$i].="')";
		}
		$prodcanal=implode(",",$imp_canal);
		qry("delete from prodcanal");
		qry("insert into prodcanal values $prodcanal");
		
		$imp_locales=array();
		$clocal=count($rrr["locales"]);
		for($i=0;$i<$clocal;$i++)
		{
			$imp_locales[$i]="('";
			$imp_locales[$i].=implode("','",$rrr["locales"][$i]);
			$imp_locales[$i].="')";
		}
		$prodlocal=implode(",",$imp_locales);
		qry("delete from productolocal");
		qry("insert into productolocal values $prodlocal");
		
		qry("delete from bolscanal where promo in (select idprod from productos where estado=0)");
		//mysql_query("update productos set categoria=4 where idprod in (22,56,67,98,118,296,301,302,303,304,305,331,332,333,334,718,922,923,924)");
		
	}
	
	function createprod($qryprod){
		$result=posturl(array("a"=>"createprod","qryprod"=>$qryprod),$XXurl_erp4);
		return $result;
	}
	
	function getrucs($cli){
		$result=posturl(array("a"=>"getrucs","cliente"=>$cli),$GLOBALS["XXurl_erp1"]);
		return $result;
	}
	
	function getenvios($cli){
		$result=posturl(array("a"=>"getenvios","cliente"=>$cli),$GLOBALS["XXurl_erp1"]);
		return $result;
	}
	
	
	function addruc($login,$ruc,$razon,$dir_ruc){
		$result=posturl(array("a"=>"addruc","login"=>$login,"ruc"=>$ruc,"razon"=>$razon,"dir_ruc"=>$dir_ruc),$GLOBALS["XXurl_erp1"]);
		return $result;
	}
	function adddest($login,$dir,$ref,$ubi){
		$result=posturl(array("a"=>"adddest","login"=>$login,"dir"=>$dir,"ref"=>$ref,"ubi"=>$ubi),$GLOBALS["XXurl_erp1"]);
		return $result;
	}
	
?>
