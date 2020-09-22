/**
 * version 1.0
 */


$(function() { 
	$(document).on('submit',"#envia",function(e){
		om = document.getElementById("inputOM").value;
		user = document.getElementById("inputUser").value;
		password = document.getElementById("inputPassword").value;
		data = {
				om:om,
				user:user,
				password:password
				
		};
		$.ajax({
			url:"/SISCAU/PHP/loginRedirect.php",
			type:"post",
			data: data,
			dataType : "json",
			success: function( response ) {
		        if(!response.ERROR){
		        	window.location = response.redirect;
		        	
		        }else{
		        	$("#credencialIncorreta").attr("class", "alert alert-danger");
		            $("#credencialIncorreta").html("<strong>"+ response.ERROR + "</strong>" );

		        }
				
		    }
		});
		        return false;

	});

});
