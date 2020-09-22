<?php /* version 1.0 */

include "funcoes.php";
include "SessionClass.php";

$sessao = new Session();

try{
	$adminData = $sessao->getAdminData();
	$sessao->hijackPrevent();
	$my = array('Status'=>'Success' );
	$myJSON = json_encode($my);
	echo($myJSON);
}catch (SessionException $e){
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$my = array('Status'=>'Erro de sessão' , 'ERROR'=> $e->getMessage() , 'ERRCODE' => $e->getCode());
	$sessao->destroy();
	$myJSON = json_encode($my);
	echo($myJSON);
}