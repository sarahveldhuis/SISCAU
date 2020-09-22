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
		
		$userrdn = $info["users"][0]["dn"];
		$_SESSION["user"]["userpath"] = $userrdn;
		$_SESSION["user"]["mail"] = $mail;
		if($mail != $info["users"][0]["mail"]){
			$data["mail"]= $mail;
			$con->modify($info["users"][0]["dn"], $data);
		}
		$redirect = "/SISCAU/HTML/contratos.html";
		header("location:$redirect");
	}
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}catch (SessionException $e){
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$my = array('Status'=>'Erro de sessão' , 'ERROR'=> $e->getMessage() , 'ERRCODE' => $e->getCode());
	$sessao->destroy();
	$myJSON = json_encode($my);
	echo($myJSON);
}finally {
	$con->close();
}
?>
