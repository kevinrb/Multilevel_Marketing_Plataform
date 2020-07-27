$(function(){

	$.each(descri, function(i, item){
		$("a[href=#"+item.ip+"]").data('des', item.des);
	});

	var doc = location.pathname.split("/").pop();

	delete window.descri;

	$("#accordion").collapse();

	$("#accordion h4 a").on('mousedown focus', function(){

		$("#preview").html("<h2>"+$(this).text()+"</h2><p>"+$(this).data('des')+"</p>");

		if(!$(this).hasClass(".loaded")){

			var body = $(this).attr('href');
			var id = body.substr(4);
			$(this).addClass(".loaded");

			$.post('ajax/elearning-ajax.php', {'id':id, 'doc': doc}, function(data){

				$(body+" .panel-body").prepend(data[0].html);

				$.each(data, function(i, item){if(i>0){
				
					$("h5#"+item.id).data('content', item.content);	

				}});

			}, "json"); }

	});

	$(".panel-body").on('click', 'h5', function(){

		if(!$(this).is(".additem")){
		
		$("#preview").html("<p>"+$(this).data('content')+"</p>");
		$("#preview").prepend("<h2>"+$(this).text()+"</h2>"); }

	});

});