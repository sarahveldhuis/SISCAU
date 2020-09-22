/**
 * version 1.0
 */
$(function(){
	// Carrega o termo do usuário
	$.ajax({
		url:"/SISCAU/Documentos/contratoUser.txt",
		type:"get",
		success: function( response ) {
			sessionStorage.setItem("divUser", response);
		}	
	});
	$(document).on('click','#botoesdesc',function(){
		$("#video_container").empty();
		$("#video_container").append('<video width="auto" height="480" controls>'+
							  '<source src="/SISCAU/Videos/botoes.mp4" type="video/mp4">'+
							  '<source src="/SISCAU/Videos/botoes.ogg" type="video/ogg">'+
							  'O seu navegador não suporta videos	'+
							  '</video>');
	});
	$(document).on('click','#mudasenhahelp',function(){
		$("#video_container").empty();
		$("#video_container").append('<video width="auto" height="480" controls>'+
							  '<source src="/SISCAU/Videos/mudasenha.mp4" type="video/mp4">'+
							  '<source src="/SISCAU/Videos/mudasenha.ogg" type="video/ogg">'+
							  'O seu navegador não suporta videos	'+
							  '</video>');
	});
	$(document).on('click','#editahelp',function(){
		$("#video_container").empty();
		$("#video_container").append('<video width="auto" height="480" controls>'+
							  '<source src="/SISCAU/Videos/edita.mp4" type="video/mp4">'+
							  '<source src="/SISCAU/Videos/edita.ogg" type="video/ogg">'+
							  'O seu navegador não suporta videos	'+
							  '</video>');
	});
	$(document).on('click','#excluihelp',function(){
		$("#video_container").empty();
		$("#video_container").append('<video width="auto" height="480" controls>'+
							  '<source src="/SISCAU/Videos/excluir.mp4" type="video/mp4">'+
							  '<source src="/SISCAU/Videos/excluir.ogg" type="video/ogg">'+
							  'O seu navegador não suporta videos	'+
							  '</video>');
	});
});