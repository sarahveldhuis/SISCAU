/**
 * version 1.0
 */
$(function(){
	$(document).on('click','#reativa',function(){
		isAlive(reativa , $(this));
	});
});
function reativa(element){
	$("#sendBt").off("click");
	$('#myModal').modal('hide');
	var data = {cpf : element.data("cpf")};
	$.ajax({
		url:"/SISCAU/PHP/sendRevalida.php",
		type:"post",
		data: data,
		dataType : "json",
		success: function( response ) {
	        if(!response.ERROR){
	        	alert(response.Status)
	        }else{
	        	alert(response.ERROR);
	        }
			
	    }
	});	
}