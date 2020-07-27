<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	
	FUnction rete($login){
		
		$qrtt=qry("SELECT DATE_SUB(concat(year(now()),'-',month(now()),'-05'), interval 4 month),DATE_SUB(concat(year(now()),'-',month(now()),'-05'), interval 3 month),DATE_SUB(concat(year(now()),'-',month(now()),'-05'), interval 2 month),DATE_SUB(concat(year(now()),'-',month(now()),'-05'), interval 1 month),CONCAT(year(now()),'-',month(now()),'-05'),day(now())");
		
		$tr=mysql_fetch_row($qrtt);
		
		if($tr[5]<5)
		{
			$qry=" select c.codigo1,
			sum(if(total>0 and hora between '$tr[0]' and '$tr[1]',1,0)), sum(if(total>0 and hora between '$tr[1]' and '$tr[2]',1,0)), sum(if(total>0 and hora between '$tr[2]' and '$tr[3]',1,0)), sum(if(total>0 and hora between '$tr[3]' and now(),1,0))
			from operacionesp o, clientes c 
			where o.idcliente=c.idcliente and o.estado=107 and c.canal='CATALOGO' and 
			hora between '$tr[0]' and now() and c.codigo1='$login'
			group by c.codigo1";
			
		}
		else
		{
			$qry="select c.codigo1,
			sum(if(total>0 and hora between '$tr[1]' and '$tr[2]',1,0)), sum(if(total>0 and hora between '$tr[2]' and '$tr[3]',1,0)), sum(if(total>0 and hora between '$tr[3]' and '$tr[4]',1,0)), sum(if(total>0 and hora between '$tr[4]' and now(),1,0)) 
			from operacionesp o, clientes c 
			where o.idcliente=c.idcliente and o.estado=107 and c.canal='CATALOGO' and 
			hora between '$tr[1]' and now() and c.codigo1='$login'
			group by c.codigo1";
			
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
	
	
	function puntos($Xlogin){
		$qrytime="if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now()))";
		if($time_start!=0)
		{
			$qrytime=" a.hora between '$time_start' and '$time_end' ";
		}
		$qry1="select sum(puntos) from puntos a where login='$Xlogin' and estado=1 and tipo=2";
		//echo $qry1;
		$res1 = qry($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		$rpuna[0] = isset($rpuna[0]) ? $rpuna[0] : 0 ;
		return $rpuna;
	}
	function puntosact($Xlogin,$mes1){
		$mes="a.hora between '2015-".$mes1."-5' and '2015-".($mes1+1)."-05'";
		$qrytime="if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now()))";
		
		$qry1="select count(*),sum(puntos),round((sum(puntos)/350)*100,2),round((sum(puntos)/700)*100,2) from puntos a where login='$Xlogin' and $mes and estado=1 and tipo=2";
		$res1 = qry($qry1) or die("2--".mysql_error());
		$rpuna=mysql_fetch_row($res1);
		return $rpuna;
	}
	
	function comision($Xlogin,$igv,$mes0,$ano0,$mes1,$ano1){
		
		//if(day(now())>4, a.hora between concat(year(now()),'-',month(now()),'-5') and now(), if( month(now())=1,a.hora between concat((year(now())-1),'-12-5') and now() ,a.hora between concat(year(now()),'-',(month(now())-1),'-5') and now()))
		
		$mes="a.hora between '$ano0-$mes0-05' and '$ano1-$mes1-05'";
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin);
		$puntos=$puntos[0];
		$re=qry("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=qry("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r2[0]>0)
		{
			//$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*((100-$r2[0])/(100*$igv))*($r3[0]/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*((100-$r2[1])/(100*$igv))*($r3[1]/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*((100-$r2[2])/(100*$igv))*($r3[2]/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c,puntos d where d.idop=a.idop and d.tipo=1 and idcliente in ($idc) and $mes and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.total,0))/($igv)*($r3[0]/100),sum(if((c.categoria=2 and b.promo=0),b.total,0))/($igv)*($r3[1]/100),sum(if((b.promo=0 and c.categoria=4),b.total,0))/($igv)*($r3[2]/100),COUNT(distinct a.idop) from operacionesp a, pedido_detalle b,productos c,puntos d where d.idop=a.idop and d.tipo=1 and idcliente in ($idc) and $mes and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO'  and a.estado=107";			 
			$res1 = qry($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(0/100)) from operacionesp a, stockmovesp b,promo_puntos c,puntos d where d.idop=a.idop and d.tipo=1 and idcliente in ($idc) and $mes and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = qry($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function comision_nivel2($Xlogin,$igv,$mes0,$ano0,$mes1,$ano1){
		$idcq=qry("select idcliente from clientes where codigo1='$Xlogin'");
		$mes="a.hora between '$ano0-$mes0-05' and '$ano1-$mes1-05'";
		$ids=array();
		while($idc1=mysql_fetch_row($idcq))
		{
			$ids[]=$idc1[0];
		}
		$idc=implode(",",$ids);
		
		$puntos=puntos($Xlogin);
		$puntos=$puntos[0];
		$re=qry("select descuento1,descuento2,descuento3,idnivel from niveles where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r2=mysql_fetch_row($re);
		$re=qry("select descuento1,descuento2,descuento3,idnivel from comision where $puntos between limite1 and limite2 and tipo='VENTALIBRE'");	
		$r3=mysql_fetch_row($re);
		if($r2[0]>0)
		{
			$qry1="select sum(if((b.promo=0 and c.categoria=1), b.precio*b.cantidad,0))*((100-$r2[0])/(100*$igv))*(5/100),sum(if((c.categoria=2 and b.promo=0),b.precio*b.cantidad,0))*((100-$r2[1])/(100*$igv))*(0/100),sum(if((b.promo=0 and c.categoria=4),b.precio*b.cantidad,0))*((100-$r2[2])/(100*$igv))*(0/100),COUNT(distinct a.idop) from operacionesp a, stockmovesp b,productos c,puntos d where d.idop=a.idop and d.tipo=1 and idcliente in ($idc) and $mes and a.idop=b.idop and c.idprod=b.idprod and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = qry($qry1) or die("2--".mysql_error());
			$prespro_past=mysql_fetch_row($res1);
			
			//////con promo
			$qry1="select sum(b.precio*b.cantidad*((100-c.descuento$r2[3])/(100*$igv))*(0/100)) from operacionesp a, stockmovesp b,promo_puntos c,puntos d where d.idop=a.idop and d.tipo=1 and idcliente in ($idc) and $mes and a.idop=b.idop and c.idprod=b.promo and b.idprod not in (254,1598) and b.promo not in (505,506) and a.canal='CATALOGO' and b.nota2='' and a.estado=107";
			$res1 = qry($qry1) or die("2--".mysql_error());
			$precpro_past=mysql_fetch_row($res1);				
			///////////		
			
			return $descact_past=$prespro_past[0]+$prespro_past[1]+$prespro_past[2]+$precpro_past[0];
		}
	}
	
	function hijos($log,$pa_log,$mes0,$ano0,$mes1,$ano1){
		$comi=0;
		$qry = "select a.nombre, a.login,a.direccion,sum(if(c.estado=1,c.puntos,0)),a.telefono,a.celular,'$log',a.login from (usuarios a, clientes b) left join puntos c on c.login=a.login where b.codigo1=a.login and a.login in (select socio from asociadas where empresa='$log') and a.login not in ($pa_log) group by a.login order by nombre";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$palog="";
		while($r = mysql_fetch_row($res)) {
			$palog.=$pa_log.",'$r[7]'";
			$comi=$comi+comision($r[1],1.18,$mes0,$ano0,$mes1,$ano1);
			//	$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			//$tbl.= hijos($r[7],$palog);
		}
		return $comi;
	}
	
	function global_comision($log,$igv,$mes0,$ano0,$mes1,$ano1){
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
			$comi_temp=comision($r[1],$igv,$mes0,$ano0,$mes1,$ano1);
			//$tbl.="<tr $cla><td>$r[0]</td><td>$r[1]</td><td>$r[3]</td><td>$qryy</td><td>$r[4]</td><td>$r[5]</td><td>$r[6]</td></tr>";
			$comi_tot=hijos($r[7],"'$log'",$mes0,$ano0,$mes1,$ano1);
			$comi_global=$comi_global+$comi_tot+$comi_temp;
		}
		return $comi_global;
	}
	
	function mess($fecha){
		$comi_global=0;
		$comi_temp=0;
		$comi_tot=0;
		$qry = "select DATEDIFF(now(),'$fecha')";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		$r = mysql_fetch_row($res);
		$tr=0;
		if($r[0]>90)
		$tr=1;
		return $tr;
	}
	if(isset($_GET["time_start"])){
		$ras=explode("-",$time_start);
		$me0=$ras[1];
		$me1=$ras[1]+1;
		$yea0=$ras[0];
		$yea1=$ras[0]+1;
		if($me1==13){
			$me1="01";
			$yea1=$ras[0]+1;
		}
		
		
		$qry = "select concat(a.nombre,' ',a.apellidos), a.login,a.direccion,b.ubigeo,a.telefono,a.celular,ffii,a.login ,(select empresa from asociadas where socio=a.login limit 1),a.email,fono1,fono2 from usuarios a, clientes b where b.idcliente=a.login group by a.login order by a.nombre";
		//echo $qry;
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			$cla="";
			if($r[3]=="")
			{
				$cla="class='warning'";
			}
			/*$pu=puntosact($r[1]);
				$qryy=global_comision($r[1],1.18,$me0,$yea0,$me1,$yea1);
				$pun=puntos($r[1],$time_start,$time_end);	
				$porr=rete($r[1]);
				$tan=mess($r[6]);
			*/
			$cesa="";
			IF(($porr==1 || $porr==0) && $tan==1){
				$cesa="CESADOS";
			}
			$tbl.="<tr $cla><td>$r[3]</td><td>$r[1]</td><td>$r[6]</td><td><a href='edituser_admin.php?login=$r[1]'>$r[0]</a></td><td>$r[2]</td><td>$r[9]</td><td>$r[10]/$r[11]</td><td>$r[8]</td><td><a href='comision_soc.php?socio=$r[1]&time_start=$time_start'>$qryy</a></td><td>$pu[1]</td><td>$pu[0]</td><td>$pu[3]%</td><td>$porr/4</td><td>$cesa</td><td><a href='det_socio.php?login=$r[1]' class='btn btn-sm'>Detalle</a></td></tr>";
			//$tbl.=hijos($r[7],"'$Xlogin'");
		}
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
			$('.time').datetimepicker({
			showMeridian: true,
			autoclose: true,
			language:'es',
			headTemplateV3:true,
			minView:3,
			startView:3,
			format: 'yyyy-mm'});
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
			
			$("#excel").val("<table>"+$($(this).data('table')).html()+"</table>");
			$("#sub").submit();
			});
			
			
			});
		</script>
	</head>
	<body>
		<div class="container"> 
			
			<FORM role="form" class="form-horizontal" method="GET">
				<div class="form-group">
					<label class="col-sm-1 control-label">
						Mes:
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
					<div class="text-center">
						<button type="submit" class="btn btn-sm btn-primary">Consultar</button>
						<input type="button" value="Exportar Excel" class="btn btn-sm imprimir" data-table="#tablee">
					</div>
				</div>
			</form>
			<input type="button" value="Exportar Excel" class="btn btn-sm imprimir" data-table="#tablee">
			<form method="POST" action="../test3.php" id="sub">
				<input type='hidden' name="local" value="<?php echo "repin$time_start - $time_end"; ?>">
				<input id="excel" type="hidden" name="data" value=""> 
				
			</form>
			
			
			<table class='table table-bordered table-hover' id="tablee">
				<thead>
					<th>Territorio</th><th>Codigo</th><th>Ingreso</th><th>Nombre</th><th>Direccion</th><th>Correo</th><th>Telefono</th><th>Lider</th><th>Comision</th><th>Puntos</th><th>Pedidos</th><th>% Minino Lider</th><th>Constancia</th><th></th><th>Detalles</th>
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