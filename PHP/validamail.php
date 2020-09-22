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
		
		$useraceite = $info["users"][0]["aceite"];
		$userrdn = $info["users"][0]["dn"];
		$path = $userrdn;
		
		$data = array("mail"=> $mail , "uuid"=> array());
		$con->read($path , array('perfil' , 'cn', 'cpf' , 'mail', 'modifyTimestamp'));
		$info = $con->getEntries()->entries();
		$dataOld = $info;		
		
		$con->modify($userrdn, $data);
		
		$con->read($path , array('perfil' , 'cn', 'cpf' , 'mail', 'modifyTimestamp'));
		$info = $con->getEntries()->entries();
		$dataNew = $info;
		
		writeModifyLog($dataOld , $dataNew , $dataOld[0]["dn"] , $dataNew[0]["dn"] );
		
		if($useraceite==1){
			$sessao->destroy();
			$redirect = "/SISCAU/HTML/mail.html";
			header("location:$redirect");
		}else{
			
			$_SESSION["user"]["userpath"] = $userrdn;
			$_SESSION["user"]["mail"] = $info["users"][0]["mail"];
			$redirect = "/SISCAU/HTML/primeiro.html";
			header("location:$redirect");
		}
		
	}
	
}catch (LdapException $e){
	$sessao->destroy();
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}finally {
	$con->close();
}
?>