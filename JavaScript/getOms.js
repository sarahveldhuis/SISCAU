/**
 * version 1.0
 */
$(function(){
// Carrega e exibe o termo do gestor de internet
	$.ajax({
		url:"/SISCAU/Documentos/contratoGestor.txt",
		type:"get",
		success: function( response ) {
			sessionStorage.setItem("divGestor", response);
		}	
	});
	$.ajax({
		url:"/SISCAU/PHP/getOms.php",
		type:"get",
		dataType : "json",
		success: function( response ) {
			var data = response;
			data.oms.sort(function(a,b){
				if(a.om>b.om){
					return 1;
				}else {
					return -1;
				}
			});
			
			$.get('/SISCAU/templates/login.mst', function(tablevis) {
			    var rendered = Mustache.render(tablevis, data);
			    $("#logincont").append(rendered);
			 });		
	    }
	});
	
	
});
