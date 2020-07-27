<?php
	require("../sec.php");
	//$res=qry("select a.idprod,CONCAT(productos.nombre,' ',productos.nombre1,' ',productos.nombre2,' ',productos.nombre3) nombre,a.cantidad,a.precio,a.precio1,a.precio2,a.precio3,a.precio4,a.precio5,a.precio6,a.precio7,a.precio8,a.precio9,a.precio10,a.precio11,a.precio12 from bolsakit a,productos where a.idprod=productos.idprod and login='$Xlogin'");
	
	if($pais!=""){
		$i=0; $tbody="";
		while($temp=mysql_fetch_row($res))
		{ $tbody .= "<tr idp='".$temp[0]."' cnt=".$temp[2]." class='list'><td>".($i+1)."</td><td>".$temp[1]."</td><td>".$temp[2]."</td><td><input value='".$temp[3]."' class='form-control input-sm pr'></td><td><input value='".$temp[4]."'  class='form-control input-sm pr1'></td><td><input value='".$temp[5]."' class='form-control input-sm pr2'></td><td><input value='".$temp[6]."' class='form-control input-sm pr3'></td><td><input value='".$temp[7]."' class='form-control input-sm pr4'></td><td><input value='".$temp[8]."' class='form-control input-sm pr5'></td><td><input value='".$temp[9]."' class='form-control input-sm pr6'></td><td><input value='".$temp[10]."' class='form-control input-sm pr7'></td><td><input value='".$temp[11]."' class='form-control input-sm pr8'></td><td><input value='".$temp[12]."' class='form-control input-sm pr9'></td><td><input value='".$temp[13]."' class='form-control input-sm pr10'></td><td><input value='".$temp[14]."' class='form-control input-sm pr11'></td><td><input value='".$temp[15]."' class='form-control input-sm pr12'></td><td><button class='btn btn-warning btn-xs remove'><span class='glyphicon glyphicon-remove'></span></button></td></tr>"; $i++;}
		
		
		$thead="<tr><th>Nro</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Eliminar</th></tr>";
		
		$listp=array();
		$qprod=qry("select idprod,precio,upper(nombre),1,precio from productos$pais a where isnull(nombre)!=1 and nombre!='' and estado=1 and promo=0 ORDER BY nombre;");
		while($temp=mysql_fetch_row($qprod))
		{$listp[]=$temp;}
		$listp=json_encode($listp);
		
		
		$res=qry("select local,nombre from locales where estado=1 order by local");
		$loc="<option value='0' selected disabled>--Elija--</option>";
		while($temp=mysql_fetch_row($res))
		{ $loc .= "<option value='$temp[0]'>$temp[1]</option>"; }
		
		
		$qcanal=qry("select idcanal,nombre from canales order by nombre");
		$ocanal="<option value='0' selected disabled>--Elija Un Canal--</option>";
		while($temp=mysql_fetch_row($qcanal))
		{ $ocanal .= "<option value='$temp[0]'>$temp[1]</option>"; }
		
	}
	
	
	$res=mysql_query("select idpais, pais from paises where estado=1");
	$opc="<option value='' disabled>-elije-</option>";
	while($r=mysql_fetch_row($res)){
		$opc.="<option value='$r[0]'>$r[1]</option>";
	}
