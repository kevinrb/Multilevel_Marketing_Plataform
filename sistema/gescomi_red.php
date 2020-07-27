<?php
	include "../sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	
	
	if($cambiar>0)
	{
		for($i=0;$i<count($lim1);$i++)
		{
			qry("update comision_red set nivel1='$lim1[$i]',nivel2='$lim2[$i]',nivel3='$desc1[$i]',nivel4='$desc2[$i]',nivel5='$desc3[$i]' where nivel='$id[$i]'");
		}
		//$idsms=posturl(array("a"=>"nivel","lim1"=>$lim1,"lim2"=>$lim2,"desc1"=>$desc1,"id"=>$id,"canal"=>"VENTALIBRE"),$XXurl_erp4);
	}
	
	$qry1="select * from comision_red ";
	//echo $qry1;
	$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
	while($r1=mysql_fetch_row($res1)) {
		$tbl.="<tr><td>$r1[0]</td><td>$r1[1]</td><td>$r1[2]</td><td>$r1[3]%</td><td>$r1[4]%</td><td>$r1[5]%</td><td><input type='checkbox' class='chksel'/></td></tr>";
		$tbl.="<tr class='htr'><td><input name='id[]' type='hidden' value='$r1[0]'/> $r1[0]</td><td><input name='lim1[]' type='text' value='$r1[1]'/></td><td><input name='lim2[]' type='text' value='$r1[2]'/></td><td><input name='desc1[]' type='text' value='$r1[3]'/></td><td><input name='desc2[]' type='text' value='$r1[4]'/></td><td><input name='desc3[]' type='text' value='$r1[5]'/></td><td><input type='checkbox' class='chknosel' checked='checked'/></td></tr>";
	}
	
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/dataTables.bootstrap.css" rel="stylesheet">
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/jquery.dataTables.js"></script>
		<script src="../js/dataTables.bootstrap.js"></script>
		<link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="../css/sb-admin.css" rel="stylesheet">
		<title>Gestion de Niveles</title>
		<style type="text/css">
			.htr {
			display: none;
			}
			input[type=text]{
			width: 70px;
			}
		</style>
		<script src="../js/jquery-1.10.2.js"></script>
		<script type="text/javascript">
			$(function(){
				$("tbody .chksel, tbody .chknosel").click(function(e){
					e.preventDefault()
					var jo_tr1 = $(this.parentNode.parentNode), jo_tr2;
					if($(this).hasClass("chksel"))
					jo_tr2 = jo_tr1.next();
					else
					jo_tr2 = jo_tr1.prev();
					jo_tr1.addClass("htr");
					jo_tr2.removeClass("htr");
				})
			})
		</script>
	</head>
	<body>
		<div class="container">
			<h2>Gestion de Niveles(Descuentos)</h2>
			<form method='POST' action='gescomi_red.php'>
				<div class="panel panel-success">
					<div class="panel-heading">Gestion de Descuentos</div>                   
					<div class="panel-body">
						<table class="table">
							<thead>
								<th>Nivel</th><th>Limite Inf.</th><th>Limite Sup.</th><th>Dsct. Regulares</th><th>Dsct. Saludables</th><th>Dsct. Especial</th><th>Editar</th>
							</thead>
							<?php echo $tbl; ?>
						</table>
					</div></div>
					<input type="hidden" value="1" name="cambiar">
					<input type="submit" value="Actualizar">
			</form>
		</div>
	</body>
</html>