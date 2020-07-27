<?php
	require_once("sec.php");
	date_default_timezone_set('America/Lima');
	/////////////////////////////////////////////////PRE-INFO///////////////////////////////////////////
	{$tabla_mes=array("1"=>"ENERO","2"=>"FEBRERO","3"=>"MARZO","4"=>"ABRIL","5"=>"MAYO","6"=>"JUNIO","7"=>"JULIO","8"=>"AGOSTO","9"=>"SEPTIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE", "12"=>"DICIEMBRE");
		$today = date("Y-m-d H:i:s");
		$tab_niv=array("pri"=>"PRIMARIA","sec"=>"SECUNDARIA","sup"=>"SUPERIOR");
		$qql=qry("select local,nombre FROM locales order by nombre");
		while($res=mysql_fetch_row($qql)){
			$ll.='<option value="'.$res[0].'">'.$res[1].'</option>';
		}
		$seloc='<select class="form-control" name="locales" id="locales"><option value="" selected disabled>--ELEGIR--</option>'.$ll.'</select>';
		
		$qry="SELECT login from usuarios WHERE login!='' ";
		$pqry=qry($qry);
		$op="";
		while($tmp=mysql_fetch_assoc($pqry)){
			$op.='<option value="'.$tmp["login"].'">'.$tmp["login"].'</option>';
		}
		
		
		$oe="";
		$opp="";
		$param[0]="skill";
		$param[1]="rol";
		$param[2]="grupo";
		$param[3]="area";
		
		for($i=0;$i<4;$i++){
			$qx="SELECT id$param[$i],nombre FROM $param[$i]s WHERE estado='1'";
			$pqx=qry($qx);
			$ox="";
			while($ff=mysql_fetch_row($pqx)){
				$ox.='<option value="'.$ff[1].'" atid="'.$ff[0].'">'.$ff[1].'</option>';
			}
			$sel[$i]='<select class="form-control input-sm" name="'.$param[$i].'" id="sel'.$param[$i].'"><option value="" selected disabled>--Elegir--</option>'.$ox.'</select>';
		}
		
		
		$qe="SELECT cola,nombre FROM colas ";
		$pqe=qry($qe);
		while($ff=mysql_fetch_row($pqe)){
			$oe.='<option value="'.$ff[1].'" atid="'.$ff[0].'">'.$ff[1].'</option>';
		}
		$qp="SELECT perfil,login,nombre FROM perfil";
		$pqp=qry($qp);
		while($ff=mysql_fetch_row($pqp)){
			$opp.='<option value="'.$ff[1].'" peid="'.$ff[0].'">'.$ff[1].'</option>';
		}
		
		$sele='<select class="form-control input-sm" name="cola" id="selcola"><option value="" selected disabled>--Elegir--</option>'.$oe.'</select>';
		$selp='<select class="form-control input-sm" name="perfil" id="perfil"><option value="" selected disabled>--Elegir--</option>'.$opp.'</select>';
	}
	
	for($i=0;$i<4;$i++){
		$y=$i+2;
		$sql="SELECT id$param[$i],nombre,estado FROM $param[$i]s";
		$psql=qry($sql);
		$cc=mysql_num_rows($psql);
		/*
			if($i==1){
			$param["$param[$i]"]='<iframe src="http://contacto123.sytes.net/vox/roles.php"><p>Your browser does not support iframes.</p></iframe>';
			return false;
		}*/
		
		if($cc!=0){
			$res="";
			while($tem=mysql_fetch_row($psql)){
				
				if($tem[2]==1){
					//$res.='<div><span id="'.$param[$i].$tem[0].'" name="'.$tem[0].'" bloque="#res'.$y.'b" tipo="'.$param[$i].'" tabla="'.$param[$i].'s" class="activo" >'.$tem[1].'</span><span  target="#'.$param[$i].$tem[0].'" class="cambest" est="act" ><img src="icons/change.png" ></span></div><br>';
					
					$res.='				
					<div class="input-group">
					<input type="text" class="form-control more '.$param[$i].'activo" id="'.$param[$i].$tem[0].'" name="'.$tem[0].'" bloque="#res'.$y.'b" tipo="'.$param[$i].'" tabla="'.$param[$i].'s"  value="'.$tem[1].'" readonly>
					<span class="input-group-btn">
					<button class="cambest btn btn-success" target="#'.$param[$i].$tem[0].'" tipo="'.$param[$i].'" est="1" type="button"><span class="glyphicon glyphicon-refresh" ></span></button>
					<button class="btn btn-default aplicar" toap="'.$tem[0].'" tipo="'.$param[$i].'" type="button" title="Aplicar '.$param[$i].'" data-toggle="modal" data-target="#modaplicar'.$param[$i].'" ttt="#st'.$param[$i].'"><span class="glyphicon glyphicon-ok"></span></button>
					</span>
					</div><!-- /input-group -->
					';
				}
				else{
					//$res.='<div><span id="'.$param[$i].$tem[0].'" name="'.$tem[0].'" bloque="#res'.$y.'b" tipo="'.$param[$i].'" tabla="'.$param[$i].'s" class="inactivo" >'.$tem[1].'</span><span  target="#'.$param[$i].$tem[0].'" class="cambest" est="ina" ><img src="icons/change.png" ></span></div><br>';
					$res.='				
					<div class="input-group">
					<input type="text" class="form-control more '.$param[$i].'inactivo" id="'.$param[$i].$tem[0].'" name="'.$tem[0].'" bloque="#res'.$y.'b" tipo="'.$param[$i].'" tabla="'.$param[$i].'s"  value="'.$tem[1].'" readonly>
					<span class="input-group-btn">
					<button class="cambest btn btn-danger" target="#'.$param[$i].$tem[0].'" tipo="'.$param[$i].'" est="0" type="button"><span class="glyphicon glyphicon-refresh"></span></button>
					<button class="btn btn-default aplicar" toap="'.$tem[0].'" tipo="'.$param[$i].'" type="button" title="Aplicar '.$param[$i].'" data-toggle="modal" data-target="#modaplicar'.$param[$i].'" ttt="#st'.$param[$i].'"><span class="glyphicon glyphicon-ok"></span></button>
					</span>
					</div><!-- /input-group -->
					';
				}
			}
			$data=$res;
			
			$param["$param[$i]"]='<div id="bloque'.$y.'sup">
			<div class="row">
			<div id="buscador">
			<div class="col-lg-6 col-md-offset-3">
			<div class="input-group">
			<span class="input-group-addon">'.strtoupper("$param[$i]").'S</span>
			<span class="input-group-btn">
			<button class="btn btn-default crear" name="'.$param[$i].'" bloque="#bloqnew'.$y.'" tabla="'.$param[$i].'s" title="CREAR" type="button" data-toggle="modal" data-target="#mod'.$param[$i].'" ><span class="glyphicon glyphicon-plus-sign"></span></button>	
			</span>
			</div><!-- /input-group -->
			</div><!-- /.col-lg-6 -->
			
			</div>
			</div><br>
			
			<div class="row">
			<div class="col-lg-6 col-md-offset-3">
			
			<div id="res'.$y.'" class="bloqres">'.$data.'</div>
			
			</div>
			<div class="col-lg-6 col-md-offset-3">
			&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
			<div class="col-lg-6 col-md-offset-3">
			<div id="res'.$y.'b" class="bloqres"></div>
			</div>
			
			
			<div id="bloque'.$y.'inf" style="width:710px; height:950px;"></div>
			<input type="hidden" id="cont" name="cont" value="0">
			
			
			<!-- Modal -->
			<div class="modal fade" id="mod'.$param[$i].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-body">
			<div class="datos">
			<div class="form-group">
			<label>Nombre del '.strtoupper("$param[$i]").'</label>
			<input class="form-control input-sm" type="text" name="add'.$param[$i].'" id="nom'.$param[$i].'" placeholder ="Escriba el nombre">
			</div>
			<div class="form-group">
			<label>Observacion</label>
			<textarea class="form-control input-sm" id="obs'.$param[$i].'" rows="6" cols="50"></textarea>
			</div>
			</div>
			
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-primary save" name="save'.$param[$i].'" tipo="'.$param[$i].'" tabla="'.$param[$i].'s" bloque="#res'.$y.'" >GUARDAR</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
			</div>
			</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			</div>
			
			</div>
			
			<!-- Modal -->
			<div class="modal fade" id="modaplicar'.$param[$i].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-body">
			<select class="form-control" name="use'.$param[$i].'" size="30" id="use'.$param[$i].'" multiple>'.$op.'</select>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary aplicarattr" id="st'.$param[$i].'" idattr="" tipo="'.$param[$i].'" tar="#use'.$param[$i].'">Aplicar</button>
			</div>
			</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			
			';
		}
		
		else{
			$param["$param[$i]"]='<div id="bloque'.$y.'sup">
			<div class="row">
			<div id="buscador">
			<div class="col-lg-6 col-md-offset-3">
			<div class="input-group">
			<span class="input-group-addon">'.strtoupper("$param[$i]").'S</span>
			<span class="input-group-btn">
			<button class="btn btn-default crear" name="'.$param[$i].'" bloque="#bloqnew'.$y.'" tabla="'.$param[$i].'s" title="CREAR" type="button" data-toggle="modal" data-target="#mod'.$param[$i].'" ><span class="glyphicon glyphicon-plus-sign"></span></button>	
			</span>
			</div><!-- /input-group -->
			</div><!-- /.col-lg-6 -->
			
			</div>
			</div><br>
			
			<div class="row">
			<div class="col-lg-6 col-md-offset-3">
			
			<div id="res'.$y.'" class="bloqres"></div>
			
			</div>
			
			<div class="col-lg-6 col-md-offset-3">
			<div id="res'.$y.'b" class="bloqres"></div>
			</div>
			
			
			<div id="bloque'.$y.'inf" style="width:710px; height:950px;"></div>
			<input type="hidden" id="cont" name="cont" value="0">
			
			
			<!-- Modal -->
			<div class="modal fade" id="mod'.$param[$i].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-body">
			<div class="datos">
			<div class="form-group">
			<label>Nombre del '.strtoupper("$param[$i]").'</label>
			<input class="form-control input-sm" type="text" name="add'.$param[$i].'" id="nom'.$param[$i].'" placeholder ="Escriba el nombre">
			</div>
			<div class="form-group">
			<label>Observacion</label>
			<textarea class="form-control input-sm" id="obs'.$param[$i].'" rows="6" cols="50"></textarea>
			</div>
			</div>
			
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-primary save" name="save'.$param[$i].'" tipo="'.$param[$i].'" tabla="'.$param[$i].'s" bloque="#res'.$y.'" >GUARDAR</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
			</div>
			</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			</div>
			
			</div>
			
			<!-- Modal -->
			<div class="modal fade" id="modaplicar'.$param[$i].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-body">
			<select class="form-control" name="use'.$param[$i].'" size="30" id="use'.$param[$i].'" multiple>'.$op.'</select>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary aplicarattr" id="st'.$param[$i].'" idattr="" tipo="'.$param[$i].'" tar="#use'.$param[$i].'">Aplicar</button>
			</div>
			</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			';
		}
		
	}
	
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<script src='js/jquery-1.10.1.min.js'></script>
		<style>
			iframe, object, embed {
			max-width: 100%;
		}</style>
		<link rel='stylesheet' type='text/css' href='css/datetimepicker.css'/> 
		<script src='js/bootstrap.js'></script>
		<script src='js/bootstrap-datetimepicker.min.js'></script>
		<script src='js/bootstrap-datetimepicker.es.js'></script>
		<script src='js/newuser.js'></script>
		<title>GESTION DE USUARIOS</title>
		<script>
			$(document).ready(function(){
			
			$("#ffnnx").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
			pickerPosition: 'top-left',
			autoclose: true,
			language:'es',minView:2,
			startDate:'-40y',
			endDate:'-10y'
			});
			
			$("#ffiix").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
			pickerPosition: 'top-left',
			autoclose: true,
			language:'es',minView:2,
			});
			
			
			////////////////////////BUSCAR USUARIO/////////////////////
			$('body').delegate('.buscar','click',function(){
			var tipo=$(this).attr('name');
			if(tipo=="user"){
			var p=$('#patronuser').val();
			
			var ff=p.length;
			if(ff!=0){
			if(ff<3){var limit=10;}
			else{var limit=0;}
			var c=1;
			$.post("res/usuarios/proc.php",{patron: p,tar: c,limit:limit},function(data){
			$('#res1').empty();
			$('#res1').html(data.dat1);
			}, "json");	
			}
			
			else{
			$('#patronuser').attr("placeholder","ESCRIBA UN NOMBRE...").focus();
			}
			
			}
			else{}    
			});
			
			
			/////////////////////////////////////////////////CARGAR Y GUARDAR DATOS DE USUARIO/////////////////////////////////////////////////  
			$('body').delegate('.usuarios','click',function(){
			var ttt=$('.tipoatributo').attr('name');
			var l=$(this).attr('name');
			var c=2;
			$.ajax({
			type: 'POST',
			url: 'res/usuarios/proc.php',
			data: {login: l , tar:c},
			success: function(data){
				$('#res1').empty();
				$('#res1').html(data);
				
				$('body').delegate('#selrol','change',function(){
					var o1=$('#selrol :selected').attr('value');
					var o2="#";
					var t1=$('#selrol :selected').attr('atid');
					var op=String(o2)+String(o1);
					op = op.replace(/\s+/g, '');
					o1 = o1.replace(/\s+/g, '');
					$(op).remove();
					$('#listro').append('<div class="input-group divro" id="'+o1+'" atid="'+t1+'"><input class="form-control input-sm" type="text"  value="'+o1+'"><span class="input-group-btn"><button class="btn btn-default input-sm delatr" id="del'+o1+'" naat="'+o1+'" type="button"><span class="glyphicon glyphicon-remove"></span></button></span></div>');
				});
				
				$('body').delegate('.delatr','click',function(){
					var o1=$(this).attr('naat');
					var o2="#";
					var op=String(o2)+String(o1);
					$(op).remove();
				});
				
				$("#ffnn").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
					pickerPosition: 'top-left',
					autoclose: true,
					language:'es',minView:2,
					startDate:'-40y',
					endDate:'-10y'
				});
				
				$("#ffii").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
					pickerPosition: 'top-left',
					autoclose: true,
					language:'es',minView:2,
				});
				
				$("#ffnnx").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
					pickerPosition: 'top-left',
					autoclose: true,
					language:'es',minView:2,
					startDate:'-40y',
					endDate:'-10y'
				});
				
				$("#ffiix").datetimepicker({format: 'yyyy-mm-dd', showMeridian: true,
					pickerPosition: 'top-left',
					autoclose: true,
					language:'es',minView:2,
				});
				
				$('#cancel').click(function(){
					//window.open("index.html",'_self');
					$("#res1").empty();
				});	 
				
			}
		});
	});
	
	//////////////GUARDAR DATOS DEL USUARIO//////////////////						
	$('body').delegate('#savecambios','click',function(){
		
		var ll=$('#login').val();
		var nn=$('#nombre').val();
		var ap=$('#apellidos').val();
		var d=$('#dni').val();
		var mail=$('#mail').val();
		var dir=$('#direc').val();
		var fn=$('#ffnn').val();
		var loc=$('#locales :selected').val();
		var estu=$('#estuser :selected').val();
		var fi=$('#ffii').val();
		var tel=$('#telefono').val();
		var cel=$('#celular').val();
		var niv=$('#niv :selected').attr('value');
		var obs=$('#obs').val();
		var ex=$('#expe').val();
		
		var c=4;
		
		var items1 = []; $(".divsk").each(function(){items1.push($(this).attr('atid')); });
		var items2 = []; $(".divro").each(function(){items2.push($(this).attr('atid')); });
		var items3 = []; $(".divgr").each(function(){items3.push($(this).attr('atid')); });
		var items4 = []; $(".divar").each(function(){items4.push($(this).attr('atid')); });
		var items5 = []; $(".divco").each(function(){items5.push($(this).attr('atid')); });	
		var ats	= []; i=0; var t1=""; var t2="";
		$(".attrx").each(function(){
			var x1=$(this).find(".atbvl").val(); var x2=$(this).find(".atblc").val();
			if(x1!=null && x2!=null){ats[i]=[x1,x2]; i++;}
		});
		
		
		var c1=items1.length; var c2=items2.length; var c3=items3.length; var c4=items4.length; var c5=items5.length;
		
		$.ajax({
			type: 'POST',
			url: 'res/usuarios/proc.php',
			data: {login:ll, nombre: nn, apellidos:ap, dni:d, email:mail, direc:dir, ffnn:fn, telefono:tel, celular:cel ,local:loc,estu:estu, nivel:niv , edobs:obs, exp:ex, ffii:fi, sk:items1, ro:items2, gr:items3, ar:items4, co:items5, css:c1, crr:c2, cgg:c3, caa:c4, ccc:c5, ats:ats, tar:c},
			success: function(data){
				alert('CAMBIOS GUARDADOS');
				$('#res1').empty();
				$('#patronuser').val('');
			}
		});
	});
	/////////////////////////////////////  
	
	/////////////////CAMBIAR DE ESTADO A LOS ATRIBUTOS////////////////////////
	$('body').delegate('.cambest','click',function(){
		
		var nn=$(this).attr('target');
		var id=$(nn).attr('name');
		var est=$(this).attr('est');
		var tipo=$(this).attr('tipo');
		var act=String(tipo)+String("activo");
		var ina=String(tipo)+String("inactivo");
		var cont=$('#cont').val();
		var nc=parseInt(cont)+1;
		$('#cont').val(nc);
		
		var tar=17;
		
		if(est==1){
			$(this).attr("est",0);
			$(this).removeClass("btn-success");
			$(this).addClass("btn-danger");
			$(nn).removeClass(act);
			$(nn).addClass(ina);
			$.post('res/usuarios/proc.php',{tar:tar, tipo:tipo, id:id, est:"0" },function(data){});
			
		}
		else if(est==0){
			$(this).attr("est",1);
			$(this).removeClass("btn-danger");
			$(this).addClass("btn-success");
			$(nn).removeClass(ina);
			$(nn).addClass(act);
			$.post('res/usuarios/proc.php',{tar:tar, tipo:tipo, id:id, est:"1" },function(data){});
		}
	});
	
	
	////////////////////////////////////GUARDAR ATRIBUTOS///////////////////////////////
	$('body').delegate('.save','click',function(){
		var bloq=$(this).attr('bloque');
		var tab=$(this).attr('tabla');
		var tipo=$(this).attr('tipo');
		var ttt=$(this).attr('name');
		var nom="#nom";
		var nom2=String(nom)+String(tipo);
		var obs="#obs";
		var obs2=String(obs)+String(tipo);
		var nombre=$(nom2).val();
		var observ=$(obs2).val();
		var ff=nombre.length;
		
		//alert(bloq+'/'+tab+'/'+tipo+'/'+ttt+'/'+nom+'/'+nom2+'/'+obs+'/'+obs2+'/'+nombre+'/'+observ+'/'+ff);
		if(ff<1){
			alert('ESCRIBA UN NOMBRE');
		}
		else{
			var c=10;
			$.post("res/usuarios/proc.php",{name:nombre, obsv:observ ,table:tab,tar: c},function(data){
				$(bloq).append('<div class="input-group"><input type="text" class="form-control more '+tipo+'activo" id="'+tipo+parseInt(data)+'" name="'+parseInt(data)+'" bloque="'+bloq+'b"  tipo="'+tipo+'" tabla="'+tipo+'s" class="activo" value="'+nombre+'" readonly><span class="input-group-btn"><button class="cambest btn btn-success" target="#'+tipo+parseInt(data)+'" tipo="'+tipo+'" est="1" type="button"><span class="glyphicon glyphicon-refresh" ></span></button><button class="btn btn-default aplicar" toap="'+parseInt(data)+'" tipo="'+tipo+'" type="button" title="Aplicar '+tipo+'" data-toggle="modal" data-target="#modaplicar'+tipo+'" ttt="#st'+tipo+'"><span class="glyphicon glyphicon-ok"></span></button></span></div>');
				$('#mod'+tipo).modal('hide');
				
			});	
		}
	});
	////////////////////////////////////USUARIO DE  ATRIBUTOS///////////////////////////////
	$('body').delegate('.more','click',function(){
		
		var bloq=$(this).attr('bloque');
		var name=$(this).attr('name');
		var tipo=$(this).attr('tipo');
		var tabla=$(this).attr('tabla');
		var c=12;
		
		$.post('res/usuarios/proc.php',{tar:c, tipo:tipo, id:name},function(data){
			$(bloq).empty();
			$(bloq).html(data);
		});
		
	});
	
	//////////////////////////////////GET ID ATTR///////////////////////////////////
	$('body').delegate('.aplicar','click',function(){
		
		var id=$(this).attr('toap');
		var tar=$(this).attr('ttt');
		$(tar).attr('idattr',id);
	});
	
	/////////////////////////////APLICAR ATRIBUTOS/////////////////////////////////
	$('body').delegate('.aplicarattr','click',function(){
		var tipo=$(this).attr('tipo');
		var sel=$(this).attr('tar');
		var id=$(this).attr('idattr');
		var items = [];
		$(sel+' :selected').each(function(){items.push($(this).text());});
		var size=items.length;
		var c=18;
		$.post('res/usuarios/proc.php',{tar:c, tipo:tipo, id:id, user:items, cc:size},function(data){
			$('#modaplicar'+tipo).modal('hide');
		});
		
	});
	
	/////////////////////////////////////////////////////////////////////////////////////////
	
	$("body").delegate('#nuevo','click',function(){
		$('#res1').empty();
		
	}); 
	
	//////////////////////////////////////////VERIFICAR CRECACION DE LOGIN USUARIO///////////////////////////////////////////////
	
	$("body").delegate('#veri','click',function(){
		var inp=$('#loginx').val();
		var c=19;
		$.post('res/usuarios/proc.php',{tar:c, log:inp},function(data){
			
			var cc=parseInt(data);
			if(cc!=0){
				$('#loginx').attr("placeholder","Usuario existente").val("");
				$('#existe').html('<span class="glyphicon glyphicon-remove"></span>');
				$('#existe').addClass("btn-danger");
				return false;
			}
			else{
				$('#loginx').val(inp);
				$('#existe').html('<span class="glyphicon glyphicon-ok"></span>');
				$('#existe').addClass("btn-success");
				return false;
			}
		});
		
	}); 
	
	//////////////////////////////////////////////////////////////////
	$( "#patronuser" ).keypress(function( event ) {
		if ( event.which == 13 ){event.preventDefault(); $('.buscar').click();}
	});
	//////////////////////////////////////////////////////////////////
	$("body").delegate('.resetpass','click',function(){
		var log=$(this).attr("d1");
		var c=20;
		$.post('res/usuarios/proc.php',{tar:c, login:log},function(data){
		});
	}); 
	///////////////////////////////////////////ATRIBUTOS////////////////////////////////////////////////
	$("body").delegate('.addatb','click',function(){
		var er=0;
		$(".atbvl").each(function(){if($(this).val()==null){$(this).focus(); er=1; return false;} });
		$(".atblc").each(function(){if($(this).val()==null){$(this).focus(); er=1; return false;} });
		if(er==0){$("#in-tab-3").append($(this).parent().parent().clone());}
	});
	////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////
});
</script>
<style>
	.datetimepicker{
	z-index: 10000 !important;
	}
	