?>
<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Creacion Promociones</title>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css"> 
		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.js"></script>
		<script>
			var listp=<?php echo $listp;?>;
			var lmres=10;
			
			function alertb(obj,time,text,tipo){
				$(obj).prepend("<div style='top:2px;' class='text-center alert alert-"+tipo+"'>"+text+"</div>");
			setTimeout(function(){$($(".alert")[0]).toggle("slow");},time*1000);}
			
			function buscaprod(idprod){r=false; $("#blist").children().each(function(index){ if($(this).attr("idp")==idprod){r=true;}}); return r;}
			$(window).load(function(){
				
				$("#addc").click(function(){$("#cant").val(parseFloat($("#cant").val())+1);});
				$("#disc").click(function(){if(parseFloat($("#cant").val())>1){$("#cant").val(parseFloat($("#cant").val())-1);}});
				
				$("#busq").keyup(function(e2){
					$("#listres").show();
					
					if(e2.keyCode!=38 & e2.keyCode!=40 & e2.keyCode!=13){
						
						$("#busq").attr("placeholder","Ingrese Un Producto");
						$("#busq").parent().removeClass("has-error");
						
						nres=0;
						lres=new Array();
						dato=document.getElementById("busq").value.toUpperCase();
						if(dato!=""){
							for(i=0;i<listp.length;i++){
								if(listp[i][2].indexOf(dato)!=-1){
									if(nres<lmres){lres[nres]=listp[i]; nres++;} else{break;}
								}
							}
							html="";
							if(nres>0){
								for(i=0;i<lres.length;i++)
								{
									var tt="";
									for(j=4;j<17;j++){
										tt= tt + " p"+(j-4)+"='"+lres[i][j]+"' ";
									}
									html=html+"<div pos='"+(i+1)+"' class='resultlist' nom='"+lres[i][2]+"' idpr='"+lres[i][0]+"' punit='"+lres[i][1]+"' pack='"+lres[i][3]+"' "+tt+" >"+lres[i][2]+" - S/."+lres[i][1]+" - x"+lres[i][3]+"</div>";
								}
								$("#listres").html(html);
								$("#listres").addClass("form-control");
								$("#listres").css({width:$("#busq").css("width")});
								h=0;
								$(".resultlist").each(function(index){h=h+parseInt($(this).css("height"));});
								$("#listres").css({height:h+"px"});
								}else{
								$("#listres").html("");
								$("#listres").hide();
								$("#listres").removeClass("form-control");
							}
							}else{
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
								obj=$(".resultselect");
								if(buscaprod(obj.attr("idpr")))
								{
									$("#busq").attr("placeholder","Ya Esta En La Lista");
									$("#busq").parent().addClass("has-error");
									setTimeout(function(){$("#busq").val(""); $("#busq").focus(); $("#listres").hide();},1);
									
								}
								else{
									objte=new Array();
									objte[0]=obj.attr("idpr");
									objte[1]=obj.attr("pack");
									objte[2]=obj.attr("nom");
									for(i=3;i<16;i++){
										objte[i]=obj.attr("p"+(i-3));
									}
									
									$("#listres").hide();
								}
								$("#busq").val(obj.attr("nom"));
								$("#listres").html("");
								$("#listres").hide();
								$("#listres").removeClass("form-control");
							}
							e.stopImmediatePropagation();
							
						});}
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
						objte[2]=obj.attr("nom");
						for(i=3;i<16;i++){
							objte[i]=obj.attr("p"+(i-3));
						}	
					}
					$("#busq").val(obj.attr("nom"));
					$("#listres").html("");
					$("#listres").hide();
					$("#listres").removeClass("form-control");
				});
				
				$("#addprod").click(function(){
					$("#listres").hide();
					c=$("#cant").val();
					if(!isNaN(parseInt($("#cant").val())) & parseInt($("#cant").val())>0)
					{
						$("#blist").append("<tr idp='"+objte[0]+"' cnt="+c+" class='list'><td>"+($(".list").length+1)+"</td><td>"+objte[2]+"</td><td>"+c+"</td><td><input class='form-control input-sm pr' value='"+objte[3]+"'></td><td><button class='btn btn-warning btn-xs remove'><span class='glyphicon glyphicon-remove'></span></button></td></tr>");
					}
					else
					{$("#cant").val("1").focus();}
				});
				
				$(document).on("click",".remove",function(){$(this).parent().parent().remove();});
				
				$("#updt").click(function(){
					
					prod=new Array(); i=0; 
					$(".list").each(function(index){
						obj=$(this);
						prod[i]=new Array();
						prod[i][0]=obj.attr("idp");
						prod[i][1]=obj.attr("cnt"); 
						prod[i][2]=obj.find(".pr").val();
						for(j=1;j<13;j++){ prod[i].push(obj.find(".pr"+j).val()); }
					i++;});
				$.post("../res/promo/promociones.php",{a:"upb",prod:prod},function(){});});
				
				
				$("#clea").click(function(){
					$.post("../res/promo/promociones.php",{a:"rbs"},function(){$("tbody").html("");});
				});
				
				$(document).on("click",".rmv",function(){$(this).parent().remove();});
				
				$("#addloc").click(function(){
					l=$("#locales").val(); e=0;
					n=$("#locales").find(":selected").text();
					$("#listloc").children().each(function(index){ if($(this).attr("inf")==l){e++;} });
					if(e==0)
					{ $("#listloc").append("<span inf='"+l+"' class='badge'>"+n+" <span class='rmv glyphicon glyphicon-remove'></span></span>"); }
				});
				
				$("#addcan").click(function(){
					l=$("#canales").val(); e=0;
					n=$("#canales").find(":selected").text();
					$("#listcan").children().each(function(index){ if($(this).attr("inf")==l){e++;} });
					if(e==0)
					{ $("#listcan").append("<span inf='"+l+"' class='badge'>"+n+" <span class='rmv glyphicon glyphicon-remove'></span></span>"); }
				});
				
				
				$("#save").click(function(){
					prod=new Array(); i=0; 
					var desc=$("#desc").val();
					$(".list").each(function(index){
						obj=$(this);
						prod[i]=new Array();
						prod[i][0]=obj.attr("idp");
						prod[i][1]=obj.attr("cnt"); 
						prod[i][2]=obj.find(".pr").val();
						for(j=1;j<13;j++){ prod[i].push(obj.find(".pr"+j).val()); }
					i++;});
					
					loc=new Array();
					if($("#listloc").children().length>0)
					{$("#listloc").children().each(function(index){ loc.push($(this).attr("inf")); });}
					else
					{loc="0";}
					
					can=new Array();
					if($("#listcan").children().length>0)
					{$("#listcan").children().each(function(index){can.push($(this).attr("inf"));});}
					else
					{can="0";}
					
					var pais=$("#pais").val();
					$.post("../res/promo/promociones.php",{a:"sp",prod:prod,fei:$("#fei").val(),fef:$("#fef").val(),loc:loc,pck:$("#pck").val(),nom:$("#nom").val(),can:can,desc:desc,pais:pais},function(data){
						if(data=="1"){
							alertb(".container",2,"Producto Guardado","warning");
							location.href='asigna_promociones.php';
							}else{
							alert("Datos enviados Incompletos");
						}
					});
				});
				
				$(document).on("click","#desc",function(){
					if($(this).val()=="0"){$(this).val("1");}
					else{$(this).val("0");}
				});
				
			});
		</script>
		<style>
			.resultselect{background:#94D5AA;}
			.resultlist{cursor:default;}
			#listres{height:auto; padding:0px;}
			.noed{display:none;}
			.nb{margin-left:0px; margin-right:0px;}
			.rmv{cursor:pointer;}
		</style>
	</head>
	<body>
		
		<div class="container">
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
				<input id="pais" type='hidden' value='<?php echo $pais;?>' />
				<div class="row">
					<div class="col-md-6 col-sm-7"><div class="form-group">
						<input id="busq" onkeyup="javascript:this.value=this.value.toUpperCase();" style="text-transform:uppercase;" class='form-control input-sm'>
						<div  style='position:absolute; z-index:9;' id='listres'></div>
					</div></div>
					<div class="col-md-2 col-sm-3 col-xs-5">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-btn">
									<button id="disc" class="btn btn-default btn-sm" type="button">
										<span class="glyphicon glyphicon-minus"></span>
									</button>
								</span>
								<input value="1" id="cant" type="text" class="form-control text-center input-sm">
								<span class="input-group-btn">
									<button id="addc" class="btn btn-default btn-sm" type="button">
										<span class="glyphicon glyphicon-plus"></span>
									</button>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-6"><button id='addprod' class="btn btn-success btn-sm">Agregar</button></div>
				</div>
				
				<div class="row">
					<div class="table-responsive">
						<table id="listprod" class="table table-bordered">
							<thead><?php echo $thead;?></thead>
							<tbody id='blist'><?php echo $tbody;?></tbody>
						</table>
					</div>
				</div>
				
				<div class="row">
					
					<div class="row">
						<div class="form-group col-sm-3">
							<label>Nombre</label>
							<input id="nom" class="form-control input-sm" placeholder="Nombre">
						</div>
						<div class="form-group col-sm-2">
							<label>Desde</label>
							<input id="fei" type="date" class="form-control input-sm" placeholder="Desde">
						</div>
						<div class="form-group col-sm-2">
							<label>Hasta</label>
							<input id="fef"  type="date" class="form-control input-sm" placeholder="Hasta">
						</div>
						<!--
							<div class="form-group col-sm-2">
							<label>Pack</label>
							<input id="pck" type="number" class="form-control input-sm"  value="1">
							</div>
							<div class="form-group col-sm-3">
							<label style="margin-top:20px;"  class="col-sm-12">Sin Stock <input value="0" type="checkbox" id="desc"/></label>
							</div>
						-->
					</div>
					
					
					
					<!--- 
						<div class="row nb">
						<div class="form-group col-sm-3">
						<label>Locales</label>
						<select id="locales" class="form-control input-sm"><?php echo $loc;?></select>
						</div>
						<div class="form-group col-sm-3">
						<br><button class="btn btn-success btn-sm" id="addloc">Agregar</button>
						</div>
						</div>
						<br>
						<div class="col-sm-12" id="listloc"></div>
						
						<div class="row nb">
						<div class="form-group col-sm-3">
						<label>Canales</label>
						<select id="canales" class="form-control input-sm"><?php echo $ocanal;?></select>
						</div>
						
						<div class="form-group col-sm-3">
						<br><button class="btn btn-success btn-sm" id="addcan">Agregar</button>
						</div>
						</div>
						<br>
						<div class="col-sm-12" id="listcan"></div>
						</div> 
					-->
					<br>
					
					<div class="row text-center">
						<!--<button class="btn btn-sm btn-primary" id="updt">Guardar</button>-->
						<button class="btn btn-sm btn-success" id="save">Crear Promo</button>
						<button class="btn btn-sm btn-warning" id="clea">Limpiar</button>
					</div> 
					
				</div>
				
			<?php } ?>
			
			
		</body>
	</html>					