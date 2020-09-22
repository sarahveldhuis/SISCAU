/**
 * version 1.0
 */
$(function(){
	$(document).on('submit','#formFiltro',function(e){
		e.preventDefault();
		postFiltro(true);		
	});
});
function postFiltro(busca){
	if(busca){
		
		var attrmap = {
				"Nome Completo" : "cn",
				"Nome de guerra": "nomeguerra" ,
				"E-mail"        : "mail",
				"Posto"         : "posto",
				"Identidade"    : "identidade",
				"CPF"           : "cpf",
				"Perfil"        : "perfil"
		};
		var option = attrmap[document.getElementById("filtroAttr").value];
		var value  = document.getElementById("filtro").value;
		var filtro = option+"="+value;
		$("#ConteudoMod").empty();
    	popula(filtro,"#ConteudoMod" ,true , "users");
	}else{
		document.getElementById("filtro").value = "";
		var  filtro= "cn=.*";
		$("#ConteudoMod").empty();
		popula(filtro,"#ConteudoMod", true , "users");
	}
}
