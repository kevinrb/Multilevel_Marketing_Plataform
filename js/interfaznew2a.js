$(document).ready(function(){
	 
$('#myTab').hide();

$("body").on("change","#clamotivo", function(){
	var valu=$(this).val();
	$.post("ajax/interfaz_ajaxnew2.php",{tipo:"motivo",val:valu},function(data){
		$("#idmotivo").html(data);

	})
});

$("body").on("change","#claidpro", function(){
	$("#idpro option").hide();
	var valu=$(this).val();
	$("#idpro ."+valu).show();
});

$('#myTab a').click(function (e) {
	e.preventDefault();
	$(this).tab('show');
})
var moti;
$("#cuerpo").hide();
$("#datos").on('focus', 'input[readonly]', function(){
	$(this).blur();
});
//I. PRINCIPAL: SELECCION DE CAMPAÑA
	$("#agenda").hide();
	var campan = $(this).children(':selected').text();
	$("#cuerpo").show();
	$("#carga").empty();
	$("#carga").append("<span>SELECCIONADA LA CAMPAÑA: <strong>" + campan + "</strong></span>");
	//SE TRAEN LOS DATOS 
	$.post("ajax/interfaz_ajaxnew2.php", {cp:'listo'}, function(data){
		$('#myTab').show();
		$("#datos").empty();
		$("#datos").append("<h2>Datos de cliente</h2>");
		$("#datos").append(data.datos);
		$("#datos").append(data.datos1);
		$("#tipos").append(data.moti);
		$("#tipos").append(data.prod);
		$("#cuadro").append(data.preg);
		$("#home").append(data.speech);
		$("#history").append(data.histo);
		$('#tablee').dataTable();
	},"json"); 
	$('#timeagenda').datetimepicker({format: 'yyyy-mm-dd hh:ii',
	showMeridian: true,
	autoclose: true,
	todayBtn: true, 
	language:'es',
	headTemplateV3:true,
	minView:0});


$("body").on("change","#idmotivo",function(){
	var idmoti = $(this).val();
	$("#agenda").hide();
	$("#cuerpo").show();
	$("#texto").empty();
	$(".active").removeClass("active");
	$("#profile").addClass("active");
	$("a[href='#profile']").parent().addClass("active");
	$.post("ajax/call.php", {idmotivo:idmoti}, function(data){
		//if(data!="\n")
		//{
			$("#texto").html(data);
			$("#collapseOne").addClass('in');
			$('#collapseOne').css('height', 'auto');
	//	}
	});
	$("#cuadro").children().prop('required', true);
	if($(this).children(':selected').hasClass("estado2"))
	{
		$("#agenda").show();
		$("#cuadro").children().removeAttr('required');
	}
	if($(this).children(':selected').hasClass("estado3"))
	{
		$("#agenda").show();
		$("#cuadro").children().removeAttr('required');
	}
});
//III. ENVIO DEL FORMULARIO
$("#btnagen, #sub").click(function(){
$("form").submit(function() {
	if($("#idmotivo").val()!=0){
		$.post($(this).attr('action'), $(this).serialize(),function(data){	
		window.location.href = "interfaznew2.php";
},"json");


	}
	else{
		alert("Ingresa un motivo.");
		return false;
	}
});
})
$("body").on("click",".call", function(){
	var tel=$(this).prev().val();
	var ido=$("#idops").val();
	var but=$(this).children();
	but.removeClass('btn-success');
	but.addClass('btn-default');
	but.button('loading');
	$.post("ajax/interfaz_ajaxnew2.php", {recall:tel, idops:ido,save:1 }, function(data){
	llamar(tel,ido,but);
	});
	
});


$("body").on("click",".call1", function(){
	var tel=$(this).children().val();
	var ido=$("#idops").val();
	var but=$(this).children();
	but.removeClass('btn-success');
	but.addClass('btn-default');
	but.button('loading');
	$.post("ajax/interfaz_ajaxnew2.php", {recall:tel, idops:ido }, function(data){
	llamar1(tel,ido,but);
	});
	
});

$("body").on("click",".cancel", function(){
	var tel=$(this).prev().val();
	var ido=$("#idops").val();
	var but=$(this).children();
	but.text('Llamar');
	but.removeClass('btn-danger');
	but.addClass('btn-success');
	but.parent().removeClass('cancel');
	but.parent().addClass('call');
	$.post("ajax/interfaz_ajaxnew2.php", {accion:"colgar", idops:ido }, function(data){
	});
});


$("body").on("click",".cancel1", function(){
	var ido=$("#idops").val();
	var but=$(this).children();
	but.text('Llamar');
	but.removeClass('btn-danger');
	but.addClass('btn-success');
	but.parent().removeClass('cancel1');
	but.parent().addClass('call1');
	$.post("ajax/interfaz_ajaxnew.php", {accion:"colgar", idops:ido }, function(data){
	});
	});
});

function xhr(){
	if(window.XMLHttpRequest){
	return new XMLHttpRequest();
	} else if(window.ActiveXObject){
	return new ActiveXObject("Microsoft.XMLHTTP");
	}
}


function llamar(tel,idops,but){
	var peticion = xhr();
	peticion.onreadystatechange = function () {
	if(peticion.readyState == 4){
	var cl=peticion.responseText;
	if(/Failure/.test(cl))
	{
	but.button('reset');
	but.removeClass('btn-default');
	but.addClass('btn-success');
	}
	if(/Success/.test(cl))
	{
	but.button('reset');
	but.text('Colgar');
	but.removeClass('btn-default');
	but.addClass('btn-danger');
	but.parent().removeClass('call');
	but.parent().addClass('cancel');
	}
	//setTimeout(function () { llamar(tel,idops); },1);
	}
	}
	peticion.open("POST","ajax/call.php",true);
	peticion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	peticion.send("idops="+idops+"&tel="+tel);
}

function llamar1(tel,idops,but){
	var peticion = xhr();
	peticion.onreadystatechange = function () {
	if(peticion.readyState == 4){
	var cl=peticion.responseText;
	if(/Failure/.test(cl))
	{
	but.button('reset');
	but.removeClass('btn-default');
	but.addClass('btn-success');
	}
	if(/Success/.test(cl))
	{
	but.button('reset');
	but.text('Colgar');
	but.removeClass('btn-default');
	but.addClass('btn-danger');
	but.parent().removeClass('call1');
	but.parent().addClass('cancel1');
	}
	//setTimeout(function () { llamar(tel,idops); },1);
	}
	}
	peticion.open("POST","ajax/call.php",true);
	peticion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	peticion.send("idops="+idops+"&tel="+tel);
}