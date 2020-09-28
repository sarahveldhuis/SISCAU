/**
 * Função de validacao de CPF
 * version 1.0
 */

function validaCpf(cpf) {
	if($('#identificacao').val() == 1) {
	cpf = cpf.toString();
	cpf = cpf.replace(/[^0-9]/g,'');
	var Soma;
	var Resto;
	Soma = 0;
	if(/(.)\1{10}/.test(cpf)){
		document.getElementById("CPF").setCustomValidity('Cpf Invalido');
		return false;
	}
	for (i=1; i<=9; i++){
		Soma = Soma + parseInt(cpf.substring(i-1, i)) * (11 - i);
	}
	Resto = (Soma)%11;
	if(Resto < 2) {
		Resto = 0;
	}else{
		Resto = 11 - Resto;
	}
	if(Resto != parseInt(cpf.substring(9, 10))){
		document.getElementById("CPF").setCustomValidity('Cpf Invalido');
		return false;
	}
	Soma=0;
	for (i = 1; i <= 10; i++){
       		Soma = Soma + parseInt(cpf.substring(i-1, i)) * (12 - i);
	}
	Resto = (Soma)%11;
	if(Resto < 2) {
		Resto = 0;
	}else{
		Resto = 11 - Resto;
	}
	if(Resto != parseInt(cpf.substring(10, 11))){
		document.getElementById("CPF").setCustomValidity('Cpf Invalido');
		return false;
	}
	document.getElementById("CPF").setCustomValidity('');
}
	return true;
    
}
// Funcao de validacao de senhas
function validaPass(){
	if($("#password").val() != $("#password2").val()){
		document.getElementById("password2").setCustomValidity('Senhas nao coincidem');
	}else{
		document.getElementById("password2").setCustomValidity('');

	}

}

// Funcao de verificacao de padrao de senha

function checkPass(){
	var input = $("#password").val();
	var qnt = /.{8,}/;
	var maiuscula = /.*[A-Z]/;
	var minuscula = /.*[a-z]/;
	var numero = /.*\d/;
	var car = /.*[!-/]|.*[:-@]|.*[\[-_]|.*[{-}]/;
	if(qnt.test(input)){
		$("#glyqnt").attr("class", "glyphicon glyphicon-ok");
		$("#cglyqnt").attr("color", "green");
	}else{
		$("#glyqnt").attr("class", "glyphicon glyphicon-remove");
		$("#cglyqnt").attr("color", "red");
	}
	if(maiuscula.test(input)){
		$("#glymai").attr("class", "glyphicon glyphicon-ok");
		$("#cglymai").attr("color", "green");
	}else{
		$("#glymai").attr("class", "glyphicon glyphicon-remove");
		$("#cglymai").attr("color", "red");
	}
	if(minuscula.test(input)){
		$("#glymin").attr("class", "glyphicon glyphicon-ok");
		$("#cglymin").attr("color", "green");
	}else{
		$("#glymin").attr("class", "glyphicon glyphicon-remove");
		$("#cglymin").attr("color", "red");
	}
	if(numero.test(input)){
		$("#glynum").attr("class", "glyphicon glyphicon-ok");
		$("#cglynum").attr("color", "green");
	}else{
		$("#glynum").attr("class", "glyphicon glyphicon-remove");
		$("#cglynum").attr("color", "red");
	}
	if(car.test(input)){
		$("#glycar").attr("class", "glyphicon glyphicon-ok");
		$("#cglycar").attr("color", "green");
	}else{
		$("#glycar").attr("class", "glyphicon glyphicon-remove");
		$("#cglycar").attr("color", "red");
	}
}
