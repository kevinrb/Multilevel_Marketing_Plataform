<?php
	require("../global.php");
	require("../cnx.php");
	foreach($_GET as $k => $v)
	$v=mysql_real_escape_string($v);
	foreach($_POST as $k => $v)
	$v=mysql_real_escape_string($v);
	$a=$_POST["a"];
	//echo "ppp";
	if($a=="users")
	{
		$qryuser  =$_POST["qryuser"];
		$qrycli=$_POST["qrycli"];
		$lider  =$_POST["lider"];
		$qryupd=$_POST["qryupd"];
		$login=$_POST["login"];
		
		mysql_query($qryuser);
		mysql_query($qrycli);
		mysql_query($qryupd);
		mysql_query("insert ignore into permisos values ('$login',1)");
		echo 1;
	}
	if($a=="confirm_puntos")
	{
		$id  =$_POST["id"];
		
		//mysql_query("update puntos set estado=1 where idop in (select idop from operacionesp where idpadre2=$id)");
		
		echo 1;
	}
	
	if($a=="nivel")
	{
		$lim1=$_POST["lim1"];
		$lim2=$_POST["lim2"];
		$desc1=$_POST["desc1"];
		$id=$_POST["id"];
		$canal=$_POST["canal"];
		
		for($i=0;$i<count($lim1);$i++)
		{
			mysql_query("update niveles set limite1='$lim1[$i]',limite2='$lim2[$i]',descuento1='$desc1[$i]' where idnivel='$id[$i]' and tipo='$canal'");
		}
		echo 1;
	}
	if($a=="upprod")
	{
		$prod=$_POST["prod"];
		//echo $prod;
		$prod=json_decode($prod);
		$bd=$_POST["bd"];
		
		if($bd="erp2")
		{
			foreach($prod as $k => $v) {
				$up = "";
				foreach($v as $kk => $vv){
					if($kk!="preciolista")
					{
					$up .= ",$kk='$vv'";}}
					$up = substr($up,1);
					$qry = "update productos set $up where codigo1=$k";
					mysql_query($qry);
					//echo "$qry";
			}
			
		}
		
		echo 1;
	}
	if($a=="createprod")
	{
		$qryprod  =$_POST["qryprod"];
		$idd=$_POST["idd"];
		mysql_query($qryprod) OR DIE(mysql_error());
		mysql_query("insert into producto_puntos(idprod,puntos,estado) select idprod, 1,1 from productos where promo=0 and idprod='$idd'");
		echo 1;
	}
	if($a=="conf_user")
	{
		$login=$_POST["login"];
		mysql_query("insert into usuarios(login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,activo,passwd,local,pass_end,ffii,edobs,nivel) select login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,1,passwd,local,curdate(),curdate(),edobs,nivel from usuarios_temp where login='$login'") OR DIE(mysql_error());
		//echo "insert into usuarios(login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,activo,passwd,local,pass_end) select login,idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,1,passwd,local,curdate() from usuarios_temp where login='$login'";
		if(mysql_affected_rows()>0)
		{
			mysql_query("insert ignore into permisos values ('$login',1)");
			mysql_query("insert ignore into asociadas select nivel,login from usuarios_temp where login ='$login' and nivel!=''");
			//mysql_query("insert ignore into puntos(idop,login,puntos,hora) select 0,nivel,500,now() from usuarios_temp where login ='$login' and nivel!=''");
			mysql_query("update usuarios_temp set activo=0 where login='$login'");
			echo "1";
		}
	}
	if($a=="createpromo")
	{
		$qrylibre  =$_POST["qrylibre"];
		$qrylibreup=$_POST["qrylibreup"];
		$qrylibrekit  =$_POST["qrylibrekit"];
		$idd  =$_POST["idd"];
		$pedidos  =$_POST["pedidos"];
		mysql_query($qrylibre);
		mysql_query($qrylibreup);
		mysql_query($qrylibrekit);
		
		$qry="select if( now()> horaini and horafin >now() ,1,0) estado,sum(cantidad*precio),sum(cantidad*precio1),sum(cantidad*precio2),            sum(cantidad*precio3),sum(cantidad*precio4),sum(cantidad*precio5), sum(cantidad*precio6),sum(cantidad*precio7),sum(cantidad*precio8),sum(cantidad*precio9) from erpd.kitparts where idkit='$idd'";
		$res = qry($qry) ;
		$v=mysql_fetch_row($res);
		$v[0] = (isset($pedidos) and $pedidos == 3)?3:$v[0];
		$qry="update erpd.productos set estado='$v[0]',precio='$v[1]',precio1='$v[2]',precio2='$v[3]', precio3='$v[4]',precio4='$v[5]',precio5='$v[6]', precio6='$v[7]',precio7='$v[8]',precio8='$v[9]',precio9='$v[10]' where idprod='$idd'";
		//echo $qry;
		$res = qry($qry) ;
		mysql_query("insert into promo_puntos(idprod) select idprod from productos where promo=0 and idprod='$idd'");
		echo 1;
	}
	
	
	if($a=="socios")
	{
		$prod=array(); 
		$kit=array();
		$arr=array();	
		$res1=mysql_query("select y.login,y.nombre,y.apellidos,y.login,z.hora_in,b.login,c.login,day(hora_update),z.estado,z.deuda,z.hora_prof,z.tprof from (user_nivel z,usuarios y) left join asociadas a on z.iduser=a.idsocio left join usuarios b on a.idempresa=b.iduser left join usuarios c on a.idpatro=c.iduser  where y.iduser=z.iduser and (hora_disabled>'2017-06-01' or hora_disabled is null)");
		while($r=mysql_fetch_row($res1))
		{
			$prod[]=$r;
		}
		$arr["socios"]=$prod;
		echo json_encode($arr);
	}
	
	if($a=="compras")
	{
		$prod=array(); 
		$kit=array();
		$arr=array();
		
		$ini=$_POST["ini"];
		$fin=$_POST["fin"];	
		$res1=mysql_query("select a.idpadre2,e.login,sum(b.precio*b.cantidad),sum(z.puntos*b.cantidad),if(iddircli>0,'hubo','no hubo'),date(hora_confirm),a.local,sum(if(b.idprod=264,b.precio,0))  from (operacionesp a,stockmovesp b, puntos c,usuarios e)  left join productos d on b.codigo1=d.idprod left join producto_puntos z on z.idprod=d.idprod where e.iduser=c.iduser and  c.idop=a.idop and a.idop=b.idop and c.hora_confirm between '$ini' and '$fin 23:59:59' and c.tipo in (9,10) and c.estado=1 group by a.idpadre2");
		while($r=mysql_fetch_row($res1))
		{
			$prod[]=$r;
		}
		$arr["compras"]=$prod;
		echo json_encode($arr);
	}
	
	if($a=="detalle_compra")
	{
		$prod=array(); 
		$kit=array();
		$arr=array();
		
		$ini=$_POST["ini"];
		$fin=$_POST["fin"];	
		$res1=mysql_query("select a.idpadre2,e.login,if(c.tipo=9,'AUTOCONSUMO','ADICIONAL'),(c.hora_confirm),b.codigo1,d.nombre,b.cantidad,z.puntos,b.precio,z.puntos*b.cantidad,b.precio*b.cantidad,tipopago1,if(iddircli>0,'hubo','no hubo')  from (operacionesp a,stockmovesp b, puntos c,usuarios e)  left join productos d on b.codigo1=d.idprod left join producto_puntos z on z.idprod=d.idprod where e.iduser=c.iduser and  c.idop=a.idop and a.idop=b.idop and c.estado=1 and c.hora_confirm between '$ini' and '$fin' and c.tipo in (9,10)");
		while($r=mysql_fetch_row($res1))
		{
			$prod[]=$r;
		}
		$arr["compras"]=$prod;
		echo json_encode($arr);
	}
	if($a=="prod_puntos")
	{
		$prod=array(); 
		$kit=array();
		$arr=array();
		
		$ini=$_POST["ini"];
		$fin=$_POST["fin"];	
		$res1=mysql_query("select codigo1,nombre,puntos,precio1,precio from productos604 a left join producto_puntos b on b.idprod=a.codigo1 where a.estado=1 and promo=0");
		while($r=mysql_fetch_row($res1))
		{
			$prod[]=$r;
		}
		$arr["puntos"]=$prod;
		echo json_encode($arr);
	}
	
	
?>
