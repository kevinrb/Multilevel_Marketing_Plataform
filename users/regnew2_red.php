<?php
	require("../sec.php");
	
	$res=mysql_query("select idpais, pais from paises where estado=1");
	while($r=mysql_fetch_row($res)){
		$sel="";
		if($r[0]==$Xpais){
			$sel="selected";
		}
		$opc.="<option $sel value='$r[0]'>$r[1]</option>";
	}
	
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
	{if(!isset($adep[$temp[0]]))
		{$adep[$temp[0]]=array();}
		if(!in_array("<option value='$temp[1]'>$temp[1]</option>",$adep[$temp[0]]))
		{$adep[$temp[0]][]="<option value='$temp[1]'>$temp[1]</option>";}
		$adpr[$temp[0]][$temp[1]] .= "<option value='$temp[2]'>$temp[2]</option>";
		$year=$temp[3];
	}
	$adep=json_encode($adep);
	$adpr=json_encode($adpr);
	
	
	//ARRAY DE MESES
	date_default_timezone_set('America/Lima');
	$tabla_mes=array("1"=>"Ene","2"=>"Feb","3"=>"Mar","4"=>"Abr","5"=>"May","6"=>"Jun","7"=>"Jul","8"=>"Agos","9"=>"Sept","10"=>"Oct","11"=>"Nov", "12"=>"Dic");
	$arf=array();
	$sme="<option selected disabled value=''>--Elija--</option>";
	$ani1=$ani="";
	
	for($i=($year-17);$i>($year-80);$i--)
	{$ani .= "<option value='".$i."'>".$i."</option>";}
	
	/*for($i=$year;$i>($year-2);$i--)
		{$ani1 .= "<option value='".$i."'>".$i."</option>";}
	*/
	for($i=1;$i<=12;$i++)
	{
		$sme .= "<option value='".$i."'>".$tabla_mes[$i]."</option>";
		if($i==2)
		{$arf[$i]=array("0"=>28,"1"=>29);}
		else
		{$cnt=cal_days_in_month(CAL_GREGORIAN, $i, 2006); $arf[$i]=array("0"=>$cnt,"1"=>$cnt);}
	}
	$arf=json_encode($arf);
	
	
	$res=mysql_query("select b.login , concat(b.nombre,' ',coalesce(b.apellidos,'')) from usuarios b where b.nombre!='' and isnull(b.nombre)!=1 and experiencia='RED' order by nombre;");
	$emp="";
	while($temp=mysql_fetch_row($res))
	{$sel=""; if($temp[0]==$Xlogin){$sel="selected";} $emp .= "<option $sel value='".$temp[0]."' label='$temp[1]'>";}
	
	//$res=mysql_query("select local from locales where local!='' and isnull(local)!=1  order by local;");
	$loc="<option selected value='".$Xlocal."'>".$Xlocal."</option>";
	//while($temp=mysql_fetch_row($res))
	//{$sel=""; if($temp[0]=="Almacen"){$sel="selected";} $loc .= "<option $sel value='".$temp[0]."'>".$temp[0]."</option>";}
	
	$res=mysql_query("select idcuenta,nombre from cuentas order by nombre;");
	$cue="";
	while($temp=mysql_fetch_row($res))
	{$cue .= "<option value='".$temp[0]."'>".$temp[1]."</option>";}
	
	$res=qry("select a.iduser,UPPER(CONCAT(apellidos,' ',nombre,' (',a.login,')')) from usuarios a,comisiones_historial_puntos2 b where (a.iduser=b.idsocio_from and b.iduser='$Xiduser') or (a.iduser='$Xiduser' and b.iduser ='$Xiduser'  )  group by a.iduser order by apellidos;");
	$lstu="";
	while($temp=mysql_fetch_row($res)){
		$sel="";
		if($Xiduser==$temp[0]){
			$sel="selected";
		}
		$lstu.="<option $sel value='".$temp[0]."'>".$temp[1]."</option>"; 
	}
	if($lstu==""){
		$lstu.="<option selected value='$Xiduser'>$Xnombre</option>"; 
	}
	
	
