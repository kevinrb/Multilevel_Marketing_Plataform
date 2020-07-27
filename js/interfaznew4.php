<?php
require("sec.php");
	 
	$qry = qry("SELECT iddata FROM ops where idop='$idop'");
	$fetch = mysql_fetch_row($qry);
	$origen= $fetch[0]; 

	$qry=qry("select count(*) from ops where login='$Xlogin' and iddata='$origen' and horaini>curdate()");
	$fetch = mysql_fetch_row($qry);
	$tto=$fetch[0]; 
	//echo $tto;
	if($tto>0 and $origen>0){
			$format=mysql_fetch_row(qry("select idformato from cargas a, cargadata b where a.idcarga=b.idcarga  and b.iddata='$origen'"));
		$format=$format[0];
	
	
		//TRAEMOS LAS motivos
	$qi = qry("SELECT idmotivo, motivo,motivo1,tipo from motivos2 where formato in ($format) and estado=1 order by motivo1");
	$moti="<div class='col-lg-3'>Tipo Motivo:<select class='form-control input-sm' id='clamotivo' required><option value=''>-seleccione-</option>";
	$temp="";
	$moti1="";
	$gmot="";
	$i=0;
	while($fetch = mysql_fetch_row($qi))
	{
		if($temp!="$fetch[2]")
		{
			$i++;
			$gmot.="<option>$fetch[2]</option>";
		}
		 $moti1.="<option class='$i estado$fetch[3]' value=$fetch[0]>$fetch[1]</option>"; 
		 $temp=$fetch[2];
	}
	$moti.="$gmot</select></div><div class='col-lg-3'>Motivo:<select class='form-control input-sm' id='idmotivo' name='mot1' required><option value='' selected disabled style='display: none;'>-seleccione-</option>$moti1</select></div>";

	//TRAEMOS LAS PROD
	$prod="";
	$qii = qry("SELECT idprod, producto,producto1 from productos2 where formato in ($format) order by producto1");
	if(mysql_num_rows($qii)>0){
	$prod="<div class='col-lg-3'>Grupo Producto:<select class='form-control input-sm' id='claidpro'><option value='' selected disabled style='display: none;'>-seleccione-</option>";
	$gprod="";
	$prod1="";
	$i=0;
	while($fetch = mysql_fetch_row($qii))
	{
		if($temp!="$fetch[2]")
		{
			$i++;
			$gprod.="<option value='$i'>$fetch[2]</option>";
		}
		$prod1.="<option class='$i' value=$fetch[0]>$fetch[1]</option>"; 
		$temp=$fetch[2]; 
	}	 
	$prod.="$gprod</select ></div><div class='col-lg-3'>Producto:<select name='prod2' class='form-control input-sm' id='idpro'><option value='' selected disabled style='display: none;'>-seleccione-</option>$prod1</select></div>";
	}
	
	//TRAEMOS LAS PREGUNTAS
	$preg_qry = qry("SELECT * from preg_formato where idformato in ($format) order by orden");
	$preg=""; $i = 1;
	while($row = mysql_fetch_row($preg_qry)){
		if($row[4]==1){$required="required";}else{$required="";}
		if($row[2]==1){ $preg.="<label for='campo$i'>$row[1]: </label><input type='text' name='campo$i' id='campo$i' $required><br><br>"; }
		if($row[2]==2){ $preg.="<label for='campo$i'>$row[1]: </label><br><textarea name='campo$i' id='campo$i' cols=30 rows=5 $required></textarea><br><br>"; }
		if($row[2]==3){
			$options = explode(", ", trim($row[3]));
			$maxe=count($options); $e="0"; $opts="";
			while($e<$maxe){
			$opts.= "<option value='$options[$e]'>$options[$e]</option>";
			++$e;}
			$preg.="<label for='campo$i'>$row[1]: </label><select name='campo$i' id='campo$i' $required>$opts</select><br><br>";
		}
		if($row[2]==4){ $preg.="<label for='campo$i'>$row[1]: </label><input type='text' name='campo$i' id='campo$i' $required class='fechas'><br><br>"; }
		++$i;
	}

	$preg.="<input type='submit' id='sub' value='Guardar Datos'>";
	
	/////
			$qryramdom = qry("select a.*,b.iddata,b.hora,b.idcarga from data a, cargadata b where a.iddata=b.iddata  and a.iddata='$origen'");
		$rowramdom = mysql_fetch_row($qryramdom);
		$qrydatos = qry("SELECT * from dataformato WHERE idformato in ($format)");
	$rowdatos = mysql_fetch_row($qrydatos);

//SE ORDENAN LOS RESULTADOS
	$premisa = $rowdatos[1]; 
	$a=1; $hola="";
	$hola1="<table id='tablee'><thead><tr><th>Campo</th><th>Dato</th></tr></thead>";
	$hola.="<input form='recabados' type='hidden' id='idops' value='$idop' name='idops'>";
	while($premisa !="" || $a<4){
        $textb="";
		$disa="";
		if($a==1){

		}
		elseif($a==2 || $a==3) {		

		}
		else{
			$hola1.= "<tr><td>$rowdatos[$a]</td> <td id='$rowdatos[$a]' class='text'>$rowramdom[$a]</td></tr>";}
			++$a; 
			$premisa=$rowdatos[$a];
	}
	$hola1.="</table>";
	}
	//TIEMPO DE REFERENCIA


		$qryramdom = qry("select a.*,b.iddata,b.hora,b.idcarga from data a, cargadata b where a.iddata=b.iddata  and a.iddata='$origen'");
		$rowramdom = mysql_fetch_row($qryramdom);
		$pst_idcarga=$rowramdom[27];
		$format=mysql_fetch_row(qry("select idformato from cargas where idcarga='$pst_idcarga'"));
		$format=$format[0];

		$idops=$idop;
		if(strlen($rowramdom[2])>7)
			$num="51".$rowramdom[2];
		else
			$num="511".$rowramdom[2];
		//qry("insert into newcalls (destino,extension,contexto,hora,login,idcarga,idop,prio,graba) values ('SIP/$num@tip','$XLanexo','internal',now(),'$Xlogin','$pst_idcarga','$idops',1,1)");
		//$idcall= mysql_insert_id();
        $res=qry("select idcall from newcalls where callerid='$origen' order by idcall desc limit 1");
		$idcall=mysql_fetch_row($res);
		$idcall=$idcall[0];

	        $colgar=1;
	//}
	//}	
