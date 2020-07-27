<?php
	require("../sec.php");
	$opt_loc="<option selected disabled>--Elija Un Local--</option>";
	$opt_fired="<option selected disabled>--Elija Un Tipo de Renuncia--</option>";
	$opt_log="<option selected disabled>--Elija Un Area--</option>";
	$opt_rol="<option selected disabled>--Elija Un Rol--</option>";
	$opt_camp="<option selected disabled>--Elija Un Campaña--</option><option value='0'>SIN CAMPAÑA</option>";
	
	if($d_val!="" )
	{
		$d_val=$_GET["d_val"];
		$est_usr=array("0"=>"DESACTIVADO","1"=>"ACTIVO","2"=>"DE BAJA");
		//echo"select a.login,a.nombre,a.apellidos,a.activo,TRIM(UPPER(CONCAT(if(isnull(a.apellidos),'',a.apellidos),' ',if(isnull(a.nombre)=1,'',a.nombre)))),d.nombre,f.login,f.nombre from usuarios a left join user_nivel b on b.login=a.login left join niveles_red d on d.idnivel=b.nivel left join asociadas e on e.socio=a.login left join usuarios f on f.login=e.empresa  where ((a.login like '%".$d_val."%') or (a.nombre like '%".$d_val."%') or (a.apellidos like '%".$d_val."%')) limit 20;";
		$res=qry("select a.login,a.nombre,a.apellidos,a.activo,TRIM(UPPER(CONCAT(if(isnull(a.apellidos),'',a.apellidos),' ',if(isnull(a.nombre)=1,'',a.nombre)))),d.nombre,f.login,f.nombre from usuarios a left join user_nivel b on b.login=a.login left join niveles_red d on d.idnivel=b.nivel left join asociadas e on e.socio=a.login left join usuarios f on f.login=e.empresa  where ((a.login like '%".$d_val."%') or (a.nombre like '%".$d_val."%') or (a.apellidos like '%".$d_val."%')) limit 20;");
		$htm="";
		if(mysql_num_rows($res)>0)
		{	
			while($temp=mysql_fetch_row($res))
			{$htm.="<tr class='showd' inf='".$temp[0]."' nom='".$temp[4]."' ><td><a href='user_datared.php?login=$temp[0]'>".$temp[0]."</a></td><td>".$temp[1]."</td><td>".$temp[2]."</td><td>".$temp[5]."</td><td>".$temp[6]."</td><td>".$temp[7]."</td></tr>";}
		}
		else
		{
		$htm="<tr><td colspan='4'>No Se Encontraron Resultados</td></tr>";}
	}
	
	$res=qry("select idarea,nombre from areas where estado=1;");
	if(mysql_num_rows($res)>0){
		while($temp=mysql_fetch_row($res))
		{ $opt_log.="<option value='".$temp[0]."'>".$temp[1]."</option>"; }
	}
	
	
	$res=qry("select local from locales where estado=1 order by local;");
	if(mysql_num_rows($res)>0){
		while($temp=mysql_fetch_row($res))
		{ $opt_loc.="<option value='".$temp[0]."'>".$temp[0]."</option>"; }
	}
	
	
	
	$res=qry("select idrol,nombre from rols where estado=1 order by nombre;");
	if(mysql_num_rows($res)>0){
		while($temp=mysql_fetch_row($res))
		{ $opt_rol.="<option value='".$temp[0]."'>".$temp[1]."</option>"; }
	}
	///////////////////////////UBIGEO//////////////////////
	//DEP
	$res=mysql_query("select CODDPTO,NOMBRE from ubigeo where CODPROV='00' AND CODDIST='00';");
	$dep="<option selected disabled>--Elija--</option>";
	while($temp=mysql_fetch_row($res))
	{$dep .= "<option value='$temp[0]'>$temp[1]</option>";}
	//ARRAY DE DATOS
	$adep=array();
	$adpr=array();
	$res=mysql_query("select CODDPTO,CODPROV,CODDIST,NOMBRE from ubigeo order by CODDIST,CODPROV,CODDPTO");
	while($temp=mysql_fetch_row($res))
	{
		if($temp[1]=="00" and $temp[2]=="00"){
			if(!isset($adep[$temp[0]]))
			{
				$adep[$temp[0]]=array();
			}
		}
		elseif($temp[2]=="00"){
			$adep[$temp[0]].="<option value='$temp[1]'>$temp[3]</option>";
		}
		else{
			$adpr[$temp[0]][$temp[1]] .= "<option value='$temp[2]'>$temp[3]</option>";
		}
	}
	$adep=json_encode($adep);
	$adpr=json_encode($adpr);
	/////
	$adar_lo=array();
	$res=mysql_query("select idlocarea,local,nombre from local_areas");
	while($temp=mysql_fetch_row($res))
	{
		if(!isset($adar_lo[$temp[1]]))
		{
			$adar_lo[$temp[1]]=array();
		}
		$adar_lo[$temp[1]].="<option value='$temp[0]'>$temp[2]</option>";
	}
	$adar_lo=json_encode($adar_lo);
	//////
	$adar_fi=array();
	$res=mysql_query("select idfired,id,nombre from fired_motivos where estado=1");
	while($temp=mysql_fetch_row($res))
	{
		if(!isset($adar_fi[$temp[1]]))
		{
			$adar_fi[$temp[1]]=array();
		}
		$adar_fi[$temp[1]].="<option value='$temp[0]'>$temp[2]</option>";
	}
	$adar_fi=json_encode($adar_fi);
	///////////////////////////////////////////////////////////////
	
	
