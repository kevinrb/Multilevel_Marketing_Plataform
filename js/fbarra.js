addEvent(window,'load',iniEvents,false);
var tid,tn=10000,tl=5000;
var inHtml = '';
//----------Inicializar------------------
function iniEvents()
{
  objdivrcbmnsj=document.getElementById('divrcbmnsj');
  objacuenta=document.getElementById('acuenta');
  addEvent(objacuenta, 'click', presionEnlace, false);
   objnoti=document.getElementById('barra_noti');
  addEvent(objnoti, 'click', presionEnlace, false);
  objamnsjs=document.getElementById('amnsjs');
  addEvent(objamnsjs, 'click', presionEnlace, false);
  objtxtescrbmnsj=document.getElementById('txtescrbmnsj');
  //addEvent(objtxtescrbmnsj, 'keydown', enviamnsj, false);
  //timer('nuevo');
}
function acomoda()
{
  objtidx2=document.getElementById('tidx2');
  xy=TamVentana();
  if(objtidx2.style.left == '5px')
    objtidx2.style.width = (xy[0]-8)+'px';
  else
    objtidx2.style.width = (xy[0]+183)+'px';
}
function TamVentana() {
  var Tam = [0, 0];
  if (typeof window.innerWidth != 'undefined')
    Tam = [window.innerWidth, window.innerHeight];
  else
    if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0)
      Tam = [document.documentElement.clientWidth, document.documentElement.clientHeight];
    else
      Tam = [document.getElementsByTagName('body')[0].clientWidth, document.getElementsByTagName('body')[0].clientHeight];
  return Tam;
}
//-----------Barra de Menu---------------
function presionEnlace(e)
{
  if (window.event)
  {
    window.event.returnValue=false;
    eobj=window.event.srcElement;
    desplegar(eobj);
  }
  else
    if (e)
    {
      e.preventDefault();
      eobj=e.target;
      desplegar(eobj);
    }
}
function desplegar(eobj)
{
  peobj=eobj.parentNode;
  if(peobj.className == 'openToggler')
  {
    peobj.className = '';
    clearTimeout(tid);
   // tid=setTimeout("timer('nuevo')", tn);
  }
  else
  {
    peobj.className = 'openToggler';
    heobj=peobj;
    while((heobj=heobj.nextSibling))
      heobj.className="";
    heobj=peobj;
    while((heobj=heobj.previousSibling))
      heobj.className="";
    if(peobj.id=="limnsjs")
    {
      objscont=document.getElementById('scont');
      objscont.style.display = 'none';
      objscont.lastChild.nodeValue = '0';
      leermnsjs();
    }
  }
}
var cnx=null;
/*
function leermnsjs()
{
  inHtml = '';
  clearTimeout(tid);
  cnx=crearXMLHttpRequest();
  cnx.onreadystatechange = rcbmnsj;
  cnx.open('GET', 'procmnsjs.php?opc=leer', true);
  cnx.send(null);
}
function rcbmnsj()
{
  if(cnx.readyState == 4)
  {
    objdivrcbmnsj=document.getElementById('divrcbmnsj');
    inHtml = cnx.responseText+inHtml;
    objdivrcbmnsj.innerHTML=inHtml;        
    tid=setTimeout("timer('leer')", tl);
  }
}
function enviamnsj(e)
{
  var kc = 0;

  if(window.event)
  {
    if(window.event.keyCode == 13)
    {
      kc = 13;
      window.event.returnValue = false;
      elem = window.event.srcElement;
    }
  }
  else
  {
    if(e.which == 13)
    {
      kc = 13;
      e.preventDefault();
      elem = e.target;
    }
  }
  if(kc == 13 && elem.value != '')
  {
    var_dest = document.getElementById('dest');

    if(var_dest.value != '-1')
    {
      emnsj = elem.value;
      clearTimeout(tid);
      cnx=crearXMLHttpRequest();
      cnx.onreadystatechange = rcbmnsj;
      cnx.open('GET', 'procmnsjs.php?mnsj='+emnsj+'&dest='+var_dest.value+'&opc=insertar', true);
      cnx.send(null);
      elem.value = "";
    }
    else
      alert('Elija destino');
  }
}
function timer(opcion)
{
  cnx=crearXMLHttpRequest();
  if(opcion == 'nuevo')
  {
    cnx.onreadystatechange = nuevomnsj;
    cnx.open('GET', 'procmnsjs.php?opc=nuevo', true);
  }
  if(opcion == 'leer')
  {
    cnx.onreadystatechange = rcbmnsj;
    cnx.open('GET', 'procmnsjs.php?opc=leernuevo', true);
  }
  cnx.send(null);
}
*/
function nuevomnsj()
{
  if(cnx.readyState == 4)
  {
    objscont=document.getElementById('scont');
    if(cnx.responseText != '0' && cnx.responseText.substring(0,5) != 'ERROR')
    {
      objscont.lastChild.nodeValue = cnx.responseText;
      objscont.style.display = 'block';
    }
    else
    {
      objscont.style.display = 'none';
      objscont.lastChild.nodeValue = '0';
    }
    tid=setTimeout("timer('nuevo')", tn);
  }
}
//Funciones Comunes
function addEvent(elemento,nomevento,funcion,captura)
{
  if (elemento.attachEvent)
  {
    elemento.attachEvent('on'+nomevento,funcion);
    return true;
  }
  else
    if (elemento.addEventListener)
    {
      elemento.addEventListener(nomevento,funcion,captura);
      return true;
    }
    else
      return false;
}
function crearXMLHttpRequest()
{
  var xmlHttp=null;
  if (window.ActiveXObject)
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  else
    if (window.XMLHttpRequest)
      xmlHttp = new XMLHttpRequest();
  return xmlHttp;
}