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
		$('#tablee').dataTable( {
		"bLengthChange": false,
		"bInfo": false, "bPaginate": false, "bSort": false
		});
		$('.fechas').datetimepicker({format: 'yyyy-mm-dd hh:ii',
		showMeridian: true,
		autoclose: true,
		language:'es',
		startDate: today,
		headTemplateV3:true,
		minView:0});
		$('#fono3').keypress(function(e){
			if(e.which == 13){
			$('.call').click();
			}
        });
        		llamar1($("#fono1").val(),$("#idops").val(),$(".cancel1").children());
	},"json"); 
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!

	var yyyy = today.getFullYear();
	if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = yyyy+'-'+mm+'-'+dd;

	$('#timeagenda').datetimepicker({format: 'yyyy-mm-dd hh:ii',
	showMeridian: true,
	autoclose: true,
	
	language:'es',
	startDate: today,
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
	
	if($(this).children(':selected').hasClass("estado2"))
	{
		$("#agenda").show();
		$("#timeagenda").prop('required',true);
		$("#cuadro").children().removeAttr('required');
	}
	if($(this).children(':selected').hasClass("estado3"))
	{
		$("#agenda").show();
		$("#timeagenda").prop('required',true);
		$("#cuadro").children().removeAttr('required');
	}
});
//III. ENVIO DEL FORMULARIO
$("#btnagen, #sub").click(function(){
       clearInterval(funcall); funcall=setInterval(contador,1000);  
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

 $("body").on("click", "#btnagen, #sub", function () {
        clearInterval(funcall); setCookie("timecallc", "", 1); timecall = 0;

        });

$("body").on("click",".call", function(){
    clearInterval(funcall); setCookie("timecallc","",1); timecall=0;
	var tel=$(this).prev().val();
	var ido=$("#idops").val();
	var but=$(this).children();
	if(parseInt($("#fono3").val())>0){
	but.children().removeClass('glyphicon-phone-earphone');
	but.children().addClass('glyphicon-phone-alt');
	but.removeClass('btn-success');
	but.addClass('btn-danger');
	but.parent().removeClass('call');
	but.parent().addClass('cancel');
	}
	$.post("ajax/interfaz_ajaxnew2.php", {recall:tel, idops:ido,save:1 }, function(data){
	$("#fono3").val(data);
	llamar(tel,ido,but);
	});
	
});


$("body").on("click",".call1", function(){
    clearInterval(funcall); setCookie("timecallc","",1); timecall=0;
	var tel=$(this).children().val();
	var ido=$("#idops").val();
	var but=$(this).children();
	but.removeClass('btn-success');
	but.addClass('btn-danger');
	but.parent().removeClass('call1');
	but.parent().addClass('cancel1');
	$.post("ajax/interfaz_ajaxnew2.php", {recall:tel, idops:ido }, function(data){
	llamar1(tel,ido,but);
	});
	
});

$("body").on("click",".cancel", function(){
    
	var tel=$(this).prev().val();
	var ido=$("#idops").val();
	var but=$(this).children();
	but.children().removeClass('glyphicon-phone-alt');
	but.children().addClass('glyphicon-phone-earphone');
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
	$.post("ajax/interfaz_ajaxnew2.php", {accion:"colgar", idops:ido }, function(data){
	});
	});
});
$(window).load(function(){

var timecallc=getCookie("timecallc");
if(timecallc!=null && timecallc!="" && timecallc<timemax)
{timecall=timecallc; funcall=setInterval(contador,1000);}

$("#llamada").click(function(){  clearInterval(funcall); setCookie("timecallc","",1); timecall=0;});
$("#colgar").click(function(){
funcall=setInterval(contador,1000);  
});

});

var timecall=0;

var funcall="";
function contador(){
timecall++;
setCookie("timecallc",timecall,1);
$("#tiempo").text(timecall);
if(timecall>=timemax)
{
    $.post("ajax/call.php", { action: "olvidado"}, function (data) {
           location.reload();
    });

    
    }
}

function getCookie(c_name)
{
var c_value = document.cookie;
var c_start = c_value.indexOf(" " + c_name + "=");
if (c_start == -1)
{c_start = c_value.indexOf(c_name + "=");}
if (c_start == -1)
{c_value = null;}
else
{
c_start = c_value.indexOf("=", c_start) + 1;
var c_end = c_value.indexOf(";", c_start);
if (c_end == -1)
{c_end = c_value.length;}
c_value = unescape(c_value.substring(c_start,c_end));
}
return c_value;
}

function setCookie(c_name,value,exdays)
{
var exdate=new Date();
exdate.setDate(exdate.getDate() + exdays);
var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
document.cookie=c_name + "=" + c_value;
}

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
	but.children().removeClass('glyphicon-phone-alt');
	but.children().addClass('glyphicon-phone-earphone');
	but.removeClass('btn-danger');
	but.addClass('btn-success');
	but.parent().removeClass('cancel');
	but.parent().addClass('call');
    clearInterval(funcall);
	funcall=setInterval(function(){contador()},1000);  
    }
        if(/noth/.test(cl))
	{
	setTimeout(function () { llamar(tel,idops,but); },1);
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
	but.removeClass('btn-danger');
	but.addClass('btn-success');
	but.parent().removeClass('cancel1');
	but.parent().addClass('call1');
    clearInterval(funcall);	
    funcall=setInterval(function(){contador()},1000);  
	}
    if(/noth/.test(cl))
	{
	setTimeout(function () { llamar(tel,idops,but); },1);
	//but.removeClass('btn-danger');
	//but.addClass('btn-success');
	//but.parent().removeClass('cancel1');
	//but.parent().addClass('call1');	
	}
	//setTimeout(function () { llamar(tel,idops); },1);
	}
	}
	peticion.open("POST","ajax/call.php",true);
	peticion.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	peticion.send("idops="+idops+"&tel="+tel);
}