/**
 * version 1.0
 */


$(function() { 
	$("#refuseBt").on('click' , function(e){
		window.location = "/";
	});
	$("#envia").on('submit', function(e){
		e.preventDefault();
		var aceite = document.getElementById("aceite").checked;
		if(aceite){
			aceite=1;
		}else{
			aceite=0;
		}
		data = {
				aceite : aceite
				
		};
		$.ajax({
			url:"/SISCAU/PHP/aceiteOm.php",
			type:"post",
			data: data,
			dataType : "json",
			success: function( response ) {
		        if(response.ERROR == 0){
		        	window.location = "/SISCAU/HTML/portal.html";
		        }
				
		    }
		});
		        return false;

	});
	
	$.ajax({
		url:"/SISCAU/PHP/getLog.php",
		type:"get",
		dataType : "json",
		success: function( response ) {
			$("#userlogin").val(response.mail);
					
	    }
	});

});
