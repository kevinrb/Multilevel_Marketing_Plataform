function valida()
{
var patt1 = /^([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])\s([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$/;                         //HORA - FECHA
var patt2 = /^([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/;
var patt3 = /^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/;   
var patt4 = /(^\\s|[,.\\"\\'\\*\\!#~!¿¡\\'])/;
var patt5 = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
var patt6 = new RegExp("(\\D|\\s)");
var patt7 = new RegExp("(\\s|\\d)");
var patt8 = /^(((\d{1,3})(,\d{3})*)|(\d+))(.\d+)?$/;
$(".ver").each(function(){
obt=$(this);
vlr=obt.val();
if(vlr!="")
{
if(obt.hasClass("ver-hf"))
{ if(vlr.match(patt1)){obt.removeClass("itsbad"); obt.addClass("itsok");} else{obt.removeClass("itsok"); obt.addClass("itsbad");} }
if(obt.hasClass("ver-fe"))
{ if(vlr.match(patt2)){obt.removeClass("itsbad"); obt.addClass("itsok");} else{obt.removeClass("itsok"); obt.addClass("itsbad");} }
if(obt.hasClass("ver-ho"))
{ if(vlr.match(patt3)){obt.removeClass("itsbad"); obt.addClass("itsok");} else{obt.removeClass("itsok"); obt.addClass("itsbad");} }
if(obt.hasClass("ver-tc"))
{ if(!patt4.test(vlr)){obt.removeClass("itsbad"); obt.addClass("itsok");} else{obt.removeClass("itsok"); obt.addClass("itsbad");} }
if(obt.hasClass("ver-em"))
{ if(String(vlr).search (patt5) != -1){obt.removeClass("itsbad"); obt.addClass("itsok");} else{obt.removeClass("itsok"); obt.addClass("itsbad");} }
if(obt.hasClass("ver-nu"))
{ if(!patt6.test(vlr)){obt.removeClass("itsbad"); obt.addClass("itsok");} else{obt.removeClass("itsok"); obt.addClass("itsbad");} }
if(obt.hasClass("ver-ne"))
{ if(!patt7.test(vlr)){obt.removeClass("itsbad"); obt.addClass("itsok");} else{obt.removeClass("itsok"); obt.addClass("itsbad");} }
if(obt.hasClass("ver-float"))
{ if(!patt7.test(vlr)){obt.removeClass("itsbad"); obt.addClass("itsok");} else{obt.removeClass("itsok"); obt.addClass("itsbad");} }
}
else
{obt.removeClass("itsok"); obt.addClass("itsbad");}
});
if($(".itsbad").length==0)
{return true;}
else
{return false;}
} 