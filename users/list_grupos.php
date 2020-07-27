<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	foreach($_GET as $k => $v)
	$$k=$v;
	
	
	FUnction rete($login){
		
		$qrtt=qry("SELECT DATE_SUB(concat(year(now()),'-',month(now()),'-05'), interval 4 month),DATE_SUB(concat(year(now()),'-',month(now()),'-05'), interval 3 month),DATE_SUB(concat(year(now()),'-',month(now()),'-05'), interval 2 month),DATE_SUB(concat(year(now()),'-',month(now()),'-05'), interval 1 month),CONCAT(year(now()),'-',month(now()),'-05'),day(now())");
		
		$tr=mysql_fetch_row($qrtt);
		
		if($tr[5]<5)
		{
			$qry=" select c.codigo1,
			sum(if(total>0 and hora  between '$tr[0]' and '$tr[1]',1,0)), sum(if(total>0 and hora  between '$tr[1]' and '$tr[2]',1,0)), sum(if(total>0 and hora  between '$tr[2]' and '$tr[3]',1,0)), sum(if(total>0 and hora  between '$tr[3]' and now(),1,0))
			from operacionesp o, clientes c 
			where o.idcliente=c.idcliente  and  o.estado=107 and c.canal='CATALOGO' and 
			hora between '$tr[0]' and now()  and c.codigo1='$login'
			group by  c.codigo1";
			
		}
		else
		{
			$qry="select c.codigo1,
			sum(if(total>0 and hora  between '$tr[1]' and '$tr[2]',1,0)), sum(if(total>0 and hora  between '$tr[2]' and '$tr[3]',1,0)), sum(if(total>0 and hora  between '$tr[3]' and '$tr[4]',1,0)), sum(if(total>0 and hora  between '$tr[4]' and now(),1,0)) 
			from operacionesp o, clientes c 
			where o.idcliente=c.idcliente  and  o.estado=107 and c.canal='CATALOGO' and 
			hora between '$tr[1]' and now() and c.codigo1='$login'
			group by  c.codigo1";
			
		}
		
		$ttt=qry($qry);
		$tree=mysql_fetch_row($ttt);
		
		$to=0;
		for($i=1;$i<5;$i++)
		{
			if($tree[$i]>0)
			{
				$to++;
			}
		}
		
		//return $tree[1];
		return $to;
	}
	
	
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
	function puntosact($Xlogin,$time_start=0,$time_end=0){
		$qrytime="if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now()))";
		
		$qry1="select count(*),sum(puntos),round((sum(puntos)/350)*100,2),round((sum(puntos)/700)*100,2) from puntos a where login='$Xlogin' and $qrytime and estado=1";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		return $rpuna;
	}
	
	function comision($Xlogin,$igv){
		
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin);
		$puntos=$puntos[0];
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r2[0]>0)
		{
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*((100-$r2[0])/(100*$igv))*($r3[0]/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*((100-$r2[1])/(100*$igv))*($r3[1]/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*((100-$r2[2])/(100*$igv))*($r3[2]/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(comision/100)) from operacionesp a, stockmovesp b,promo_puntos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function comision_nivel2($Xlogin,$igv){
		
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin);
		$puntos=$puntos[0];
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=mysql_query("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r2[0]>0)
		{
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*((100-$r2[0])/(100*$igv))*(1/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*((100-$r2[1])/(100*$igv))*(1/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*((100-$r2[2])/(100*$igv))*(1/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(1/100)) from operacionesp a, stockmovesp b,promo_puntos c where idcliente in ($idc) and if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now())) and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function hijos($log,$pa_log){
		$comi=0;
		$qry = "select a.nombre, a.idpersona,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'$log',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log') and a.login not in ($pa_log) group by a.login  order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$palog.=$pa_log.",'$r[7]'";
			$comi=$comi+comision_nivel2($r[1],1.18);
			//	$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			//$tbl.= hijos($r[7],$palog);
		}
		return $comi;
	}
	
	function global_comision($log){
		$comi_global=0;
		$comi_temp=0;
		$comi_tot=0;
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log') group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$cla="";
			if($r[3]=="")
			{
				$cla="class='warning'";
			}
			$comi_temp=comision($r[1],1.18);
			//$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			$comi_tot=hijos($r[7],"'$log'");
			$comi_global=$comi_global+$comi_tot+$comi_temp;
		}
		return $comi_global;
	}
	
	
	
	$qry = "select concat(a.nombre,' ',a.apellidos), a.login,a.direccion,b.ubigeo,a.telefono,a.celular,ffii,a.login ,(select empresa from asociadas where socio=a.login limit 1),a.email,fono1,fono2  from usuarios a, clientes b   where b.idcliente=a.login  and a.login in (select socio from asociadas where empresa='$login') group by a.login order by a.nombre";
	//echo $qry;
	$res = qry($qry) or die("ERROR D: " . mysql_error());
	while($r = mysql_fetch_row($res)) {
		$cla="";
		if($r[3]=="")
		{
			$cla="class='warning'";
		}
		$pu=puntosact($r[1]);
		$qryy=global_comision($r[1],1.18);
		$pun=puntos($r[1],$time_start,$time_end);	
		$porr=rete($r[1]);
		$tbl.="<tr $cla><td>$r[3]</td><td>$r[1]</td><td>$r[6]</td><td><a href='edituser_admin.php?login=$r[1]'>$r[0]</a></td><td>$r[2]</td><td>$r[9]</td><td>$r[10]/$r[11]</td><td>$r[8]</td><td>$qryy</td><td>$pu[1]</td><td>$pu[0]</td><td>$pu[3]%</td><td>$porr/4</td><td><a href='det_socio.php?login=$r[1]' class='btn btn-sm'>Detalle</a></td></tr>";
		$tbl.=hijostab($r[7],"'$login'");
		//$tbl.=hijos($r[7],"'$Xlogin'");
	}
	
	
	function hijostab($log,$pa_log){
		
		$qry = "select concat(a.nombre,' ',a.apellidos), a.login,a.direccion,b.ubigeo,a.telefono,a.celular,ffii,a.login ,(select empresa from asociadas where socio=a.login limit 1),a.email,fono1,fono2  from usuarios a, clientes b   where b.idcliente=a.login  and a.login in (select socio from asociadas where empresa='$log') and a.login not in ($pa_log) group by a.login order by a.nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$cla="";
			if($r[3]=="")
			{
				$cla="class='warning'";
			}
			$pu=puntosact($r[1]);
			$qryy=global_comision($r[1],1.18);
			$palog.=$pa_log.",'$r[1]'";
			$pun=puntos($r[1],$time_start,$time_end);	
			$porr=rete($r[1]);
			$tbl.="<tr $cla><td>$r[3]</td><td>$r[1]</td><td>$r[6]</td><td><a href='edituser_admin.php?login=$r[1]'>$r[0]</a></td><td>$r[2]</td><td>$r[9]</td><td>$r[10]/$r[11]</td><td>$r[8]</td><td>$qryy</td><td>$pu[1]</td><td>$pu[0]</td><td>$pu[3]%</td><td>$porr/4</td><td><a href='det_socio.php?login=$r[1]' class='btn btn-sm'>Detalle</a></td></tr>";
			$tbl.= hijostab($r[7],$palog);
		}
		return $tbl;
	}
	$qr="select login,concat(nombre,' ',apellidos) from usuarios where activo=1 order by nombre";
	$res=qry($qr);
	$usu="";
	while($r=mysql_fetch_row($res))
	{
		$usu.="<option value='$r[0]'>$r[1]</option>";
		
	}
	
	
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Mis Socios</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" media="screen" href="../css/bootstrap-datetimepicker.min.css">
		<link href="../css/dataTables.bootstrap.css" rel="stylesheet">
		<script src="../js/jquery-1.10.1.min.js"></script>
		<script src="../js/jquery.dataTables.js"></script>
		<script src="../js/dataTables.bootstrap.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script src="../js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap-datetimepicker.es.js"></script>
		<script>
			$(function(){
			///////////////////////////////////////////MAKING A REAL TABLE RESPONSIVE ///////////////////////////////////////
			if (screen.width < 500){$('.datatab').parent().css({ overflowX: 'scroll'});} 
			window.onresize = resize;
			function resize(){
			var w1=$('.datatab').parent().width(); var w2=$('.datatab').width();
			if(w1<w2){$('.datatab').parent().css({ overflowX: 'scroll'});}
			else if(w1>=w2){$('.datatab').parent().removeAttr("style");}
			} 
			var w1=$('.datatab').parent().width(); var w2=$('.datatab').width();
			if(w1<w2){$('.datatab').parent().css({ overflowX: 'scroll'});}
			else if(w1==w2){$('.datatab').parent().removeAttr("style");}
			//$('#datatab').dataTable();
			$('#tablee').dataTable({"order": [[ 0, "desc" ]],"oLanguage": {"sUrl": "../css/spanish.txt"}, "pageLength": 100});
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			var dd=new Date();
			$('.time').datetimepicker({format: 'yyyy-mm-dd hh:ii',
			showMeridian: true,
			autoclose: true,
			language:'es',
			endDate:dd,
			headTemplateV3:true,
			minView:0});
			$('#time_start').datetimepicker().on('changeDate', function(ev){ 
			$('#time_end').datetimepicker('setStartDate',ev.date);
			});
			
			$('#time_end').datetimepicker().on('changeDate', function(ev){
			$('#time_start').datetimepicker('setEndDate',ev.date);
			});
			$(".calendar").click(function(){
			$(this).parent().parent().children('.time').datetimepicker('show');
			});
			
			$(".deta").click(function(){
			var log=$(this).data('login');
			
			
			});
			});
			
			$(window).load(function(){
			$("#exp").show();
			$(".imprimir").click(function(){
			alert($($(this).data('table')).html());
			$("#excel").val("<table>"+$($(this).data('table')).html()+"</table>");
			$("#sub").submit();
			});
			
			
			});
		</script>
	</head>
	<body>
		<div class="container"> 
			<!--
				<FORM role="form" class="form-horizontal" method="GET">
				<div class="form-group">
				<label class="col-sm-1 control-label">
				Del:
				</label >
				<div class="col-sm-7">
				<div class="input-group">
				<input class="time form-control input-sm" id="time_start" name="time_start" type="text" value="<?php echo $time_start; ?>" readonly/>
				<span class="input-group-btn">
				<button class="btn btn-sm btn-info calendar" type="button" ><span class="glyphicon glyphicon-calendar"></span></button>
				</span>
				</div>
				</div>
				</div>
				<div class="form-group">			 
				<label class="col-sm-1 control-label">
				Al:
				</label>
				<div class="col-sm-7">
				<div class="input-group">
				<input class="time form-control input-sm" id="time_end" name="time_end" type="text" value="<?php echo $time_end; ?>" readonly/>
				<span class="input-group-btn">
				<button class="btn btn-sm btn-info calendar" type="button" ><span class="glyphicon glyphicon-calendar"></span></button>
				
				</span>
				</div>
				<br />	
				</div>
				<div class="text-center">
				<button type="submit" class="btn btn-sm btn-primary">Consultar</button>
				<input type="button" value="Exportar Excel" class="btn btn-sm imprimir" data-table="#tablee">
				</div>
				</div>
			</form>-->
			<FORM role="form" class="form-horizontal" method="GET">
				<select name="login" id="login" class="form-control"><?php echo $usu; ?></select>
				<button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
			</form>
			<input type="button" value="Exportar Excel" class="btn btn-sm imprimir" data-table="#tablee">
			<form method="POST" action="../test3.php" id="sub">
				<input type='hidden' name="local" value="<?php echo "repin$time_start - $time_end"; ?>">
				<input id="excel" type="hidden" name="data" value=""> 
				
			</form>
			
			
			<table class='table table-bordered table-hover' id="tablee">
				<thead>
					<th>Territorio</th><th>Codigo</th><th>Ingreso</th><th>Nombre</th><th>Direccion</th><th>Correo</th><th>Telefono</th><th>Lider</th><th>Comision</th><th>Puntos</th><th>Pedidos</th><th>% Minino Lider</th><th>Constancia</th><th>Detalles</th>
				</thead>
				<?php echo $tbl;?>
			</table>
			
		</div>
		<!--
			<form method='POST' action='regnew2.php'>
			<input type="submit" value="Ingresar nuevo Socio">
			</div>
			</form>
		-->
	</body>
</html>