//SE EXTRAE LOS LABEL DE DATAFORMATO
	$qrydatos = qry("SELECT * from dataformato WHERE idformato in ($format)");
	$rowdatos = mysql_fetch_row($qrydatos);

//SE ORDENAN LOS RESULTADOS
	$premisa = $rowdatos[1]; 
	$a=1; $hola="";//$hola1="<table id='tablee'><thead><tr><th>Campo</th><th>Dato</th></tr></thead>";
	$hola.="<input form='recabados' type='hidden' id='idops' value='$idops' name='idops'>";
	while($premisa !="" || $a<4){
        $textb="";
		$disa="";
		if($a==1){
			$hola .= "<fieldset style='margin:0px 15px;padding: 0;'><span>ID: </span><input id='$rowdatos[$a]' class='form-control input-sm' type='text' form='recabados' value='$rowramdom[$a]' size='20' name='claver' readonly><input form='recabados' type='hidden' value='$rowramdom[0]' id='iddata' name='iddata'><input form='recabados' type='hidden' value='$pst_idcarga' name='idcarga'>";
						if($a==3)
				$hola .= "</fieldset><br />";
		}
		

		
		else{
			//$hola1.= "<tr><td>$rowdatos[$a]</td> <td id='$rowdatos[$a]' class='text'>$rowramdom[$a]</td></tr>";
			}
			++$a; 
			$premisa=$rowdatos[$a];
	}
	//$hola1.="</table>";
	//historico
	$res=qry("select a.horaini, b.nombre,c.motivo, d.c1 from ops a, usuarios b, motivos2 c,feedback d where a.idop=d.idop and c.idmotivo=a.idmotivo and a.iddata='$rowramdom[0]' and a.login=b.login");
	$histo="<table class='table'><thead><th>Hora</th><th>Operador</th><th>Tipificacion</th><th></th><thead>";
	while($r=mysql_fetch_row($res))
	{
		$histo.="<tr><td>$r[0]</td><td>$r[1]</td><td>$r[2]</td><td>$r[3]</td></tr>";
	}
	$histo.="</table>";
 //Speech de la campana
	$speech = qry("SELECT speech from formatos where idformato in ($format)");
	$speech = mysql_fetch_row($speech);
	$speech=$speech[0];
	
	
	
	
	
