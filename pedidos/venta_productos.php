<?php
	require("../sec.php");
	
	$listp=array();
	$qprod=qry("select productos.idprod,productos.precio,TRIM(UPPER(CONCAT(productos.nombre,' ',productos.nombre1,' ',productos.nombre2,' ',productos.nombre3,' ',productos.nombre4))) nombre,productos.pack,if(isnull(productos.barras)=1,'',productos.barras) barras,0 stock,productos.promo,productos.precvar,productos.fraccion,productos.unmedida,productos.unidades from productos where isnull(productos.nombre)!=1 and productos.nombre!='' and porlocal=0 and productos.estado=1 and if(productos.porcanal=1,if(productos.idprod in (select idprod from prodcanal where canal='9'),1,0),1) GROUP BY productos.idprod order by productos.nombre;");
	//echo "select productos.idprod,productos.precio,TRIM(UPPER(CONCAT(productos.nombre,' ',productos.nombre1,' ',productos.nombre2,' ',productos.nombre3,' ',productos.nombre4))) nombre,productos.pack,if(isnull(productos.barras)=1,'',productos.barras) barras,0 stock,productos.promo,productos.precvar,productos.fraccion,productos.unmedida,productos.unidades from productos where isnull(productos.nombre)!=1 and productos.nombre!=''and productos.estado=1 and if(productos.porcanal=1,if(productos.idprod in (select idprod from prodcanal where canal='9'),1,0),1) GROUP BY productos.idprod order by productos.nombre;";
	while($temp=mysql_fetch_row($qprod))
	{$listp[]=$temp;}
	$listp=json_encode($listp);
	
	$tbody="";
	$i=1;
	$res=qry("select bolscanal.idprod,TRIM(UPPER(CONCAT(productos.nombre,' ',productos.nombre1,' ',productos.nombre2,' ',productos.nombre3,' ',productos.nombre4))) nombre,bolscanal.cantidad,if(bolscanal.promo=0,bolscanal.precio,kitparts.precio),(bolscanal.cantidad+if(bolscanal.fraccion/productos.unidades is null,0,bolscanal.fraccion/productos.unidades))*if(bolscanal.promo=0,bolscanal.precio,kitparts.precio) total,bolscanal.promo,if(bolscanal.fraccion>0,CONCAT(bolscanal.fraccion,productos.unmedida),''),(bolscanal.cantidad+(bolscanal.fraccion/productos.unidades)),UPPER(bolscanal.cantidad+(bolscanal.fraccion/productos.unidades)),bolscanal.canal,bolscanal.lista,bolscanal.localdst,FLOOR(bolscanal.cantidad/productos.pack) packs,productos.pack from bolscanal,productos,kitparts where productos.idprod=bolscanal.idprod and bolscanal.login='$Xlogin' and bolscanal.local='$Xlocal' and bolscanal.estado=1 and bolscanal.idbolsa='0' and if(bolscanal.promo=0,1,if(bolscanal.promo=kitparts.idkit and bolscanal.idprod=kitparts.idprod,1,0)) group by bolscanal.idprod,bolscanal.promo order by bolscanal.promo,nombre;");
	
	
	$formap=formap($Xlogin,$Xlocal);
	if($formap=="P"){$extra="<th>Packs</th><th>Und. Por Pack</th>";}
	else{$extra="<th class='ex'>Packs</th><th class='ex'>Und. Por Pack</th>";}
	
	$can="0"; $lp=""; $loc="";
	while($temp=mysql_fetch_row($res))
	{if($temp[5]!="0"){$ex="info";} if($formap=="P"){$exb="<td>$temp[12]</td><td>$temp[13]</td>";} $tbody .= "<tr promo='$temp[5]' unit='$temp[3]' cant='$temp[7]' class='list $ex' idp='$temp[0]'><td>$i</td><td>$temp[1]</td>".$exb."<td>$temp[2]</td><td>$temp[6]</td><td>$temp[3]</td><td>$temp[4]</td><td><button class='btn btn-warning remove btn-xs'><span class='glyphicon glyphicon-remove'></span></button></td></tr>"; $i++; $can=$temp[9]; $lp=$temp[10]; $loc=$temp[11];}
	
	if($can!="0" && $lp!="0")
	{ $adjs="$('#listcanales').val('$can'); $('#listcanales').attr('disabled',true); $('#listpre').val('$lp'); $('#listpre').attr('disabled',true); $('#listloc').val('$loc'); $('#listloc').attr('disabled',true);";
		$res=qry("select productos.idprod,productos.precio$lp,TRIM(UPPER(CONCAT(productos.nombre,' ',productos.nombre1,' ',productos.nombre2,' ',productos.nombre3,' ',productos.nombre4))) nombre,productos.pack,if(isnull(productos.barras)=1,'',productos.barras) barras,sum(if(stockmoves2.idprod=productos.idprod,stockmoves2.cantidad*stockmoves2.tipo,0)) stock,productos.promo,productos.precvar,productos.fraccion,productos.unmedida,productos.unidades from productos,stockmoves2 where isnull(productos.nombre)!=1 and productos.nombre!='' and if(productos.porlocal=1,if(productos.idprod in (select idprod from productolocal where local='$Xlocal'),1,0),1) and if(productos.porcanal=1,if(productos.idprod in (select idprod from prodcanal where canal='$can'),1,0),1) and productos.estado=1 GROUP BY productos.idprod order by productos.nombre;");
		$prod=array();
		while($temp=mysql_fetch_row($res)){ $prod[]=$temp; }
		$listp=json_encode($prod);
	}
	
	
	
	$res=qry("select distinct idbolsa from bolscanal where login='$Xlogin' and local='$Xlocal' and estado=1 and idbolsa!=0;");
	$html="";
	if(mysql_num_rows($res)>0)
	{ while($temp=mysql_fetch_row($res)){ $html .= "<button inf='$temp[0]' class='btn btn-info ped'>Pedido $temp[0]</button>"; } }
	
	$res=qry("select id,distrito from ubigeo where departameNto='LIMA' and provincia='LIMA' ORDER BY distrito;");
	$dist="<option disabled selected value='0'>--Elija--</option>";
	while($temp=mysql_fetch_row($res))
	{$dist .= "<option value='$temp[0]'>$temp[1]</option>"; }
	
	$usr="<option value='0'>--Elija--</option>";
	$idcq=qry("select idcliente,concat(nombre,' ',nombre2) from clientes where codigo1='$Xlogin' and canal='CATALOGO'");
	while($idc1=mysql_fetch_row($idcq))
	{
		$usr.="<option value='$idc1[0]'>$idc1[1]($idc1[0])</option>";
	}
	
	/*$res=qry("select tipodoc from puntoemision where local='$Xlocal';");
		while($temp=mysql_fetch_row($res))
		{ $tipodoc .= "<option value='$temp[0]'>$temp[0]</option>"; }
		
		
		$qcanal=qry("select idcanal,nombre from canales where estado=1;");
		$ocanal="<option value='0' selected disabled>--Elija Un Canal--</option>";
		while($temp=mysql_fetch_row($qcanal))
		{ $ocanal .= "<option value='$temp[0]'>$temp[1]</option>"; }
	*/
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
	///////////////////////////////////////////////////////////////
