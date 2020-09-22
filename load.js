/**
 * version 1.0
 */
function isAlive(callback , element){
	var saida;
	$.ajax({
		url:"/SISCAU/PHP/isAlive.php",
		type:"get",
		dataType : "json",
		success: function( response ) {
			if(!(response.ERROR===undefined)){
				$('#myModal').modal({
				  keyboard: false
				})
				if(element===undefined){
					$("#sendBt").click(function(){ loginsess(callback)});
				}else{
					$("#sendBt").click(function(){ loginsess(callback , element)});
				}
				
			}else{
				if(element===undefined){
					callback();
				}else{
					callback(element);
				}
				
			}			
	    },   	
	   
	});
	return saida;
}

$(function(){
	

// Carrega Perfil
	$.ajax({
		url:"/SISCAU/Documentos/perfil.txt",
		type:"get",
		success: function( response ) {
			sessionStorage.setItem("perfil", response);
		}	
	});
	
	$.ajax({
		url:"/SISCAU/Documentos/vpn.txt",
		type:"get",
		success: function( response ) {
			sessionStorage.setItem("vpn", response);
		}	
	});	
	
// Carrega a tabela de dados da OM

	var erro;
	$.ajax({
		url:"/SISCAU/PHP/search.php",
		type:"post",
		data : {filtro : "(&(cn=*)(!(cn=admin*))(objectClass=userCta))"},
		dataType : "json",
		success: function( response ) {
			if(response.ERROR === undefined){
				if(typeof(Storage) !== "undefined") {
					sessionStorage.setItem("usersTable", JSON.stringify(response));
				} else {
				    alert("Atualize o seu navegador para ter acesso a sistema!")
				}
			}else {
				erro = response.ERROR;
			}			 
		},
		complete: function(){
			if(erro === undefined){
				if(typeof(Storage) !== "undefined") {
					$("body").attr("class" , "loaded");
					$("#Conteudo").empty();
					var div = $("<div class='termos'></div>").append(sessionStorage.divGestor);
					$("#Conteudo").append(div);
				}
			}else {
				alert("Erro na inicialização:\n" + erro );
				window.location = "/SISCAU/HTML/login.html";
			}
		}
	});
	
});


