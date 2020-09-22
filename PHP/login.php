<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";

 // extrai os dados do post
extract($_POST);


$filter = "(cn=$user)";
$base_dn = "DC=". $om . ",DC=eb,DC=mil,DC=br";
//*************************************************************************
if($_SESSION["admin"]["user"] == 'admin'){
    $ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
}else{
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
$ldaprdn=$_SESSION["admin"]["user"]; //depois
}
//**************************************************************************
//$ldaprdn="CN=" . $user . "," . $base_dn;


try{
	$con = new Ldap();
	$con->bind($ldaprdn, $password);
	$con->search($base_dn, $filter);
	$info =  $con->getEntries()->parseUsers();
	session_start();
	 
	$_SESSION["login"] = true;
	$_SESSION["user"] = $user;
	$_SESSION["usercn"] = $info["users"][0]['cn'];
	$_SESSION["userdn"] = $info["users"][0]['dn'];
	$_SESSION["basedn"] = $base_dn;
	$_SESSION["password"] = $password;
	$_SESSION["aceite"] = $info["users"][0]['aceite'];
	$_SESSION["om"] = $om;
	$my = array('Status'=>'Login ok' , 'ERROR'=> 0);
	$myJSON = json_encode($my);
	echo($myJSON);
	
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no Cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}finally {
	$con->close();
}


?>

