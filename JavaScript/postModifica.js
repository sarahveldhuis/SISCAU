/**
 * version alterada pelo ST Mathias em (Mar/19)
 */

$(function(){
	$(document).on('submit', "#formModifica" , function(e){            
		e.preventDefault();
		isAlive(postModifica);		
		});
	$(document).on('click', "#cancelChange" , function(e){
		e.preventDefault();
		$("#Conteudo").empty();
    	$("#Conteudo").load("/SISCAU/HTML/modificar.html");
	});
         //********************inclusão troca adm (inclusão ST Mathias)*******************************         
         $(document).on('submit', "#formModificaAdm" , function(e){           
		e.preventDefault();
		isAlive(postModificaAdm);		
		});
	$(document).on('click', "#cancelChange" , function(e){
		e.preventDefault();
		$("#Conteudo").empty();
    	$("#Conteudo").load("/SISCAU/HTML/modificarAdm.html");
	});        
         //******************Fim inclusao adm (inclusão ST Mathias)********************************8
        
        
        
        
        
});
function postModifica(){
	$("#sendBt").off("click");
	$('#myModal').modal('hide');
	var formIds = ["#nomecompleto","#nomeguerra","#mail","#posto","#identidade","#cpf","#perfil" , "#VPN"];
	var data = new Object();
	
	for(i=0;i<8;i++){
		if($(formIds[i]).attr("disabled")=== undefined){
			if((formIds[i]== "#perfil") || (formIds[i]== "#VPN") ) {
				data['servicos'] = new Object();
				data['servicos']['Internet'] = document.getElementById("perfil").value;
				data['servicos']['VPN'] = document.getElementById("VPN").value;
			}else{
				data[formIds[i].slice(1)] = document.getElementById(formIds[i].slice(1)).value;
			}		
		}
	}
	data['dn'] = $("#formModifica").data('dn');        
	$.ajax({
		url:"/SISCAU/PHP/changeUser.php",
		type:"post",
		data: data,
		dataType : "json",
		success: function( response ) {
			resposta = response;                       
			if(!(resposta.ERROR)){
				var Table = JSON.parse(sessionStorage.usersTable);                               
				users = $.map(Table.users , function(n){
					var temp={};
					temp = n;
					if(n.dn == data.dn){
						if(!(data.nomecompleto===undefined)){
							data.cn = data.nomecompleto;
						}
						for(var key in n){
							if((key!="mail")&&!(data[key]===undefined)){
								if(key=='servicos'){
									for(var svKey in data[key]){
										temp[key][svKey] = data[key][svKey];
									}
								}else{
									temp[key] = data[key];
								}								
							}							
						}
						if((n.identidade===undefined)&&(!(data['identidade']===undefined))){
							temp['identidade'] = data['identidade'];
						}
						temp['dn']=resposta.dn;
					}
					return temp;
				});
				Table.users = users;
				sessionStorage.setItem('usersTable' , JSON.stringify(Table));
				if(!(data["mail"]===undefined)){
					alert("Modificação realizada com sucesso\nAguardando a confirmação do email pelo usuário");
				}else{
					alert("Modificação realizada com sucesso.")
				}
				$("#Conteudo").empty();
                                $("#Conteudo").load("/SISCAU/HTML/modificar.html");
			    
			}else{
				if(resposta.ERROR == "Erro de Colisão"){
					var alertmsg = "Os atributos listados abaixo ja estão cadastrados:\n";
					$.map(resposta.colided , function(val , key){
						alertmsg = alertmsg + key + "\n";
						
					});
					alert("Erro na modificação:\n" + alertmsg);					
				}else{
					alert("Erro no cadastro:\n" + resposta.ERROR);
				}				
			}			
	    }
	});
}

//***************************inclusao troca adm (inclusão ST Mathias)************************************
function postModificaAdm(){   
	$("#sendBt").off("click");
	$('#myModal').modal('hide');	
        var formIds = ["#cpf","#telephonenumber"];
	var data = new Object();
	
	for(i=0;i<2;i++){
		if($(formIds[i]).attr("disabled")=== undefined){			
                    data[formIds[i].slice(1)] = document.getElementById(formIds[i].slice(1)).value;	
		}
	}
	data['dn'] = $("#formModificaAdm").data('dn');        
	$.ajax({
		url:"/SISCAU/PHP/changeUserAdm.php",
		type:"post",
		data: data,
		dataType : "json",
		success: function( response ) {
			resposta = response;
			//if((resposta.ERROR == 0)){
				var Table = JSON.parse(sessionStorage.usersTableAdm);
				users = $.map(Table.users , function(n){
					var temp={};
					temp = n;
					if(n.dn == data.dn){
                                            if(!(data['cpf']===undefined)){
                                                temp['cpf']=data['cpf'];
                                            }
                                            if(!(data['telephonenumber']===undefined)){
                                                temp['telephonenumber']=data['telephonenumber'];
                                            }
                                            temp['dn']=resposta.dn;                                            
					}
					return temp;
				});
				Table.users = users;
				sessionStorage.setItem('usersTableAdm' , JSON.stringify(Table));				
				alert("Modificação realizada com sucesso.");
				
				$("#Conteudo").empty();
                                $("#Conteudo").load("/SISCAU/HTML/modificarAdm.html");
			    
			//}
			
	    },
                /*error: function(response) {
                    alert("Modificação NÃO realizada.");
                }*/
	});
}
//**************************Fim inclusao troca adm (inclusão ST Mathias)*********************************
	