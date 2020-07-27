$(document).ready(function(){


$('#camp').change(function(){

var tt=$('#camp :selected').text();
$('#titulo').text(tt);

var idc=$('#camp :selected').attr('name');
var c=1;
var idcur=$('#idcur').val();



var items = [];
$('.highlight').each(function(){
 items.push($(this).attr('name')); 
   });
var result = items.join(',');

var n=items.toString();


var items2 = [];
$('.desasig').each(function(){
 items2.push($(this).attr('name')); 
   });
var result2 = items2.join(',');

var n2=items2.toString();


if(n || n2){
var res=confirm("DESEA GUARDAR CAMBIOS ?");

if (res==true)
  {
     if(n){

if(n2){
var c=5;
$.ajax({
	type: 'POST',
	url: 'save.php',
	data: {nom: n, nom2:n2, idcamp: idcur, idcamb:idc , tar:c},
	success: function(data){
			$('#contenedor').empty();
			$('#contenedor').append(data);
			var tem=$('#camp :selected').attr('name');
            $('#idcur').val(tem);
			
///////////////////////////////////////////////////			
$('.nombre').mousedown(function(){
 
$(this).toggleClass("nombre").toggleClass("highlight");
  
 $('.nombre').mouseover(function(){
 $(this).toggleClass("nombre").toggleClass("highlight");
 
 });
 
 $('.highlight').mouseover(function(){
 $(this).toggleClass("highlight").toggleClass("nombre");
 });
 
 
 });

$('.nombre').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////			
			
///////////////////////////////////////////////////			
$('.asig').mousedown(function(){
 
$(this).toggleClass("asig").toggleClass("desasig");
  
 $('.asig').mouseover(function(){
 $(this).toggleClass("asig").toggleClass("desasig");
 
 });
 
 $('.desasig').mouseover(function(){
 $(this).toggleClass("desasig").toggleClass("asig");
 });
 
 
 });

$('.asig').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////

	}
     });
}

else{
var c=6;
$.ajax({
	type: 'POST',
	url: 'save.php',
	data: {nom: n, idcamp: idcur, idcamb:idc, tar:c},
	success: function(data){
			$('#contenedor').empty();
			$('#contenedor').append(data);
			var tem=$('#camp :selected').attr('name');
            $('#idcur').val(tem);
			
///////////////////////////////////////////////////			
$('.nombre').mousedown(function(){
 
$(this).toggleClass("nombre").toggleClass("highlight");
  
 $('.nombre').mouseover(function(){
 $(this).toggleClass("nombre").toggleClass("highlight");
 
 });
 
 $('.highlight').mouseover(function(){
 $(this).toggleClass("highlight").toggleClass("nombre");
 });
 
 
 });

$('.nombre').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////			
			
///////////////////////////////////////////////////			
$('.asig').mousedown(function(){
 
$(this).toggleClass("asig").toggleClass("desasig");
  
 $('.asig').mouseover(function(){
 $(this).toggleClass("asig").toggleClass("desasig");
 
 });
 
 $('.desasig').mouseover(function(){
 $(this).toggleClass("desasig").toggleClass("asig");
 });
 
 
 });

$('.asig').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////

	}
     });
}
	 	 
	 }


	 
else if(n2){

var items2 = [];
$('.desasig').each(function(){
 items2.push($(this).attr('name')); 
   });
var result2 = items2.join(',');

var n2=items2.toString();
var c=7;

$.ajax({
	type: 'POST',
	url: 'save.php',
	data: {nom2: n2, idcamp: idcur, idcamb:idc, tar:c},
	success: function(data){
			$('#contenedor').empty();
			$('#contenedor').append(data);
			var tem=$('#camp :selected').attr('name');
            $('#idcur').val(tem);
			
///////////////////////////////////////////////////			
$('.nombre').mousedown(function(){
 
$(this).toggleClass("nombre").toggleClass("highlight");
  
 $('.nombre').mouseover(function(){
 $(this).toggleClass("nombre").toggleClass("highlight");
 
 });
 
 $('.highlight').mouseover(function(){
 $(this).toggleClass("highlight").toggleClass("nombre");
 });
 
 
 });

$('.nombre').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////			
			
///////////////////////////////////////////////////			
$('.asig').mousedown(function(){
 
$(this).toggleClass("asig").toggleClass("desasig");
  
 $('.asig').mouseover(function(){
 $(this).toggleClass("asig").toggleClass("desasig");
 
 });
 
 $('.desasig').mouseover(function(){
 $(this).toggleClass("desasig").toggleClass("asig");
 });
 
 
 });

$('.asig').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////

	}
     });
} 
  }
else
  {
  $.ajax({
	type: 'POST',
	url: 'save.php',
	data: { idcamp: idc, tar:c},
	success: function(data){
			$('#contenedor').empty();
			$('#contenedor').append(data);
			var tem=$('#camp :selected').attr('name');
            $('#idcur').val(tem);
			
///////////////////////////////////////////////////			
$('.nombre').mousedown(function(){
 
$(this).toggleClass("nombre").toggleClass("highlight");
  
 $('.nombre').mouseover(function(){
 $(this).toggleClass("nombre").toggleClass("highlight");
 
 });
 
 $('.highlight').mouseover(function(){
 $(this).toggleClass("highlight").toggleClass("nombre");
 });
 
 
 });

$('.nombre').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////			
			
///////////////////////////////////////////////////			
$('.asig').mousedown(function(){
 
$(this).toggleClass("asig").toggleClass("desasig");
  
 $('.asig').mouseover(function(){
 $(this).toggleClass("asig").toggleClass("desasig");
 
 });
 
 $('.desasig').mouseover(function(){
 $(this).toggleClass("desasig").toggleClass("asig");
 });
 
 
 });

$('.asig').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////

	}
	
	});
  } 

  
 }

else{
$.ajax({
	type: 'POST',
	url: 'save.php',
	data: { idcamp: idc, tar:c},
	success: function(data){
			$('#contenedor').empty();
			$('#contenedor').append(data);
			var tem=$('#camp :selected').attr('name');
            $('#idcur').val(tem);
			
///////////////////////////////////////////////////			
$('.nombre').mousedown(function(){
 
$(this).toggleClass("nombre").toggleClass("highlight");
  
 $('.nombre').mouseover(function(){
 $(this).toggleClass("nombre").toggleClass("highlight");
 
 });
 
 $('.highlight').mouseover(function(){
 $(this).toggleClass("highlight").toggleClass("nombre");
 });
 
 
 });

$('.nombre').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////			
			
///////////////////////////////////////////////////			
$('.asig').mousedown(function(){
 
$(this).toggleClass("asig").toggleClass("desasig");
  
 $('.asig').mouseover(function(){
 $(this).toggleClass("asig").toggleClass("desasig");
 
 });
 
 $('.desasig').mouseover(function(){
 $(this).toggleClass("desasig").toggleClass("asig");
 });
 
 
 });

$('.asig').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////

	}
	
	});
}

});


$('#save').click(function(){

var idc=$('#camp :selected').attr('name');

if(idc==0){
alert('ELIJA UNA CAMPAÑA');
}

else{
var items = [];
$('.highlight').each(function(){
 items.push($(this).attr('name')); 
   });
var result = items.join(',');

var n=items.toString();


var items2 = [];
$('.desasig').each(function(){
 items2.push($(this).attr('name')); 
   });
var result2 = items2.join(',');

var n2=items2.toString();





if(n){

if(n2){
var c=2;
$.ajax({
	type: 'POST',
	url: 'save.php',
	data: {nom: n, nom2:n2, idcamp: idc, tar:c},
	success: function(data){
			$('#contenedor').empty();
			$('#contenedor').append(data);
			
			
///////////////////////////////////////////////////			
$('.nombre').mousedown(function(){
 
$(this).toggleClass("nombre").toggleClass("highlight");
  
 $('.nombre').mouseover(function(){
 $(this).toggleClass("nombre").toggleClass("highlight");
 
 });
 
 $('.highlight').mouseover(function(){
 $(this).toggleClass("highlight").toggleClass("nombre");
 });
 
 
 });

$('.nombre').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////			
			
///////////////////////////////////////////////////			
$('.asig').mousedown(function(){
 
$(this).toggleClass("asig").toggleClass("desasig");
  
 $('.asig').mouseover(function(){
 $(this).toggleClass("asig").toggleClass("desasig");
 
 });
 
 $('.desasig').mouseover(function(){
 $(this).toggleClass("desasig").toggleClass("asig");
 });
 
 
 });

$('.asig').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////

	}
     });
}

else{
var c=3;
$.ajax({
	type: 'POST',
	url: 'save.php',
	data: {nom: n, idcamp: idc, tar:c},
	success: function(data){
			$('#contenedor').empty();
			$('#contenedor').append(data);
			
			
///////////////////////////////////////////////////			
$('.nombre').mousedown(function(){
 
$(this).toggleClass("nombre").toggleClass("highlight");
  
 $('.nombre').mouseover(function(){
 $(this).toggleClass("nombre").toggleClass("highlight");
 
 });
 
 $('.highlight').mouseover(function(){
 $(this).toggleClass("highlight").toggleClass("nombre");
 });
 
 
 });

$('.nombre').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////			
			
///////////////////////////////////////////////////			
$('.asig').mousedown(function(){
 
$(this).toggleClass("asig").toggleClass("desasig");
  
 $('.asig').mouseover(function(){
 $(this).toggleClass("asig").toggleClass("desasig");
 
 });
 
 $('.desasig').mouseover(function(){
 $(this).toggleClass("desasig").toggleClass("asig");
 });
 
 
 });

$('.asig').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////

	}
     });
}


	 
	 
	 
	 
	 
	 
	 }


	 
else if(n2){

var items2 = [];
$('.desasig').each(function(){
 items2.push($(this).attr('name')); 
   });
var result2 = items2.join(',');

var n2=items2.toString();
var c=4;

$.ajax({
	type: 'POST',
	url: 'save.php',
	data: {nom2: n2, idcamp: idc, tar:c},
	success: function(data){
			$('#contenedor').empty();
			$('#contenedor').append(data);
			
			
///////////////////////////////////////////////////			
$('.nombre').mousedown(function(){
 
$(this).toggleClass("nombre").toggleClass("highlight");
  
 $('.nombre').mouseover(function(){
 $(this).toggleClass("nombre").toggleClass("highlight");
 
 });
 
 $('.highlight').mouseover(function(){
 $(this).toggleClass("highlight").toggleClass("nombre");
 });
 
 
 });

$('.nombre').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////			
			
///////////////////////////////////////////////////			
$('.asig').mousedown(function(){
 
$(this).toggleClass("asig").toggleClass("desasig");
  
 $('.asig').mouseover(function(){
 $(this).toggleClass("asig").toggleClass("desasig");
 
 });
 
 $('.desasig').mouseover(function(){
 $(this).toggleClass("desasig").toggleClass("asig");
 });
 
 
 });

$('.asig').mouseup(function(){
  $('div').unbind('mouseover');
 });
////////////////////////////////////////////////////

	}
     });
}	 
	 
	 
else{
alert('ELIJA ALGO');
}





}
});


$('.nombre').click(function(){
alert('ELIJA UNA CAMPAÑA');
});
 
 

});