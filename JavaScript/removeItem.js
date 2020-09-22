/**
 * version 1.0
 */
$(function(){
	$(document).on('click','#remover',function(){
		isAlive(remove , $(this));
		
		
    });
});
function remove(element){
	$("#sendBt").off("click");
	$('#myModal').modal('hide');
	var usersTable = JSON.parse(sessionStorage.getItem('usersTable'));
	var cpf = element.data("cpf");
	var user = $.map(usersTable.users , function(n){
		if(n.cpf == cpf){
			dados = {
					nomeCompleto : n.cn,
					cpf          : n.cpf,
					dn           : n.dn
			};
			return dados;
		}
	});
	if(confirm("Confirma a exclusão do: " +"\n" +
			 user[0].nomeCompleto+ "\n" +
			"CPF= " + user[0].cpf
	)){
	   	$.ajax({
	   		url:"/SISCAU/PHP/delUser.php",
	   		type:"post",
	   		data : {dn : user[0].dn},
	   		dataType : "json",
	   		success: function( saida ) {
	   			if (!(saida.ERROR === 0)) {
	   			    alert(saida.ERROR);
	   			}else{
	   				users = $.map(usersTable.users , function(n){
	   					if(n.cpf != cpf){
	   						return n;
	   					}
	   				});
	   				var usersAtivos = $.map(usersTable.usersAtivos , function(n){
	   					if(n.cpf != cpf){
	   						return n;
	   					}
	   				});
	   				var usersInativos = $.map(usersTable.usersInativos , function(n){
	   					if(n.cpf != cpf){
	   						return n;
	   					}
	   				});
	   				usersTable.users = users;
	   				usersTable.usersAtivos = usersAtivos;
	   				usersTable.usersInativos = usersInativos;
	   				usersTable.count -= 1;
	   				sessionStorage.setItem("usersTable", JSON.stringify(usersTable)); 
	   				$("#Conteudo").empty();
	   				$("#Conteudo").load("/SISCAU/HTML/modificar.html");
	   			}
	   			
	   			
	   	    }
	   	});
	}
}