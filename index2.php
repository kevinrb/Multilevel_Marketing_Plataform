<?php
	include "sec.php";
	
	$qry = "select m.nombre,m.url,m.grupo from permisos p,menu m where
	p.idfunc=m.idfunc and p.login='$Xlogin' order by m.orden,m.idmenu";
	$resd2 = mysql_query($qry) or die("ERROR: ".mysql_error());
	$gp = array();
	while($r = mysql_fetch_row($resd2))  {
		if(!isset($gp["$r[2]"])) $gp["$r[2]"] = "";
		$gp["$r[2]"] .= "<a href='$r[1]' target='pagina'>".$r[0]."</a>";
	}
	//////////////////////////////////
	
	$qry="select login,nombre,local,perfil from usuarios where (activo=1 ) or login='alanm'
	order by nombre";
	$res = mysql_query($qry) or die("Error en consulta users".mysql_error());
	$destinos="";
	while($row = mysql_fetch_array($res, MYSQL_NUM))
	{
		$destinos .= "<option value='$row[0]'>$row[1]</otion>";
		if($row[0] == $Xlogin)
		$Xperfil = $row[3];
	}
	$idsess=$_COOKIE["idsess"];
	$ip=$_SERVER['REMOTE_ADDR'];
	$local_nomb = $Xlocal;
	
	
	$qqr="SELECT DATE_FORMAT(horai, '%b %d, %Y %H:%i') from logs where login='$Xlogin' and date(hora)=date(now()) and date(horai)!='0000-00-00' order by horai limit 1";
	$timelog=mysql_fetch_row(mysql_query($qqr));
	$timelog=$timelog[0];
	
	
	
	/////////////////Tablero//////////////
	
	////////////////////////////