?>
<!doctype html>
<html>
	<head>
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css"> 
		<link rel="stylesheet" type="text/css" href="../css/dtpicker.css"> 
		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap.js"></script>
		<script src="../js/moment.js"></script>
		<script src="../js/dtpicker.js"></script>
		<script>
			function previewFile(file,img) {
				var preview = img;
				var file    = file.files[0];
				var reader  = new FileReader();
				
				reader.onloadend = function () {
					preview.src = reader.result;
				}
				
				if (file) {
					reader.readAsDataURL(file);
					} else {
					preview.src = "";
				}
			}
			
			function size_file(obj){
				obj=obj[0];
				var f=obj.files[0];
				if(f)
				{return f.size;}
				else{return false;}
			}
			
			function validateEmail(email) {
				var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
				return re.test(email);
			}
			
			var adep=<?php echo $adep;?>;
			var adpr=<?php echo $adpr;?>;
			var adar_lo=<?php echo $adar_lo;?>;
			var adar_fi=<?php echo $adar_fi;?>;
			$(window).load(function(){
				
				$(document).on("change",".depa",function(){
					var dprov=$(this).parent().parent().find(".prov");
					var ddist=$(this).parent().parent().find(".dist");
					dprov.html("");
					ddist.html("");
					dp=$(this).val();
					lp="<option selected disabled>--Elija--</option>";
					ap=adep[dp];
					//for(var i=0;i<ap.length;i++){lp=lp+ap[i];}
					dprov.html(lp+ap);
				});
				
				$(document).on("change",".prov",function(){
					var ddist=$(this).parent().parent().find(".dist");
					ddist.html("");
					dd=$(this).parent().parent().find(".depa").val();
					dp=$(this).val();
					lp="<option selected disabled>--Elija--</option>";
					ddist.html(lp+adpr[dd][dp]);
				});
				
				$(document).on("change",".loc",function(){
					var ddist=$(this).parent().parent().find(".loc_area");
					ddist.html("");
					dp=$(this).val();
					lp="<option selected value=''>----</option>";
					ddist.html(lp+adar_lo[dp]);
				});
				$(document).on("change","#d_tipof",function(){
					var ddist=$(this).parent().parent().find("#d_motif");
					ddist.html("");
					dp=$(this).val();
					lp="<option selected value=''>----</option>";
					ddist.html(lp+adar_fi[dp]);
				});
				
				
				
				
				
				
				
				
				
				$(document).on("click","#nusr",function(){
					$("#n_dni,#n_nom,#n_ape,#n_ema,#n_fna,#n_ruc,#n_dir,#n_tel,#n_cel,#n_fot,#n_cal,#n_depa,#n_prov,#n_dist").val("");
					$("#n_tse").val("18000");
					$("#n_act").val("1").prop("checked",true);
					$("#n_zkp").val("0").prop("checked",false);
					$("#n_tje").val("0").prop("checked",false);
					$("#n_pfo").attr("src","");
					$("#n_loc").children()[0].selected=true;
					$("#n_jef").children()[0].selected=true;
					$("#divo").attr("inf","+");
					$("#labs").text("+");
					$("#divso").hide();
					$("#n_fna,#n_fin,#n_fce").datetimepicker({pickTime:false});
					$("#modal1").modal();
				});
				
				
				
				
				///////////////creacion///////////////////////////////////////
				$(document).on("change","#n_fot",function(evt){
					var obj=$(this);
					var tmp=obj.val().split(".");
					var res=tmp[tmp.length-1];
					if(res.toUpperCase()=="JPG")
					{
						var siz=size_file(obj);
						if(siz>0)
						{
							previewFile(obj[0],$("#n_pfo")[0]);
						}else{alert("El Archivo Esta Vacio"); obj.val("").focus();}
					}else{alert("Debe usar un archivo JPG"); obj.val("").focus();}
				});
				
				$(document).on("click","#divo",function(){
					var obj=$(this);
					var inf=obj.attr("inf");
					if(inf=="+")
					{ obj.attr("inf","-"); $("#labs").text("-"); var oac="-"; }
					else
					{ obj.attr("inf","+"); $("#labs").text("+"); var oac="+"; }
					if(oac=="+"){ $("#divso").hide("fast"); }else{ $("#divso").show("fast"); }
				});
				
				$(document).on("click","#divop",function(){
					var obj=$(this);
					var inf=obj.attr("inf");
					if(inf=="+")
					{ obj.attr("inf","-"); $("#labsp").text("-"); var oac="-"; }
					else
					{ obj.attr("inf","+"); $("#labsp").text("+"); var oac="+"; }
					if(oac=="+"){ $("#divsop").hide("fast"); }else{ $("#divsop").show("fast"); }
				});
				
				$(document).on("change","#n_jef",function(){
					var inf=$(this).val();
					if(inf!=null && inf!=""){
						$.post("ajax/usuarios.php",{a:"dcar",inf:inf},function(data){
							if(data[0]=="1"){ $("#n_car").html(data[1]); }
							else{ $("#n_car").html("<option value='SIN ASIGNAR'>SIN ASIGNAR</option>"); }
						},"json");
					}});
					///////////////////////////////////////////////////////////////
					
					//////////////////////edicion///////////////////////////////////
					$(document).on("click","#andkey",function(){
						$.post("ajax/andr.php",{log:$('#usra').val(),dni:$('#d_ruc').val()},function(data){
							alert(data);
						});
					});
					
					$(document).on("change","#d_fot",function(evt){
						var obj=$(this);
						var tmp=obj.val().split(".");
						var res=tmp[tmp.length-1];
						if(res.toUpperCase()=="JPG")
						{
							var siz=size_file(obj);
							if(siz>0)
							{
								previewFile(obj[0],$("#d_pfo")[0]);
							}else{alert("El Archivo Esta Vacio"); obj.val("").focus();}
						}else{alert("Debe usar un archivo JPG"); obj.val("").focus();}
					});
					
					$(document).on("click","#divod",function(){
						var obj=$(this);
						var inf=obj.attr("inf");
						if(inf=="+")
						{ obj.attr("inf","-"); $("#labsd").text("-"); var oac="-"; }
						else
						{ obj.attr("inf","+"); $("#labsd").text("+"); var oac="+"; }
						if(oac=="+"){ $("#divsod").hide("fast"); }else{ $("#divsod").show("fast"); }
					});
					
					$(document).on("click","#divd",function(){
						var obj=$(this);
						var inf=obj.attr("inf");
						if(inf=="+")
						{ obj.attr("inf","-"); $("#labd").text("-"); var oac="-"; }
						else
						{ obj.attr("inf","+"); $("#labd").text("+"); var oac="+"; }
						if(oac=="+"){ $("#divsd").hide("fast"); }else{ $("#divsd").show("fast"); }
					});
					
					$(document).on("change","#d_jef",function(){
						var inf=$(this).val();
						if(inf!=null && inf!=""){
							$.post("ajax/usuarios.php",{a:"dcar",inf:inf},function(data){
								if(data[0]=="1"){ $("#d_car").html(data[1]); }
								else{ $("#d_car").html("<option value='SIN ASIGNAR'>SIN ASIGNAR</option>"); }
							},"json");	
						}});
						
						$(document).on("click","#rstps",function(){
							var inf=$("#usra").val();
							var r=confirm("Se Procedera A Resetear El Password Del Usuario \""+inf+"\", Desea Continuar?");
							if(r==true)
							{ $.post("ajax/usuarios.php",{a:"rstp",inf:inf},function(data){
								
								if(data[0]=="1")
								{
									alert("Se reseteo la Contraseña a: "+inf);
								}else{alert("No Se Pudo Resetear El Password");}
								
							},"json");}
						});
						
						<?php if($Xpermiso>998){?>
							$(document).on("click","#addr",function(){
								var inf=$("#lstrd").val();
								var usr=$("#usra").val();
								if($(".lstr[inf='"+inf+"']").length==0)
								{
									if(inf!=null && inf.trim()!="" && !isNaN(inf) && parseInt(inf)>0)
									{
										var r=confirm("Se Procedera A Asignar El Rol, Desea Continuar?");
										if(r==true){
											$.post("ajax/usuarios.php",{a:"addr",inf:inf,usr:usr},function(data){
												if(data[0]=="1")
												{$("#lstpd").html(data[1]);}
												else if(data[0]=="0")
												{alert("El Rol Ya Esta Asignado"); $("#lstrd").focus();}
											},"json");
										}
									}	
								}else{alert("El Rol Ya Esta Asignado"); $("#lstrd").focus();}
							});
							$(document).on("click",".rmvr",function(){
								var inf=$(this).attr("inf");
								var usr=$("#usra").val();
								if(inf!=null && inf.trim()!="" && !isNaN(inf) && parseInt(inf)>0)
								{
									var r=confirm("Se Procedera Retirar El Rol, Desea Continuar?");
									if(r==true){
										$.post("ajax/usuarios.php",{a:"rmvr",inf:inf,usr:usr},function(data){
											if(data[0]=="1")
											{ $("#lstpd").html(data[1]); }
										},"json");
									}
								}});
						<?php }?>
						////////////////////////////////////////////////////////////////
						
						
						$(document).on("click","#n_act,#n_zkp,#n_tje,#d_act,#d_zkp,#d_tje",function(){
							if($(this).val()=="0"){$(this).val("1");}
							else{$(this).val("0");}
						});
						
						$(document).on("click","#savc",function(){
							var nom=$("#d_nom").val();
							var ape=$("#d_ape").val();
							var ema=$("#d_ema").val();
							var fna=$("#d_fna").val();
							var ruc=$("#d_ruc").val();
							var dir=$("#d_dir").val();
							var tel=$("#d_tel").val();
							var cel=$("#d_cel").val();
							var loc=$("#d_loc").val();
							var jef=$("#d_jef").val();
							var car=$("#d_car").val();
							var fot=$("#d_fot").val();
							
							var depa=$("#d_depa").val();
							var prov=$("#d_prov").val();
							var dist=$("#d_dist").val();
							
							var sex=$("#d_sex").val();
							var state=$("#d_state").val();
							
							var loc_area=$("#d_loc_area").val();
							var camp=$("#d_camp").val();
							/************************/
								/************************/
									var tje=$("#d_tje").val();
									var tse=$("#d_tse").val();
									var cal=$("#d_cal").val();
									var act=$("#d_act").val();
									var zkp=$("#d_zkp").val();
									var usr=$("#usra").val();
									
									if(nom!=null && nom.trim()!="")
									{
										if(ape!=null && ape.trim()!="")
										{
											if(1 /*ema!=null && ema.trim()!="" && validateEmail(ema)*/)
											{
												var tmp=fna.split("-");
												if(1 /*fna!=null && fna.trim()!="" && !isNaN(tmp[0]) && !isNaN(tmp[1]) && !isNaN(tmp[2]) && parseInt(tmp[0])>0 && parseInt(tmp[1])>0 && parseInt(tmp[2])>0*/)
												{
													if(ruc!=null && ((ruc=="") || (ruc.trim()!="" && !isNaN(ruc) && ruc.length==8)))
													{
														if(1 /*dir!=null && dir.trim()!=""*/)
														{
															if(1 /*tel!=null && ((tel=="") || (tel.trim()!="" && !isNaN(tel) && tel.length==7))*/)
															{
																if(1 /*cel!=null && ((cel=="") || (cel.trim()!="" && !isNaN(cel) && cel.length==9))*/)
																{
																	if(1 /*tel!="" || cel!=""*/)
																	{
																		if(loc!=null && loc.trim()!="")
																		{
																			if(tje=="0" || (tje=="1" && jef!=null && jef.trim()!=""))
																			{
																				if(car!=null && car.trim()!="")
																				{	
																					if(tse!=null && tse.trim()!="" && !isNaN(tse) && parseInt(tse)>0)
																					{
																						if(act=="0" || act=="1")
																						{
																							if(zkp=="0" || zkp=="1")
																							{
																								if(tje=="0" || tje=="1")
																								{
																									var r=confirm("Se Procedera A Actualizar Los Datos Del Usuario, Desea Continuar?");
																									if(r==true)
																									{
																										var data = new FormData();
																										
																										if(fot!=""){
																											var fot=document.getElementById("d_fot").files[0];
																											data.append('fot',fot);
																										}
																										
																										data.append('nom',nom);
																										data.append('ape',ape);
																										data.append('ema',ema);
																										data.append('fna',fna);
																										data.append('ruc',ruc);
																										data.append('dir',dir);
																										data.append('tel',tel);
																										data.append('cel',cel);
																										data.append('loc',loc);
																										data.append('jef',jef);
																										data.append('car',car);
																										data.append('tse',tse);
																										data.append('cal',cal);
																										data.append('act',act);
																										data.append('zkp',zkp);
																										data.append('tje',tje);
																										
																										data.append('depa',depa);
																										data.append('prov',prov);
																										data.append('dist',dist);
																										
																										data.append('sex',sex);
																										data.append('state',state);
																										data.append('camp',camp);
																										
																										data.append('loc_area',loc_area);
																										
																										data.append('usr',usr);
																										data.append('a','updu');
																										
																										$.ajax({url:'ajax/usuarios.php',type:'POST',contentType:false,data:data,processData:false,cache:false,success:function(data){
																											data=JSON.parse(data);
																											
																											if(data[0]=="1")
																											{ alert("Se Actualizaron Los Datos Del Usuario: \""+data[1]+"\""); }
																											else if(data[0]=="0")
																											{
																												if(data[1]=="1")
																												{ alert("No Se Pudieron Actualizar Los Datos"); }
																											}
																											else if(data[0]=="2")
																											{ alert("Error En Datos"); $("#d_"+data[1]).focus(); }
																											
																										}});
																									}
																								}else{alert("Marque O Desmarque La Casilla de Tiene Jefe");}
																							}else{alert("Marque O Desmarque La Casilla de ZK Privilege");}
																						}else{alert("Marque O Desmarque La Casilla de Activo");}
																					}else{alert("Indique Un Tiempo De Sesion Correcta"); $("#d_tse").focus();}
																				}else{alert("Debe Especificar Un Cargo"); $("#d_car").focus();}
																			}else{alert("Debe Especificar Un Area"); $("#d_jef").focus();}
																		}else{alert("Debe Especificar Un Local"); $("#d_loc").focus();}
																	}else{alert("Debe Indicar Almenos Un Numero de Contacto"); $("#d_tel").focus();}
																}else{alert("Indique Un Celular Correcto"); $("#d_cel").focus();}
															}else{alert("Indique Un Telefono Correcto"); $("#d_tel").focus();}
														}else{alert("Indique Una Direccion Correcta"); $("#d_dir").focus();}
													}else{alert("Indique Un Numero De DNI Correcto"); $("#d_ruc").focus();}
												}else{alert("Indique Una Fecha De Nacimiento Correcta"); $("#d_fna").focus();}
											}else{alert("Indique Un Email Correcto"); $("#d_ema").focus();}
										}else{alert("Indique Apellidos Correctos"); $("#d_ape").focus();}
									}else{alert("Indique Un Nombre Correcto"); $("#d_nom").focus();}
								});
								
								
								$(document).on("click","#svdpx",function(){
									var niv=$("#d_niv").val();
									var fin=$("#d_fin").val();
									var fce=$("#d_fce").val();
									var exp=$("#d_exp").val();
									var not=$("#d_not").val();
									var usr=$("#usra").val();
									var motif=$("#d_motif").val();
									
									if(niv!=null && niv.trim()!="")	
									{
										var tmp=fin.split("-");
										if(fin!=null && ((fin=="") || (fin.trim()!="" && !isNaN(tmp[0]) && !isNaN(tmp[1]) && !isNaN(tmp[2]) && parseInt(tmp[0])>0 && parseInt(tmp[1])>0 && parseInt(tmp[2])>0)))
										{
											var tmp=fce.split("-");
											if(fce!=null && ((fce=="") || (fce.trim()!="" && !isNaN(tmp[0]) && !isNaN(tmp[1]) && !isNaN(tmp[2]) && parseInt(tmp[0])>0 && parseInt(tmp[1])>0 && parseInt(tmp[2])>0)))
											{
												var data = new FormData();
												data.append('niv',niv);
												data.append('fin',fin);
												data.append('fce',fce);
												data.append('exp',exp);
												data.append('not',not);
												data.append('usr',usr);
												data.append('motif',motif);
												data.append('a','updo');
												$(".listac").each(function(){
													data.append($(this).data("id"),$(this).val());
												});
												var r=confirm("Se Procedera A Actualizar Los Datos Del Usuario, Desea Continuar?");
												if(r==true)
												{
													$.ajax({url:'ajax/usuarios.php',type:'POST',contentType:false,data:data,processData:false,cache:false,success:function(data){
														data=JSON.parse(data);
														if(data[0]=="1")
														{ alert("Se Actualizaron Los Datos Del Usuario: \""+data[1]+"\""); }
														else if(data[0]=="0")
														{
															if(data[1]=="1")
															{ alert("No Se Pudieron Actualizar Los Datos"); }
														}
														else if(data[0]=="2")
														{ alert("Error En Datos"); $("#d_"+data[1]).focus(); }
													}});
												}
											}else{alert("Indique Una Fecha De Cese Correcta"); $("#d_fce").focus();}
										}else{alert("Indique Una Fecha De Ingreso Correcta"); $("#d_fin").focus();}
									}else{alert("Indique Un Nivel Correcto"); $("#d_niv").focus();}
									
								});
								
								
								
								$(document).on("click","#saven",function(){
									var dni=$("#n_dni").val();
									var nom=$("#n_nom").val();
									var ape=$("#n_ape").val();
									var ema=$("#n_ema").val();
									var fna=$("#n_fna").val();
									var ruc=$("#n_ruc").val();
									var dir=$("#n_dir").val();
									var tel=$("#n_tel").val();
									var cel=$("#n_cel").val();
									var loc=$("#n_loc").val();
									var jef=$("#n_jef").val();
									var car=$("#n_car").val();
									var fot=$("#n_fot").val();
									
									var depa=$("#n_depa").val();
									var prov=$("#n_prov").val();
									var dist=$("#n_dist").val();
									
									var sex=$("#n_sex").val();
									var state=$("#n_state").val();
									
									var loc_area=$("#n_loc_area").val();
									
									var camp=$("#n_camp").val();
									/************************/
										/************************/
											var tse=$("#n_tse").val();
											var cal=$("#n_cal").val();
											var act=$("#n_act").val();
											var zkp=$("#n_zkp").val();
											var tje=$("#n_tje").val();
											
											if(dni!=null && dni.trim()!="" && !isNaN(dni) && dni.length==8)
											{
												if(nom!=null && nom.trim()!="")
												{
													if(ape!=null && ape.trim()!="")
													{
														if(1 /*ema!=null && ema.trim()!="" && validateEmail(ema)*/)
														{
															var tmp=fna.split("-");
															if(1 /*fna!=null && fna.trim()!="" && !isNaN(tmp[0]) && !isNaN(tmp[1]) && !isNaN(tmp[2]) && parseInt(tmp[0])>0 && parseInt(tmp[1])>0 && parseInt(tmp[2])>0*/)
															{
																if(ruc!=null && ((ruc=="") || (ruc.trim()!="" && !isNaN(ruc) && ruc.length==8)) || 1)
																{
																	if(1 /*dir!=null && dir.trim()!=""*/)
																	{
																		if(1 /*tel!=null && ((tel=="") || (tel.trim()!="" && !isNaN(tel) && tel.length==7))*/)
																		{
																			if(1 /*cel!=null && ((cel=="") || (cel.trim()!="" && !isNaN(cel) && cel.length==9))*/)
																			{
																				if(1  /*tel!="" || cel!=""*/)
																				{
																					if(loc!=null && loc.trim()!="")
																					{
																						if(tje=="0" || (tje=="1" && jef!=null && jef.trim()!=""))
																						{
																							if(car!=null && car.trim()!="")
																							{
																								if(1 /*fot!=null && fot!=""*/)
																								{	
																									if(tse!=null && tse.trim()!="" && !isNaN(tse) && parseInt(tse)>0)
																									{	
																										if(act=="0" || act=="1")
																										{
																											if(zkp=="0" || zkp=="1")
																											{
																												if(tje=="0" || tje=="1")
																												{
																													var r=confirm("Se Procedera A Crear El Usuario, Desea Continuar?");
																													if(r==true)
																													{
																														var fot=document.getElementById("n_fot").files[0];
																														var data = new FormData();
																														data.append('fot',fot);
																														data.append('dni',dni);
																														data.append('nom',nom);
																														data.append('ape',ape);
																														data.append('ema',ema);
																														data.append('fna',fna);
																														data.append('ruc',ruc);
																														data.append('dir',dir);
																														data.append('tel',tel);
																														data.append('cel',cel);
																														data.append('loc',loc);
																														data.append('depa',depa);
																														data.append('prov',prov);
																														data.append('dist',dist);
																														data.append('sex',sex);
																														data.append('state',state);
																														data.append('camp',camp);
																														data.append('jef',jef);
																														data.append('car',car);
																														data.append('tse',tse);
																														data.append('cal',cal);
																														data.append('loc_area',loc_area);
																														data.append('act',act);
																														data.append('zkp',zkp);
																														data.append('tje',tje);
																														data.append('a','snu');
																														
																														$.ajax({url:'ajax/usuarios.php',type:'POST',contentType:false,data:data,processData:false,cache:false,success:function(data){
																															data=JSON.parse(data);
																															
																															if(data[0]=="1")
																															{ alert("Se Creo El Usuario Con El Codigo: "+data[1]+", y el login: "+data[2]); location.reload(); }
																															else if(data[0]=="0")
																															{
																																if(data[1]=="1")
																																{alert("El DNI Ingresado Ya Esta Registrado"); $("#n_dni").focus();}
																																else if(data[1]=="2")
																																{alert("No Se Pudo Registrar El Usuario"); location.reload();}
																															}
																															else if(data[0]=="2")
																															{ alert("Error En Datos"); $("#n_"+data[1]).focus(); }
																														}});
																													}
																												}else{alert("Marque O Desmarque La Casilla de Tiene Jefe");}
																											}else{alert("Marque O Desmarque La Casilla de ZK Privilege");}
																										}else{alert("Marque O Desmarque La Casilla de Activo");}
																									}else{alert("Indique Un Tiempo De Sesion Correcta"); $("#n_tse").focus();}
																								}else{alert("Debe Adjuntar Una Foto"); $("#n_fot").focus();}
																							}else{alert("Debe Especificar Un Cargo"); $("#n_car").focus();}
																						}else{alert("Debe Especificar Un Area"); $("#n_jef").focus();}
																					}else{alert("Debe Especificar Un Local"); $("#n_loc").focus();}
																				}else{alert("Debe Indicar Almenos Un Numero de Contacto"); $("#n_tel").focus();}
																			}else{alert("Indique Un Celular Correcto"); $("#n_cel").focus();}
																		}else{alert("Indique Un Telefono Correcto"); $("#n_tel").focus();}
																	}else{alert("Indique Una Direccion Correcta"); $("#n_dir").focus();}
																}else{alert("Indique Un Numero De RUC Correcto"); $("#n_ruc").focus();}
															}else{alert("Indique Una Fecha De Nacimiento Correcta"); $("#n_fna").focus();}
														}else{alert("Indique Un Email Correcto"); $("#n_ema").focus();}
													}else{alert("Indique Apellidos Correctos"); $("#n_ape").focus();}
												}else{alert("Indique Un Nombre Correcto"); $("#n_nom").focus();}
											}else{alert("Indique Un DNI Correcto"); $("#n_dni").focus();}
											
										});
										
										
										$(document).on("click","#close_sess",function(){
											var usr=$("#usra").val();
											var r=confirm("Se va cerrar la sesion del Asesor, Desea Continuar?");
											if(r==true)
											{
												$.post("ajax/usuarios.php",{a:"close_s",usr:usr},function(data){
													if(data=="1")
													{alert("Se Cerro la ultima sesion de "+usr);}
													else if(data=="0")
													{}
												});
											}
										});
										$(document).on("click","#gener",function(){
											
											$.post("ajax/usuarios.php",{a:"rep_gen"},function(data){
												document.location = data;
											});
											
										});
										
										
									});
								</script>
							</head>
							<body>
								<div class="container">
									<h2 class="text-center">Gestion de Usuarios</h2>
									<div class="col-sm-12">
										<form method="GET">
											<div style="margin-bottom:10px;" class="input-group">
												
												<input id="vbusq" type="text" name="d_val" class="form-control" placeholder="Buscar...">
												<span class="input-group-btn">
													<button id="busq" class="btn btn-primary" type="button">Buscar</button>
												</span>
											</div>
										</form>
									</div>
									
									<div  class="col-sm-12" id="work1">
										<table class="table">
											<thead id="tres"><tr><th>Login</th><th>Nombre</th><th>Apellidos</th><th>Campaña</th><th>ID PATROC.</th><th>NOM. PATROC.</th></tr></thead>
											<tbody id="resb"><?php echo $htm;?></tbody>
										</table>
									</div>
									
									
									
									<div style="display:none;" class="col-sm-12" id="work2">
										<h4>Datos Usuario: <label id="lbua"></label> | <label>Activo <input id="d_act" type="checkbox" value="1" checked></label></h4>
										<div role="tabpanel">
											<!-- Nav tabs -->
											<ul class="nav nav-tabs" role="tablist">
												<li id="tab1" role="presentation" class="active"><a href="#usuario" aria-controls="usuario" role="tab" data-toggle="tab">Datos de Usuario</a></li>
												<li id="tab3" role="presentation"><a href="#dopc" aria-controls="dopc" role="tab" data-toggle="tab">Datos Opcionales</a></li>
												<li id="tab2" role="presentation"><a href="#acceso" aria-controls="acceso" role="tab" data-toggle="tab">Permisos</a></li>
											</ul>
											
											<!-- Tab panes -->
											<div class="tab-content">
												<div role="tabpanel" class="tab-pane active" id="usuario">
													
													<h4 class="col-sm-12">Datos Personales:</h4>
													<div style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-9">
														<div class="col-sm-4">
															<label>Nombre:</label>
															<input placeholder="Nombre" id="d_nom" class="form-control input-sm" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
														</div>
														
														<div class="col-sm-4">
															<label>Apellidos:</label>
															<input placeholder="Apellidos" id="d_ape" class="form-control input-sm" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
														</div>
														<div class="col-sm-4">
															<label>Email:</label>
															<input placeholder="Email" id="d_ema" class="form-control input-sm"/>
														</div>
														
														<div class="col-sm-3">
															<label>F. Nacimiento:</label>
															<input data-date-format='YYYY-MM-DD' placeholder="F. Nacimiento" id="d_fna" class="form-control input-sm"/>
														</div>
														<div class="col-sm-3">
															<label>DNI:</label>
															<input placeholder="DNI" id="d_ruc" class="form-control input-sm"/>
														</div>
														<div class="col-sm-3">
															<label>Sexo:</label>
															<SELECT id="d_sex" class="form-control input-sm">
																<option value="">----</option><option value="M">MASCULINO</option><option value="F">FEMENINO</option>
															</SELECT>
														</div>
														<div class="col-sm-3">
															<label>Estado Civil:</label>
															<SELECT id="d_state" class="form-control input-sm">
																<option value="">----</option><option>SOLTERO</option><option>CASADO</option><option>CONVIVIENTE</option><option>DIVORCIADO</option><option>VIUDO</option>
															</SELECT>
														</div>
														
														<div class="col-sm-6">
															<label>Direccion:</label>
															<input placeholder="Direccion" id="d_dir" class="form-control input-sm" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
														</div>
														<div class="col-sm-3">
															<label>Telefono:</label>
															<input placeholder="Telefono" id="d_tel" class="form-control input-sm"/>
														</div>
														<div class="col-sm-3">
															<label>Celular:</label>
															<input placeholder="Celular" id="d_cel" class="form-control input-sm"/>
														</div>
														<div class='form-group col-sm-4'><label>Departamento</label><select id='d_depa' class='depa form-control input-sm indest'><?php echo $dep;?></select></div><div class='form-group col-sm-4'><label>Provincia</label><select id='d_prov' class='prov form-control input-sm indest'></select></div><div class='form-group col-sm-4 indest'><label>Distrito</label><select id='d_dist' class='dist form-control input-sm indest'></select></div>
														
														<!-------->
														<h4 class="col-sm-12">Datos de Sistema:</h4>
														<div style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-12">
															<div class="col-sm-3">
																<label>Local:</label>
																<select placeholder="Local" id="d_loc" class="loc form-control input-sm"><?php echo $opt_loc;?></select>
															</div>
															<div class="col-sm-3">
																<label>Area Local:</label>
																<select placeholder="Local" id="d_loc_area" class="loc_area form-control input-sm"></select>
															</div>
															<div class="col-sm-3">
																<label>Area:</label>
																<select placeholder="Jefe" id="d_jef" class="form-control input-sm"><?php echo $opt_log;?></select>
															</div>
															<div class="col-sm-3">
																<label>Cargo:</label>
																<select placeholder="Cargo" id="d_car" class="form-control input-sm"></select>
															</div>
														</div>
														<!-------->
														
													</div>
													
													<div style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-3">
														<div class="col-sm-12">
															<label>Campaña:</label>
															<select  id="d_camp" class="form-control input-sm"><?php echo $opt_camp;?></select>
														</div>
														<div class="col-sm-12">
															<label>Foto:</label>
															<input type="file" placeholder="Foto" id="d_fot" class="form-control input-sm"/>
														</div>
														<div class="col-sm-12 text-center">
															<img id="d_pfo" style="margin-top:10px; width:100px; height:100px;"></img>
														</div>
														<div class="text-center">
															<button id="rstps" style="margin-top:10px;" class="btn btn-danger">Resetear Password</button>
															<button id="andkey" style="margin-top:10px;" class="btn btn-primary">Generar llave</button>
															<button id="close_sess" style="margin-top:10px;" class="btn btn-sm btn-primary">Cerrar Ultima Sesion</button>
														</div>
													</div>
													
													
													
													
													
													
													<h4 inf="+" style="cursor:pointer;" class="col-sm-12">Datos de Sistema Opcionales:</h4>
													<div id="divsd" style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-12">
														<div class="col-sm-3">
															<label>Tiempo Sesion:</label>
															<input value="18000" placeholder="Tiempo Sesion" id="d_tse" class="form-control input-sm"/>
														</div>
														<div class="col-sm-3">
															<label>Codigo Alterno:</label>
															<input placeholder="Codigo Alterno" id="d_cal" class="form-control input-sm"/>
														</div>
														<div style="margin-top:10px;" class="col-sm-6">
															<label class="col-xs-12" style="height:10px;"></label>
															<label>&nbsp;&nbsp;ZK Privilege <input id="d_zkp" type="checkbox" value="0"></label>
															<label></label>
															<label style="display:none;">&nbsp;&nbsp;Es Jefe <input id="d_tje" type="checkbox" value="0"></label>
														</div>
													</div>
													<input type="hidden" id="usra"/>
													<div class="text-center col-xs-12"><button style="margin-top:10px;" id="savc" class="btn btn-success">Actualizar</button></div>
													
												</div>
												<div role="tabpanel" class="tab-pane" id="acceso">
													
													<div style="margin-bottom:10px;" class="col-sm-3">
														<label>Roles:</label>
														<select id="lstrd" class="form-control input-sm"><?php echo $opt_rol;?></select>
													</div>
													<div style="margin-bottom:10px;" class="col-sm-3">
														<label class="col-xs-12" style="height:20px;"></label>
														<button id="addr" class="btn btn-success btn-sm">Agregar</button>
													</div>
													
													<table class="table table-bordered">
														<thead><tr><th>ID Rol</th><th>Descripcion</th><th>Quitar</th></tr></thead>
														<tbody id="lstpd"></tbody>
													</table>
													
												</div>
												
												<div role="tabpanel" class="tab-pane" id="dopc">
													<h4 inf="+" id="divod" style="cursor:pointer;" class="col-sm-12">Datos Personales Opcionales:</h4>
													<div style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-12">
														<div class="col-sm-3">
															<label>Nivel:</label>
															<select id="d_niv" class="form-control input-sm">
																<option value="PRI">PRIMARIA</option>
																<option selected value="SEC">SECUNDARIA</option>
																<option value="SUP">SUPERIOR</option>
															</select>
														</div>
														<div class="col-sm-3">
															<label>Fecha Ingreso:</label>
															<input data-date-format='YYYY-MM-DD' placeholder="Fecha Ingreso" id="d_fin" class="form-control input-sm"/>
														</div>
														<div class="col-sm-3">
															<label>Fecha Cese:</label>
															<input data-date-format='YYYY-MM-DD' placeholder="Fecha Cese" id="d_fce" class="form-control input-sm"/>
														</div>
														<div class="col-sm-3">
															<br />
															<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_fired">Detalles de Cese</button>
														</div>
														
														<div class="col-sm-6">
															<label>Experiencia:</label>
															<textarea style="resize:none;" id="d_exp" class="form-control"></textarea>
														</div>
														<div class="col-sm-6">
															<label>Notas:</label>
															<textarea style="resize:none;" id="d_not" class="form-control"></textarea>
														</div>
														<?php 
															//////DATOS ADICIONALES CON LISTA
															$lista=2;
															$res=mysql_query("select idcampo,nombre from list_lista_campos where idlista=$lista and idcampo!=1");
															while($r=mysql_fetch_row($res)){
																echo "<div  class='col-sm-6'><label>$r[1]:</label> <input data-id='C$r[0]' id='C$r[0]' class='listac form-control input-sm'/></div>";
															}
															
															
														?>
														<div class="col-xs-12 text-center"><button style="margin-top:10px;" id="svdpx" class="btn btn-success">Actualizar</button></div>
													</div>	
												</div>
											</div>
										</div>
									</div>
									
									
									
									<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													<h4 class="modal-title">Nuevo Usuario</h4>
												</div>
												<div class="modal-body">
													<div class="row">
														
														<h4 class="col-sm-12">Datos Personales:</h4>
														<div style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-9">
															<div class="col-sm-4">
																<label>DNI:</label>
																<input placeholder="DNI" id="n_dni" class="form-control input-sm"/>
															</div>
															<div class="col-sm-4">
																<label>Nombre:</label>
																<input placeholder="Nombre" id="n_nom" class="form-control input-sm" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
															</div>
															
															<div class="col-sm-4">
																<label>Apellidos:</label>
																<input placeholder="Apellidos" id="n_ape" class="form-control input-sm" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
															</div>
															<div class="col-sm-4">
																<label>Email:</label>
																<input placeholder="Email" id="n_ema" class="form-control input-sm"/>
															</div>
															
															<div class="col-sm-3">
																<label>F. Nacimiento:</label>
																<input data-date-format='YYYY-MM-DD' placeholder="F. Nacimiento" id="n_fna" class="form-control input-sm"/>
															</div>
															<div class="col-sm-2">
																<label>SEXO:</label>
																<SELECT id="n_sex" class="form-control input-sm">
																	<option value="">----</option><option value="M">MASCULINO</option><option value="F">FEMENINO</option>
																</SELECT>
															</div>
															<div class="col-sm-3">
																<label>ESTADO CIVIL:</label>
																<SELECT id="n_state" class="form-control input-sm">
																	<option value="">----</option><option>SOLTERO</option><option>CASADO</option><option>CONVIVIENTE</option><option>DIVORCIADO</option><option>VIUDO</option>
																</SELECT>
															</div>
															<div class="col-sm-6">
																<label>Direccion:</label>
																<input placeholder="Direccion" id="n_dir" class="form-control input-sm" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
															</div>
															<div class="col-sm-3">
																<label>Telefono:</label>
																<input placeholder="Telefono" id="n_tel" class="form-control input-sm"/>
															</div>
															<div class="col-sm-3">
																<label>Celular:</label>
																<input placeholder="Celular" id="n_cel" class="form-control input-sm"/>
															</div>
															<div class='form-group col-sm-4'><label>Departamento</label><select id='n_depa' class='depa form-control input-sm indest'><?php echo $dep;?></select></div><div class='form-group col-sm-4'><label>Provincia</label><select id='n_prov' class='prov form-control input-sm indest'></select></div><div class='form-group col-sm-4 indest'><label>Distrito</label><select id='n_dist' class='dist form-control input-sm indest'></select></div>
														</div>
														<div style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-3">
															<div class="col-sm-12">
																<label>Campaña:</label>
																<select  id="n_camp" class="form-control input-sm"><?php echo $opt_camp;?></select>
															</div>
															<div class="col-sm-12">
																<label>Foto:</label>
																<input type="file" placeholder="Foto" id="n_fot" class="form-control input-sm"/>
															</div>
															<div class="col-sm-12 text-center">
																<img id="n_pfo" style="margin-top:10px; width:100px; height:100px;"></img>
															</div>
															<div class="text-center"><button id="saven" class="btn btn-success">Guardar</button></div>
														</div>
														
														<h4 class="col-sm-12">Datos de Sistema:</h4>
														<div style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-12">
															<div class="col-sm-3">
																<label>Local:</label>
																<select placeholder="Local" id="n_loc" class="loc form-control input-sm"><?php echo $opt_loc;?></select>
															</div>
															<div class="col-sm-3">
																<label>Area Local:</label>
																<select placeholder="Local" id="n_loc_area" class="loc_area form-control input-sm"></select>
															</div>
															<div class="col-sm-3">
																<label>Area:</label>
																<select placeholder="Jefe" id="n_jef" class="form-control input-sm"><?php echo $opt_log;?></select>
															</div>
															<div class="col-sm-3">
																<label>Cargo:</label>
																<select placeholder="Cargo" id="n_car" class="form-control input-sm"></select>
															</div>
														</div>
														
														
														<!--
															<h4 inf="+" id="divop" style="cursor:pointer;" class="col-sm-12">Datos Personales Opcionales: (<label style="cursor:pointer;" id="labsp">+</label>)</h4>
															<div id="divsop" style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px; display:none;" class="col-sm-12">
															<div class="col-sm-3">
															<label>Nivel:</label>
															<select id="n_niv" class="form-control input-sm">
															<option value="PRI">PRIMARIA</option>
															<option selected value="SEC">SECUNDARIA</option>
															<option value="SUP">SUPERIOR</option>
															</select>
															</div>
															<div class="col-sm-3">
															<label>Fecha Ingreso:</label>
															<input data-date-format='YYYY-MM-DD' placeholder="Fecha Ingreso" id="n_fin" class="form-control input-sm"/>
															</div>
															<div class="col-sm-3">
															<label>Fecha Cese:</label>
															<input data-date-format='YYYY-MM-DD' placeholder="Fecha Cese" id="n_fce" class="form-control input-sm"/>
															</div>
															<div class="col-sm-6">
															<label>Experiencia:</label>
															<textarea style="resize:none;" id="n_exp" class="form-control"></textarea>
															</div>
															<div class="col-sm-6">
															<label>Notas:</label>
															<textarea style="resize:none;" id="n_not" class="form-control"></textarea>
															</div>
														</div>-->
														
														
														<h4 inf="+" id="divo" style="cursor:pointer;" class="col-sm-12">Datos de Sistema Opcionales: (<label style="cursor:pointer;" id="labs">+</label>)</h4>
														<div id="divso" style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px; display:none;" class="col-sm-12">
															<div class="col-sm-3">
																<label>Tiempo Sesion:</label>
																<input value="18000" placeholder="Tiempo Sesion" id="n_tse" class="form-control input-sm"/>
															</div>
															<div class="col-sm-3">
																<label>Codigo Alterno:</label>
																<input placeholder="Codigo Alterno" id="n_cal" class="form-control input-sm"/>
															</div>
															<div style="margin-top:10px;" class="col-sm-12">
																<label>Activo <input id="n_act" type="checkbox" value="1" checked></label>
																<label>&nbsp;&nbsp;ZK Privilege <input id="n_zkp" type="checkbox" value="0"></label>
																<label>&nbsp;&nbsp;Jefe <input id="n_tje" type="checkbox" value="0"></label>
															</div>
														</div>
														
														
														
													</div>
												</div>
											</div>
										</div>
									</div>
									
									
									<div class="modal fade" id="modal_fired" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													<h4 class="modal-title">Detalles de Cese</h4>
												</div>
												<div class="modal-body">
													<div class="row">
														<div style="margin-left:0px; margin-right:0px; padding-left:0px; padding-right:0px;" class="col-sm-12">
															<div class="col-sm-6">
																<label>TIPO DE RENUNCIA:</label>
																<SELECT id="d_tipof" class="form-control input-sm">
																	<?php echo $opt_fired;?>
																</SELECT>
															</div>
															<div class="col-sm-6">
																<label>MOTIVO DE RENUNCIA:</label>
																<SELECT id="d_motif" class="form-control input-sm">
																	<option value="">----</option>
																</SELECT>
															</div>
															
															
															
														</div>
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<!-- <button id="save_fired" class="btn btn-success">Guardar</button>-->
												</div>
											</div>
										</div>
										
									</body>
								</html>
														