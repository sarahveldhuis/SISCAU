/**
 * version 1.0
 */


$(function() { 
	$("#envia").on('submit', function(e){
		user = document.getElementById("inputUser").value;
		data = {
				user:user,
				
		};
		$.ajax({
			url:"/SISCAU/PHP/recoverUuid.php",
			type:"post",
			data: data,
			dataType : "json",
			success: function( response ) {
		        if(!response.ERROR){
		        	$("#credencialIncorreta").attr("class", "alert alert-info");
		            $("#credencialIncorreta").html("Email enviado para " + response.email);
		        }else{
		        	alert(response.ERROR);
		        }
				
		    }
		});
		        return false;

	});

});
