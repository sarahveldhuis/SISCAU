<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();


$path = $_SESSION["user"]["userdn"];

$data["userPassword"] = "{MD5}" . base64_encode(pack('H*',md5($_POST["userPassword"]))) ;
$data["uuid"] = array();

try{
	$con = new Ldap();
	$adminData = $con->getAdminData();
	$con->bind($adminData["user"], $adminData["pass"]);
	$con->modify($path, $data);
	$my = array('Status'=>'Alteração ok' , 'ERROR'=> 0);
	$myJSON = json_encode($my);
	echo($myJSON);
	$sessao->destroy();	

}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no Cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}finally {
	$con->close();
}
?>
