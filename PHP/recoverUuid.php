<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";

// extrai os dados do post
extract($_POST);

$filter = "(mail=$user)";
$base_dn = "DC=eb,DC=mil,DC=br";



try{
	$con = new Ldap();
	$adminData = $con->getAdminData();
	$con->bind($adminData["user"], $adminData["pass"]);
	$con->search($base_dn, $filter);
	$info = $con->getEntries()->parseUsers();
	if($info["count"]==0){
		$my = array('ERROR'=> "Usuário inexistente!");
		$myJSON = json_encode($my);
		echo($myJSON);
	}else{
		$mail = $info["users"][0]['mail'];
		$path = $info["users"][0]['dn'];
		$data["uuid"] = uniqid(substr(base64_encode($_SERVER["SERVER_ADDR"]),0,-2) , true);
		$con->modify($path, $data);
		emailrecupera($mail,$data["uuid"] ,$info["users"][0]['nomeguerra'] ,$info["users"][0]['posto'] );
		$my = array('Status'=>'Envio ok' , 'ERROR'=> 0 , "email"=>$mail);
		$myJSON = json_encode($my);
		echo($myJSON);
	}
	
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no Cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}catch (phpmailerException $e){
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$data["uuid"] = array();
	$con->modify($path, $data);
	$my = array('Status'=>'Erro no envio de email' , 'ERROR'=> $e->getMessage() );
	$myJSON = json_encode($my);
	echo($myJSON);
	
}finally {
	$con->close();
}

?>
