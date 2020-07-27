$(document).ready(function(){
/*
$('#loginx').keyup(function(){
var inp=$(this).val();
var c=19;
$.post('ajax/proc_b.php',{tar:c, log:inp},function(data){

var cc=parseInt(data);
if(cc!=0){
$('#loginx').val("Usuario existente");
$('#verifica').removeClass("glyphicon-ok");
$('#verifica').addClass("glyphicon-remove");
$('#veri').removeClass("btn-success");
$('#veri').addClass("btn-danger");

}

else{
$('#loginx').val(inp);
$('#verifica').removeClass("glyphicon-remove");
$('#verifica').addClass("glyphicon-ok");
$('#veri').removeClass("btn-danger");
$('#veri').addClass("btn-success");

}

});

});*/

						$('body').delegate('#selskill','change',function(){
						var o1=$('#selskill :selected').attr('value');
						var t1=$('#selskill :selected').attr('atid');
						var o2="#";
						var op=String(o2)+String(o1);
						op = op.replace(/\s+/g, '');
						o1 = o1.replace(/\s+/g, '');
						$(op).remove();
						//$('#listsk').append('<div id='+o1+' atid="'+t1+'" class="divsk">'+o1+'<input type="button" id="del'+o1+'" naat="'+o1+'" class="delatr" value="X"></div>');
						$('#listsk').append('<div class="input-group divsk" id="'+o1+'" atid="'+t1+'"><input class="form-control input-sm" type="text"  value="'+o1+'"><span class="input-group-btn"><button class="btn btn-default input-sm delatr" id="del'+o1+'" naat="'+o1+'" type="button"><span class="glyphicon glyphicon-remove"></span></button></span></div>');						
						});
						
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
						
						$('body').delegate('#selgrupo','change',function(){
						var t1=$('#selgrupo :selected').attr('atid');
						var o1=$('#selgrupo :selected').attr('value');
						var o2="#";
						var op=String(o2)+String(o1);
						op = op.replace(/\s+/g, '');
						o1 = o1.replace(/\s+/g, '');
						$(op).remove();
						$('#listgr').append('<div class="input-group divgr" id="'+o1+'" atid="'+t1+'"><input class="form-control input-sm" type="text"  value="'+o1+'"><span class="input-group-btn"><button class="btn btn-default input-sm delatr" id="del'+o1+'" naat="'+o1+'" type="button"><span class="glyphicon glyphicon-remove"></span></button></span></div>');
						});
						
						$('body').delegate('#selarea','change',function(){
						var t1=$('#selarea :selected').attr('atid');
						var o1=$('#selarea :selected').attr('value');
						var o2="#";
						var op=String(o2)+String(o1);
						op = op.replace(/\s+/g, '');
						o1 = o1.replace(/\s+/g, '');
						$(op).remove();
						$('#listareas').append('<div class="input-group divar" id="'+o1+'" atid="'+t1+'"><input class="form-control input-sm" type="text"  value="'+o1+'"><span class="input-group-btn"><button class="btn btn-default input-sm delatr" id="del'+o1+'" naat="'+o1+'" type="button"><span class="glyphicon glyphicon-remove"></span></button></span></div>');
						});
						
						$('body').delegate('#selcola','change',function(){
						var t1=$('#selcola :selected').attr('atid');
						var o1=$('#selcola :selected').attr('value');
						var o2="#";
						var op=String(o2)+String(o1);
						op = op.replace(/\s+/g, '');
						o1 = o1.replace(/\s+/g, '');
						$(op).remove();
						$('#listco').append('<div class="input-group divco" id="'+o1+'" atid="'+t1+'"><input class="form-control input-sm" type="text"  value="'+o1+'"><span class="input-group-btn"><button class="btn btn-default input-sm delatr" id="del'+o1+'" naat="'+o1+'" type="button"><span class="glyphicon glyphicon-remove"></span></button></span></div>');
						});
						
						
						
						
							$('body').delegate('#aplicar','click',function(){
						var pf=$('#perfil :selected').attr('value');
						
						if(pf!=""){
							var c=5;
							$.post("ajax/proc.php",{perfil: pf, tar:c},function(data){
							var rm=$('.divgr').attr('pfid');
							var o2="#";
							var op=String(o2)+String(rm);
							$(op).remove();
							$('#listsk').append(data.sk);
							$('#listro').append(data.ro);
							$('#listgr').append(data.gr);
							}, "json");
						}
						
						else{alert("No hay Perfiles que aplicar");}
						
						});
	
	
   $('body').delegate('#savecambiosx','click',function(){
   var ll=$('#loginx').val();
    var nn=$('#nombrex').val();
	var ap=$('#apellidosx').val();
	var d=$('#dnix').val();
	var mail=$('#mailx').val();
	var dir=$('#direcx').val();
	var fn=$('#ffnnx').val();
	var fi=$('#ffiix').val();
	var tel=$('#telefonox').val();
	var cel=$('#celularx').val();
	var niv=$('#nivx :selected').attr('value');
	var obs=$('#obsx').val();
	var ex=$('#expex').val();


   
   var c=3;
   if(ll==""){
   //$('#element').tooltip('show');
   $('#loginx').focus();
   }
   else{
   
  /* var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
if (!filter.test(mail) && mail!="") {
alert('Direccion de Correo Invalida');
}*/
	
	var inp=$('#loginx').val();
	var c=19;
	$.post('res/usuarios/proc.php',{tar:c, log:inp},function(data){

	var cc=parseInt(data);
	if(cc!=0){
	$('#loginx').val("Usuario existente");
	$('#verifica').removeClass("glyphicon-ok");
	$('#verifica').addClass("glyphicon-remove");
	$('#veri').removeClass("btn-success");
	$('#veri').addClass("btn-danger");
	return false;
	}

	else{
	 var items1 = [];
	 var items2 = [];
	 var items3 = [];
	$(".divsk").each(function(){items1.push($(this).attr('atid')); });
	$(".divro").each(function(){items2.push($(this).attr('atid')); });
	$(".divgr").each(function(){items3.push($(this).attr('atid')); });		
	var c1=items1.length;
	var c2=items2.length;
	var c3=items3.length;
	var cc=3; 
	 $.ajax({
							type: 'POST',
							url: 'res/usuarios/proc.php',
							data: {login:ll, nombre: nn, apellidos:ap, dni:d, email:mail, direc:dir, ffnn:fn, telefono:tel, celular:cel  , nivel:niv , edobs:obs, exp:ex, ffii:fi, sk:items1, ro:items2, gr:items3, css:c1, crr:c2, cgg:c3, tar:cc},
							success: function(){
							$('#verifica').removeClass("glyphicon-remove");
							$('#verifica').addClass("glyphicon-ok");
							$('#veri').removeClass("btn-danger");
							$('#veri').addClass("btn-success");
							$('#loginx').val("");
							$('#nombrex').val("");
							$('#apellidosx').val("");
							$('#dnix').val("");
							$('#mailx').val("");
							$('#direcx').val("");
							$('#ffnnx').val("");
							$('#ffiix').val("");
							$('#telefonox').val("");
							$('#celularx').val("");
							$('#obsx').val("");
							$('#expex').val("");
							$('#modnewuser').modal('hide');
							alert("USUARIO GUARDADO");
							}
					});
	}

	});

   }
   
  });
	

});