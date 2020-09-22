<?php /* version 1.0 */
//die("Aquiii");
include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";


$base_dn = "DC=eb,DC=mil,DC=br";
$filter = "(&(dc=*)(!(dc=siscau)))";
$attr= array("dc");



try{
	
	$con = new Ldap();
	$adminData = $con->getAdminData();
	$con->bind($adminData["user"], $adminData["pass"]);
	$con->lista($base_dn, $filter , $attr);
	$info = $con->getEntries()->parseOms();
	ob_start('ob_gzhandler');
	$myJSON = json_encode($info);
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
