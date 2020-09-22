<?php /* version 1.0 */
include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();

$passwordAdm = $_SESSION["admin"]["pass"];
$basedn = "DC=". $_SESSION["admin"]["om"] . ",DC=eb,DC=mil,DC=br";
//**************(ST Mathias)***********************************************************
if($_SESSION["admin"]["user"] == 'admin'){
    $ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
}else{
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
$ldaprdn=$_SESSION["admin"]["user"]; //depois
}
//**************************************************************************
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;


$data = array(
	'aceite' => $_POST['aceite']	
);
$path = $ldaprdn;

try{
	$con = new Ldap();
	$con->bind($ldaprdn, $passwordAdm);
	$con->modify($path, $data);
	$_SESSION["admin"]["login"] = true;

	$my = array('Status'=>'Cadastro ok' , 'ERROR'=> 0  );
	$myJSON = json_encode($my);
	echo($myJSON);
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage(), 'Line ' => $e->getLine());
	$myJSON = json_encode($my);
	echo($myJSON);
	
}finally {
	$con->close();
}

?>
