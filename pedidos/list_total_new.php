<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	$niveles=1;
	function last_days($month){
			$temp=explode("-",$month);
			$arr=array();
			if($temp[0]>=2017){
				
				if($temp[0]>2017){
					for($i=4;$i<12;$i++){
						$tt=mysql_fetch_row(qry("select last_day('2017-".$i."-01')"));
						$arr[]=$tt[0];
					}
				
				
					for($ii=2018;$ii<$temp[0];$ii++){
						for($i=1;$i<13;$i++){
							$tt=mysql_fetch_row(qry("select last_day('$ii-".$i."-01')"));
							$arr[]=$tt[0];
						}
						
					}
					for($i=1;$i<=$temp[1];$i++){
						$tt=mysql_fetch_row(qry("select last_day('$temp[0]-".$i."-01')"));
						$arr[]=$tt[0];
						
					}
					
				}elseif($temp[0]==2017){
				for($i=4;$i<=$temp[1];$i++){
					$tt=mysql_fetch_row(qry("select last_day('2017-".$i."-01')"));
					$arr[]=$tt[0];
				}
				
				}
				
				
			}
			return $arr;
		}

	$temp=last_days("2019-05");
		
	if(isset($time_start)){
		
		$temp=last_days($time_start);
		$fchs=implode("','",$temp);
		$last_d=end($temp);
		//echo "select * from user_nivel_day a, (select iduser,max(nivel) nil from user_nivel_day  where  fecha in ('$fchs') and fecha>'2017-04-01' and nivel>0 group by iduser) b where a.iduser=b.iduser and b.nil=a.nivel and a.fecha in ('$fchs')";
		qry("select u.iduser,u.nombre,u.apellidos,a.nivel from usuarios u,user_nivel_day a, (select iduser,max(nivel) nil from user_nivel_day  where  fecha in ('$fchs') and fecha>'2017-04-01' and nivel>0 group by iduser) b where a.iduser=b.iduser and b.nil=a.nivel and a.fecha in ('$fchs') and u.iduser=a.iduser  group by a.iduser having min(a.fecha)='$last_d'");
		

	
		
		$tbl="";
		$qry = "select u.iduser,u.nombre,u.apellidos,nr.nombre from niveles_red nr,usuarios u,user_nivel_day a, (select iduser,max(nivel) nil from user_nivel_day  where  fecha in ('$fchs') and fecha>'2017-04-01' and nivel>0 group by iduser) b where a.iduser=b.iduser and b.nil=a.nivel and a.fecha in ('$fchs') and u.iduser=a.iduser and idnivel=a.nivel  group by a.iduser having min(a.fecha)='$last_d'";
		$res = qry($qry) or die("ERROR D: " . mysql_error());
		while($r = mysql_fetch_row($res)) {
			//echo $r[1]."<br />";
			$tan1="";
			FOR($i=1;$i<11;$i++){
				$tt=$i+10;
				$tan1.=" and nivel$i<={$r[$tt]}";	
			}
			$tempo="style='".'mso-number-format:"\@"'."'";
			$tbl.="<tr><td>$r[0]</td><td $tempo>$r[1]</td><td $tempo>$r[2]</td><td $tempo>$r[3]</td><td $tempo>$r[4]</td><td $tempo>$r[6]</td><td >$r[7]</td><td >$r[8]</td><td >$bono[0]</td><td >$bonoadi[0]</td><td >$bonobon[0]</td><td >$bonocorre[0]</td></tr>";
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
		<link href="../css/bootstrap-datetimepicker.min.css" rel="stylesheet">
		<script src="../js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script src="../js/moment.min.js"></script>
		<script src="../js/bootstrap-datetimepicker.min.js"></script>
		<script>
			$(function(){
				var dd=new Date();
				dd.setTime(<?php echo $tr."000";?>-(300-dd.getTimezoneOffset())*60000);
				var dd=new Date();
				$('#fchini').datetimepicker({
					locale:'es',
					viewMode: 'years',
					format: 'YYYY-MM'
				});
				$(".det").click(function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					var fec=$(this).data("fecha");
					$.post("../res/pedidos/proc.php",{login:log,a:"det_comi2",fecha:fec,iduser:iduser},function(data){
						$("#mod_body").html(data);
					});
				});
				$(".tree").click(function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					$.post("../res/pedidos/proc.php",{login:log,a:"tree",iduser:iduser},function(data){
						$("#mod_body").html(data);
					});
				});	
				$(".venta").click(function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					$.post("../res/pedidos/proc.php",{login:log,a:"venta",iduser:iduser},function(data){
						$("#mod_body").html(data);
					});
				})
				$(".correc").click(function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					var fec=$(this).data("fecha");
					$.post("../res/pedidos/proc.php",{login:log,a:"correc",iduser:iduser,fecha:fec},function(data){
						$("#mod_body").html(data);
					});
				})
				$("body").on("click",".save_correc",function(){
					var log=$(this).data("login");
					var iduser=$(this).data("iduser");
					var fec=$(this).data("fecha");
					var montcorr=$("#moncorr").val();
					
					$.post("../res/pedidos/proc.php",{login:log,a:"mcorrec",iduser:iduser,fecha:fec,montcorr:montcorr},function(data){
						$("#mod_body").html(data);
					});
				})				
			});
			$(window).load(function() {
				$("#gcampa").change();
				$(".imprimir").prop( "disabled", false );
				$(".imprimir").click(function(){
					//alert($(this).data('table'));
					$("#excel").val("<table>"+$($(this).data('table')).html()+"</table>");
					$("#sub").submit();
				});
			});
		</script>
	</head>
	<body>
		<div class="">
			<form method="POST" >
				<div class="form-group">
					<label class="col-sm-1 control-label">
						MES :
					</label >
					<div class="col-sm-7">
						<div class="input-group date"  id="fchini">
							<input class="form-control input-sm" id="time_start" name="time_start"  type="text" value="<?php echo $time_start; ?>" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<button type="submit" class=" btn btn-info" >Mostrar</button>
			</form>
			<form method="POST" action="../test3.php" id="sub">
				<input type='hidden' name="local" value="<?php echo "rep"; ?>">
				<input id="excel" type="hidden" name="data" value=""> 
			</form>
			<button id="su" type="botton" disabled  class="imprimir btn btn-info" data-table="#tablee">Archivo Excel</button>
			<div class="panel panel-success">
				<div class="panel-heading"><h3 class="panel-title">Socios</h3></div>
				<table class='table table-condensed' id='tablee'>
					<thead>
						<th>ID</th><th>NOMBRE</th><th>APELLIDOS</th><th>NIVEL</th>
					</thead>
					
					<?php echo $tbl;?>
				</table>
			</div>
			
			
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Modal title</h4>
						</div>
						<div class="modal-body" id ="mod_body">
							...
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							
						</div>
					</div>
				</div>
			</div>
		</body>
	</html>		