?>
<!doctype html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css"> 
		<script src="../js/jquery.js"></script>
		<script src="../js/busq_select.js"></script>		
		<script>
			var adep=<?php echo $adep;?>;
			var adpr=<?php echo $adpr;?>;
			var arf =<?php echo $arf;?>;
			$(window).load(function(){
				$("#upline").busc({limit: 10});
				$(".depo,.efect").hide();
				$("#pais").change(function(){
					var pais=$(this).val();
					$.post("../res/users/regnewred.php",{a:"ubigeo",pais:pais},function(data){
						adep=data.adep;
						adpr=data.adpr;
						$("#depa").html(data.dep);
					},"json");
					
				});
				$("#pago").change(function(){
					var pagoval=$("#pago").val();
					if(pagoval=="EFECTIVO")
					{
						$(".depo").hide();
						$(".depo").each(function(){
							$(this).prop("required",false);
						});
						$(".efect").show();
						$(".efect").each(function(){
							
							$(this).prop("required",true);
						});	
					}
					else{
						if(pagoval=="DEPOSITO")
						{
							$(".efect").hide();
							$(".efect").each(function(){
								$(this).prop("required",false);
							});	
							$(".depo").show();
							$(".depo").each(function(){
								$(this).prop("required",true);
							});
						}
						else{
							$(".efect").hide();
							$(".depo").hide();
						}
					}
				});
				
				$("#depa").change(function(){
					$("#prov").html("");
					$("#dist").html("");
					dp=$("#depa").val();
					lp="<option selected disabled>--Elija--</option>";
					ap=adep[dp];
					for(i=0;i<ap.length;i++){lp=lp+ap[i];}
					$("#prov").html(lp);
				});
				
				$("#prov").change(function(){
					$("#dist").html("");
					dd=$("#depa").val();
					dp=$("#prov").val();
					lp="<option selected disabled>--Elija--</option>";
					$("#dist").html(lp+adpr[dd][dp]);
				});
				
				$(".ano").change(function(){$(this).parent().children(".dia").html("");
				$(this).parent().children(".mes").val("");});
				$(".mes").change(function(){
					mes=$(this).val();
					var obj=$(this).parent();
					anio=parseInt(obj.children(".ano").val());
					if ((anio % 4 == 0) && ((anio % 100 != 0) || (anio % 400 == 0))){b=1;} else{b=0;}
					hdi="";
					for(i=1;i<=arf[mes][b];i++){hdi=hdi+"<option value='"+i+"'>"+i+"</option>";}
					obj.children(".dia").html(hdi);
				});
				
				
				$("#save").click(function(){
					dvr=[];
					//alert("al");
					vnum = new RegExp("(\\D|\\s)");
					vtxt = new RegExp("^[a-zA-ZñÑ ]*$");
					vflo = new RegExp("^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$");
					vema = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
					
					pais=$("#pais").val(); dvr.push([1,$("#pais"),"num",0]);//
					dni=$("#dni").val(); dvr.push([1,$("#dni"),"num",0]);//
					nom=$("#nom").val(); dvr.push([1,$("#nom"),"let",0]);//
					ape=$("#ape").val(); dvr.push([1,$("#ape"),"let",0]);//
					ema=$("#ema").val(); dvr.push([0,$("#ema"),"ema",0]);
					dir=$("#dir").val(); dvr.push([1,$("#dir"),"",0]);//
					depa=$("#depa").val(); dvr.push([1,$("#depa"),"",0]);//
					prov=$("#prov").val(); dvr.push([1,$("#prov"),"",0]);//
					dist=$("#dist").val(); dvr.push([1,$("#dist"),"",0]);//
					anio=$("#anio").val(); dvr.push([1,$("#anio"),"num",4]);//
					mes =$("#mes").val(); dvr.push([1,$("#mes"),"num",0]);//
					dia =$("#dia").val(); dvr.push([1,$("#dia"),"num",0]);//
					tel=$("#tel").val(); //dvr.push([0,$("#tel"),"",0]);
					cel=$("#cel").val(); //dvr.push([0,$("#cel"),"num",9]);
					ting=$("#ting").val(); dvr.push([1,$("#ting"),"",0]);
					sex=$("#sex").val(); dvr.push([1,$("#sex"),"",0]);
					idup =$("#upline").val(); dvr.push([1,$("#upline"),"num",0]);//
					//emp=$("#emp").val(); dvr.push([1,$("#emp"),"",0]);//
					//loc=$("#loc").val(); dvr.push([1,$("#loc"),"",0]);//
					//cue=$("#cue").val(); dvr.push([0,$("#cue"),"",0]);//
					//vou=$("#vou").val(); dvr.push([0,$("#vou"),"num",0]);//
					//mon=$("#mon").val(); dvr.push([0,$("#mon"),"flo",0]);//
					//aniod=$("#aniod").val(); dvr.push([0,$("#aniod"),"num",4]);//
					//mesd =$("#mesd").val(); dvr.push([0,$("#mesd"),"num",0]);//
					//diad =$("#diad").val(); dvr.push([0,$("#diad"),"num",0]);//
					//pago =$("#pago").val(); dvr.push([0,$("#pago"),"let",0]);//
					
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
									if(fl=="num"){if(!vnum.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
									if(fl=="let"){if(vtxt.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
									if(fl=="ema"){if(String(ob.val()).search(vema)!=-1 && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
									if(fl=="flo"){if(vflo.test(ob.val()) && (cn==0 || (cn!=0 && (ob.val().length==cn)))){}else{ob.parent().addClass("has-error"); ob.focus(); return false;}}
								}
							}
							else
							{
							ob.parent().addClass("has-error"); ob.focus(); return false;}
						}
					}
					var r=confirm('Se va ingresar al SOCIO:'+dni+' , EL DNI ES CORRECTO?');
					if(r==true){
						$.post("../res/users/regnewred.php",{a:"new",pais:pais,dni:dni,nom:nom,ape:ape,ema:ema,dir:dir,depa:depa,prov:prov,dist:dist,anio:anio,mes:mes,dia:dia,tel:tel,cel:cel,ting:ting,sex:sex,idup:idup},function(data){
							rst=data[0];
							msj=data[1];
							if(rst=="1" || rst=="2" || rst=="5")
							{alert(msj); if(rst=="5"){location.href="list_socios_red.php";}}
							if(rst=="0")
							{$("#"+msj).parent().addClass("has-error"); $("#"+msj).focus();}
						},"json");
					}
				});
				
				$("#pais").change();
				
			});
		</script>
	</head>
	<body>
		<div class="container">
			<div class="col-sm-6">
				<h3>Datos Personales</h3>
				<hr size="2"/>
				<div class="form-group">
					<select class="form-control" placeholder="Pais" name="pais" id="pais">
						<?php echo $opc;?>	
					</select>
				</div>
				<div class="form-group">
					<label>DNI*</label>
					<input id="dni" placeholder="DNI" class="form-control">
				</div>
				<div class="form-group">
					<label>Nombre*</label>
					<input id="nom" placeholder="Nombre" class="form-control">
				</div>
				<div class="form-group">
					<label>Apellido*</label>
					<input id="ape" placeholder="Apellido" class="form-control">
				</div>
				<div class="form-group">
					<label>Email*</label>
					<input id="ema" placeholder="Email" class="form-control">
				</div>
				<div class="form-group">
					<label>Direccion*</label>
					<input id="dir" placeholder="Direccion" class="form-control">
				</div>
				
				<div class="form-group col-sm-4">
					<label>Departamento*</label>
					<select id="depa" class="form-control"><?php echo $dep;?></select>
				</div>
				<div class="form-group  col-sm-4">
					<label>Provincia*</label>
					<select id="prov" class="form-control"></select>
				</div>
				<div class="form-group  col-sm-4">
					<label>Distrito*</label>
					<select id="dist" class="form-control"></select>
				</div>
				<div class="form-group col-md-8">
					<label>Fecha de Nacimiento</label>
					<div class="form-inline"> 
						<select id="anio" class="form-control ano"><?php echo $ani;?></select>
						<select id="mes" class="form-control mes"><?php echo $sme;?></select>
						<select id="dia" class="form-control dia"></select>
					</div>
				</div>
				<div class="form-group col-md-4">
					<label>SEXO*</label>
					<select required class="form-control" name="sex" id="sex"><option>---</option><option value="M">MASCULINO</option><option value="F">FEMENINO</option></select>
				</div>
				<div class="form-group">
					<label>Telefono</label>
					<input id="tel" placeholder="Telefono" class="form-control">
				</div>
				<div class="form-group">
					<label>Celular*</label>
					<input id="cel" placeholder="Celular" class="form-control">
				</div>
			</div>
			
			<div class="col-sm-6">
				<h3>Santa Natura</h3>
				<hr size="2"/>
				<div class="form-group">
					<label>Patrocinador</label>
					
					<input  id="emp" class="form-control" id="datalisttestinput" list="stuff" value="<?php echo $Xlogin;?>" disabled></input>	
				</div>
				<div class="form-group">
					<label>UPLINE</label>
					
					<select id="upline" name="upline" class="form-control">
						<?php echo $lstu;?>
					</select>
				</div>	
				<div class="form-group">
					<label>Local</label>
					<select id="loc" disabled class="form-control" disabled><?php echo $loc;?></select>
				</div>
				<label>Tipo de Ingreso</label>
				<select id="ting" class="form-control" ><option value="RED">SOCIOS</option><option value="VENTAS">PROMOTORES</option></select>
			</div>
			<!--
				<h3>Tipo de Pago</h3>
				<hr size="2"/>
				<div class="form-group">
				<label>Tipo de Pago*</label>
				<select id="pago" class="form-control"><option disabled selected >--Seleccione--</option><option value="NN">ANTIGUO</option><option value="DEPOSITO">DEPOSITO</option><option value="EFECTIVO">EFECTIVO</option><option value="POSVISA">VISA</option><option value="POSMC">MASTERCARD</option></select>
				</div>
				<div class="form-group depo">
				<label>Cuenta*</label>
				<select id="cue" class="form-control"><option disabled selected value="">--Seleccione--</option><?php echo $cue;?></select>
				</div>
				<div class="form-group depo">
				<label>Nro. Voucher*</label>
				<input id="vou" placeholder="Nro. Voucher" class="form-control">
				</div>
				<div class="form-group depo efect">
				<label>Monto*</label>
				<input id="mon" placeholder="Monto" class="form-control">
				</div>
				<div class="form-group depo">
				<label>Fecha de Deposito*</label>
				<div class="form-inline"> 
				<select id="aniod" class="form-control ano"><?php echo $ani1;?></select>
				<select id="mesd" class="form-control mes"><?php echo $sme;?></select>
				<select id="diad" class="form-control dia"></select>
				</div>
			</div>-->
		</div>
	</div>
	<div class="text-center"><button id="save" class="btn btn-success">GUARDAR</button><a href="../users/list_socios_red.php" id="canc" class="btn btn-primary">CANCELAR</a></div>
</body>
</html>
