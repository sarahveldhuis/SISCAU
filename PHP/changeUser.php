<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();

$vpnNumber = "maxnumvpn";

$passwordAdm = $_SESSION["admin"]["pass"];
$basedn = "DC=". $_SESSION["admin"]["om"] . ",DC=eb,DC=mil,DC=br";
//**********************(ST Mathias)***************************************************
if($_SESSION["admin"]["user"] == 'admin'){
    $ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
}else{
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
$ldaprdn=$_SESSION["admin"]["user"]; //depois
}
//**************************************************************************
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;
$rdnAttr = "cpf";
$path = $_POST["dn"];

// validacao da entrada de dados e criacao do filtro
$filter = NULL;
isset($_POST["cpf"])  ? $filter = $filter . "(cpf=". $_POST["cpf"] . ")" : $filter = $filter;
isset($_POST["mail"]) ? $filter = $filter . "(mail=". $_POST["mail"] . ")" : $filter = $filter;
isset($filter)        ? $filter = "(|" . $filter . ")" : $filter = $filter;

try {
	$con = new Ldap();
	$con->bind($ldaprdn, $passwordAdm);
	// Inicio do teste de colisão
	if(isset($filter)){
		$con->search("DC=eb,DC=mil,DC=br", $filter , array("cpf" , "mail"));
		$info = $con->getEntries()->entries();               
	}else{
		$info["count"]=0;
	}	
	if(($info["count"]!= 0 )){
		$colided = array();
		for($i=0 ; $i<$info["count"] ; $i++){
			if((isset($_POST["cpf"]))&&($info[$i]["cpf"][0] == $_POST["cpf"])){
				$colided["CPF"] = true;
			}
			if((isset($_POST["mail"]))&&($info[$i]["mail"][0] == $_POST["mail"])){
				$colided["Email"] = true;
			}
		}		
		$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> 'Erro de Colisão', 'colided' => $colided );
		$myJSON = json_encode($my);
		echo($myJSON); 
	}else {
		$con->read($path , array('cn' , 'aceite', "perfil" , "nomecompleto","nomeguerra","mail","posto","identidade","cpf", 'modifyTimestamp'));
		$info = $con->getEntries()->entries();                
		$formData = $_POST;
		$dataOld = $info;               
                
		if((isset($formData["servicos"]["VPN"]))&&($formData["servicos"]["VPN"]==0)) unset($formData["servicos"]["VPN"]);
		if(isset($formData["mail"])){
			$newmail = $formData["mail"];
			unset($formData["mail"]);
			$formData["uuid"]=uniqid(base64_encode($_SERVER["SERVER_ADDR"]) , true);
		}
		$data = Ldap::parseData($formData , $dataOld);
               
		if(isset($_POST[$rdnAttr])){
			$newrdn = $rdnAttr . "=" . $_POST[$rdnAttr];
			$con->rename($path , $newrdn , $basedn , true);
			$path = $newrdn . "," . $basedn;
		}
		
		if(isset($data["newService"])){
			$data["modify"]["uuid"] = uniqid(base64_encode($_SERVER["SERVER_ADDR"]) , true);
			$filter = "(&(|(perfil=VPN_1)(perfil=VPN_2))(objectClass=userCta))";
			$con->search($basedn, $filter , array("mail","cpf"));
			$info = $con->getEntries()->entries();
			$VpnCadastradas = $info["count"];
			$con->read($basedn , array( $vpnNumber ));
			$info = $con->getEntries()->entries(); 
			isset($info[0][$vpnNumber][0]) ? $VpnDisponiveis = $info[0][$vpnNumber][0] : $VpnDisponiveis = 0;
			if($VpnDisponiveis<= $VpnCadastradas){
				$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> "A sua OM não possui contas de VPN disponívies" );
				$myJSON = json_encode($my);
				echo($myJSON);
				exit();
			}
			
			newServiceEmail($dataOld[0]["mail"][0],$data["modify"]["uuid"],$dataOld[0]["nomeguerra"][0],$dataOld[0]["posto"][0] );
			
		}
		$con->modify($path , $data["modify"]);
                
		if(isset($data["modify_batch"])){
			$con->modify_batch($path, $data["modify_batch"]);
		}
		if(isset($formData["uuid"])){
			if( (in_array("USER" , $info[0]["aceite"]) ) || (in_array(1 , $info[0]["aceite"]) ) ) {
				emailvalidamail($newmail ,$formData["uuid"]);
			}else{
				greetEmail($newmail,$formData["uuid"],$info[0]["nomeguerra"][0],$info[0]["posto"][0] );
			}
		}
		$con->read($path , array('perfil' , 'cn', 'cpf' , 'mail', 'modifyTimestamp'));
		$info = $con->getEntries()->entries();
		$dataNew = $info;
		writeModifyLog($dataOld , $dataNew , $dataOld[0]["dn"] , $dataNew[0]["dn"] );
		$my = array('Status'=>'Cadastro ok' , 'ERROR'=> 0  , "dn" => $path );
		$myJSON = json_encode($my);
		echo($myJSON);
		
	}	
	
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





