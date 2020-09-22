/**
 * version 1.0
 */


$(function() { 
	$("#envia").on('submit', function(e){
		userPassword = document.getElementById("password").value;
		data = {
				userPassword:userPassword,
				
		};
		$.ajax({
			url:"/SISCAU/PHP/recoverPost.php",
			type:"post",
			data: data,
			dataType : "json",
			success: function( response ) {
		        if(!response.ERROR){
		        	alert("Senha alterada com sucesso!");
		        	window.location = "http://intranet.2cta.eb.mil.br/";
		        }
				
		    }
		});
		        return false;

	});

});
