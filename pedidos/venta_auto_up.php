<?php
	require_once("../sec.php");
	require_once("../ws/func.php");
	$result=posturl(array("a"=>"stock_local","local"=>$Xlocal),$XXurl_erp1);
	$idblo=json_decode($result);
	$idprosblo=implode(",",$idblo);	
	if($idprosblo=="") {$idprosblo="0";}
	//////
	
	$res=qry("select id from categorias where idpadre=2");
	$arr_id=array();
	while($r=mysql_fetch_row($res)){
		$arr_id[]=$r[0];
	}
	$cats=implode(",",$arr_id);
	if($cats==""){
		$cats="99999";
	}
	
	$res=qry("select estado,nivel,hora_in,(concat(date(hora_update),' 23:59:59') + INTERVAL (TIMESTAMPDIFF(MONTH,concat(date(hora_update),' 23:59:59'),now())) month),deuda from user_nivel where iduser='$Xiduser'");
	$data=mysql_fetch_row($res);
	if($data[0]>0){
		if($data[2]!="")
		{
			
			if($data[4]==0){
				$qry1="select sum(if(a.estado=1,1,0)),sum(if(a.estado=3,1,0)) from puntos  a, user_nivel b where a.login=b.login and  a.login='$Xlogin' and a.tipo=3 and a.fecha_venc>'$data[3]'";
				$res1 = qry($qry1);
				$nped=mysql_fetch_row($res1);
				if($nped[0]>0){
					$arr["msg"]="Ya Ingreso Pedido Del Mes";
					$arr["est"]=4;
					}elseif($nped[1]>0){
					$arr["msg"]="Ya Ingreso Pedido Del Mes, a la Espera de la confirmación";
					$arr["est"]=4;
					}elseif($nped[0]==0){
					$arr["est"]=3;
				}
				
				$arr["nped"]=$nped[0];
				}else{
				$qry1="select sum(if(a.estado=1,1,0)),sum(if(a.estado=3,1,0)) from puntos  a, user_nivel b where a.login=b.login and  a.login='$Xlogin' and a.tipo=3 and a.hora>'$data[3]' and date('$data[3]')=a.fecha_venc";
				$res1 = qry($qry1);
				$nped=mysql_fetch_row($res1);
				if($nped[0]>0){
					$arr["msg"]="Ya Ingreso Pedido Del Mes Pasado";
					$arr["est"]=4;
					}elseif($nped[1]>0){
					$arr["msg"]="Ya Ingreso Pedido Del Mes, a la Espera de la confirmación";
					$arr["est"]=4;
					}elseif($nped[0]==0){
					$arr["msg"]="No ha ingresado el Autoconsumo de este mes";
					$arr["est"]=3;						
				}
			}
			$qryup=mysql_fetch_row(qry("select count(*) from puntos where iduser='$Xiduser' and SUBSTRING(curdate(),1,7)=SUBSTRING(fecha_venc,1,7) and tipo=9 and estado=1"));
			if($qryup[0]>0){
				$arr["est"]=5;
			} 				
			
			
		}
		else{
			$qry1="select sum(if(a.estado=1,1,0)),sum(if(a.estado=3,1,0)) from puntos  a, usuarios b where a.login=b.login and  a.login='$Xlogin' and a.tipo=3";
			$res1 = qry($qry1);
			$nped=mysql_fetch_row($res1);
			if($nped[1]>0){
				$arr["msg"]="Ya se Ingreso su primer Pedido, Esperando Validacion";
				$arr["est"]=2;					
				}else{
				$arr["msg"]="El Paquete de Inscripcion va ser Incluido en su Primer Pedido";
				$arr["est"]=2;					
			}
			
		}
		}else{
		$arr["msg"]="Empresario Desactivado, Ingrese Nuevamente su Pedido inicial";
		$arr["est"]=1;
	}
	/////
	
	if($arr["est"]!=5){
		$arr["msg"]="Primero debe ingresar el Autoconsumo del Mes";
	}
	
	$qry=qry("select idcliente from clientes where codigo1='$Xlogin' order by lastupdate desc limit 1");
	$idcli=mysql_fetch_row($qry);
	$idcli=$idcli[0];
	$listp=array();
	
	
	$tbody="";
	$i=1;
	$res=qry("select a.idprod,TRIM(UPPER(CONCAT(b.nombre,' ',b.nombre1,' ',b.nombre2,' ',b.nombre3,' ',b.nombre4))) nombre,a.cantidad,if(a.promo=0,a.precio,a.precio),(a.cantidad+if(a.fraccion/b.unidades is null,0,a.fraccion/b.unidades))*if(a.promo=0,a.precio,a.precio) total,a.promo,if(a.fraccion>0,CONCAT(a.fraccion,b.unmedida),''),(a.cantidad+(a.fraccion/b.unidades)),UPPER(a.cantidad+(a.fraccion/b.unidades)),a.canal,a.lista,a.localdst,FLOOR(a.cantidad/b.pack) packs,b.pack from bolscanal a,productos b where b.idprod=a.codigo1 and a.login='$Xlogin' and a.local='$Xlocal' and a.estado=1 and a.idbolsa='0'  and a.lista=0 group by a.idprod,a.promo order by a.promo,nombre;");
	
	$formap=formap($Xlogin,$Xlocal);
	if($formap=="P"){
		$extra="<th>Packs</th><th>Und. Por Pack</th>";
		}else{
		$extra="<th class='ex'>Packs</th><th class='ex'>Und. Por Pack</th>";
	}
	
	$can="14"; $lp="0"; $loc="";
	while($temp=mysql_fetch_row($res)){
		$exb="";
		if($temp[5]!="0"){
			$ex="info";
			$exb="<td class='text-center'>$temp[2]</td>";
		}
		else{
			if($temp[2]==1)
			{
				$exb="<td class='text-center'>$temp[2]<button type='button' class=' pull-right plus btn btn-xs' ><span class='glyphicon glyphicon-plus'></span></button></td>";		
				}else{
				$exb="<td class='text-center'><button type='button' class='less btn btn-xs pull-left' ><span class='glyphicon glyphicon-minus'></span></button>$temp[2]<button type='button' class=' pull-right plus btn btn-xs' ><span class='glyphicon glyphicon-plus'></span></button></td>";		
			}
		}
		if($formap=="P"){
			$exb="<td>$temp[12]</td><td>$temp[13]</td><td>$temp[2]</td>";
		} 
		$tbody .= "<tr promo='$temp[5]' unit='$temp[3]' cant='$temp[7]' class='list $ex' idp='$temp[0]'><td>$i</td><td>$temp[1]</td>".$exb."<td>$temp[6]</td><td>$temp[3]</td><td>$temp[4]</td><td><button class='btn btn-warning remove btn-xs'><span class='glyphicon glyphicon-remove'></span></button></td></tr>";
		$i++; $can=$temp[9]; $lp=$temp[10]; $loc=$temp[11];
	}
	
	if($can!="0" && $lp!="0")
	{
		$adjs="$('#listcanales').val('$can'); $('#listcanales').attr('disabled',true); $('#listpre').val('$lp'); $('#listpre').attr('disabled',true); $('#listloc').val('$loc'); $('#listloc').attr('disabled',true);";
		$res=qry("select a.idprod,a.precio,UPPER(a.nombre) nombre,1 pack,'' barras,0 stock,a.promo, '' precvar, '' fraccion,'' unmedida, '' unidades,if(a.codigo1 in ($idprosblo ) and  a.codigo1!=505,1,0) from productos$Xpais a   where a.categoria not in ($cats) and isnull(a.nombre)!=1 and a.nombre!='' and if(a.promo=0,1, a.prof in (0,1,3,5,7)) and a.estado=1 GROUP BY a.idprod order by a.nombre;");
		$prod=array();
		
		
		while($temp=mysql_fetch_row($res)){ $prod[]=$temp; }
		$listp=json_encode($prod);
		}else{
		$qprod=qry("select a.idprod,a.precio,UPPER(a.nombre) nombre,1 pack,'' barras,0 stock,a.promo, '' precvar, '' fraccion,'' unmedida, '' unidades,if(a.codigo1 in ($idprosblo) and a.codigo1!=505,1,0) from productos$Xpais a where a.categoria not in ($cats) and isnull(a.nombre)!=1 and a.nombre!='' and if(a.promo=0,1, a.prof in (0,1,3,5,7)) GROUP BY a.idprod order by a.nombre;");
		
		while($temp=mysql_fetch_row($qprod)){
			$listp[]=$temp;
		}
		$listp=json_encode($listp);
	}
	$res=qry("select distinct idbolsa from bolscanal where login='$Xlogin' and local='$Xlocal' and estado=1 and idbolsa!=0;");
	$html="";
	if(mysql_num_rows($res)>0){
		while($temp=mysql_fetch_row($res)){ $html .= "<button inf='$temp[0]' class='btn btn-info ped'>Pedido $temp[0]</button>"; } 
	}
	
	$res=qry("select id,distrito from ubigeo where departamento='LIMA' and provincia='LIMA' ORDER BY distrito;");
	$dist="<option disabled selected value='0'>--Elija--</option>";
	while($temp=mysql_fetch_row($res))
	{$dist .= "<option value='$temp[0]'>$temp[1]</option>"; }
	
	$lprec="<option value='0' selected disabled>--Elija Una Lista--</option><option value=''>Precio Regular</option>";
	for($i=1;$i<13;$i++)
	{ $lprec .= "<option value='$i'>Lista $i</option>"; }
	
	$qloc=qry("select local,nombre,formap from locales where tipo='P';");
	$loc="<option value='0' selected disabled>--Elija Una Local--</option>";
	while($temp=mysql_fetch_row($qloc)){ $loc .= "<option inf='$temp[2]' value='$temp[0]'>$temp[1]</option>"; }
	
	
	function formap($login,$local){ $r=mysql_fetch_row(qry("select locales.formap from bolscanal,locales where bolscanal.localdst=locales.local and bolscanal.login='$login' and bolscanal.local='$local' limit 1")); return $r[0];}
	
	///////////////////////////UBIGEO//////////////////////
	//DEP
	$res=mysql_query("select distinct departamento from ubigeo;");
	$dep="<option selected disabled>--Elija--</option>";
	while($temp=mysql_fetch_row($res))
	{$dep .= "<option value='$temp[0]'>$temp[0]</option>";}
	
	//ARRAY DE DATOS
	$adep=array();
	$adpr=array();
	$res=mysql_query("select departamento,provincia,distrito,year(now()) from ubigeo;");
	while($temp=mysql_fetch_row($res))
	{
		if(!isset($adep[$temp[0]]))
		{$adep[$temp[0]]=array();}
		if(!in_array("<option value='$temp[1]'>$temp[1]</option>",$adep[$temp[0]]))
		{$adep[$temp[0]][]="<option value='$temp[1]'>$temp[1]</option>";}
		$adpr[$temp[0]][$temp[1]] .= "<option value='$temp[2]'>$temp[2]</option>";
		$year=$temp[3];
	}
	$adep=json_encode($adep);
	$adpr=json_encode($adpr);
	////////////Nuevo Direccion///
	
	$selcli="<option disabled selected value=''>--seleccine--</option>";
	$arrcli=getrucs($Xlogin);
	$r=json_decode($arrcli);
	$n=count($r);
	for($ii=0;$ii<$n;$ii++)
	{
		$selcli.="<option value='{$r[$ii][0]}'>{$r[$ii][2]}({$r[$ii][1]})</option>";
	}
	$a_cli=json_encode($r);
	///////////Envio///
	
	$arrclit=getenvios($Xlogin);
	$rt=json_decode($arrclit);
	$seldest1="<option disabled selected value=''>--seleccine--</option>";
	$seldest2="";
	$n=count($rt);
	for($ii=0;$ii<$n;$ii++)
	{
		$seldest2.="<option value='{$rt[$ii][0]}' $selec>{$rt[$ii][1]}</option>";
	}
	$seldest=$seldest1.$seldest2;
	$a_dest=json_encode($rt);	
	
	
	///////////////////////////////////////////////////////////////
