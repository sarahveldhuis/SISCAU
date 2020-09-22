/**
 * version alterada pelo ST Mathias em (Mar/19)
 */
$(function(){
	$("#cadastro_cpf").click(function(){

    	$("#Conteudo").empty();
    	$("#Conteudo").load("/SISCAU/HTML/cadastro.html" , function(){
    		$('[data-toggle="internet"]').popover({html:true,
            	content:sessionStorage.perfil});
    		$('[data-toggle="VPN"]').popover({html:true,
            	content:sessionStorage.vpn});
    	} );
	});
	$("#cadastro_passaporte").click(function(){

    	$("#Conteudo").empty();
    	$("#Conteudo").load("/SISCAU/HTML/cadastro_passaporte.html" , function(){
    		$('[data-toggle="internet"]').popover({html:true,
            	content:sessionStorage.perfil});
    		$('[data-toggle="VPN"]').popover({html:true,
            	content:sessionStorage.vpn});
    	} );
	});
	
    $("#ativos").click(function(){
    	$("#Conteudo").empty();
    	var  filtro= "cn=.*";
    	$("#Conteudo").append('<div id = "ConteudoTab" ></div>');
    	popula(filtro,"#ConteudoTab" , false , "usersAtivos");
    });
    $("#inativos").click(function(){
    	$("#Conteudo").empty();
    	var  filtro= "cn=.*";
    	$("#Conteudo").append('<div id = "ConteudoTab" ></div>');
    	popula(filtro,"#ConteudoTab" , false , "usersInativos");
    });
    $("#inativosvpn").click(function(){
    	$("#Conteudo").empty();
    	var  filtro= "cn=.*";
    	$("#Conteudo").append('<div id = "ConteudoTab" ></div>');
    	popula(filtro,"#ConteudoTab" , false , "usersInativosvpn");
    });
    $("#alterar").click(function(){
    	$("#Conteudo").empty();
    	$("#Conteudo").load("/SISCAU/HTML/modificar.html");
    });
    //***************inclusão do menu trocar administrador************************
    $("#trocaradm").click(function(){
    	$("#Conteudo").empty();
    	$("#Conteudo").load("/SISCAU/HTML/modificarAdm.html");
    });
   //***************Fim da inclusão do menu trocar administrador************************ 
    $("#termoGestor").click(function(){
    	$("#Conteudo").empty();
    	var div = $("<div class='termos'></div>").append(sessionStorage.divGestor);
    	$("#Conteudo").append(div);
    });
    $("#termoUsuario").click(function(){
    	$("#Conteudo").empty();
    	var div = $("<div class='termos'></div>").append(sessionStorage.divUser);
    	$("#Conteudo").append(div);

    });
    $("#helpModificar").click(function(){
    	$("#Conteudo").empty();
    	$("#Conteudo").load("/SISCAU/HTML/help_modificar.html");
/*    	$("#Conteudo").append('<video width="auto" height="480" controls>'+
    							'<source src="/SISCAU/Videos/excluir.mp4" type="video/mp4">'+
    							'<source src="/SISCAU/Videos/excluir.ogg" type="video/ogg">'+
    							'O seu navegador não suporta videos	'+
    							'</video>');*/

    });
    $("#sair").click(function(){
    	$.ajax({
    		url:"/SISCAU/PHP/logOut.php",
    		type:"get",
    		complete: function(){
    			window.location.assign("/SISCAU/HTML/login.html")
    		}
    	});
    });
    $("#cancelBt").click(function(){
    	$.ajax({
    		url:"/SISCAU/PHP/logOut.php",
    		type:"get",
    		complete: function(){
    			window.location.assign("/SISCAU/HTML/login.html")
    		}
    	});
    });
    
    	
    
});