?>
<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Venta Productos</title>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css"> 
		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.js"></script>
		<script>
			var adep=<?php echo $adep;?>;
			var adpr=<?php echo $adpr;?>;
			var listp=<?php echo $listp;?>;
			var idbol=0;
			var lmres=10;
			var idcli=0;
			var formp="<?php echo $formap;?>";
			var objte=new Array();
			var bus=setTimeout(function(){},500);
			
			function buscaprod(idprod){r=false; $(".list").each(function(index){ if($(this).attr("idp")==idprod){r=true;}}); return r;}
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
			
			$(window).load(function(){ $('#tabledv').hide(); $('#dvsavecl').hide(); $('#logcl').hide();
				
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
								{if(lres[i][6]!="0"){st="style='color:#284DAA; font-weight: bold;'";} else{st="";} html=html+"<div "+st+" pos='"+(i+1)+"' stk='"+lres[i][5]+"' class='resultlist' barr='"+lres[i][4]+"' nom='"+lres[i][2]+"' fra='"+lres[i][8]+"' unm='"+lres[i][9]+"' unp='"+lres[i][10]+"' idpr='"+lres[i][0]+"' punit='"+lres[i][1]+"' precv='"+lres[i][7]+"' pack='"+lres[i][3]+"'>"+lres[i][2]+" - S/."+lres[i][1]+" - x"+lres[i][3]+"</div>";}
								$("#listres").html(html);
								$("#listres").addClass("form-control");
								$("#listres").css({width:$("#busq").css("width")});
								h=0;
								$(".resultlist").each(function(index){h=h+parseInt($(this).css("height"));});
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
					{$.post("../res/pedidos/venta_canales.php",{i:idbol,a:"ap",c:c,p:objte[0],de:de,fr:$("#esfraccion").val(),cfr:$("#cantf").val(),can:$("#listcanales").val(),lp:$("#listpre").val(),loc:$("#listloc").val()},function(data){
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
					$.post("../res/pedidos/venta_canales.php",{a:"db",i:obj},function(data){
						idbol=obj;
						$("#listprod").html(data);
					});
					$("#mntacm").val(monto());
				});
				
				
				$(document).on("click",".remove",function(){
					p=$(this).parent().parent().attr("idp");
					m=$(this).parent().parent().attr("promo");
					$.post("../res/pedidos/venta_canales.php",{i:idbol,a:"qp",p:p,m:m},function(data){$("#listprod").html(data); $("#mntacm").val(monto());});
				});
				
				$(document).on("click",".plus",function(){
					p=$(this).parent().parent().attr("idp");
					m=$(this).parent().parent().attr("promo");
					$.post("../res/pedidos/venta_canales.php",{i:idbol,a:"plus_prod",p:p,m:m},function(data){$("#listprod").html(data); $("#mntacm").val(monto());});
				});
				
				$(document).on("click",".less",function(){
					p=$(this).parent().parent().attr("idp");
					m=$(this).parent().parent().attr("promo");
					$.post("../res/pedidos/venta_canales.php",{i:idbol,a:"less_prod",p:p,m:m},function(data){$("#listprod").html(data); $("#mntacm").val(monto());});
				});
				
				$("#save").click(function(){
					$.post("../res/pedidos/venta_canales.php",{a:"sb"},function(data){if(data[0]=="1"){ idbol="0"; $("#listb").html(data[2]); 
						alertb(".container",1,"Bolsa Guardada","warning");
					$("#listprod").html("");} else{alertb(".container",1,"Numero Maximo de Bolsas Alcanzado","warning");}},"json");
				});
				
				
				$("#canc").click(function(){
					$.post("../res/pedidos/venta_canales.php",{a:"bb",i:idbol},function(data){
						alertb(".container",1,"Bolsa Borrada","warning");
					$("#listb").html(data); idbol="0"; $("#listprod").html(""); $($("#listpre").children()[0]).attr("selected",true); $($("#listcanales").children()[0]).attr("selected",true); $($("#listloc").children()[0]).attr("selected",true); $("#listcanales").attr("disabled",true); $("#listpre").attr("disabled",true); $("#listloc").attr("disabled",false);});
				});
				
				var capitalizate=[];
				$("#conf").click(function(){
					
					if($(".list").length>0){
						
						if($("#transp").is(':checked')){
							$("#transp").click();
						}
						$('#infocl').hide();
						//$("#totmon").val(monto());
						$("#vend1").val(0);
						$(".desc").html("");	
						$("#montotmp").val(monto());
						$("#myModal").modal({backdrop:"static"});
						////calculo de niveles//
						$.post("../res/pedidos/venta_canales.php",{a:'descuento',i:idbol},function(data){
							$(".desc").html(data.text);
							$("#totmon").val(data.total);
							$("#totpeso").val(parseFloat(data.peso));
							$(".divcap").html(data.capitalizate);
							capitalizate=eval("(" + data.capti_prod + ')');
							if(data.est==0)
							{
								$("#savev").prop("disabled",true)						
							}
							else{
								$("#savev").prop("disabled",false)
							}
						},"json");////
					}
					else
					{alert("Debe Agregar Productos");}
				});
				///////////////Añadir Transporte/////////////
				$(document).on("click","#transp",function(){
					if($("#transp").is(':checked')){
						if($("#vend1").val()>0)
						{
							////calculo de niveles//
							$("#envd").html("<div class='form-group nm col-sm-6'><label>Direccion</label><input placeholder='Direccion' class='form-control input-sm' id='dir'/></div><div class='form-group nm col-sm-6'><label>Referencia</label><input placeholder='Referencia' class='form-control input-sm' id='ref'/></div><div class='form-group col-sm-4'><label>Departamento</label><select id='depa' class='form-control input-sm'><?php echo $dep;?></select></div><div class='form-group col-sm-4'><label>Provincia</label><select id='prov' class='form-control input-sm'></select></div><div class='form-group col-sm-4'><label>Distrito</label><select id='dist' class='form-control input-sm'></select></div>");
							$("#apenv").attr("disabled",false);
							$("#savev").prop("disabled",true);
							$(".capital").attr("disabled",true);
							$("#vend1").attr("readonly",true);
							/*$.post("../res/pedidos/venta_canales.php",{a:'descuento_transporte',i:idbol},function(data){
								$(".desc").html(data.text);
								$("#totmon").val(data.total);
								$("#totpeso").val(parseFloat(data.peso));
								$(".capital:checked").each(function(){
								$("#totmon").val(parseFloat($("#totmon").val())+parseFloat(capitalizate[$(this).data('ped')][1]));
								$("#totpeso").val(parseFloat($("#totpeso").val())+parseFloat(capitalizate[$(this).data('ped')][4]));
								});
								
								if(data.est==0)
								{
								$("#savev").prop("disabled",true)
								
								}
								else{
								$("#savev").prop("disabled",false)
								}
							},"json");////*/
						}
						else{
							alert("Seleccione una Razon Social para su comprobante");
							$("#vend1").focus();
							$("#transp").click();
						}
						
					}
					else{
						$("#envd").html("");
						$("#apenv").attr("disabled",true);
						$(".capital").attr("disabled",false);
						$("#vend1").attr("readonly",false);
						$("#savev").prop("disabled",false);
						$("#totmon").val(parseFloat($("#totmon").val())-parseFloat($("#totenv").val()));
						$("#totenv").val(0);
						/*$.post("../res/pedidos/venta_canales.php",{a:'descuento',i:idbol},function(data){
							$(".desc").html(data.text);
							$("#totmon").val(data.total);
							$("#totpeso").val(parseFloat(data.peso));
							$(".capital:checked").each(function(){
							$("#totmon").val(parseFloat($("#totmon").val())+parseFloat(capitalizate[$(this).data('ped')][1]));
							$("#totpeso").val(parseFloat($("#totpeso").val())+parseFloat(capitalizate[$(this).data('ped')][4]));
							});			
							if(data.est==0)
							{
							$("#savev").prop("disabled",true)
							
							}
							else{
							$("#savev").prop("disabled",false)
							}
						},"json");////	*/
					}
				})
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
					if($("#vend1").val()>0){
						if($("#fpago").val()!=""){
							r=confirm("Desea Proceder?");
							if(r==true){
								var val = [];
								$('.capital:checked').each(function(i){
									val[i] = $(this).val();
								});
								aver=["#dir","#ref","#depa","#prov","#dist"];
								if($("#transp").is(':checked')){
									for(i=0;i<aver.length;i++){obj=$(aver[i]); if(obj.val()=="" || obj.val()==null || obj.val().trim()==""){obj.focus(); return false;}}
								}
								dir=$("#dir").val();
								ref=$("#ref").val();
								dep=$("#depa").val();
								pro=$("#prov").val();
								dis=$("#dist").val();
								idcl1=$("#idcl1").val();
								$.post("../res/pedidos/venta_canales.php",{
									a:"sv",
									idbol:idbol,
									ruc:$("#vend1").val(),
									fpago:$("#fpago").val(),
									infex:$("#nombre").val(),
									nota:$("#nota").val(),
									nopc:$("#nameopc").val(),
									capital:val,
									trans:$("#transp:checked").val(),
									dir:dir,
									ref:ref,
									dep:dep,
									pro:pro,
									dis:dis,
								idcl1:idcl1},
								function(data){
									if(data=="1"){ alert("Pedido Enviado");
										location.reload();
									}});
							}
							else{}
						}
						else{
							alert("Seleccione la forma de pago");
							$("#fpago").focus();
						}
					}
					else
					{
						alert("Seleccione una Razon Social para su comprobante");
						$("#vend1").focus();
					}
				});
				
				///////////////////////////////////////////////////////////////////////////////////////
				
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
					
					
					$.post("../res/pedidos/venta_canales.php",{a:"newcl",data:data,canal:$("#listcanales :selected").text()},function(fff){
						if(fff=="1"){$("#infocl").hide(); $('#dvsavecl').hide();}
					});
				});
				///////////////////////////////////////////////////////////////////////////////////////
				
				$(document).on("click",function(){$("#listres").html(""); $("#listres").hide();$("#listres").removeClass("form-control");});
				//$("#vend1").val("<?php echo $Xlogin;?>");
				
				
				
				
				$(document).on("click","#cancel",function(){
					$("#infocl").hide();
					$(".nclx").val("");
				});
				
				$(document).on("click",".capital",function(){
					if($(this).is(':checked')){
						////calculo de niveles//
						$("#totmon").val(parseFloat($("#totmon").val())+parseFloat(capitalizate[$(this).data('ped')][1]));
						$("#totpeso").val(parseFloat($("#totpeso").val())+parseFloat(capitalizate[$(this).data('ped')][4])/1000);
					}
					else{
						$("#totmon").val($("#totmon").val()-capitalizate[$(this).data('ped')][1]);
						$("#totpeso").val(parseFloat($("#totpeso").val())-parseFloat(capitalizate[$(this).data('ped')][4])/1000);
					}
				});
				
				$(document).on("click",function(){$("#listres2").hide();});
				
				
				$("#esfraccion").click(function(){
					if($(this).prop("checked"))
					{$(this).val("1"); $("#cantf").attr("disabled",false); }
					else{$(this).val("0"); $("#cantf").attr("disabled",true); }
				});
				
				
				$("#listcanales").change(function(){
					$.post("../res/pedidos/venta_canales.php",{a:"filprod",data:$(this).val(),lp:$("#listpre").val()},function(data){
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
					dir=$("#dir").val();
					ref=$("#ref").val();
					dep=$("#depa").val();
					pro=$("#prov").val();
					dis=$("#dist").val();
					idc=$("#vend1").val();
					aver=["#dir","#ref","#depa","#prov","#dist"];
					for(i=0;i<aver.length;i++){obj=$(aver[i]); if(obj.val()=="" || obj.val()==null || obj.val().trim()==""){obj.focus(); return false;}}
					var val = [];
					$('.capital:checked').each(function(i){val[i] = $(this).val();});
					$.post("../res/pedidos/venta_canales.php",{a:"calcenv",dir:dir,ref:ref,dep:dep,pro:pro,dis:dis,val:val,i:idbol,idc:idc,tip:"g"},
					function(data){
						$("#savev").prop("disabled",false);
						$("#totpeso").val(data[2]);
						$("#totenv").val(data[1]);
						$("#totmon").val(data[0]);
					},"json");
				});
				
			});
		</script>
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
				<input id="listcanales" type='hidden' value='9' />
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
					<div style="height:48px;" class="panel-heading">Lista Productos</div> 
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
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3 class="modal-title" id="myModalLabel">Informacion del Pago</h3>
					</div>
					<div class="modal-body"><div class="row">
						<div class="col-md-12">	
							<div class="form-group col-sm-6 nm">
								<label>Razon Social</label>
								<select id="vend1" class='form-control input-sm'><?php echo $usr;?></select>
							</div>
							<div class="form-group col-sm-6 nm">
								<label>Forma de Pago</label>
								<select id="fpago" class='form-control input-sm'><option value=''>--Elija--</option><option>DEPOSITO</option><option>EFECTIVO</option></select>
							</div>	
							<div class="row nb">
								<div class="form-group col-sm-6 nm">
									
								</div>
							</div>
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
											<input type="checkbox" id="transp" value="1" />
										</span>
										<input type="text" value="Incluir Movilidad" disabled class="form-control">
										<div class="input-group-btn">
											<button id="apenv" disabled class="btn btn-success">Calcular</button>
										</div>
									</div>
									<div id="envd">
										
									</div>
									
								</div> 
								<!--<div class="col-md-12">
									<label>Nota</label>
									<input id="nota" class="form-control" />
								</div>-->
								
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
		</div>
	</body>
</html>