/**
 * 
 */
$(function(){

	$.ajax({
		url:"/SISCAU/PHP/getUser.php",
		type:"get",
		dataType : "json",
		success: function( response ) {
			if(!response.Login){
				window.location = "/SISCAU/HTML/login.html";
			}else{
				$("#om").html(response.om);
				lugged = true;
			}			
	    },   	
	   
	});

	
});