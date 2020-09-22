<?php /* version 1.0 desenvolvida pelo ST Mathias (Mar/19) */ 

include "funcoes.php";
include "LdapClass.php";
include "SessionClass.php";
include "LdapException.php";

$sessao = new Session();

extract($_POST);
$filter = $filtro;
$attr = array("cn" , "cpf","telephonenumber" ,"userPassword");

try{
	$adminData = $sessao->getAdminData();      
	$sessao->hijackPrevent();
	$base_dn = "dc=".$adminData['om'].",dc=eb,dc=mil,dc=br";
	$password = $adminData['pass'];
        //******************************************
        if($adminData["user"] == 'admin'){
            $ldaprdn= "cn=admin," .$base_dn;
        }else{
	//$ldaprdn= "cn=admin," .$base_dn;
	$ldaprdn= $adminData["user"];//************************
        }
        //***************************************************
	//$ldaprdn= "cn=admin," .$base_dn;	
	$con = new Ldap();
	$con->bind($ldaprdn, $password);      
	$con->search($base_dn, $filter,$attr);        
	$data =  $con->getEntries()->parseUsersAdm();
        
	ob_start('ob_gzhandler');
	$myJSON = json_encode($data);
	echo($myJSON);
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no Cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}catch (SessionException $e){
	$sessao->destroy();
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$my = array('Status'=>'Erro de sessão' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);
}finally {
	if(isset($con)){
		$con->close();
	}
}

?>