<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();

$passwordAdm = $_SESSION["admin"]["pass"];
$basedn = "DC=". $_SESSION["admin"]["om"] . ",DC=eb,DC=mil,DC=br";
//*************************************************************************
if($_SESSION["admin"]["user"] == 'admin'){
    $ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
}else{
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
$ldaprdn=$_SESSION["admin"]["user"]; //depois
}
//**************************************************************************
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;


extract($_POST);



$attr = array( "mail" , "nomeguerra","posto");
$filtro = "(&(cpf=". $cpf .")(objectClass=userCta)(aceite=USER))" ;

try {
	$con = new Ldap();
	$con->bind($ldaprdn, $passwordAdm);
	$con->search($basedn, $filtro,$attr);
	$data = $con->getEntries()->parseUsers();
	if($data["count"]==0){
		$my = array('ERROR'=> "Conta não ativada!");
		$myJSON = json_encode($my);
		echo($myJSON);
	}else{
		$nomeguerra=$data["users"][0]["nomeguerra"];
		$posto=$data["users"][0]["posto"];
		$mail=$data["users"][0]["mail"];
		$path=$data["users"][0]["dn"];
		$val["uuid"] = uniqid(substr(base64_encode($_SERVER["SERVER_ADDR"]),0,-2) , true);
		$con->modify($path, $val);
		changePassEmail($mail,$val["uuid"],$nomeguerra,$posto);
		$my = array('Status'=>'Email enviado com sucesso' , 'ERROR'=> 0 );
		$myJSON = json_encode($my);
		echo($myJSON);
		
	}
	
} catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage() , $val , $path);
	$myJSON = json_encode($my);
	echo($myJSON);
	
}catch (phpmailerException $e){
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$data["uuid"] = array();
	$con->modify($path, $data);
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);
	
}finally {
	$con->close();
}

?>