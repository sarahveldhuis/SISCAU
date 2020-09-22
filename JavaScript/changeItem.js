/**
 * version alterada pelo ST Mathias em (Mar/19)
 */
$(function(){
	$(document).on('click','#modificar',function(){
		var table = JSON.parse(sessionStorage.usersTable);
		var cpf = $(this).data('cpf');
		var saida = $.map(table.users , function(n){
			if(n.cpf == cpf){
				return n;
			}
			
		});
		$("#Conteudo").empty();
		$("#Conteudo").load("/SISCAU/HTML/modificarForm.html", function(){
		$('[data-toggle="internet"]').popover({html:true,content:sessionStorage.perfil});
    		$('[data-toggle="VPN"]').popover({html:true,content:sessionStorage.vpn});
            
			$("#formModifica").data('dn', saida[0]['dn']);
			$("#nomecompleto").val(saida[0]["cn"]);
			$("#nomeguerra").val(saida[0]["nomeguerra"]);
			$("#mail").val(saida[0]["mail"]);
			$("#posto").val(saida[0]["posto"]);
			if (!(saida[0]["identidade"] === undefined)) {
                        $("#identidade").val(saida[0]["identidade"]);
                        }	
			$("#cpf").val(saida[0]["cpf"]);
			$("#cn").val(saida[0]["cn"]);
			$("#perfil").val(saida[0]["servicos"]["Internet"]);
                        //Alteração para resolver o problema da caixa de seleção VPN, q não ficava selecionada ao editar o form (alteração ST Mathias)
                        var vpn = saida[0]["servicos"]["VPN"];
                        if(vpn == "VPN"){
                            vpn = 1;
                        } if(vpn == "VPN_C2"){
                            vpn = 2;
                        }
                        $('#VPN option[value="' + vpn + '"]').attr({ selected : "selected" });
			//Fim da para alteração resolver o problema da caixa de seleção VPN, q não ficava selecionada ao editar o form (alteração ST Mathias)
		});
    	
    });
    
    //******************inclusao do troca admin (inclusão ST Mathias)************************************
    $(document).on('click','#modificarAdm',function(){
		var table = JSON.parse(sessionStorage.usersTableAdm);
		var cpf = $(this).data('cpf');               
		var saida = $.map(table.users , function(n){
			if(n.cpf == cpf){
				return n;
			}
			
		});
                //****** Parte do código q preenche a caixa de seleção com o militares da om******
                $.ajax({
		url:"/SISCAU/PHP/getCpf.php",
		type:"get",
		dataType : "json",
		success: function( response ) {
			var data = response;
                        data.cpfs.sort(function(a,b){
				if(a.antigo>b.antigo){
					return 1;
				}else {
					return -1;
				}
			});
			
			$.get('/SISCAU/templates/cpf.mst', function(tablevis) {
			    var rendered = Mustache.render(tablevis, data);
                            $("#cpfAdm").empty;
			    $("#cpfAdm").append(rendered);                           
                            $('#cpf option[value="' + cpf + '"]').attr({ selected : "selected" });                            
			 });		
                    }
                }); 
                //****** Fim da Parte do código q preenche a caixa de seleção com o militares da om******
		$("#Conteudo").empty();
		$("#Conteudo").load("/SISCAU/HTML/modificarFormAdm.html", function(){
			
			$("#formModificaAdm").data('dn', saida[0]['dn']);
                        $("#telephonenumber").val(saida[0]["telephonenumber"]);
		});
    	
    });    
    //*****************Fim inclusao troca adm*************************************
    
    
    
    
	$(document).on('click','#locker',function(){
		itemId = this.dataset.formitem;
		estadodis = $("#"+itemId).attr("disabled");
		$("#"+itemId).attr("disabled" , !estadodis);
		estadoreq = $("#"+itemId).attr("required");
		$("#"+itemId).attr("required" , !estadoreq);
		if(itemId == "CPF"){
			validaCpf($("#"+itemId).val());
		}
	});
});
	