?>
<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Venta Productos</title>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css"> 
		
	</head>
	<style>
		.resultselect{background:#94D5AA;}
		.resultlist{cursor:default;}
		#listres{height:auto; padding:0px;}
		#listres2{height:auto; padding:0px;}
		.nm{padding-left:2px; padding-right:2px;}
		.nb{margin-left:0px; margin-right:0px;}
		.t20{height:15px;}
		.reslistcli{background:#94D5AA;}
		.listcli{cursor:default;}
		.ex{display:none;}
	</style>
	<body>
		<div class="container">
			<br>
			<div id='listb' class="row"><?php echo $html;?></div>
			<br>
			<div class="row">
				<!-- 
					<div class="col-sm-2">
					
					<select class="form-control" id="listloc"><?php echo $loc;?></select>
					</div>
					<div class="col-sm-2"><select disabled class="form-control" id="listpre"><?php echo $lprec;?></select></div>
					<div class="col-sm-2"><select disabled class="form-control" id="listcanales"><?php echo $ocanal;?></select></div>
					
				-->
				<input id="listloc" type='hidden' value='<?php echo $Xlocal;?>' />
				<input id="listpre" type='hidden' value='0' />
				<input id="listcanales" type='hidden' value='14' />
				<div class="col-sm-6 col-md-5 col-xs-12 col-lg-5">
					<div class="form-group">
						<div class="input-group">
							<input id="busq" type="text" placeholder="Ingrese Un Producto" class="form-control input-sm">
							<span class="input-group-btn">
								<button class="btn btn-default btn-sm" type="button">
									<span class="glyphicon glyphicon-search"></span>
								</button>	
							</span>
						</div>
						<div style='position:absolute; z-index:9;' id='listres'></div>
					</div>
				</div>
			</div>
			
			
			<div style="display:none;" id="infprod">
				<div class="row">
					<div class="col-md-6 col-sm-7 col-xs-6">
						<input disabled id="nametmp" class="form-control" />
					</div>
					<div class="col-md-2 col-sm-3 col-xs-6"> 
						<div class="form-group"> 
							<div class="input-group">
								<span class="input-group-btn">
									<button id="disc" class="btn btn-default btn-sm" type="button">
										<span class="glyphicon glyphicon-minus"></span>
									</button>
								</span>
								<input value="1" id="cant" type="text" class="form-control input-sm text-center">
								<span class="input-group-btn">
									<button id="addc" class="btn btn-default btn-sm" type="button">
										<span class="glyphicon glyphicon-plus"></span>
									</button>
								</span>
							</div>
						</div>
					</div>
					
					<div class="col-md-2 col-sm-3 col-xs-6">
						<div class="form-group">
							<input class="form-control" disabled id="pretemp"/>
						</div>
					</div>		 
					<div class="col-sm-4 col-md-1 col-xs-4 col-lg-1">
						<div class="form-group">
							<button id="addprod" class="btn btn-success btn-sm">Agregar</button>
						</div>
					</div>
				</div>
				<div id="inffr" class="row">
					<div class="col-sm-1 form-group">
						<label>Fraccion</label>
						<input value="0" id="esfraccion" type="checkbox">
					</div>
					<div class="col-sm-3 form-group">
						<label>Cantidad<label id="unmd"></label></label>
						<input disabled id="cantf" class="form-control input-sm">
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="panel panel-success">
					<div style="height:48px;" class="panel-heading">Lista Productos <strong>(<?php echo $arr["msg"];?>)</strong></div> 
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead><tr><th>Orden</th><th>Nombre</th><?php echo $extra;?><th>Cantidad</th><th>Fraccion</th><th>Precio Unitario</th><th>Precio Total</th><th>Eliminar</th></tr></thead>
							<tbody id='listprod'><?php echo $tbody;?></tbody>
						</table>
					</div>
				</div>
				<div class="col-sm-2 pull-right"><input disabled class="form-control input-sm" value="0" id="mntacm"></div>
			</div>
			
			<div class="row text-center">
				<button id="canc" class="btn btn-danger">Cancelar</button>
				<button id="save" class="btn btn-primary">Guardar</button>
				<button id="conf" class="btn btn-success">Confirmar</button>
			</div>
		</div>
		
		<!-------------------------------- Modal ---------------------------------------------------------------------------->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header"> 
						<h3 class="modal-title" id="myModalLabel">Informacion del Pago</h3>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">	
								<!--<div class="form-group col-sm-6 nm">
									<label>Razon Social</label>
									<select id="idcl1" class='form-control input-sm'></select>
								</div>-->
								<div class="form-group">
									<label for="idruc"> RAZON SOCIAL:</label>
									<div class="input-group">	
										<select class="form-control input-sm" name="idruc" id="idruc">'.<?php echo $selcli; ?>.'</select>
										<span class="input-group-btn">
											<button data-toggle="tooltip" data-placement="left" title="Nueva Razon Social" class="btn btn-info calendar btn-sm" type="button" id="add_ruc" ><span class="glyphicon glyphicon-plus"></span></button>
										</span>	
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="col-sm-12">
											<div class="form-group adic"><label for="ruc">RUC o DNI*</label><input required class="form-control input-sm dataruc" type="text" name="ruc" id="ruc" value=""></div>
											<div class="form-group adic"><label for="razon">Razon Social*</label><input required class="form-control input-sm dataruc" type="text" name="razon" id="razon" value=""></div>
											<div class="form-group adic"><label for="dir_ruc">Direccion*</label><input required class="form-control input-sm dataruc" type="text" name="dir_ruc" id="dir_ruc" value=""></div>
											<div class="text-center adic">
												<!--<input type="button" class=" btn btm-sm btn-primary btn-sm" data-loading-text="Agregando..." id="add_cli" value="Agregar" />
												<input type="button" class="btn btm-sm btn-primary btn-sm" data-loading-text="Actualizando..." id="save_cli" value="Actualizar" />-->
											</div>
										</div>
									</div>
								</div>						
								<!--<div class="row nb">
									<div class="form-group col-sm-6 nm">
									</div>
								</div>-->
								<div class="col-md-12 desc">
									
								</div>
								<div class="row">
									<div class="col-sm-6 ">
										<div class="divcap">
											
										</div>
									</div>
									<div id="divmov" class="col-sm-12">
										<label>Movilidad</label>
										<div class="input-group">
											<span class="input-group-addon">
												<input type="checkbox" class="" name="transp"  id="transp"  value="1">
											</span>
											<input type="text" value="Incluir Movilidad" disabled class="form-control">
											<div class="input-group-btn">
												<button id="apenv" disabled class="btn btn-success">Calcular</button>
											</div>
										</div>
										<small>* No olvidar hacerle click al boton Calcular</small>
										<div id="envd">
											<div class="form-group">
												<label for="idruc"> DESTINO:</label>
												<div class="input-group">	
													<select class="form-control input-sm" name="iddest" id="iddest"><?php echo $seldest; ?></select>
													<span class="input-group-btn">
														<button data-toggle="tooltip" data-placement="left" title="Nueva Direccion" class="btn btn-info calendar btn-sm" type="button" id="add_dest" ><span class="glyphicon glyphicon-plus"></span></button>
													</span>	
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12" id="div_dest">
													<div class='form-group nm col-sm-6'><label>Direccion</label><input placeholder='Direccion' class='form-control input-sm indest' id='dir'/></div><div class='form-group nm col-sm-6'><label>Referencia</label><input placeholder='Referencia' class='form-control input-sm indest' id='ref'/></div><div class='form-group col-sm-4'><label>Departamento</label><select id='depa' class='form-control input-sm indest'><?php echo $dep;?></select></div><div class='form-group col-sm-4'><label>Provincia</label><select id='prov' class='form-control input-sm indest'></select></div><div class='form-group col-sm-4 indest'><label>Distrito</label><select id='dist' class='form-control input-sm indest'></select></div>
												</div>
											</div>									
										</div>
									</div> 
								</div>
								<div class="form-group col-sm-6 nm">
									<label>Forma de Pago</label>
									<select id="fpago" class='form-control input-sm'><option value='' selected disabled>--Elija--</option><option>DEPOSITO</option><option>EFECTIVO</option><option value='POSVISA'>VISA</option><option value="POSMC">MASTERCARD</option><option value="AMERICAN">AMERICAN EXPRESS</option></select>
								</div>	
							</div>
						</div>
					</div>
					<div class="modal-footer text-center form-inline">
						<label>Peso:</label>
						<input id="totpeso" disabled value="0" class="form-control" style="width:70px"/>
						<label>Envio</label>
						<input id="totenv" disabled value="0" class="form-control" style="width:70px"/>	
						<label>Monto a Pagar:</label>
						<input id="totmon" disabled value="0" class="form-control" style="width:120px"/> 
						<button id="savev" type="button" class="btn btn-success">Confirmar</button>
						<button data-dismiss="modal" type="button" class="btn btn-warning">Cancelar</button>
					</div> 
				</div>
			</div>
		</div>
		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.js"></script>
		<script>
			var adep=<?php echo $adep;?>;
			var adpr=<?php echo $adpr;?>;
			var a_cli= <?php echo $a_cli;?>;
			var a_dest= <?php echo $a_dest;?>;
			var listp=<?php echo $listp;?>;
			var idbol=0;
			var lmres=10;
			var idcli=0;
			var formp="<?php echo $formap;?>";
			var objte=new Array();
			var bus=setTimeout(function(){},500);
			function buscaprod(idprod){
				r=false;
				$(".list").each(function(index){
					if($(this).attr("idp")==idprod || $(this).attr("promo")==idprod){
						r=true;
					}
				});
				return r;
			}
			function monto(){
				tot=0; $(".list").each(function(index){
					tot=parseFloat(tot)+(parseFloat($(this).attr("cant"))*parseFloat($(this).attr("unit")));
				});
				tot=Math.round(1000*tot)/1000;
				tot=tot.toFixed(2);
				return tot;
			}
			function sumapag(){ tot=0; $(".listmnt").each(function(index){tot=tot+(parseFloat($(this).attr("inf")));}); return tot;}
			
			function alertb(obj,time,text,tipo){
				$(obj).prepend("<div style='top:2px;' class='text-center alert alert-"+tipo+"'>"+text+"</div>");
				setTimeout(function(){$($(".alert")[0]).toggle("slow");},time*1000);
			}
			function cambio_trans(){
				if($("#transp").is(':checked')){
					////calculo de niveles//	
					$("#envd").show();
					$("#apenv").attr("disabled",false);
					$("#savev").prop("disabled",true);				
				}
				else{
					$("#envd").hide();
					$("#apenv").attr("disabled",true);
					$("#vend1").attr("readonly",false);
					$("#vend1 option").attr("disabled",false);
					$("#savev").prop("disabled",false);
					var tott=parseFloat($("#totmon").val())-parseFloat($("#totenv").val());
					$("#totmon").val(tott);
					$("#totenv").val(0);
					
				}	
			}
			$(window).load(function(){
				$("#envd").hide();
				////JS DATOS//
				$('#add_ruc').click(function(){
					$(".dataruc").prop("readonly",false);
					$(".adic").show();
					$("#ruc").val('');
					$("#razon").val('');
					$("#dir_ruc").val('');
					$("#add_cli").show();
					$("#save_cli").hide();
					$("#idruc").val('');
				});
				$("body").on("change","#idruc", function(){
					var valu=$(this).val();
					$(".dataruc").parent().removeClass('has-error');
					$(".adic").show();
					var opcs="<option value='' selected disabled style='display: none;'>-seleccione-</option>";
					for(i=0;i<a_cli.length;i++){
						if(valu==a_cli[i][0])
						{
							$("#ruc").val(a_cli[i][1]);
							$("#razon").val(a_cli[i][2]);
							$("#dir_ruc").val(a_cli[i][3]);
						}
						
					}
					$(".dataruc").prop("readonly",true);
					$("#add_cli").hide();
					$("#save_cli").show();
				});
				
				/////
				/////JS ENVIO///
				
				$("body").on("change","#iddest", function(){
					var valu=$(this).val();
					$(".indest").parent().removeClass('has-error');
					$("#div_dest").show();
					var opcs="<option value='' selected disabled style='display: none;'>-seleccione-</option>";
					for(var i=0;i<a_dest.length;i++){
						if(valu==a_dest[i][0])
						{
							var temp=a_dest[i];
							$("#dir").val(a_dest[i][1]);
							$("#ref").val(a_dest[i][2]);
							$("#depa").val(a_dest[i][3])
							$("#depa").change();			
							$("#prov").val(temp[4]).change();
							$("#dist").val(temp[5]);
							$(".indest").prop('disabled',true);
							//$("#dir_ruc").val(a_dest[i][2]);
						}
					}
					$("#save_dest").show();
				});
				
				$('#add_dest').click(function(){
					$("#div_dest").show();
					$(".indest").val('');
					$(".indest").prop('disabled',false);
					$("#iddest").val('');
				});
				///
				$('#tabledv').hide(); $('#dvsavecl').hide(); $('#logcl').hide();
				<?php echo $adjs;?>
				$("#mntacm").val(monto());
				$("#addc").click(function(){$("#cant").val(parseFloat($("#cant").val())+1);});
				$("#disc").click(function(){if(parseFloat($("#cant").val())>1){$("#cant").val(parseFloat($("#cant").val())-1);}});
				$("#busq").keyup(function(e2){
					$("#listres").show();
					if(e2.keyCode!=38 & e2.keyCode!=40 & e2.keyCode!=13){
						
						$("#busq").attr("placeholder","Ingrese Un Producto");
						$("#busq").parent().removeClass("has-error");
						$("#infprod").hide();
						nres=0;
						lres=new Array();
						dato=document.getElementById("busq").value.toUpperCase();
						if(dato!=""){
							for(i=0;i<listp.length;i++){if((listp[i][2].indexOf(dato)!=-1) | (listp[i][4]==dato)){ if(nres<lmres){lres[nres]=listp[i]; nres++;} else{break;}}}
							html="";
							
							if(nres>0){
								for(i=0;i<lres.length;i++)
								{
									if(lres[i][6]!="0"){
										st="style='color:#284DAA; font-weight: bold;'";
									}else{st="";}
									if(lres[i][11]==1){
										st+="style='color:#CCC;'";
										html=html+"<div "+st+" pos='"+(i+1)+"' stk='"+lres[i][5]+"' class='resultlist_stockless' barr='"+lres[i][4]+"' nom='"+lres[i][2]+"' fra='"+lres[i][8]+"' unm='"+lres[i][9]+"' unp='"+lres[i][10]+"' idpr='"+lres[i][0]+"' punit='"+lres[i][1]+"' precv='"+lres[i][7]+"' pack='"+lres[i][3]+"'>"+lres[i][2]+" - <strong>(SIN STOCK)</strong></div>";
										}else{
										html=html+"<div "+st+" pos='"+(i+1)+"' stk='"+lres[i][5]+"' class='resultlist' barr='"+lres[i][4]+"' nom='"+lres[i][2]+"' fra='"+lres[i][8]+"' unm='"+lres[i][9]+"' unp='"+lres[i][10]+"' idpr='"+lres[i][0]+"' punit='"+lres[i][1]+"' precv='"+lres[i][7]+"' pack='"+lres[i][3]+"'>"+lres[i][2]+" - S/."+lres[i][1]+" - x"+lres[i][3]+"</div>";
									}	
								}
								$("#listres").html(html);
								$("#listres").addClass("form-control");
								$("#listres").css({width:$("#busq").css("width")});
								h=0;
								$(".resultlist").each(function(index){h=h+parseInt($(this).css("height"));});
								$(".resultlist_stockless").each(function(index){h=h+parseInt($(this).css("height"));});
								$("#listres").css({height:(h+6)+"px"});
							}
							else
							{$("#listres").html("");
								$("#listres").hide();
							$("#listres").removeClass("form-control");}
						}
						else{
							$("#listres").html("");
							$("#listres").hide();
							$("#listres").removeClass("form-control");
							$("#listres").css({height:"0px"});
						}
						
						$("#busq").keydown(function(e){
							tecla=e.keyCode;
							if((tecla==38 | tecla==40))
							{
								if($(".resultselect").length>0)
								{
									pos=$(".resultselect").attr("pos");
									if(tecla==40){sig=parseInt(pos)+1;}
									else{sig=parseInt(pos)-1;}
									if(sig==0){ sig=$(".resultlist").length; } 
									if(sig== ($(".resultlist").length+1)){sig=1;}
									$(".resultlist").removeClass("resultselect");
									$('div[pos='+sig+']').addClass("resultselect");
								}
								else
								{$($(".resultlist")[0]).addClass("resultselect");}
							}
							
							if(tecla==13)
							{
								if(nres==1)
								{
									$(".resultlist").addClass("resultselect");
									$(".resultselect").trigger("click");
								}
								else{
									obj=$(".resultselect");
									if(buscaprod(obj.attr("idpr")))
									{
										$("#busq").attr("placeholder","Ya Esta En La Lista");
										$("#busq").parent().addClass("has-error");
										setTimeout(function(){$("#busq").val(""); $("#busq").focus();},1);
									}
									else{
										objte=new Array();
										objte[0]=obj.attr("idpr");
										objte[1]=obj.attr("pack");
										objte[2]=obj.attr("precv");
										$("#unmd").text(" ("+obj.attr("unm")+")");
										$("#cantf").attr("disabled",true);
										$("#pretemp").val(obj.attr("punit"));
										if(obj.attr("precv")=="1")
										{$("#pretemp").attr("disabled",false);}
										else
										{$("#pretemp").attr("disabled",true)}
										
										if(obj.attr("fra")=="1")
										{ $("#inffr").show(); $("#cantf").val(obj.attr("unp"));}
										else
										{ $("#inffr").hide(); }
										
										
										$("#stctemp").val(obj.attr("stk"));
										$("#nametmp").val(obj.attr("nom")+"("+obj.attr("stk")+")");
										$("#cant").focus();
										$("#infprod").show();
									}
									$("#busq").val("");
									$("#listres").html("");
									$("#listres").hide();
								$("#listres").removeClass("form-control");}	
							}
							e.stopImmediatePropagation();
						});
					}
				});
				
				
				
				$(document).on("mouseenter",".listcli",function(){
					$(".listcli").removeClass("reslistcli");
					$(this).addClass("reslistcli");
				});
				
				
				
				$(document).on("mouseenter",".resultlist",function(){
					$(".resultlist").removeClass("resultselect");
					$(this).addClass("resultselect");
				});
				
				$(document).on("click",".resultselect",function(){
					obj=$(".resultselect");
					if(buscaprod(obj.attr("idpr")))
					{
						$("#busq").attr("placeholder","Ya Esta En La Lista");
						$("#busq").parent().addClass("has-error");
						setTimeout(function(){$("#busq").val(""); $("#busq").focus();},1);
					}
					else{
						objte=new Array();
						objte[0]=obj.attr("idpr");
						objte[1]=obj.attr("pack");
						objte[2]=obj.attr("precv");
						$("#pretemp").val(obj.attr("punit"));
						$("#unmd").text(" ("+obj.attr("unm")+")");
						$("#cantf").attr("disabled",true);
						if(obj.attr("precv")=="1")
						{$("#pretemp").attr("disabled",false);}
						else
						{$("#pretemp").attr("disabled",true);}
						
						$("#esfraccion").val("0"); $("#esfraccion").prop("checked",false); $("#cantf").val("");
						if(obj.attr("fra")=="1")
						{ $("#inffr").show(); $("#cantf").val(obj.attr("unp"));}
						else
						{ $("#inffr").hide(); }
						
						$("#stctemp").val(obj.attr("stk"));
						$("#nametmp").val(obj.attr("nom")+"("+obj.attr("stk")+")");
						$("#infprod").show();
						$("#cant").focus();
					}
					$("#busq").val("");
					$("#listres").html("");
					$("#listres").hide();
					$("#listres").removeClass("form-control");
				});
				
				
				$("#addprod").click(function(){
					c=$("#cant").val();
					if(formp=="P"){c=parseInt(c)*parseInt(objte[1]);}
					de=new Array();
					de[0]=objte[2];
					de[1]=$("#pretemp").val();
					if((!isNaN(parseInt($("#cant").val())) & (parseInt($("#cant").val())>0)) | ( $("#esfraccion").val()=="1" ))
					{$.post("../res/pedidos/venta_canales_auto.php",{i:idbol,a:"ap",c:c,p:objte[0],de:de,fr:$("#esfraccion").val(),cfr:$("#cantf").val(),can:$("#listcanales").val(),lp:$("#listpre").val(),loc:$("#listloc").val()},function(data){
						$("#listprod").html(data);
						$("#cant").val("1");
						$("#infprod").hide();
						$("#mntacm").val(monto());
					});}
					else
					{$("#cant").val("1").focus();}
					$("#busq").focus();
				});
				
				$(document).on("click",".ped",function(){
					obj=$(this).attr("inf");
					$.post("../res/pedidos/venta_canales_auto.php",{a:"db",i:obj},function(data){
						idbol=obj;
						$("#listprod").html(data);
						$("#mntacm").val(monto());
					});
					
				});
				
				
				$(document).on("click",".remove",function(){
					p=$(this).parent().parent().attr("idp");
					m=$(this).parent().parent().attr("promo");
					$.post("../res/pedidos/venta_canales_auto.php",{i:idbol,a:"qp",p:p,m:m,lp:$("#listpre").val()},function(data){$("#listprod").html(data); $("#mntacm").val(monto());});
				});
				
				$(document).on("click",".plus",function(){
					p=$(this).parent().parent().attr("idp");
					m=$(this).parent().parent().attr("promo");
					$.post("../res/pedidos/venta_canales_auto.php",{i:idbol,a:"plus_prod",p:p,m:m,lp:$("#listpre").val()},function(data){$("#listprod").html(data); $("#mntacm").val(monto());});
				});
				
				$(document).on("click",".less",function(){
					p=$(this).parent().parent().attr("idp");
					m=$(this).parent().parent().attr("promo");
					$.post("../res/pedidos/venta_canales_auto.php",{i:idbol,a:"less_prod",p:p,m:m,lp:$("#listpre").val()},function(data){$("#listprod").html(data); $("#mntacm").val(monto());});
				});
				
				
				$("#save").click(function(){
					$.post("../res/pedidos/venta_canales_auto.php",{a:"sb"},function(data){if(data[0]=="1"){ idbol="0"; $("#listb").html(data[2]); 
						alertb(".container",1,"Bolsa Guardada","warning");
					$("#listprod").html("");} else{alertb(".container",1,"Numero Maximo de Bolsas Alcanzado","warning");}},"json");
				});
				
				
				$("#canc").click(function(){
					$.post("../res/pedidos/venta_canales_auto.php",{a:"bb",i:idbol},function(data){
						alertb(".container",1,"Bolsa Borrada","warning");
					$("#listb").html(data); idbol="0"; $("#listprod").html(""); $($("#listpre").children()[0]).attr("selected",true); $($("#listcanales").children()[0]).attr("selected",true); $($("#listloc").children()[0]).attr("selected",true); $("#listcanales").attr("disabled",true); $("#listpre").attr("disabled",true); $("#listloc").attr("disabled",false);});
				});
				
				$("#conf").click(function(){
					if($(".list").length>0){
						//$("#transp").click();
						if(!$("#transp").is(':checked')){
							//$("#transp").click();
						}
						$('#infocl').hide();
						//$("#totmon").val(monto());
						$(".desc").html("");	
						$("#montotmp").val(monto());
						$("#myModal").modal({backdrop:"static"});
						////calculo de niveles//
						$.post("../res/pedidos/venta_canales_auto.php",{a:'descuento_unico',i:idbol,lp:$("#listpre").val(),tipov:"up"},function(data){
							$(".desc").html(data.text);
							if($("#iddest").children().length==1){
								$("#dir").val(data.dir);
								$("#ref").val(data.ref);
								$("#depa").val(data.dep);
								$("#prov").html("<option>"+data.prov+"</option>");
								$("#dist").html("<option>"+data.dist+"</option>");
							}
							$("#totmon").val(data.total);
							$("#totpeso").val(parseFloat(data.peso));
							//alert(parseFloat(data.peso));
							
							if(data.est==0)
							{
								$("#savev").prop("disabled",true);
								$("#idruc").prop("disabled",true);
								$("#transp").prop("disabled",true);
							}
							else{
								$("#savev").prop("disabled",false);
								$("#idruc").prop("disabled",false);
								$("#transp").prop("disabled",false);
								cambio_trans();							
							}
						},"json");////
					}
					else
					{alert("Debe Agregar Productos");}
				});
				
				
				///////////////Añadir Transporte/////////////
				$(document).on("click","#transp",function(){
					
					cambio_trans();
					
				});
				//$("#transp").trigger("click");
				///////////////////////////////////////////REMOVE PAGO//////////////////////////////////////////
				$(document).on("click",".rmvpag",function(){
					$(this).parent().parent().remove();
					if(sumapag()<monto()){$("#addpag").prop("disabled",false);}
					var op=$(this).parent().parent().find("td:nth-child(2)").text();
					$("#tipopago").append('<option value="'+op+'">'+op+'</option>');
				});
				/////////////////////////////////////////AÑADIR PAGO////////////////////////////////////////////
				$("#addpag").click(function(){
					if((parseFloat(sumapag())+parseFloat($("#montotmp").val()))>monto()){return false;}
					if(sumapag()<=monto()){}
					else{$(this).prop("disabled",true); return false;}
					
					var t=$("#tipopago").val();
					$("#tipopago option[value='"+t+"']").remove();
					
					$("#montotmp").attr("placeholder","");
					$("#montotmp").parent().removeClass("has-error");
					l=$(".listmnt").length+1;
					m=$("#montotmp").val();
					
					if(l<4){
						if(m>0){
							$("#listpag").append("<tr tip='"+t+"' class='listmnt' inf='"+m+"'><td>"+l+"</td><td>"+t+"</td><td>S/."+m+"</td><td><button class='rmvpag btn btn-warning btn-xs'><span class='glyphicon glyphicon-remove'></span></button></td></tr>");
							$('#tabledv').show();
							$("#montotmp").val("");
						}else
						{
							$("#montotmp").attr("placeholder","Ingrese Datos Correctos");
							$("#montotmp").parent().addClass("has-error");
							setTimeout(function(){$("#montotmp").val(""); $("#montotmp").focus();},1);
						}
					}else
					{alert("Numero Maximo de Pagos Alcanzado");}
					if(sumapag()>=monto()){$("#addpag").prop("disabled",true);}
					else{$("#addpag").prop("disabled",false);}
				});
				//////////////////////////////////////////////////////////////////////////////////////////////////////
				
				$("#savev").click(function(){
					r=confirm("Desea Proceder?");
					if(r==true){
						var totalp=0;
						var ruc='';
						var razon='';
						var dir_ruc='';
						var idruc='';
						var dir='';
						var ref='';
						var depa='';
						var prov='';
						var dist='';
						var iddest='';
						var transp=0;
						var tpago=[];
						var pago=[];		
						var btn = $(this);
						var iden=0;
						btn.button('loading');
						
						/*aver=["#dir","#ref","#depa","#prov","#dist"];
							if($("#transp").is(':checked')){
							for(i=0;i<aver.length;i++){obj=$(aver[i]); if(obj.val()=="" || obj.val()==null || obj.val().trim()==""){obj.focus(); return false;}}
						}*/
						var dvr=[];
						/////
						//alert(3);
						if($('#idruc').val()==null){
							ruc=$('#ruc').val(); dvr.push([1,$('#ruc'),'',0]);
							razon=$('#razon').val(); dvr.push([1,$('#razon'),'',0]);
							dir_ruc=$('#dir_ruc').val(); dvr.push([0,$('#dir_ruc'),'',0]);	
						}
						else{
							idruc=$('#idruc').val(); dvr.push([1,$('#idruc'),'',0]);
						}
						var fpago=$('#fpago').val(); dvr.push([1,$('#fpago'),'',0]);
						/*if(fpago=="EFECTIVO"){
							transp=0;
							$('#transp').prop('checked', false);
							}else{
							transp=1;
							$('#transp').prop('checked', true);
						}*/
						if($('#transp').is(':checked'))
						{
							transp=1;
							if($('#iddest').val()==null){
								dir=$('#dir').val(); dvr.push([1,$('#dir'),'',0]);
								ref=$('#ref').val(); dvr.push([1,$('#ref'),'',0]);
								depa=$('#depa').val(); dvr.push([1,$('#depa'),'',0]);
								prov=$('#prov').val(); dvr.push([1,$('#prov'),'',0]);
								dist=$('#dist').val(); dvr.push([1,$('#dist'),'',0]);
							}
							else{
								iddest=$('#iddest').val(); dvr.push([1,$('#iddest'),'',0]);
								depa=$('#depa').val(); dvr.push([1,$('#depa'),'',0]);
								prov=$('#prov').val(); dvr.push([1,$('#prov'),'',0]);
								dist=$('#dist').val(); dvr.push([1,$('#dist'),'',0]);					
							}
						}
						/////
						if(tr(dvr)==false){
							//	alert(1);
							btn.button('reset');
							return false;
						}
						//alert(2);
						$.post("../res/pedidos/venta_canales_auto.php",{
							a:"sv_transporte",
							idbol:idbol,
							fpago:$("#fpago").val(),
							/*idcli:$("#vend1").val(),
								infex:$("#nombre").val(),
								socio:$("#vend1").val(),
								nota:$("#nota").val(),
								nopc:$("#nameopc").val(),
								trans:$("#transp:checked").val(),
								dir:dir,
								ref:ref,
								dep:dep,
								pro:pro,
								dis:dis,
							ruc:ruc*/
							idruc:idruc ,razon:razon ,dir_ruc:dir_ruc ,ruc:ruc ,dir:dir ,ref:ref ,depa:depa ,prov:prov ,dist:dist ,iddest:iddest,tpago:tpago,pago:pago,transp:transp,lp:$("#listpre").val()
							,tipov:"up"},function(data){
							btn.button('reset');
							if(data=="1"){ alert("Pedido Enviado");location.reload();
							}});
					}
					else{}
				});
				
				///////////////////////////////////////////////////////////////////////////////////////
				/*$("#fpago").change(function(){
					var valfp=$(this).val();
					if(valfp=="EFECTIVO"){
					$("#envd").hide();
					}else{
					$("#envd").show();
					}
				})*/
				///////////////////////////////////////////////////////////////////////////////////////
				$('#datacliente').submit(function(event){
					event.preventDefault();
					var patt = new RegExp("(\\D|\\s)");
					if(patt.test($("#idncl").val())==true){$("#idncl").val("").attr("placeholder","ID NO VALIDO(SOLO NUMEROS)"); return false;}
					
					idcli=$("#idncl").val(); $("#busq2").val($("#nombre").val());
					var data=[];
					$(".nclx").each(function(){data.push($(this).val());});
					
					$("#namem").text($("#nombre").val()+","+$("#apellido").val());
					$("#docim").text("("+$("#idncl").val()+")");
					
					$("#infclnn").hide();
					$("#infclr").show();
					
					
					$.post("../res/pedidos/venta_canales_auto.php",{a:"newcl",data:data,canal:$("#listcanales :selected").text()},function(fff){
						if(fff=="1"){$("#infocl").hide(); $('#dvsavecl').hide();}
					});
				});
				///////////////////////////////////////////////////////////////////////////////////////
				
				$(document).on("click",function(){$("#listres").html(""); $("#listres").hide();$("#listres").removeClass("form-control");});
				
				
				
				
				$(document).on("click","#cancel",function(){
					$("#infocl").hide();
					$(".nclx").val("");
				});
				
				
				$(document).on("click",function(){$("#listres2").hide();});
				
				
				$("#esfraccion").click(function(){
					if($(this).prop("checked"))
					{$(this).val("1"); $("#cantf").attr("disabled",false); }
					else{$(this).val("0"); $("#cantf").attr("disabled",true); }
				});
				
				
				$("#listcanales").change(function(){
					$.post("../res/pedidos/venta_canales_auto.php",{a:"filprod",data:$(this).val(),lp:$("#listpre").val()},function(data){
						listp=data;
						$("#listcanales").attr("disabled",true);
						$("#busq").attr("disabled",false);
						
					},"json");
				});
				
				$("#listpre").change(function(){ $("#listpre").attr("disabled",true); $("#listcanales").attr("disabled",false); });
				$("#listloc").change(function(){
					if($("#listloc :selected").attr("inf")=="P"){$(".ex").show(); formp="P";} else{$(".ex").hide(); formp="";}
					$("#listloc").attr("disabled",true);
					$("#listpre").attr("disabled",false);
				});
				
				
				$(document).on("change","#depa",function(){
					$("#prov").html("");
					$("#dist").html("");
					dp=$("#depa").val();
					lp="<option selected disabled>--Elija--</option>";
					ap=adep[dp];
					for(i=0;i<ap.length;i++){lp=lp+ap[i];}
					$("#prov").html(lp);
				});
				
				$(document).on("change","#prov",function(){
					$("#dist").html("");
					dd=$("#depa").val();
					dp=$("#prov").val();
					lp="<option selected disabled>--Elija--</option>";
					$("#dist").html(lp+adpr[dd][dp]);
				});
				
				
				$("#apenv").click(function(){
					var dir='';
					var ref='';
					var dep='';
					var pro='';
					var dis='';
					var iddest='';
					var transp=1;
					if($("#iddest").val()>0){
						dep=$("#depa").val();
						pro=$("#prov").val();
						dis=$("#dist").val();		
					}
					else{
						dir=$("#dir").val();
						ref=$("#ref").val();
						dep=$("#depa").val();
						pro=$("#prov").val();
						dis=$("#dist").val();
						aver=["#dir","#ref","#depa","#prov","#dist"];
						for(i=0;i<aver.length;i++){obj=$(aver[i]); if(obj.val()=="" || obj.val()==null || obj.val().trim()==""){obj.focus(); return false;}}		
					}
					$.post("../res/pedidos/venta_canales_auto.php",{a:"calcenv",dir:dir,ref:ref,dep:dep,pro:pro,dis:dis,i:idbol,tip:"u",iddest:iddest,lp:$("#listpre").val(),tipov:"up"},
					function(data){
						$("#savev").prop("disabled",false);
						$("#totpeso").val(data[2]);
						$("#totenv").val(data[1]);
						$("#totmon").val(data[0]);
					},"json");
				});
				
				
			});
			vnum = new RegExp("(\\D|\\s)");
			vtxt = new RegExp("^[a-zA-ZñÑ ]*$");
			vflo = new RegExp("^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$");
			vema = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
			function tr(dvr){
				for(i=0;i<dvr.length;i++)
				{dvr[i][1].parent().removeClass("has-error");}
				
				for(i=0;i<dvr.length;i++)
				{
					fn=dvr[i][0];
					ob=dvr[i][1];
					fl=dvr[i][2];
					cn=dvr[i][3];
					if(fn==1 || (fn==0 && ob.val()!="" && ob.val()!=null)){
						if(ob.val()!="" && ob.val()!=null)
						{
							if(fl!="")
							{
								if(fl=="num"){if(!vnum.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ ob.parent().addClass("has-error"); ob.focus(); return false;}}
								if(fl=="let"){if(vtxt.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ ob.parent().addClass("has-error"); ob.focus(); return false;}}
								if(fl=="ema"){if(String(ob.val()).search(vema)!=-1 && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ ob.parent().addClass("has-error"); ob.focus(); return false;}}
								if(fl=="flo"){if(vflo.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ ob.parent().addClass("has-error"); ob.focus(); return false;}}
							}
						}
						else
						{
							
						ob.parent().addClass("has-error"); ob.focus(); return false;}
					}
				}
			}
		</script>
	</body>
</html>