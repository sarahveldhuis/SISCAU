/**
 * 
 */


$(function() { 
	$("#okBt").on('click', function(e){
		window.location = "http://www.google.com.br";
	});
	$("#refuseBt").on('click' , function(e){
		window.location = "http://intranet.2cta.eb.mil.br";
	});
	$("#envia").on('submit', function(e){
		e.preventDefault();
		data = {};
		data.aceite = [];
		inputs = $("#envia :input");
		inputs.each(function(){
				if(this.name !== ""){
					var patt = new RegExp("aceite");
					if(patt.test(this.name)){
						data.aceite.push(this.name.split("-")[1]);
					}
					if(this.name == "userpassword"){
						data.aceite.push("USER");
						data.userpassword = $(this).val();
					}
				}
		})
		console.log(data);
		$.ajax({
			url:"/SISCAU/PHP/firstLog.php",
			type:"post",
			data: data,
			dataType : "json",
			success: function( response ) {
		        if(response.ERROR == 0){
		        	$("#SuccessModal").modal();
		        			        }
				
		    }
		});
		        return false;

	});
	$(document).on('click','#contract',function(){
		var service = $(this).data('service');
		 $('.nav li.active').removeClass('active');
		$( "#li"+service ).addClass( "active" );
		$.ajax({
			url:"/SISCAU/Documentos/contrato" + service + ".txt" ,
			type:"get",
			success: function( response ) {
				$("#termos").empty();
				$("#termos").append(response);
			}	
		});
			
		});
	
	$.get("/SISCAU/PHP/contracts.php", function(data){
		$.get('/SISCAU/templates/contracts.mst', function(tablevis) { 
			var rendered = Mustache.render(tablevis , data);
			    $("#termoscont").append(rendered);
			    $( "#li"+data.services[0] ).addClass( "active" );
			    $.ajax({
					url:"/SISCAU/Documentos/contrato" + data.services[0] + ".txt" ,
					type:"get",
					success: function( response ) {
						$("#termos").append(response);
						$("#userlogin").val(data.mail);
						$("#login").append(data.mail);
					}	
				});
		 });
		if(!data.hasPass){
			$.get('/SISCAU/templates/password.mst', function(tablevis) { 
				var rendered = Mustache.render(tablevis);
				    $("#passcont").append(rendered);
				   
			 });
		}else {
			$('#termwrapper').removeClass('col-md-8');
			$( "#termwrapper" ).addClass( "col-md-8" );
		}
		    
	}, "json");
	
	
	
	

});
