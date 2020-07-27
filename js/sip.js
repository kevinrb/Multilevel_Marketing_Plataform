var cuadro="<div id='cuadro'></div>";
var lista="<div id='lista'></div>";
$(window).load(function(){
$('.container').append(lista);
$('.container').append(cuadro);
$.post("ajax/sip.php",{action:"data_list"},function(data){
$('#lista').append(data);

$('#select_sip').change(function(){
$.post("ajax/sip.php",{action:"consulta", info:$('#select_sip :selected').text()},function(data){	
$('#cuadro').html(data);


$('body').on("change","#host",function(){
if($("#host").val()=="IPAddr" || $("#host").val()=="hostname"){
if($('#nhost').length==0){
$("#host").parent().append("<input placeholder='Enter hostname / IP Address' name='nhost' id='nhost'>");
}} else {
$("#nhost").replaceWith("");
}
});

$('#cuadro').append("<input id='guarda' class='btn btn-success' type='button' value='Guardar'/>");	


$('#guarda').click(function(){
data=new Array();
cont=0;
$('#cuadro .input_data').each(function(){
temp=new Array();

if($(this).attr("id")=='host' && $('#nhost').length==1){
temp[0]=$(this).attr("id");
temp[1]=$('#nhost').val();
} else {
temp[0]=$(this).attr("id");
temp[1]=$(this).val();
}
data[cont]=temp;
cont++;
	
});

$.post("ajax/sip.php",{action:"guarda", data:data, cont:cont, info:$('#name').val()},function(data){
location.reload();
alert(data);
});

});
});
});

});
//$.post("ajax/sip.php",{action:"data_cuadro"},function(data){
$.post("ajax/sip.php",{action:"data_cuadr"},function(data){
$('#cuadro').html(data);
//$('#cuadro').append("<input id='guarda' type='button' value='Guardar'/>");

$('#guarda').click(function(){
data=new Array();
cont=0;
$('#cuadro .input_data').each(function(){

temp=new Array();
if($(this).attr("id")=='host' && $('#nhost').length==1){
temp[0]=$(this).attr("id");
temp[1]=$('#nhost').val();
} else {
temp[0]=$(this).attr("id");
temp[1]=$(this).val();
}
data[cont]=temp;
cont++;

});

$.post("ajax/sip.php",{action:"guarda", data:data, cont:cont, info:$('#name').val()},function(data){
location.reload();
alert(data);
});

});

});



});
