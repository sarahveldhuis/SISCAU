<?php /* version 1.0 */ 

include "LdapClass.php";
include "LdapException.php";


session_start();

$path = $_SESSION["user"]["userpath"];
try {
	$con = new Ldap();
	$adminData = $con->getAdminData();
	$con->bind($adminData["user"], $adminData["pass"]);
	$con->read($path, array( "mail" , "aceite" , "perfil" ));
	$info = $con->getEntries()->entries();
	$services = array();
	$hasPass = false;
	unset($info[0]["aceite"]["count"]);
	if((in_array("USER", $info[0]["aceite"]))||(in_array(1, $info[0]["aceite"]))){
		$hasPass = true;
	}
	for($i = 0 ; $i< $info[0]["perfil"]["count"] ; $i++){
		$servico = split("_", $info[0]["perfil"][$i]) ;
		if(!in_array($servico[0] , $info[0]["aceite"])){
			if(($servico[0] == "Internet")){
				if(!(in_array(1 , $info[0]["aceite"]))){
					
					$services[] = $servico[0];
				}
			}else{
				$services[] = $servico[0];
			}
			
		}
	}

	
	$my = array('services'	=> $services,
			'hasPass'	=> $hasPass,
			'mail'		=> $_SESSION["user"]["mail"]
	);
	$myJSON = json_encode($my);
	echo($myJSON);
	
	
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage(), 'path'=> $path , "data"=>$data, "reaper"=>$reaper);
	$myJSON = json_encode($my);
	echo($myJSON);
	
}



?>