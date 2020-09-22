<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();
extract($_POST);


$passwordAdm = $_SESSION["admin"]["pass"];
$basedn = "DC=". $_SESSION["admin"]["om"] . ",DC=eb,DC=mil,DC=br";
//*************************************************************************
if($_SESSION["admin"]["user"] == 'admin'){
    $ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
}else{
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
$ldaprdn=$_SESSION["admin"]["user"]; //depois
}
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;


$attr = array("uuid" , "mail" , "nomeguerra","posto","perfil" , "aceite");
$filtro = "(&(cpf=". $cpf .")(objectClass=userCta))" ;

try {
	$con = new Ldap();
	$con->bind($ldaprdn, $passwordAdm);
	$con->search($basedn, $filtro,$attr);
	$data = $con->getEntries()->parseUsers();
	$notAccepted = 0;
	foreach ($data["users"][0]["servicos"] as $key => $value){
		error_log($key);
		if(!in_array($key , $data["users"][0]["aceite"])){
			if($key=="Internet"){
				if ((!in_array(1 , $data["users"][0]["aceite"]))||(!in_array("Internet" , $data["users"][0]["aceite"]))){				
					$notAccepted++;
				}					
			}else{
				$notAccepted++;
			}
			
		}
	}
	
	error_log($notAccepted);
	if($notAccepted==0){
		$my = array('ERROR'=> "Usuário já ativado!");
		$myJSON = json_encode($my);
		echo($myJSON);
	}else{
		$newUuid["uuid"] = uniqid(substr(base64_encode($_SERVER["SERVER_ADDR"]),0,-2) , true);
		$uuid=$newUuid["uuid"];
		$path = $data["users"][0]['dn'];
		$con->modify($path, $newUuid);
		$nomeguerra=$data["users"][0]["nomeguerra"];
		$posto=$data["users"][0]["posto"];
		$mail=$data["users"][0]["mail"];
		$perfil = "Internet_" . $data["users"][0]["servicos"]["Internet"];
		if((in_array("USER" , $data["users"][0]["aceite"]))||(in_array(1 , $data["users"][0]["aceite"]))){
			newServiceEmail($mail,$uuid,$nomeguerra,$posto );
		}else {
			greetEmail($mail,$uuid,$nomeguerra,$posto );
		}		
		writeMailLog($mail , $uuid);
		$my = array('Status'=>'Email enviado com sucesso' , 'ERROR'=> 0 , "perfil"=>$perfil , "data"=>$data);
		$myJSON = json_encode($my);
		echo($myJSON);
		
	}
	
} catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);
	
}catch (phpmailerException $e){
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);
	
}finally {
	$con->close();
}

?>
