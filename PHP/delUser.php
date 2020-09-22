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
$path = $dn;

try{
	$con = new Ldap();
	$con->bind($ldaprdn, $passwordAdm);
	$con->read($path , array('perfil' , 'mail','cpf','cn'));
	$info = $con->getEntries()->entries();
	$data = array( "cn"     =>$info[0]["cn"][0] , 
		       "mail"   =>$info[0]["mail"][0] ,
                       "cpf"    =>$info[0]["cpf"][0] ,
                       "perfil" => $info[0]["perfil"]
	);
	$con->delete($path);
	writeCreationLog($data , $path , false );
	$my = array('Status'=>'Remove ok' , 'ERROR'=> 0 , 'mail'=> $data["mail"]);
	$myJSON = json_encode($my);
	echo($myJSON);
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);
	
}finally {
	$con->close();
}


?>
