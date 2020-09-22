/**
 * version alterada pelo ST Mathias em (Mar/19)
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
	
// ********************Armazena na session os dados de todo pessoal da OM********************
       filter = "(&(cn=*)(!(cn=admin*))(objectClass=userCta))";      
	var erro;
	$.ajax({
		url:"/SISCAU/PHP/search.php",
		type:"post",               
                data: {filtro:filter},
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
        
     // ********************Armazena na session os dados do Admin da OM (ST Mathias)********************
      filteradm = "(&(cn=admin)(aceite=1))";
        $.ajax({
		url:"/SISCAU/PHP/searchAdm.php",
		type:"post",                
                data: {filtro:filteradm},
		dataType : "json",
		success: function( response ) {                    
                        
			if(response.ERROR === undefined){
				if(typeof(Storage) !== "undefined") {
					sessionStorage.setItem("usersTableAdm", JSON.stringify(response));
				} else {
				    alert("Atualize o seu navegador para ter acesso a sistema!")
				}
			}else {
				erro = response.ERROR;
			}			 
		},
                error: function(response) {
                    alert("Data not found");
                }
		
	});
        
        
        //****************************************************
        //*************Faz chamada ao alerta p os administradores no portal.html (ST Mathias)****************
		$('#ModalAdm').modal();
});


