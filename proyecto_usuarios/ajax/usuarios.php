<?php
	require("../../sec.php");
	$d_a=$_POST["a"];
	
	if($d_a=="busr")
	{
		$d_val=$_POST["val"];
		$est_usr=array("0"=>"DESACTIVADO","1"=>"ACTIVO","2"=>"DE BAJA");
		$res=qry("select a.login,a.nombre,a.apellidos,a.activo,TRIM(UPPER(CONCAT(if(isnull(a.apellidos),'',a.apellidos),' ',if(isnull(a.nombre)=1,'',a.nombre)))),d.nombre,f.iduser,f.nombre,b.deuda,day(b.hora_update),a.iduser from usuarios a left join user_nivel b on b.iduser=a.iduser left join niveles_red d on d.idnivel=b.nivel left join asociadas e on e.idsocio=a.iduser left join usuarios f on f.iduser=e.idempresa  where ((a.login like '%".$d_val."%') or (a.nombre like '%".$d_val."%') or (a.apellidos like '%".$d_val."%') or (a.iduser like '%".$d_val."%')) limit 20;");
		if(mysql_num_rows($res)>0)
		{
			$htm="";
			while($temp=mysql_fetch_row($res))
			{$htm.="<tr class='showd' inf='".$temp[0]."' nom='".$temp[4]."' ><td><a href='../users/edituser_admin.php?login=$temp[0]&iduser=$temp[10]'>".$temp[0]."</a></td><td>".$temp[1]."</td><td>".$temp[2]."</td><td>".$temp[5]."</td><td>".$temp[6]."</td><td>".$temp[7]."</td><td>Deuda:".$temp[8]."<br />Corte: $temp[9]</td></tr>";}
			echo json_encode(array("1",$htm));
		}
		else
		{echo json_encode(array("1","<tr><td colspan='4'>No Se Encontraron Resultados</td></tr>"));}
	}
	
	if($d_a=="snu")
	{
		$d_dni=$_POST["dni"];
		$d_nom=$_POST["nom"];
		$d_ape=$_POST["ape"];
		$d_ema=$_POST["ema"];
		$d_fna=$_POST["fna"];
		$d_ruc=$_POST["ruc"];
		$d_dir=$_POST["dir"];
		$d_tel=$_POST["tel"];
		$d_cel=$_POST["cel"];
		$d_loc=$_POST["loc"];
		$d_jef=$_POST["jef"];
		$d_car=$_POST["car"];
		$d_depa=$_POST["depa"];
		$d_prov=$_POST["prov"];
		$d_dist=$_POST["dist"];
		$d_sex=$_POST["sex"];
		$d_state=$_POST["state"];
		$d_loc_area=$_POST["loc_area"];
		$d_camp=$_POST["camp"];
		/******************/
		$d_tse=$_POST["tse"];
		$d_cal=$_POST["cal"];
		$d_act=$_POST["act"];
		$d_zkp=$_POST["zkp"];
		$d_tje=$_POST["tje"];
		/*******************/
		
		
		
		if(!is_null($d_dni) && trim($d_dni)!="" && is_numeric($d_dni) && strlen($d_dni)==8)
		{
			if(!is_null($d_nom) && trim($d_nom)!="")
			{
				if(!is_null($d_ape) && trim($d_ape)!="")
				{
					if(1 /*!is_null($d_ema) && trim($d_ema)!="" && filter_var($d_ema, FILTER_VALIDATE_EMAIL)*/)
					{
						$tmp=explode("-",$d_fna);
						if(1 /*!is_null($d_fna) && trim($d_fna)!="" && is_numeric($tmp[0]) && is_numeric($tmp[1]) && is_numeric($tmp[2]) && $tmp[0]>0 && $tmp[1]>0 && $tmp[2]>0*/)
						{
							if(!is_null($d_ruc) && (($d_ruc=="") || (trim($d_ruc)!="" && is_numeric($d_ruc) && strlen($d_ruc)==11)) || 1)
							{
								if(1 /*!is_null($d_dir) && trim($d_dir)!=""*/)
								{
									if(1 /*!is_null($d_tel) && (($d_tel=="") || (trim($d_tel)!="" && is_numeric($d_tel) && strlen($d_tel)==7))*/)
									{
										if(1 /*!is_null($d_cel) && (($d_cel=="") || (trim($d_cel)!="" && is_numeric($d_cel) && strlen($d_cel)==9))*/)
										{
											if(1 /*$d_tel!="" || $d_cel!=""*/)
											{
												if(!is_null($d_loc) && trim($d_loc)!="")
												{
													if($d_tje=="0" || ($d_tje=="1" && !is_null($d_jef) && trim($d_jef)!=""))
													{
														if(!is_null($d_car) && trim($d_car)!="")
														{
															if(!is_null($d_tse) && trim($d_tse)!="" && is_numeric($d_tse) && $d_tse>0)
															{
																if($d_act=="0" || $d_act=="1")
																{
																	if($d_zkp=="0" || $d_zkp=="1")
																	{
																		if($d_tje=="0" || $d_tje=="1")
																		{
																			if(1 || isset($_FILES['fot']))
																			{
																				$fsize=$_FILES['fot']['size'];
																				if(1 ||  $fsize>0)
																				{
																					///nombre de la foto
																					$c_fot=clave(20);
																					$carpf="/var/www/html/proyecto_usuarios/cfotos/";
																					$mvul=move_uploaded_file($_FILES['fot']['tmp_name'],$carpf.$c_fot.".jpg");
																					if(1 || $mvul)
																					{
																						if(!dni_existe($d_dni)){
																							
																							///password
																							$c_pass=clave(6);
																							$c_pass=$d_dni;
																							
																							$qry="insert into usuarios(idpersona,nombre,apellidos,email,ffnn,direccion,telefono,celular,local,idarea,timeout,cod_usu_sn,activo,zk_privilege,tiene_jefe,foto,passwd,cargo,ubigeo,ruc,sexo,estado_civil,local_area) values('".$d_dni."','".$d_nom."','".$d_ape."','".$d_ema."','".$d_fna."','".$d_dir."','".$d_tel."','".$d_cel."','".$d_loc."','".$d_jef."','".$d_tse."','".$d_cal."','".$d_act."','".$d_zkp."','".$d_tje."','".$c_fot."','".$c_pass."','".$d_car."','$d_depa$d_prov$d_dist','123456','$d_sex','$d_state','$d_loc_area');";
																							qry($qry);
																							$idusr=mysql_insert_id();
																							tracechange($Xlogin,"usuarios","iduser",$idusr,"iduser","N",$idusr);
																							
																							if($idusr!="0" && $idusr!="")
																							{
																								///generando login
																								/*$d_log="";
																									$d_nom1=str_replace(" ","",strtolower($d_nom));
																									$d_ape1=str_replace(" ","",strtolower($d_ape));
																								$d_log.=substr($d_nom1,0,1).substr($d_ape1,0,14);*/
																								$d_log=$d_dni;
																								qry("update usuarios set login='".$d_log."' where iduser='".$idusr."';");
																								////mandar password en bandeja de supervisor
																								
																								///si es jefe actualizar en area y desactivar a los demas
																								if($d_tje=="1")
																								{
																									$ida=$d_jef;
																									mysql_query("update areas set jefe_area='".$d_log."' where idarea='".$ida."' and estado='1';");
																									mysql_query("update usuarios set tiene_jefe='0' where login!='".$d_log."' and idarea='".$ida."';");
																								}
																								mysql_query("insert into permisos values ('$d_log',100)");
																								////////Permisos Automaticos
																								/*
																									$sqln = mysql_query("INSERT INTO erp2.usuarios (nombre, login, passwd, local, activo,apellidos,dni,idpersona) VALUES('$d_nom','$d_log','$c_pass','$d_loc','$d_act','$d_ape','$d_dni','$d_dni')") or die(mysql_error());
																									mysql_query("insert into erp2.permisos values ('$d_log',101),('$d_log',302)");
																									mysql_query("insert into erp2.permisos values ('$d_log',100)");
																									mysql_query("insert into permisos values ('$d_log',1)");
																									mysql_query("insert into permisos values ('$d_log',3)");
																									mysql_query("insert into permisos values ('$d_log',5)");
																								mysql_query("insert into userrol values (10000,'$d_log',now())");*/
																								
																								
																								echo json_encode(array("1",$idusr,$d_log));
																							}else{echo json_encode(array("0","2")); unlink($carpf.$c_fot.".jpg");}
																						}else{echo json_encode(array("0","1")); unlink($carpf.$c_fot.".jpg");}
																					}else{echo json_encode(array("2","fot1"));}
																				}else{echo json_encode(array("2","fot2"));}
																			}else{echo json_encode(array("2","fot3"));}
																		}else{echo json_encode(array("2","tje"));}
																	}else{echo json_encode(array("2","zkp"));}
																}else{echo json_encode(array("2","act"));}
															}else{echo json_encode(array("2","tse"));}
														}else{echo json_encode(array("2","car"));}
													}else{echo json_encode(array("2","jef"));}
												}else{echo json_encode(array("2","loc"));}
											}else{echo json_encode(array("2","tel"));}
										}else{echo json_encode(array("2","cel"));}
									}else{echo json_encode(array("2","tel"));}
								}else{echo json_encode(array("2","dir"));}
							}else{echo json_encode(array("2","ruc"));}
						}else{echo json_encode(array("2","fna"));}
					}else{echo json_encode(array("2","ema"));}
				}else{echo json_encode(array("2","ape"));}
			}else{echo json_encode(array("2","nom"));}
		}else{echo json_encode(array("2","dni"));}
		
	}
	
	if($d_a=="updu")
	{
		$d_nom=$_POST["nom"];
		$d_ape=$_POST["ape"];
		$d_ema=$_POST["ema"];
		$d_fna=$_POST["fna"];
		$d_ruc=$_POST["ruc"];
		$d_dir=$_POST["dir"];
		$d_tel=$_POST["tel"];
		$d_cel=$_POST["cel"];
		$d_loc=$_POST["loc"];
		$d_jef=$_POST["jef"];
		$d_car=$_POST["car"];
		$d_depa=$_POST["depa"];
		$d_prov=$_POST["prov"];
		$d_dist=$_POST["dist"];
		$d_sex=$_POST["sex"];
		$d_state=$_POST["state"];
		$d_loc_area=$_POST["loc_area"];
		$d_camp=$_POST["camp"];
		/******************/
		$d_tse=$_POST["tse"];
		$d_cal=$_POST["cal"];
		$d_act=$_POST["act"];
		$d_zkp=$_POST["zkp"];
		$d_tje=$_POST["tje"];
		/*******************/
		$d_usr=$_POST["usr"];
		
		
		if(!is_null($d_nom) && trim($d_nom)!="")
		{
			if(!is_null($d_ape) && trim($d_ape)!="")
			{
				if(1 /*!is_null($d_ema) && trim($d_ema)!="" && filter_var($d_ema, FILTER_VALIDATE_EMAIL)*/)
				{
					$tmp=explode("-",$d_fna);
					if(!is_null($d_fna) && trim($d_fna)!="" && is_numeric($tmp[0]) && is_numeric($tmp[1]) && is_numeric($tmp[2]) && $tmp[0]>0 && $tmp[1]>0 && $tmp[2]>0)
					{
						if(!is_null($d_ruc) && (($d_ruc=="") || (trim($d_ruc)!="" && is_numeric($d_ruc) && strlen($d_ruc)==8)))
						{
							if(1 /*!is_null($d_dir) && trim($d_dir)!=""*/)
							{
								if(1 /*!is_null($d_tel) && (($d_tel=="") || (trim($d_tel)!="" && is_numeric($d_tel) && strlen($d_tel)==7))*/)
								{
									if(1 /*!is_null($d_cel) && (($d_cel=="") || (trim($d_cel)!="" && is_numeric($d_cel) && strlen($d_cel)==9))*/)
									{
										if(1 /*$d_tel!="" || $d_cel!=""*/)
										{
											if(!is_null($d_loc) && trim($d_loc)!="")
											{
												if($d_tje=="0" || ($d_tje=="1" && !is_null($d_jef) && trim($d_jef)!=""))
												{
													if(!is_null($d_car) && trim($d_car)!="")
													{
														
														
														
														
														
														if(!is_null($d_tse) && trim($d_tse)!="" && is_numeric($d_tse) && $d_tse>0)
														{
															if($d_act=="0" || $d_act=="1")
															{
																if($d_zkp=="0" || $d_zkp=="1")
																{
																	if($d_tje=="0" || $d_tje=="1")
																	{
																		$c_fot=get_foto($d_usr);
																		if($c_fot=="" || isset($_FILES['fot']))
																		{ $c_fot=clave(20); }
																		
																		$carpf="/var/www/html/proyecto_usuarios/cfotos/";
																		if(!isset($_FILES['fot']) || ( isset($_FILES['fot']) && move_uploaded_file($_FILES['fot']['tmp_name'],$carpf.$c_fot.".jpg") ))
																		{
																			$tr=mysql_fetch_row(qry("select iduser from usuarios where login='$d_usr'"));
																			tracechange($Xlogin,"usuarios","iduser",$tr[0],"nombre",1,$d_nom);
																			tracechange($Xlogin,"usuarios","iduser",$tr[0],"apellidos",1,$d_ape);
																			tracechange($Xlogin,"usuarios","iduser",$tr[0],"email",1,$d_ema);
																			tracechange($Xlogin,"usuarios","iduser",$tr[0],"ffnn",1,$d_fna);
																			tracechange($Xlogin,"usuarios","iduser",$tr[0],"idpersona",1,$d_ruc);
																			tracechange($Xlogin,"usuarios","iduser",$tr[0],"local",1,$d_loc);	
																			tracechange($Xlogin,"usuarios","iduser",$tr[0],"activo",1,$d_act);
																			tracechange($Xlogin,"usuarios","iduser",$tr[0],"campout",1,$d_camp);
																			$qry="update usuarios set nombre='".$d_nom."',apellidos='".$d_ape."',email='".$d_ema."',ffnn='".$d_fna."',idpersona='".$d_ruc."',direccion='".$d_dir."',telefono='".$d_tel."',celular='".$d_cel."',local='".$d_loc."',idarea='".$d_jef."',timeout='".$d_tse."',cod_usu_sn='".$d_cal."',activo='".$d_act."',zk_privilege='".$d_zkp."',tiene_jefe='".$d_tje."',foto='".$c_fot."',cargo='".$d_car."', ubigeo='$d_depa$d_prov$d_dist',sexo='$d_sex',estado_civil='$d_state',local_area='$d_loc_area', campout='$d_camp' where login='".$d_usr."';";
																			qry($qry);
																			if(mysql_affected_rows()==1)
																			{
																				asigna_cola($d_usr,$d_camp,$Xlogin);
																				/*$qry="update erp2.usuarios set nombre='".$d_nom."',apellidos='".$d_ape."',email='".$d_ema."',ffnn='".$d_fna."',idpersona='".$d_ruc."',direccion='".$d_dir."',telefono='".$d_tel."',celular='".$d_cel."',local='".$d_loc."',timeout='".$d_tse."',cod_usu_sn='".$d_cal."',activo='".$d_act."',foto='".$c_fot."' where login='".$d_usr."';";
																				qry($qry);*/
																				echo json_encode(array("1",$d_usr));
																			}else{echo json_encode(array("0","1"));}
																		}else{echo json_encode(array("2","fot"));}
																		
																		
																		/*	
																			if(isset($_FILES['fot']))
																			{
																			$fsize=$_FILES['fot']['size'];
																			if($fsize>0)
																			{
																			///nombre de la foto
																			$c_fot=clave(20);
																			$carpf="/var/www/html/claro/proyecto_usuarios/cfotos/";
																			if(move_uploaded_file($_FILES['fot']['tmp_name'],$carpf.$c_fot.".jpg"))
																			{
																			if(!dni_existe($d_dni)){
																			
																			///password
																			$c_pass=clave(10);
																			$qry="insert into usuarios(idpersona,nombre,apellidos,email,ffnn,ruc,direccion,telefono,celular,local,idarea,timeout,cod_usu_sn,activo,zk_privilege,tiene_jefe,foto,nivel,ffii,ffcc,experiencia,edobs,passwd,cargo) values('".$d_dni."','".$d_nom."','".$d_ape."','".$d_ema."','".$d_fna."','".$d_ruc."','".$d_dir."','".$d_tel."','".$d_cel."','".$d_loc."','".$d_jef."','".$d_tse."','".$d_cal."','".$d_act."','".$d_zkp."','".$d_tje."','".$c_fot."','".$d_niv."','".$d_fin."','".$d_fce."','".$d_exp."','".$d_not."','".$c_pass."','".$d_car."');";
																			qry($qry);
																			$idusr=mysql_insert_id();
																			if($idusr!="0" && $idusr!="")
																			{
																			///generando login
																			$d_log="";
																			$d_pre=explode(" ",$d_ape);
																			$d_pre=$d_pre[0];
																			$cda=19-strlen($idusr);
																			$d_log.=substr($d_nom,0,1).substr($d_pre,0,$cda).$idusr;
																			qry("update usuarios set login='".$d_log."' where iduser='".$idusr."';");
																			////mandar password en bandeja de supervisor
																			echo json_encode(array("1",$idusr,$d_log));
																			}else{echo json_encode(array("0","2")); unlink($carpf.$c_fot.".jpg");}
																			}else{echo json_encode(array("0","1")); unlink($carpf.$c_fot.".jpg");}
																			}else{echo json_encode(array("2","fot"));}
																			}else{echo json_encode(array("2","fot"));}
																			}else{echo json_encode(array("2","fot"));}
																		*/
																		
																	}else{echo json_encode(array("2","tje"));}
																}else{echo json_encode(array("2","zkp"));}
															}else{echo json_encode(array("2","act"));}
														}else{echo json_encode(array("2","tse"));}
														
														
														
														
														
													}else{echo json_encode(array("2","car"));}
												}else{echo json_encode(array("2","jef"));}
											}else{echo json_encode(array("2","loc"));}
										}else{echo json_encode(array("2","tel"));}
									}else{echo json_encode(array("2","cel"));}
								}else{echo json_encode(array("2","tel"));}
							}else{echo json_encode(array("2","dir"));}
						}else{echo json_encode(array("2","ruc"));}
					}else{echo json_encode(array("2","fna"));}
				}else{echo json_encode(array("2","ema"));}
			}else{echo json_encode(array("2","ape"));}
		}else{echo json_encode(array("2","nom"));}
		
		
	}
	
	if($d_a=="updo")
	{
		$d_niv=$_POST["niv"];
		$d_fin=$_POST["fin"];
		$d_fce=$_POST["fce"];
		$d_exp=$_POST["exp"];
		$d_not=$_POST["not"];
		$d_usr=$_POST["usr"];
		$d_motif=$_POST["motif"];
		
		if(!is_null($d_niv) && trim($d_niv)!="")	
		{
			$tmp=explode("-",$d_fin);
			if(!is_null($d_fin) && (($d_fin=="") || (trim($d_fin)!="" && is_numeric($tmp[0]) && is_numeric($tmp[1]) && is_numeric($tmp[2]) && $tmp[0]>0 && $tmp[1]>0 && $tmp[2]>0)))
			{
				$tmp=explode("-",$d_fce);
				if(!is_null($d_fce) && (($d_fce=="") || (trim($d_fce)!="" && is_numeric($tmp[0]) && is_numeric($tmp[1]) && is_numeric($tmp[2]) && $tmp[0]>0 && $tmp[1]>0 && $tmp[2]>0)))
				{
					$qry="update usuarios set nivel='".$d_niv."',ffii='".$d_fin."',ffcc='".$d_fce."',experiencia='".$d_exp."',edobs='".$d_not."',motivo_fired='$d_motif' where login='".$d_usr."';";
					qry($qry);
					$n_aff=mysql_affected_rows();
					
					/////Datos RRHH
					$lista=2;
					$res=mysql_query("select idcampo,nombre from list_lista_campos where idlista=$lista and idcampo!=1");
					$temp="";
					$temp1="";
					$temp2=array();
					while($r=mysql_fetch_row($res)){
						$temp.=",C".$r[0];
						$VAR="C".$r[0];
						$temp1.=",'".$_POST[$VAR]."'";
						$temp2[]=" C".$r[0]."='".$_POST[$VAR]."'";
					}
					$str_temp2=implode(",",$temp2);
					mysql_query("insert into lista_$lista(hora_crea,login_crea,C1$temp) values (now(),'$Xlogin','$d_usr'$temp1) on duplicate key update $str_temp2");
					$n_aff1=mysql_affected_rows();
					//echo $n_aff1;
					/////
					//if(mysql_affected_rows()==1)
					
					if($n_aff>0 or $n_aff1)
					{
						//mysql_query("select")
						/*$qry="update erp2.usuarios set nivel='".$d_niv."',ffii='".$d_fin."',experiencia='".$d_exp."',edobs='".$d_not."' where login='".$d_usr."';";
						qry($qry);*/
						echo json_encode(array("1",$d_usr));
					}else{echo json_encode(array("0","1"));}
				}else{echo json_encode(array("2","fce"));}
			}else{echo json_encode(array("2","fin"));}
		}else{echo json_encode(array("2","niv"));}
	}
	
	
	if($d_a=="showd")
	{
		$lista=2;
		$d_inf=$_POST["inf"];
		$res=qry("select a.nombre,a.apellidos,a.email,if(ffnn='0000-00-00','',ffnn),idpersona,direccion,if(telefono=0,'',telefono),if(celular=0,'',celular),local,idarea,timeout,cod_usu_sn,activo,zk_privilege,tiene_jefe,if(isnull(foto),'default',foto),UPPER(nivel),if(ffii='0000-00-00','',ffii),if(ffcc='0000-00-00','',ffcc),experiencia,edobs,cargo,b.CODDPTO,b.CODPROV,b.CODDIST,a.sexo,a.estado_civil,a.local_area,c.id,a.motivo_fired,a.campout from usuarios a left join fired_motivos c on c.idfired=a.motivo_fired left join ubigeo b on b.ubigeo=a.ubigeo where login='".$d_inf."';");
		if(mysql_num_rows($res)==1)
		{
			$res=mysql_fetch_row($res);
			$rst=qry("select a.idrol,b.nombre from userrol a left join rols b on a.idrol=b.idrol where a.login='".$d_inf."' order by b.nombre;");
			$tab="";
			if(mysql_num_rows($rst)>0)
			{
				while($temp=mysql_fetch_row($rst)){ $tab.="<tr class='lstr' inf='".$temp[0]."'><td>".$temp[0]."</td><td>".$temp[1]."</td><td class='text-center'><button class='btn btn-warning btn-xs rmvr' inf='".$temp[0]."'><span class='glyphicon glyphicon-remove'></span></button></td></tr>"; }
			}else{$tab="<tr><td colspan='3'>No Tiene Roles Asignados</td></tr>";}
			/////DAtos adicionales
			$rest=mysql_query("select idcampo,nombre from list_lista_campos where idlista=$lista and idcampo!=1");
			$Carr=array();
			while($r=mysql_fetch_row($rest)){
				$Carr[]=$r[0];
			}
			$Cs=implode(",C",$Carr);
			$res1=qry("select '','',C$Cs from lista_$lista where C1='$d_inf'");
			$Cdata=mysql_fetch_row($res1);
			
			
			echo json_encode(array($res,$tab,"C"=>$Cdata));
		}
		else{echo json_encode(array("0"));}
	}
	
	if($d_a=="dcar")
	{
		$d_inf=$_POST["inf"];
		$res=qry("select puesto from area_puesto where idarea='".$d_inf."' and activo=1;");
		if(mysql_num_rows($res)>0)
		{
			$opt="<option value='SIN ASIGNAR'>SIN ASIGNAR</option>";
			while($temp=mysql_fetch_row($res)){ $opt.="<option value='".$temp[0]."'>".$temp[0]."</option>"; }
			echo json_encode(array("1",$opt));
		}else{echo json_encode(array("0"));}
	}
	
	if($d_a=="rep_gen")
	{
		exec("rm -rf /var/www/html/redsanta/vox/rrhh.csv");
		$res=qry("select 'ID','LOGIN','APELLIDOS','NOMBRES','DNI','F. INGRESO','NIVEL','LOCAL','DIRECCION','TELEFONO','CELULAR','CORREO','DEPART.','PROVINCIA','DISTRITO','F. NACIMIENTO','DNI PATROC.','NOMBRE PATROC.','APELL. PATROC.','ACUMULADO','INGRESO','TIPO','ESTADO' UNION ALL
		select a.iduser,a.login,a.apellidos,a.nombre,a.idpersona,a.ffii,d.nombre,a.local,a.direccion,a.telefono,a.celular,a.email,ub.departamento,ub.provincia,ub.distrito,a.ffnn,f.login,f.nombre,f.apellidos,c.acumu,c.hora_in,a.nivel,a.activo from usuarios a left join user_nivel c on c.login=a.login left join niveles_red d on d.idnivel=c.nivel left join asociadas e on e.socio=a.login left join usuarios f on f.login=e.empresa left join ubigeo ub on ub.idpais=a.idpais and ub.id=a.ubigeo INTO OUTFILE '/var/www/html/redsanta/vox/rrhh.csv'
		FIELDS TERMINATED BY ','
		ENCLOSED BY '\"'
		LINES TERMINATED BY '\n'");
		echo "/vox/rrhh.csv";
	}
	
	if($d_a=="addr")
	{
		$d_inf=$_POST["inf"];
		$d_usr=$_POST["usr"];
		$res=qry("select idrol from userrol where login='".$d_usr."' and idrol='".$d_inf."';");
		if(mysql_num_rows($res)==0)
		{
			qry("insert into userrol(idrol,login,hora) values('".$d_inf."','".$d_usr."',now());");
			
			$rst=qry("select a.idrol,b.nombre from userrol a left join rols b on a.idrol=b.idrol where a.login='".$d_usr."' order by b.nombre;");
			$tab="";
			if(mysql_num_rows($rst)>0)
			{
				while($temp=mysql_fetch_row($rst)){$tab.="<tr class='lstr' inf='".$temp[0]."'><td>".$temp[0]."</td><td>".$temp[1]."</td><td class='text-center'><button class='btn btn-warning btn-xs rmvr' inf='".$temp[0]."'><span class='glyphicon glyphicon-remove'></span></button></td></tr>";}
			}else{$tab="<tr><td colspan='3'>No Tiene Roles Asignados</td></tr>";}
			
			update_permisos($d_usr);
			echo json_encode(array("1",$tab));
		}
		else{echo json_encode(array("0"));}
	}
	if($d_a=="close_s")
	{
		$d_usr=$_POST["usr"];
		mysql_query("update logs set horafin=now() where login='$d_usr' and (horafin='0000-00-00 00:00:00' or horafin is null) and hora>curdate()");
		echo 1;
	}
	
	if($d_a=="rmvr")
	{
		$d_inf=mysql_real_escape_string($_POST["inf"]);
		$d_usr=mysql_real_escape_string($_POST["usr"]);
		qry("delete from userrol where idrol='".$d_inf."' and login='".$d_usr."';");
		update_permisos($d_usr);
		$rst=qry("select a.idrol,b.nombre from userrol a left join rols b on a.idrol=b.idrol where a.login='".$d_usr."' order by b.nombre;");
		$tab="";
		if(mysql_num_rows($rst)>0)
		{
			while($temp=mysql_fetch_row($rst)){$tab.="<tr class='lstr' inf='".$temp[0]."'><td>".$temp[0]."</td><td>".$temp[1]."</td><td class='text-center'><button class='btn btn-warning btn-xs rmvr' inf='".$temp[0]."'><span class='glyphicon glyphicon-remove'></span></button></td></tr>";}
		}else{$tab="<tr><td colspan='3'>No Tiene Roles Asignados</td></tr>";}
		
		echo json_encode(array("1",$tab));
	}
	
	if($d_a=="rstp")
	{
		$d_inf=$_POST["inf"];
		//$c_pass=clave(6);
		//qry("update usuarios set passwd='".$c_pass."',pass_end=now() where login='".$d_inf."';");
		qry("update usuarios set passwd=login,pass_end=now() where login='".$d_inf."';");
		if(mysql_affected_rows()==1)
		{echo json_encode(array("1"));}
		else{echo json_encode(array("0"));}
	}
	
	function clave($longitud){ 
		$cadena="[^A-Z0-9]"; 
		return substr(eregi_replace($cadena, "", md5(rand())) . 
		eregi_replace($cadena, "", md5(rand())) . 
		eregi_replace($cadena, "", md5(rand())), 
		0, $longitud); 
	}
	function dni_existe($dni){
		$res=qry("select login from usuarios where idpersona='".$dni."';");
		if(mysql_num_rows($res)>0){return true;}
		else{return false;}
	}
	
	function get_foto($login){
		$res=qry("select foto from usuarios where login='".$login."';");
		if(mysql_num_rows($res)>0)
		{
			$res=mysql_fetch_row($res);
			return $res[0];
		}else{return "";}
	}
	
	function update_permisos($login)
	{
		tracechange("","permisos","login",$login,"idfunc","M","select distinct idfunc from rolfunc where idrol in (select idrol from userrol where login='".$login."')");
		qry("delete from permisos where login='".$login."';");
		qry("insert into permisos(idfunc,login) select distinct idfunc,'".$login."' login from rolfunc where idrol in (select idrol from userrol where login='".$login."');");
	}
?>