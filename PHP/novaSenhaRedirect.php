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
		$redirect = "/SISCAU/HTML/expired.html";
		header("location:$redirect");
	}else{		
		session_start();
		$userrdn = $info["users"][0]["dn"];
		$_SESSION["user"]["userdn"] = $userrdn;
		$redirect = "/SISCAU/HTML/recover.html";
		header("location:$redirect");

	}
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}finally {
	$con->close();
}
?>