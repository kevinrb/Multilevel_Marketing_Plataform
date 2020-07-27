<?php
	include "sec.php";
	foreach($_POST as $k => $v)
	$$k=$v;
	
	if($pais!=""){
		if($cambiar>0)
		{
			//echo "jallo";
			for($i=0;$i<count($idprod);$i++)
			{
				$id=$i+1;
				qry("update productos$pais set precio='$precio[$i]',precio1='$precio1[$i]' where idprod='$idprod[$i]'");
				//qry("update menu set nombre='$name[$i]',url='$url[$i]',idfunc='$idfunc[$i]',grupo='$grupo[$i]',orden='$orden[$i]' where idmenu='$idmenu[$i]'");
			}
			/*$to=count($grupo)+1;
				for($i=0;$i<count($grupo1);$i++)
				{
				//mysql_query("insert into menu values ('$to','$name1[$i]','$url1[$i]','$idfunc1[$i]','$grupo1[$i]','$orden[$i]',0)") or die(mysql_error());
				$to++;
				}
			*/
		}
		
		$qry1="select a.idprod,nombre,precio,precio1,p.puntos from productos$pais a left join producto_puntos p on p.idprod=a.codigo1  where a.promo=0 and a.estado=1";
		$res1 = MYSQL_QUERY($qry1) or die("2--".mysql_error());
		while($r1=mysql_fetch_row($res1)) {
			$tbl.="<tr><td>$r1[0]</td><td>$r1[1]</td><td>$r1[2]</td><td>$r1[3]</td><td>$r1[4]</td><td><input type='checkbox' class='chksel'/></td></tr>";
			$tbl.="<tr class='htr'><td><input name='idprod[]' type='hidden' value='$r1[0]'/>$r1[0]</td><td>$r1[1]</td><td><input name='precio[]' type='text' value='$r1[2]'/></td><td><input name='precio1[]' type='text' value='$r1[3]'/></td><td>$r1[4]</td><td><input type='checkbox' class='chknosel' checked='checked'/></td></tr>";
		}
	}
	
	$res=mysql_query("select idpais, pais from paises where estado=1");
	$opc="<option value='' disabled>-elije-</option>";
	while($r=mysql_fetch_row($res)){
		$opc.="<option value='$r[0]'>$r[1]</option>";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Gestion de PRODUCTOS</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<style type="text/css">
			.htr {
			display: none;
			}
			input[type=text]{
			width: 70px;
			}
		</style>
		<script src="js/jquery-1.10.1.min.js"></script>
	    <script type="text/javascript">
			$(function(){
				$("#butadd").click(function(){
					$("table").append("<tr><td></td><td><input name='name1[]' type='text' value=''/></td><td><input name='url1[]' type='text' value=''/></td><td><input name='idfunc1[]' type='text' value=''/></td><td><input name='grupo1[]' type='text' value=''/></td><td><input name='orden[]' type='text' value=''/></td><td><input type='checkbox' class='chknosel' checked='checked'/></td></tr>");
					
				});
				$("tbody .chksel, tbody .chknosel").click(function(e){
					e.preventDefault()
					var jo_tr1 = $(this.parentNode.parentNode), jo_tr2;
					if($(this).hasClass("chksel"))
					jo_tr2 = jo_tr1.next();
					else
					jo_tr2 = jo_tr1.prev();
					jo_tr1.addClass("htr");
					jo_tr2.removeClass("htr");
				});
			});
		</script>
	</head>
	<body>
		<h2>Gestion de PRODUCTOS</h2>
		<?php if(!$pais!=""){?>
			<form >
				<div class="form-group">
					<label for="pais">Elije un pais</label>
					<select class="form-control" placeholder="Pais" name="pais" id="pais">
						<?php echo $opc;?>
						
					</select>
					<button >Confirmar</button>
				</div>
			</form>
			<?php }else{ ?>
			<form method='POST' action='gesprod.php?pais=<?php echo $pais;?>'>
				<table class="table">
					<thead>
						<th>IDPROD</th><th>NOMBRE</th><th>PRECIO SOCIO</th><th>PRECIO PUBLICO</th><th>Editar</th>
					</thead>
					<?php echo $tbl; ?>
				</table>
				<input type="hidden" value="1" name="cambiar">
				<input type="submit" value="Actualizar">
				<!--<input type="button" id="butadd" value="anadir">-->
			</form>
			
		<?php } ?>
	</body>
	
</html>