//}
//else
//{
	//header("Location: nologin.php"); 
//}
?>

<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Interfaz operador</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/jquery.dataTables.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker.min.css">
	<style>
	.col-lg-3 {
display: inline-block;
float: none;
}

#tipos{
margin: 20px 0;
}
</style>
	<script src="js/jquery-1.10.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script type='text/javascript' src='js/bootstrap-datetimepicker.min.js'></script>
	<script type='text/javascript' src='js/bootstrap-datetimepicker.es.js'></script>
	<?php if($XLestado==1 and $origen!="" and $tto==0){?>
 <script> 
	var timemax = <?php echo $times;?>;
	var origen= <?php echo $origen;?>;
	         //  window.onbeforeunload = function (e) {
         //       var e = e || window.event;
                //IE & Firefox
         //       if (e) {
       //             e.returnValue = 'Are you sure?';
          //      }
                // For Safari
        //         return 'Are you sure?';
       //     };
 </script>

<script src="js/interfaznew_rec.js"></script>
	<?php } ?> 
</head>
<body>
	<input type='hidden' id="tiempo" value="0">
	<form id="recabados" method="POST" action="ajax/interfaz_ajaxnew_pre.php" autocomplete="off">
	<div class="row" style="margin: 0 2px;">
		<div class="text-center col-sm-offset-4 col-sm-4" id="carga">
		<?php echo $cargas;?>
		</div>
	</div>
	<div class="row">
	<div class="col-12 col-sm-4 col-lg-4">
		<div id="datos">
		
		<?php echo $hola.$hola1;?>
		</div>
	</div>
	<div class="col-12 col-sm-8 col-lg-8">
	<div class="text-center">
		<div id="tipos">
		
		<?php echo $moti.$prod;?>
		</div>
		<ul class="nav nav-tabs nav-justified" id="myTab">
			<li class="active"><a href="#home">Speech</a></li>
			<li><a href="#profile">Campana</a></li>
			<li><a href="#history">Historico</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="home">
			<?php echo $speech;?>
			</div>
			<div class="tab-pane" id="profile">
				<div class="panel-group" id="accordion">
					<div class="panel panel-default">
						<div class="panel-heading">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
							Speech de Motivo
							</a>
						</div>
						<div id="collapseOne" class="panel-collapse collapse ">
						<div class="panel-body">
						<pre id="texto" style="text-align: justify;"></pre>
								<div id="agenda">
						<input type="text" id="timeagenda" name="agenda"/> 
							<input type="submit" id="btnagen" value="Agendar"/>
							</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
							Preguntas
							</a>
						</div>
						<div id="collapseTwo" class="panel-collapse collapse in">
						<div class="panel-body">
						<div id="cuadro">
						<?php echo $preg; ?>
						
						</div>
						</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="history">
			
			<?php echo $histo; ?>
			</div>
		</div>
	</div>
	</div>
	</div>
	</form>
</body>
</html>