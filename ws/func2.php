<?php
	//include("../sec.php");
	function posturl($data,$url)
	{$postdata = http_build_query($data);
		$opts = array('http' => array('method'  => 'POST','header'  => 'Content-type: application/x-www-form-urlencoded','content' => $postdata));
		$context = stream_context_create($opts);
		$result  = file_get_contents($url,false,$context);
	return $result;}
	
	//upda_ped();
	
	function upda_ped($fini,$ffin){
		$result=posturl(array("a"=>"getopsv2","canal"=>"RED","fini"=>$fini,"ffin"=>$ffin),$GLOBALS["XXurl_erp1"] );
		//$result;
		return $resul=json_decode($result);
		/*$i=0;
			foreach($resul as $a=>$b )
			{
			$bb=explode("----",$b);
			//echo $bb[1];
			$tempo=mysql_fetch_row(qry("select date(b.hora_confirm),a.idop,round(b.puntos,3),$bb[1] - round(b.puntos,3),b.tipo,b.id from operacionesp a, puntos b  where a.idop=b.idop and a.idpadre2=$a and b.hora_confirm>'2016-03-01' and !(floor(b.puntos)=floor($bb[1]) or round($bb[1] - round(b.puntos,3))=15  or round($bb[1] - round(b.puntos,3))=10) and b.tipo in (4,6) limit 1"));
			//$bbb=explode(" ",$bb[1]);
			if($tempo[1]>0){
			//	if($bbb[0]!=$tempo[0] and $bb[0]>0)
			if($tempo[3]>0){
			echo $b."++$a//$tempo[4]--$tempo[0]--".$bb[1]." ++$tempo[2]++<strong>$tempo[3]</strong><br />";
			//mysql_query("update puntos set puntos='$bb[1]' where id=$tempo[5]");
			$i++;}
			}
			
			/*mysql_query("update opsv set n=$bb[0] where idop=$a");
			if(mysql_affected_rows()>0){
			$idop=mysql_fetch_row(mysql_query("select idop from operacionesp where idpadre2=$a"));
			$idp=$idop[0];
			if($bb[0]==0)
			{
			mysql_query("update operacionesp set horaanul=now() where idpadre2=$a");
			mysql_query("update stockmovesp a, operacionesp b set a.estado=b.estado where a.idop=b.idop and idpadre2=$a");
			mysql_query("update puntos set estado=2 where idop='$idp'");
			if(mysql_affected_rows()>0){
			mysql_query("update puntos a, user_nivel b set b.deuda=1 where  a.login=b.login and a.tipo=3 and now()>(concat(date(hora_in),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_in),' 23:59:59'),now())) month) and date(concat(date(hora_in),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_in),' 23:59:59'),now())) month)=fecha_venc and a.estado=2 and hora_confirm>'2016-02-01' and a.idop='$idp'");
			mysql_query("update user_nivel a,puntos b set a.hora_in=null,a.idop=0,a.monto=0,a.nivel=null,a.hora_update=null,deuda=0 where a.login=b.login and b.idop='$idp' and b.tipo=3 and b.fecha_venc=date(a.hora_in)");					
			}
			}
			if($bb[0]>0)
			{
			mysql_query("update opsv set hora='$bb[1]' where idop=$a");
			mysql_query("update puntos set estado=1,hora_confirm=now() where idop='$idp' and estado!=1");
			if(mysql_affected_rows()>0){
			mysql_query("update user_nivel a,puntos b set a.nivel=(select idnivel from niveles_red where b.puntos between limite1 and limite2 and b.puntos<limite2), a.monto=b.puntos, a.idop=b.idop,a.hora_update=now(),a.hora_in=now(),b.fecha_venc=curdate() where a.login=b.login and b.idop='$idp' and a.hora_in is null and b.tipo=3 and b.estado=1 and a.estado=1");
			mysql_query("update user_nivel a,puntos b set a.nivel=(select idnivel from niveles_red where b.puntos between limite1 and limite2 and b.puntos<limite2), a.monto=b.puntos, a.idop=b.idop,a.hora_update=now(),a.hora_in=now(),b.fecha_venc=curdate(),a.estado=1 where a.login=b.login and b.idop='$idp' and a.hora_in is null and b.tipo=3 and b.estado=1 and a.estado=0");	
			mysql_query("update user_nivel a,puntos b set a.deuda=a.deuda-1 where a.login=b.login and b.idop='$idp'  and b.hora_confirm>(concat(date(hora_in),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_in),' 23:59:59'),now())) month) and b.tipo=3 and b.estado=1 and a.deuda>0");
			}
			}
		}*/	
		//}
		//echo $i;
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