</style>
</head>
<body>
	
	<div class="container">
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<ul class="nav nav-pills nav-justified">
					<li class="active"><a href="#users" data-toggle="tab">USUARIOS</a></li>
					<li><a href="#skills" data-toggle="tab">SKILLS</a></li>
					<li><a href="#roles" data-toggle="tab">ROLES</a></li>
					<li><a href="#grupos" data-toggle="tab">GRUPOS</a></li>
					<li><a href="#areas" data-toggle="tab">AREAS</a></li>
				</ul>
			</div>
			
			
			<div class="panel-body">
				<div class="tab-content">
					<div class="tab-pane active" id="users">
						<div id="bloque1">
							
							<div class="row">
								<div class="col-lg-10 col-lg-offset-1" id="buscador">
									<div class="input-group">
										<span class="input-group-addon">BUSCAR</span>
										<input type="text" class="form-control" name="patron" id="patronuser" >
										<span class="input-group-btn">
											<button class="btn btn-default buscar"  type="button" name="user" title="BUSCAR USUARIO"><span class="glyphicon glyphicon-search"></span></button>
											<button class="btn btn-default" data-toggle="modal" data-target="#modnewuser" type="button"  title="AGREGAR USUARIO" id="nuevo"><span class="glyphicon glyphicon-plus-sign"></span></button>
										</span>
									</div><!-- /input-group -->
									<input type="hidden" name="cont" id="cont" value="0">
								</div>		
							</div><br>
							<div class="row">
								<div class=" col-lg-10 col-lg-offset-1" id="res1">
								</div>
							</div>
						</div>
					</div>
					
					
					<div class="tab-pane" id="skills"><?php echo $param["skill"];?></div>
					<div class="tab-pane" id="roles"><iframe src="res/usuarios/roles.php" id="irol" frameborder="0" height="600" style="width:100%"><p>Your browser does not support iframes.</p></iframe></div>
					<div class="tab-pane" id="grupos"><?php echo $param["grupo"];?></div>
					<div class="tab-pane" id="areas"><?php echo $param["area"];?></div>
				</div>
			</div>
		</div>
		
	</div>
	
	
	<!-- Modal -->
	<div class="modal fade" id="modnewuser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">AGREGAR USUARIO</h4>
				</div>
				<div class="modal-body"><div class="row" id="res1">
					
					<div id="tabs">					  
						<ul class="nav nav-pills nav-justified">
							<li class="active"><a href="#tabs-1" data-toggle="tab">Datos Personales</a></li>
							<li><a href="#tabs-2" data-toggle="tab">Otros</a></li>
							
						</ul> 
					</div>
					
					<div class="tab-content">
						
						<div class="tab-pane active" id="tabs-1"><br>
							<div class="row" id="">
								<div class="col-md-6">
									<div class="col-md-12 datos"><legend>Datos Personales</legend></div>
									<div class="col-md-12 datos"><label>Login</label><br><div class="input-group"><input class="form-control" type="text" name="loginx" id="loginx" value=""><span class="input-group-btn"><button class="btn btn-default" type="button" id="veri"> <span class="glyphicon glyphicon-search"></span></button></span></div></div>
									<div class="col-md-12 datos"><label>Nombre</label><input class="form-control" type="text" name="nombrex" id="nombrex" value=""></div>
									<div class="col-md-12 datos"><label>Apellidos</label><input class="form-control" type="text" name="apellidosx" id="apellidosx" value=""></div>
									<div class="col-md-12 datos"><label>DNI</label><input class="form-control" type="text" name="dnix" id="dnix" value=""></div>
									<div class=" col-md-7 datos"><label>E-Mail</label><input class="form-control" type="text" name="mailx" id="mailx" value=""></div>
									<div class=" col-md-5 datos"><label>&nbsp;</label><select class="form-control"><option>@gmail.com</option><option value="2">@hotmail.com</option><option value="3">@yahoo.com</option></select></div>				
									<div class="col-md-12 datos"><label>Dirección</label><input class="form-control" type="text" name="direcx" id="direcx" value=""></div>
									<div class="col-md-12 datos"><label>Fecha de Nacimiento</label><input class="form-control" type="text" id="ffnnx" value="" /></div>
									<div class="col-md-12 datos"><label>Telefono</label><input class="form-control" type="text" name="telefonox" id="telefonox" value=""></div>
									<div class="col-md-12 datos"><label>Celular</label><input class="form-control" type="text" name="celularx" id="celularx" value=""></div>
									
								</div>
								
								<div class="col-md-6">
									<div class="col-md-12 datos"><legend>Educacion</legend></div>
									<div class="col-md-12 datos"><label>Nivel de Educacion</label>			
										<select class="form-control input-sm" name="nivx" id="nivx">
											<option value="" selected disabled>--ELEGIR--</option>
											<option value="pri">PRIMARIA</option>
											<option value="sec">SECUNDARIA</option>
											<option value="sup">SUPERIOR</option>
										</select>
									</div>
									<div class="col-md-12 datos"><label>Local</label><?php echo $seloc;?></div>
									<div class="col-md-12 datos"><label>Estado</label><select class="form-control" id="estuser"><option value="1">ACTIVO</option><option value="0">INACTIVO</option></select></div>
									<div class="col-md-12 datos"><label>Fecha Ingreso</label><input class="form-control" type="text" id="ffiix" value="" /></div>
									<div class="col-md-12 datos"><label>Educacion(Otros)</label><textarea class="form-control" name="obsx" id="obsx" cols="40" rows="5"></textarea></div>
									<div class="col-md-12 datos"><label>Experiencia</label><textarea class="form-control" name="expex" id="expex" cols="40" rows="5"></textarea></div>	
									
								</div>			
							</div>
						</div>
						
						
						<div class="tab-pane" id="tabs-2">
							<div class="table-responsive">
								<table class="table">
									<tbody>
										<tr>
											<td class="col-md-6">
												<div class="attr">
													<div class="form-group"><legend>Skills</legend></div>
													<?php echo $sel[0];?>
													<div class="listas" id="listsk"></div>
												</div> 
											</td>
											
											<td class="col-md-6">
												<div class="attr">
													<div class="form-group"><legend>Rols</legend></div>
													<?php echo $sel[1];?>
													<div class="listas" id="listro"></div>
												</div> 
											</td>
										</tr>
										
										<tr>
											<td class="col-md-6">
												<div class="attr">
													<div class="form-group"><legend>Grupos</legend></div>
													<?php echo $sel[2];?>
													<div class="listas" id="listgr"></div>
												</div>
											</td>
											
											<td class="col-md-6">
												<div class="attr">
													<div class="form-group"><legend>Areas</legend></div>
													<?php echo $sel[3];?>
													<div class="listas" id="listareas"></div>
												</div>
											</td>
										</tr>
										
										<tr>
											<td class="col-md-6">
												<div class="attr">
													<div class="form-group"><legend>Colas</legend></div>
													<?php echo $sele;?>
													<div class="listas" id="listco"></div>
												</div>
											</td>
											
											<td class="col-md-6">
												<div class="perfil">
													<div class="form-group"><legend>Perfil</legend></div>
													<div class="input-group">
														<?php echo $selp;?>
														<span class="input-group-btn">
															<button class="btn btn-default input-sm" id="aplicar" type="button">
																<span class="glyphicon glyphicon-thumbs-up"></span>
															</button>
														</span>
													</div><!-- /input-group -->
												</div> 
											</td>
										</tr></tbody>
								</table></div>
								
						</div>
						
						
						
					</div> 
					
					
				</div></div>
				<div class="modal-footer">
					<button type="button" id="savecambiosx" class="btn btn-success">GUARDAR</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
	
</body>
</html>