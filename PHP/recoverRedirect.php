<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();
$sessao->destroy();
$sessao->start();

extract($_GET);


$base_dn = "DC=eb,DC=mil,DC=br";
$filter = "(&(uuid=". $uuid . ")(objectClass=userCta))";


try{
	$con = new Ldap();
	$adminData = $con->getAdminData();
	$con->bind($adminData["user"], $adminData["pass"]);
	$con->search($base_dn, $filter);
	$info = $con->getEntries()->parseUsers();
	if($info["count"]==0){
		$sessao->destroy();
		$redirect = "/SISCAU/HTML/expired.html";
		header("location:$redirect");
	}else{
		$_SESSION["user"]["userdn"] = $info["users"][0]["dn"];
		$redirect = "/SISCAU/HTML/recover.html";
		header("location:$redirect");
	}

}catch (LdapException $e){
	$sessao->destroy();
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no Cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}finally {
	$con->close();
}


?>