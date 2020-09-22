/**
 * version 1.0
 */
function loginsess(callback , element){
	user     = document.getElementById("inputUser").value;
	password = document.getElementById("inputPassword").value;
	om       = $("#om").html();
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
	        	$("#credencialIncorreta").css("display" ,"none");
	        	if(element===undefined){
	        		callback();
	        	}else{
	        		callback(element);
	        	}
	        	
	        	
	        }else{
	        	$("#credencialIncorreta").css("display" ,"block");
	        	$("#credencialIncorreta").attr("class", "alert alert-danger");
	            $("#credencialIncorreta").html("<strong>"+ response.ERROR + "</strong>" );

	        }
			
	    }
	});
}