/* $formIds = ["nomecompleto","nomeguerra","mail","posto","identidade","cpf"];

$rdnAttr = "cpf";

for($i=0;$i<8;$i++){
	if(isset($_POST[$formIds[$i]])){
		if($formIds[$i]=="nomecompleto"){
			$reaper = split(' ',$_POST[$formIds[$i]]);
			$data['cn']          =  $_POST[$formIds[$i]];
			$data["sn"]          = $reaper[count($reaper)-1];
			$data["givenName"]   = $reaper[0];
		}else{
			$data[$formIds[$i]] = $_POST[$formIds[$i]];
		}
		if($formIds[$i]== $rdnAttr){
			$newrdn = $rdnAttr . "=" . $_POST[$formIds[$i]];
			unset($data[$rdnAttr]);
		}
		
	}
}
if(isset($data["mail"])){
	$newmail = $data["mail"];
	unset($data["mail"]);
}
$path = $_POST["dn"];

try{
	$con = new Ldap();
	$con->connect("ldap://ldap.2cta.eb.mil.br");
	$con->bind($ldaprdn, $passwordAdm);
	$con->read($path , array('aceite' , 'perfil' , 'cn', 'cpf' , 'mail', 'modifyTimestamp'));
	$info = $con->getEntries()->entries();
	$dataOld = $info;
 	if(isset($data)){
		$con->modify($path, $data);		
	} 	
	if(isset($newrdn)){
		$con->rename($path , $newrdn , $basedn , true);
		$path = $newrdn . "," . $basedn;
	}
	if(isset($newmail)){
		$data = array();
		$data["uuid"]=uniqid(substr(base64_encode($_SERVER["SERVER_ADDR"]),0,-2) , true);
		$con->modify($path, $data);
		if($info[0]["aceite"][0]){
			emailvalidamail($newmail ,$data["uuid"]);
		}else{
			greetEmail($newmail,$data["uuid"],$info[0]["nomeguerra"][0],$info[0]["posto"][0]);
		}
		
	}
	if(isset($_POST['servicos'])){
		$servicos = $_POST['servicos'];
		for($j=0;$j<$info[0]['perfil']['count'];$j++){
			foreach ($servicos as $key => $value){
				if(ereg($key , $info[0]['perfil'][$j])){
					$modifs = [
							[
									"attrib"  => "perfil",
									"modtype" => LDAP_MODIFY_BATCH_REMOVE,
									"values"  => [$info[0]['perfil'][$j]],
							],
							[
									"attrib"  => "perfil",
									"modtype" => LDAP_MODIFY_BATCH_ADD,
									"values"  => [$key."_".$value],
							],
					];
					$con->modify_batch($path, $modifs);
				}
			}
		}
	}
	$con->read($path , array('perfil' , 'cn', 'cpf' , 'mail', 'modifyTimestamp'));
	$info = $con->getEntries()->entries();
	$dataNew = $info;
	writeModifyLog($dataOld , $dataNew , $dataOld[0]["dn"] , $dataNew[0]["dn"] );
	$my = array('Status'=>'Cadastro ok' , 'ERROR'=> 0  , "dn" => $path );
	$myJSON = json_encode($my);
	echo($myJSON);
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage() , 'rdn' => $newrdn);
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
?> */
