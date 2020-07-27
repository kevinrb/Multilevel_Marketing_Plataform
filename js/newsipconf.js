$(function(){

	function isip(valor) {
         reg = /^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/  
         return valor.match(reg); 
    }

    //Se carga un anexo

	$("#anexo").change(function(){
		
		var anexo = this.value;

		if(anexo == 'newer'){

			$("[type=text], #callingpres #mohsuggest").val('');
			$("#name").removeAttr('readonly');
			$("#tipohost").val('dynamic').trigger('change');
			$("#type").val('friend');
			$("#context").val('internal');
			$("#nat").val('force_rport,comedia');
			$("#qualify").val('no');

		}else{	
			
			$("#name").attr('readonly', true);

			$.post('ajax/newsipconf_ajax.php', {'anexo': anexo}, function(data){
				$.each(data, function(i, item) {

					if(item.id=='host' && item.valor=='dynamic'){$("#tipohost").val('dynamic').trigger('change');}
					else if(item.id=='host' && isip(item.valor)){$("#tipohost").val('IPAddr').trigger('change');}
					else if(item.id=='host' && !isip(item.valor)){$("#tipohost").val('hostname').trigger('change');}

					$("#"+item.id).val(item.valor); $('#generate').show();

				});	
			}, "json");

		}

		$("form fieldset, [type=submit]").removeAttr('disabled');	
		
	});

	//Mostrar caja de IP

	$("#tipohost").change(function(){
		if(this.value!='dynamic'){$("#host").val('').show();}
		else{$("#host").hide(); $("#host").val('dynamic');}
	});

	//Envio de formulario

	$("form").submit(function(){
		if(!confirm("Desea guardar los cambios?")){return false;}
	});

	//Generar SIPconf

	$("#generate").click(function(){

		if(confirm("Generar archivo SIPconf?")){
			var anexo = $("#anexo").val();
			$.post('ajax/newsipconf_ajax.php', {accion: 'genanexo'}, function(data){
								
			});
		}	

	});

});