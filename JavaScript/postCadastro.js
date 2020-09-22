/**
 * version 1.0
 */
$(function(){
	$(document).on('submit', "#formCadastro" , function(e){
		e.preventDefault();
		isAlive(postCadastro);
		
		
	});

});

function postCadastro(){
	$("#sendBt").off("click");
	$('#myModal').modal('hide');
	var newUsers = [];
	var nomeCompleto = document.getElementById("nomeCompleto").value;
	var nomeGuerra = document.getElementById("nomeGuerra").value;
	var mail = document.getElementById("mail").value;
	var Posto = document.getElementById("Posto").value;
	var Identidade = document.getElementById("Identidade").value;
	var CPF = document.getElementById("identificacao").value;
	mail = mail.toString();
	mail = mail.toLowerCase(); 
	CPF = CPF.toString();
	CPF = CPF.replace(/[^0-9]/g,'');
	var Perfil = ["Internet_" + document.getElementById("Perfil").value];       
	if(document.getElementById("VPN").value !== "0"){
		Perfil[1] = document.getElementById("VPN").value;
	}
	// Perfil[1] = ...
	nomeCompleto = nomeCompleto.toString();
	nomeCompleto = nomeCompleto.replace(/ +$/, "");
	nomeCompleto = nomeCompleto.replace(/^ +/, "");
	data = {
			nomeCompleto:nomeCompleto,
			nomeGuerra:nomeGuerra,
			mail:mail,
			Posto:Posto,
			Identidade:Identidade,
			cpf:CPF,
			Perfil:Perfil
			
	};
	var newUser = {
			cn:nomeCompleto,
			cpf:CPF,
			identidade:Identidade,
			mail:mail,
			nomeguerra:nomeGuerra,
			servicos:{Internet : document.getElementById("Perfil").value},
			posto:Posto,
	}
	if(confirm("Confirme os dados inseridos:\n" +
			   "Nome completo:"  + nomeCompleto +"\n" +
			   "Nome de guerra:" + nomeGuerra   +"\n" +
			   "E-mail:"         + mail         +"\n" +
			   "Posto:"          + Posto        +"\n" +
			   "Identidade:"     + Identidade   +"\n" +
			   "Identificação:  "          + CPF          +"\n" +
			   "Perfil:"         + Perfil 
	)){
		$.ajax({
			url:"/SISCAU/PHP/addUser.php",
			type:"post",
			data: data,
			dataType : "json",
			success: function( response ) {
				resposta = response;
				if(!(resposta.ERROR)){
					newUser['dn']=resposta.dn;
					Table = JSON.parse(sessionStorage.usersTable);
					Table.count++;
					Table.users.push(newUser);
					Table.usersInativos.push(newUser);
					sessionStorage.setItem('usersTable',JSON.stringify(Table));
					alert("Cadastro realizado com sucesso!");
				    document.getElementById("formCadastro").reset();
				    
				}else{
					alert("Erro no cadastro:\n" + resposta.ERROR );
					
				}
				
		    }
		});
	}
	
}