?>
<!DOCTYPE html>
<html lang="es">
	
	<head>
		
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" href="img/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
		<title>Red Santa Natura</title>
		
		<!-- Core CSS - Include with every page -->
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="font-awesome/css/font-awesome.css" rel="stylesheet">
		
		<!-- Page-Level Plugin CSS - Dashboard -->
		<link href="css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">
		<link href="css/plugins/timeline/timeline.css" rel="stylesheet">
		
		<!-- SB Admin CSS - Include with every page -->
		<link href="css/sb-admin.css" rel="stylesheet">
		<style>
			#showmenu{font-weight: bolder; display: none; position: absolute; top: 2px; left: 17px; z-index: 1000; opacity: 0.2;}
			#showmenu:hover{opacity: 0.7;}
			#hidemenu{font-weight: bolder; display: inline; margin-top: -6px;} 
		</style>
	</head>
	
	<body>
		
		<div id="wrapper">
			<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0; ">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<input height='57px' width='302px' style='width:377px' type="image" class="col-md-12" name="imageField" id="imageField" src="img/logo2.png" alt="logo"/>
				</div>
				<!-- /.navbar-header -->
				<div class="col-sm-4 col-md-3 ">
					
				</div>
				
				<ul class="nav navbar-nav navbar-right" id="last">
					<?php if($XLanexo != 0 ){ ?>
						<li class="dropdown" id='menu_est'>
							<a style="padding:5px 10px" class="dropdown-toggle text-center" data-toggle="dropdown" href="menuest" ><?php echo $info_auth_anexo;?>
								<span id="amenu" ><?php echo $estado;?></span><b class="caret"></b>
							</a>
							<ul class="dropdown-menu dropdown-alerts" id='body_menu_estado'><?php echo $state;?></ul>
						</li>
						<?php } else {?>
						<li id='cod_auth' ><a style="padding:5px 10px" href="#"  class="text-center"><?php echo $info_auth_anexo;?></a></li>
					<?php }?>
				</ul>
				<ul class="nav navbar-top-links navbar-right" style="float:right;">
					<!--<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-messages">
                        <li>
						<a href="#">
						<div>
						<strong>John Smith</strong>
						<span class="pull-right text-muted">
						<em>Yesterday</em>
						</span>
						</div>
						<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<strong>John Smith</strong>
						<span class="pull-right text-muted">
						<em>Yesterday</em>
						</span>
						</div>
						<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<strong>John Smith</strong>
						<span class="pull-right text-muted">
						<em>Yesterday</em>
						</span>
						</div>
						<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a class="text-center" href="#">
						<strong>Read All Messages</strong>
						<i class="fa fa-angle-right"></i>
						</a>
                        </li>
						</ul>
					<!-- /.dropdown-messages -->
					<!-- </li> -->
					<!-- /.dropdown --><!--
						<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-tasks">
                        <li>
						<a href="#">
						<div>
						<p>
						<strong>Task 1</strong>
						<span class="pull-right text-muted">40% Complete</span>
						</p>
						<div class="progress progress-striped active">
						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
						<span class="sr-only">40% Complete (success)</span>
						</div>
						</div>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<p>
						<strong>Task 2</strong>
						<span class="pull-right text-muted">20% Complete</span>
						</p>
						<div class="progress progress-striped active">
						<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
						<span class="sr-only">20% Complete</span>
						</div>
						</div>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<p>
						<strong>Task 3</strong>
						<span class="pull-right text-muted">60% Complete</span>
						</p>
						<div class="progress progress-striped active">
						<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
						<span class="sr-only">60% Complete (warning)</span>
						</div>
						</div>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<p>
						<strong>Task 4</strong>
						<span class="pull-right text-muted">80% Complete</span>
						</p>
						<div class="progress progress-striped active">
						<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
						<span class="sr-only">80% Complete (danger)</span>
						</div>
						</div>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a class="text-center" href="#">
						<strong>See All Tasks</strong>
						<i class="fa fa-angle-right"></i>
						</a>
                        </li>
						</ul>
					<!-- /.dropdown-tasks -->
					<!--  </li>-->
					<!--  </li>-->
					<!-- /.dropdown -->
					<!--   <li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-alerts">
                        <li>
						<a href="#">
						<div>
						<i class="fa fa-comment fa-fw"></i> New Comment
						<span class="pull-right text-muted small">4 minutes ago</span>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<i class="fa fa-twitter fa-fw"></i> 3 New Followers
						<span class="pull-right text-muted small">12 minutes ago</span>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<i class="fa fa-envelope fa-fw"></i> Message Sent
						<span class="pull-right text-muted small">4 minutes ago</span>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<i class="fa fa-tasks fa-fw"></i> New Task
						<span class="pull-right text-muted small">4 minutes ago</span>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a href="#">
						<div>
						<i class="fa fa-upload fa-fw"></i> Server Rebooted
						<span class="pull-right text-muted small">4 minutes ago</span>
						</div>
						</a>
                        </li>
                        <li class="divider"></li>
                        <li>
						<a class="text-center" href="#">
						<strong>See All Alerts</strong>
						<i class="fa fa-angle-right"></i>
						</a>
                        </li>
						</ul>
						/.dropdown-alerts 
					</li>-->
					<!-- /.dropdown -->
					<li class="dropdown" style="margin-right:0px">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#Estado" title="Estado">
							<span class="glyphicon glyphicon-home" aria-hidden="true"></span> 
							<?php 
								if($Xmonto>0){
									$nomb[0]="ACTIVO";
									}else{
									$nomb[0]="INACTIVO";
								}
								
							echo $nomb[0];?>
							
						</a>
					</li>
					<li class="dropdown" style="margin-right:0px">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#Volumen" title="Volumen">
							<span class="glyphicon glyphicon-signal" aria-hidden="true"></span>
							<?php 
								
							echo "<STRONG>VC:</strong>".$Xvolumen;?>
							
						</a>
						<!-- /.dropdown-user -->
					</li>				
					<li class="dropdown" style="margin-right:0px">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Rango del Ultimo Cierre">
							<span class="glyphicon glyphicon-tag" aria-hidden="true"></span>
							<?php 
								$nomb=mysql_fetch_row(qry("select nombre,if(SUBSTRING('$Xfchin',0,7)=SUBSTRING(curdate(),0,7),1,0) from niveles_red where idnivel='$Xnivel'"));
								if($nomb[0]!=""){}else{
									if($Xfchin!=""){
										$nomb=mysql_fetch_row(qry("select if(SUBSTRING('$Xfchin',0,7)=SUBSTRING(curdate(),0,7),1,0)"));
										if($nomb[0]==1){
											$nomb[0]="SOCIO NUEVO";
											}else{
											$nomb[0]="SIN NIVEL";
										}								
										}else{
										$nomb[0]="SIN INSCRIP.";
									}
									
									
								}
								
							echo $nomb[0];?>
							
						</a>
						<ul class="dropdown-menu dropdown-user">
							<li><a href="#"><span class="glyphicon glyphicon-leaf" aria-hidden="true"></span>&nbsp;Autoconsumo: <strong><?php 
							echo $Xmonto;?></strong></a>
							</li>
							
							<li><a href="#" ><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span>&nbsp;1ra Linea: <strong><?php 
							echo $Xnivel_1;?></strong></a>
							</li>
							<li><a href="#" ><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span>&nbsp;Platinos: <strong><?php 
							echo $Xplati;?></strong></a>
							</li>
							<li><a href="#" ><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span>&nbsp;Diamantes: <strong><?php 
							echo $Xdiama;?></strong></a>
							</li>						
							<li><a href="#" ><span class="glyphicon glyphicon-fire" aria-hidden="true"></span>&nbsp;Acumulado: <strong><?php 
							echo $Xacum;?></strong></a>
							</li>
							<li class="divider"></li>
							<li><a href="#" ><span class="glyphicon glyphicon-record" aria-hidden="true"></span>&nbsp;Fecha Pago: <strong><?php 
								$temp=mysql_fetch_row(qry("select (date('$Xfchcorte') + INTERVAL (TIMESTAMPDIFF(MONTH,date('$Xfchcorte'),curdate())+1) month)"));
							echo $temp[0];?></strong></a>
							</li>
							<li><a href="#" > <strong><?php 
								if($Xdeuda==0){
									//echo "AL DIA :D!";
									}elseif($Xdeuda>0){
									//echo "Debes $Xdeuda Meses";
								}
							?></strong></a>
							</li>						
						</ul>
						<!-- /.dropdown-user -->
					</li>
					
					<li class="dropdown" style="margin-right:0px">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-user fa-fw"></i>
							<?php 
								$nomb=explode(" ",$Xnombre);
								$nombre=$nomb[0]." ".$nomb[1];
								
							echo $nomb[0];?>
							<i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-user">
							<li><a href="users/reguser.php" target="pagina" ><i class="fa fa-user fa-fw"></i>Datos Personales</a>
							</li>
							
							<li class="divider"></li>
							<li><a href="nuevopass.php"  target="pagina" ><i class="fa fa-gear fa-fw"></i> Cambiar Contraseña</a>
							</li>
							<li><a href="logoff.php"><i class="fa fa-sign-out fa-fw"></i> Cerrar Sesión</a>
							</li>
						</ul>
						<!-- /.dropdown-user -->
					</li>				
					
					<!-- /.dropdown -->
				</ul>
				<!-- /.navbar-top-links -->
				
			</nav>
			<!-- /.navbar-static-top -->
			
			<nav class="navbar-default navbar-static-side" id="menu" role="navigation">
				<div class="sidebar-collapse">
					<ul class="nav" id="side-menu">
						<!-- <li class="sidebar-search">
							<div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
							<button class="btn btn-default" type="button">
							<i class="fa fa-search"></i>
							</button>
                            </span>
							</div>
							
							</li>
						<!-- /input-group -->
						
					    <?php
							
							
							
							foreach($gp as $k => $v) {
								//<button type='button' class='btn btn-info btn-sm pull-right toogle-menu' id='hidemenu'>&nbsp;&laquo;&nbsp;</button>
								echo "<li><a href='#'>$k<span class='fa arrow'></span></a><ul class='nav nav-second-level'>
								<li>$v</li>
								</ul></li>";
							}
						?>
						
					</ul>
					<!-- /#side-menu -->
				</div>
				<!-- /.sidebar-collapse -->
			</nav>
			<!-- /.navbar-static-side -->
			
			<div id="page-wrapper" style="padding:0px !important;min-height:300px !important ">
				<iframe src ="<?php if(substr($Xperfil, 0, 7) == 'parcial') echo 'usuario_local_2.php'?>" id="pagina" name="pagina" style="width:100%;" frameBorder="0">
					<p>Your browser does not support iframes.</p>
				</iframe>
			</div>
			<button type="button" id="showmenu" title="Mostrar Menu" class="btn btn-info toogle-menu">&nbsp;&raquo;&nbsp;</button>
			<!-- /#page-wrapper -->
			
		</div>
		<!-- /#wrapper -->
		
		<!-- Core Scripts - Include with every page -->
		<script src="js/jquery-1.10.2.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
		
		<!-- Page-Level Plugin Scripts - Dashboard -->
		<script src="js/plugins/morris/raphael-2.1.0.min.js"></script>
		<script src="js/plugins/morris/morris.js"></script>
		
		<!-- SB Admin Scripts - Include with every page -->
		<script src="js/sb-admin.js"></script>
		<script>
			
			function xhr(){
				if(window.XMLHttpRequest){
					return new XMLHttpRequest();
				}
				else if(window.ActiveXObject){
					return new ActiveXObject("Microsoft.XMLHTTP");
				}
			}
			function user_state(myAudioin,myAudiout){
				var peticion = xhr();
				peticion.onreadystatechange = function () {
					if(peticion.readyState == 4){
						var cron = eval('(' + peticion.responseText + ')');
						$('#fcall').html(cron.fcall);
						$('#cout').html(cron.cout);
						$('#tmo').html(cron.tmo);
						$('#agenda').html(cron.agenda);
						$('#lost').html(cron.lost);
						$('#cin').html(cron.cin);
						$('#cnin').html(cron.cnin);
						$('#tmi').html(cron.tmi);
						$('#wait').html(cron.wait);
						$('#resttime').html(cron.resttime);
						///////////////////
						
						
						if(cron.statecall!=""){
							$("#info_llamada").html(cron.statecall);
							$("#txmenu").html(cron.txmenu);
							el=document.getElementById("iel").getAttribute("value"); 
							if(el=='1')
							{
								$(".in").show();
								$(".out").hide();
								$("#incall").removeClass("disabled btn-default");
								$("#incall").addClass("contestar_llamada btn-danger");
								$("#txcall").removeClass("disabled");
								$("#incall").children(".glyphicon-earphone").addClass('glyphicon-phone-alt');
								$("#incall").children(".glyphicon-earphone").removeClass('glyphicon-earphone');
								//$("#txcall").addClass(" btn-success");
							myAudioin.play();}
							if(el=='2')
							{
								//	$("#txcall").removeClass("disabled");
								myAudioin.pause();
								$("#pagina").attr('src', 'interfaznew3.php');
								//document.getElementById('pagina').src = 'inlist.php';
							}
							if(el=='3')
							{
								$("#incall").removeClass("btn-danger");
								$("#incall").addClass("disabled btn-default");
								$("#txcall").removeClass("btn-success");
								//$("#txcall").addClass("disabled btn-default");
								$("#txcall").addClass("btn-default");
								$("#incall").children(".glyphicon-phone-alt").addClass('glyphicon-earphone');
								$("#incall").children(".glyphicon-phone-alt").removeClass('glyphicon-phone-alt');
							myAudioin.pause();}
						}
						
						//////////////////////
						
						$("#count").html(cron.cant_temp);
						if(parseInt(cron.cant_temp)==1)
						{
							myAudiout.play();			
						}
						else
						{
						myAudiout.pause();}
						
						$("#amenu").text(cron.estado);			
						setTimeout(user_state(myAudioin,myAudiout),1);
					}
				}
				peticion.open("POST","ajax/usersstate2.php",true);
				peticion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				peticion.send("action=<?php echo $Xlogin;?>");
			}
			
			function llamarout(but,data){
				var peticion = xhr();
				peticion.onreadystatechange = function () {
					if(peticion.readyState == 4){
						data=parseInt(data);
						var cl=peticion.responseText;
						cl=parseInt(cl);
						//alert(cl);
						if(cl==0)
						{
							but.removeClass('btn-danger');
							but.addClass('btn-success');
							but.removeClass('cancel');
							but.addClass('outcall');
							but.children(".glyphicon-phone-alt").addClass('glyphicon-earphone');
							but.children(".glyphicon-phone-alt").removeClass('glyphicon-phone-alt');
						}
						else
						{
							setTimeout(llamarout(but,data),1);
						}
					}
				}
				peticion.open("POST","ajax/call.php",true);
				peticion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				peticion.send("idcall="+data+"&action=estadoout");
			}
			
			
			function viewport() {
				var e = window, a = 'inner';
				if (!('innerWidth' in window )) {
					a = 'client';
					e = document.documentElement || document.body;
				}
				return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
			} 
			
			//var myAudioin = new Audio('ring.og');
			//var myAudiout = new Audio('ring.ogg');
			$(function(){
				
				var vpw = viewport().width;
				$(".toogle-menu").click(function(event){
					event.preventDefault();
					event.stopPropagation();
					$(".in").attr('class', 'panel-collapse collapse');
					$(this).hide(); var that = $(this);
					
					if(vpw>=768){
						
						$("body").css('overflow', 'hidden');
						var porcen = 20000/$("#main").width();
						var redon = Math.round(porcen);
						if(redon-porcen<0){redon++;}
						var tam = 100-redon+'%'; 
						
						if($(this).is('#hidemenu')){
							$("#page-wrapper").width('99%').css('float', 'right');
							$("#page-wrapper").animate({width:tam},{duration: 250, queue: false,
								complete: function(){$("#contenido").attr('class', 'col-xs-12 col-sm-10 staticontend').css('width', '');}
							});
						}
						
						if($(this).is('#showmenu')){
							$("#page-wrapper").width(tam).css('float', 'left');
							$("#page-wrapper").animate({width:'99%'},{duration: 250, queue: false,
								complete: function(){$("#contenido").attr('class', 'col-xs-12 col-sm-12').css('width', '');}
							});
						}
						
					}
					
					$("#menu").animate({width:'toggle'},
					{duration: 250,
						queue: false,
						complete: function(){$("body").css('overflow', ''); $(".toogle-menu").not(that).show();}
					});
					
				});	
				
				//////////////////////////////
				$(".out").hide();
				$("body").on("click","#newcall",function(){
					$(".out").show();
					$(".in").hide();
				});
				
				$('.out').keypress(function(e){
					if(e.which == 13){
						$('.outcall').click();
					}
				});
				
				
				///////OUT//////
				var idcall="";
				$("body").on("click",".outcall", function(){
					var tel=$("#numberout").val();
					var but=$(this);
					but.removeClass('btn-success');
					but.children(".glyphicon-earphone").addClass('glyphicon-phone-alt');
					but.children(".glyphicon-earphone").removeClass('glyphicon-earphone');
					$.post("ajax/call.php", {recallout:tel}, function(data){
						data=parseInt(data);
						idcall=data;
						but.addClass('btn-danger');
						but.removeClass('outcall');
						llamarout(but,data);
						but.addClass('cancel');	
					});
				});
				
				$("body").on("click",".cancel", function(){
					var but=$(this);
					but.removeClass('btn-danger');
					but.addClass('btn-success');
					but.removeClass('cancel');
					but.addClass('outcall');
					but.children(".glyphicon-phone-alt").addClass('glyphicon-earphone');
					but.children(".glyphicon-phone-alt").removeClass('glyphicon-phone-alt');
					$.post("ajax/call.php", {action:"colgar_out",idcall:idcall}, function(data){
					});
				});
				//////////
				
				/////IN////////
				
				$("body").delegate(".contestar_llamada","click",function(){
					//window.frames["pagina"].location="index.php";
					$.post("polling2.php",{action:'colgar_in'},function(){
						$("#incall").addClass("disabled btn-default");
						$("#incall").removeClass("contestar_llamada btn-success");	
					});
				});	
				
				/////
				
				$("body").delegate(".transfer","click",function(){
					$.post("polling2.php",{action:'transfer_llamada',anex:$(this).data('value')},function(){
					});
				});	
				
				if('<?php echo $XLanexo;?>' == 0 )
				{
					$("#numberout").prop('disabled', true);
					/*$.post("polling.php",{action:"anexo",idsess:'<?php echo $idsess;?>',login:'<?php echo $Xlogin;?>'},function(data){
						if (data=='1')
						location.reload();
						else
						$.post("polling.php",{action:"anexo",idsess:'<?php echo $idsess;?>',login:'<?php echo $Xlogin;?>'},function(data){ if (data=='1') location.reload(); });
					});*/
					
				}
				else{
					$("#numberout").prop('disabled', false);
					$.post("polling2.php",{action:'info_llamada_data'},function(data){	
						$("#info_llamada").html(data.statecall);
						$("#txmenu").html(data.txmenu);
						el=document.getElementById("iel").getAttribute("value");
						if(el=='1')
						{
							$(".in").show();
							$(".out").hide();
							$("#incall").removeClass("disabled btn-default");
							$("#incall").addClass("contestar_llamada btn-danger");
							$("#txcall").removeClass("disabled");
							$("#incall").children(".glyphicon-earphone").addClass('glyphicon-phone-alt');
							$("#incall").children(".glyphicon-earphone").removeClass('glyphicon-earphone');							
							myAudioin.play();
						}
						if(el=='2')
						{
							$("#incall").removeClass("disabled btn-default");
							$("#incall").addClass("contestar_llamada btn-danger");
							$("#txcall").removeClass("disabled");
							$("#incall").children(".glyphicon-earphone").addClass('glyphicon-phone-alt');
							$("#incall").children(".glyphicon-earphone").removeClass('glyphicon-earphone');		
							//	$("#txcall").removeClass("disabled");
						myAudioin.pause();}
						if(el=='3')
						{
							$("#incall").removeClass("btn-danger");
							$("#incall").addClass("disabled btn-default");
							$("#txcall").removeClass("btn-success");
							//$("#txcall").addClass("disabled btn-default");
							$("#txcall").addClass("btn-default");
							$("#incall").children(".glyphicon-phone-alt").addClass('glyphicon-earphone');
							$("#incall").children(".glyphicon-phone-alt").removeClass('glyphicon-phone-alt');
						myAudioin.pause();}
						user_state(myAudioin,myAudiout);
					},"json");
					
				}
				$("#menu_est").click(function(){
					$(".element_m_e").css({display:"block"}); 
					$("#body_menu_estado").toggle();
					var data=$("#amenu").text();
					$(".element_m_e").each(function(index){
						if($(this).text()==data)
						{
							$(this).css({display:"none"});
						} 
					});
					$(window).resize();			
				});
				$(".element_m_e").click(function(){		
					var estado=$(this).attr("value");
					$("#amenu").text($(this).text());
					$.post("logs.php",{estado:estado},function(){});
				});
				/*---------------------------------------------------*/
				$("#oculta").click(function() {
					if($("#menu").css("display") != "none") {
						$("#menu").css("display","none");
						$(this).attr("src","hidemenu2.png");
					}
					else {
						$("#menu").css("display","");
						$(this).attr("src","hidemenu1.png");
					}
				});
				
				$(window).resize(function(){ 
					var mdh = $(window).height();
					var mdh1 = $(window).width();
					var hint=$(".navbar-static-side").height();
					var hhead= $(".navbar-static-top").height();
					
					if(mdh>(hint+hhead))
					$("#pagina").height(mdh-56);
					else
					$("#pagina").height((hint+hhead)-56);
					// version min $("#middle").width(mdh1-(190+last+5))
					
				});
				$("#side-menu li a").click(function(){
					//alert("horal");
					$(window).resize();
					
				});
				$(window).resize();
				
			});
		</script>
		<script type="text/javascript">
			var current="Winter is here!" 
			var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
			function countdown(){
				<?php if($timelog!=""){?>
					var tz=-5; 
					var today=new Date();
					var todayy=today.getYear();
					if (todayy< 1000) {todayy+=1900;}
					var todaym=today.getMonth();
					var todayd=today.getDate();
					var todayh=today.getHours();
					var todaymin=today.getMinutes();
					var todaysec=today.getSeconds();
					var todaystring1=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec;
					var todaystring=Date.parse(todaystring1)+(tz*1000*60*60);
					var futurestring1="<?php echo $timelog;?>";
					var futurestring=Date.parse(futurestring1)-(today.getTimezoneOffset()*(1000*60));
					var dd=todaystring-futurestring;
					var dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);
					var dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);
					var dseg=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/(1000)*1);
					if(dhour<0&&dmin<0){
					}
					else {
						var hour=dhour+":"+dmin+":"+dseg;
						document.getElementById('hour').innerHTML=hour;
						setTimeout("countdown()",1000);
					}
				<?php }?>
			}
		</script>
		
		<!-- Page-Level Demo Scripts - Dashboard - Use for reference 
		<script src="js/demo/dashboard-demo.js"></script>-->
		
	</body>
	
</html>
