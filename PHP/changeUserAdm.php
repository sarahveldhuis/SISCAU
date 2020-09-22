<?php /* version criada pelo ST Mathias em (Mar/19) */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();

$vpnNumber = "businesscategory";

$passwordAdm = $_SESSION["admin"]["pass"];
$basedn = "DC=". $_SESSION["admin"]["om"] . ",DC=eb,DC=mil,DC=br";
//***********************(ST Mathias)**************************************************
if($_SESSION["admin"]["user"] == 'admin'){
    $ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
}else{
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
$ldaprdn=$_SESSION["admin"]["user"]; //depois
}
//**************************************************************************
$rdnAttr = "cpf";
$path = $_POST["dn"];

// validacao da entrada de dados e criacao do filtro
$filter = NULL;
isset($_POST["cpf"])  ? $filter = $filter . "(cpf=". $_POST["cpf"] . ")" : $filter = $filter;
isset($_POST["telephonenumber"]) ? $filter = $filter . "(telephonenumber=". $_POST["telephonenumber"] . ")" : $filter = $filter;
isset($filter)        ? $filter = "(|" . $filter . ")" : $filter = $filter;

try {
                $con = new Ldap();
                $con->bind($ldaprdn, $passwordAdm);
                
		$con->read($path , array("cn" , "cpf", "userPassword","telephonenumber", 'modifyTimestamp'));
		$info = $con->getEntries()->entries();               
		$formData = $_POST;
		$dataOld = $info;     
		
		$data = Ldap::parseDataAdm($formData , $dataOld);
              
		$con->modify($path , $data["modify"]); 
		//************Log não foi emprimentado*********************************************
		//$dataNew = $info;
		//writeModifyLog($dataOld , $dataNew , $dataOld[0]["dn"] , $dataNew[0]["dn"] );
                //**********************************************************************************
		$my = array('Status'=>'Cadastro ok' , 'ERROR'=> 0  , "dn" => $path );
		$myJSON = json_encode($my);
		echo($myJSON);	
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage() );
	$myJSON = json_encode($my);
	echo($myJSON);
	
}catch (phpmailerException $e){
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage() );
	$myJSON = json_encode($my);
	echo($myJSON);
	
}finally {
	$con->close();
} 





