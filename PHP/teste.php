<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";




$ldaprdn = "CN=admin,DC=eb,DC=mil,DC=br";
$password = "brasil500";
$base_dn = "DC=eb,DC=mil,DC=br";
$filter = "(&(uuid=*)(objectClass=userCta))";



try{
	$con = new Ldap();
	$con->connect("ldap://ldap.2cta.eb.mil.br");
	$con->bind($ldaprdn, $password);
	$con->search($base_dn, $filter , array("uuid"));
	$info = $con->getEntries()->entries();
	var_dump($info);
/* 	for($i=0 ; $i<$info["count"];$i++){
		$modifs = array(array(
				"attrib"  => "uuid",
				"modtype" => LDAP_MODIFY_BATCH_REMOVE_ALL,
		));
		$con->modify_batch($info[$i]["dn"] , $modifs);
	} */
	
	
}catch (LdapException $e){
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> json_encode($e->getTrace()) );
	$myJSON = json_encode($my);
	echo($myJSON);

}finally {
	$con->close();
}
