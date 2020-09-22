<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";


$sessao = new Session();
$vpnNumber = "maxnumvpn";
$passwordAdm = $_SESSION["admin"]["pass"];
$basedn = "DC=". $_SESSION["admin"]["om"] . ",DC=eb,DC=mil,DC=br";
//******************(ST Mathias)*******************************************************
if($_SESSION["admin"]["user"] == 'admin'){
    $ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
}else{
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;//antes
$ldaprdn=$_SESSION["admin"]["user"]; //depois
}
//**************************************************************************
//$ldaprdn="CN=" . $_SESSION["admin"]["user"] . "," . $basedn;

extract($_POST);
try {
	$con = new Ldap();
	$con->bind($ldaprdn, $passwordAdm);
	if(in_array("VPN" , $Perfil) || in_array("VPN_C2" , $Perfil) ){
		in_array("VPN" , $Perfil) ? $Perfil[1] = "VPN_1" : false;
		in_array("VPN_C2" , $Perfil) ? $Perfil[1] = "VPN_2" : false;
		$filter = "(&(|(perfil=VPN_1)(perfil=VPN_2))(objectClass=userCta))";
		$con->search($basedn, $filter , array("mail","cpf"));
		$info = $con->getEntries()->entries();
		$VpnCadastradas = $info["count"];
		$con->read($basedn , array( $vpnNumber ));
		$info = $con->getEntries()->entries();
		$VpnDisponiveis = $info[0][$vpnNumber][0];
		if($VpnDisponiveis<= $VpnCadastradas){
			$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> "A sua OM não possui contas de VPN disponívies" );
			$myJSON = json_encode($my);
			echo($myJSON);
			exit();
		}		
	}
	$uuid = uniqid(substr(base64_encode($_SERVER["SERVER_ADDR"]),0,-2) , true);
	$reaper = preg_split('/ /',$nomeCompleto); //split(' ',$nomeCompleto);
	$rdnAtr = "cpf";
	
	$data = array(
			"cn"          => $nomeCompleto,
			"sn"          => $reaper[count($reaper)-1],
			"displayName" => $Posto. " " . $nomeGuerra,
			"nomeGuerra"  => $nomeGuerra,
			"givenName"   => $reaper[0],
			"cpf"         => $cpf,
			"userPassword"=> "{MD5}" . base64_encode(pack('H*',md5($uuid))),
			"mail"        => $mail,
			"posto"       => $Posto,
			"objectclass" => ["userCta","top","inetOrgPerson"],
			"perfil"      => $Perfil,
			"uuid"		  => $uuid
	
	);
	

	if($Identidade != ""){
		$data['identidade'] = $Identidade;
	}
	$base_dn = "DC=eb,DC=mil,DC=br";
	$path = $rdnAtr . "=" . $_POST[$rdnAtr] . "," . $basedn;
	unset($data[$rdnAtr]);
	$filter = "(&(|(mail=".$mail.")(cpf=".$cpf."))(objectClass=userCta))";
	$con->search($base_dn, $filter , array("mail","cpf"));
	$info = $con->getEntries()->entries();
	if($info["count"]==0){
		$con->add($path, $data);
		greetEmail($mail,$uuid,$nomeGuerra,$Posto );
		$con->read($path , array('perfil' , 'cn', 'cpf' , 'mail', 'createTimestamp'));
		$data = $con->getEntries()->entries();
		writeMailLog($mail , $uuid);
		writeCreationLog($data , $path);
		$my = array('Status'=>'Cadastro ok' , 'ERROR'=> 0 ,'dn'=>$path );
		$myJSON = json_encode($my);
		echo($myJSON);
	}else{
		if(($info[0]["mail"][0]==$mail)&&($info[0]["cpf"][0]==$cpf)){
			$my = array('Status'=>'Erro de colisão' , 'ERROR'=> "Email e CPF já cadastrados!" );
			$myJSON = json_encode($my);
			echo($myJSON);
		}
		if(($info[0]["mail"][0]==$mail)&&($info[0]["cpf"][0]!=$cpf)){
			$my = array('Status'=>'Erro de colisão' , 'ERROR'=> "Email já cadastrado!" );
			$myJSON = json_encode($my);
			echo($myJSON);
		}
		if(($info[0]["mail"][0]!=$mail)&&($info[0]["cpf"][0]==$cpf)){
			$my = array('Status'=>'Erro de colisão' , 'ERROR'=> "CPF já cadastrado!" );
			$myJSON = json_encode($my);
			echo($myJSON);
		}
	
	}

	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage(), 'path'=> $path , "data"=>$data, "reaper"=>$reaper);
	$myJSON = json_encode($my);
	echo($myJSON);
	
}catch (phpmailerException $e){
	$con->delete($path);
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);
	
}finally {
	$con->close();
}



/*
$uuid = uniqid(substr(base64_encode($_SERVER["SERVER_ADDR"]),0,-2) , true);
$reaper = split(' ',$nomeCompleto);
$rdnAtr = "cpf";

$data = array(
	"cn"          => $nomeCompleto,	
	"sn"          => $reaper[count($reaper)-1],
	"displayName" => $Posto. " " . $nomeGuerra,
	"nomeGuerra"  => $nomeGuerra,
	"givenName"   => $reaper[0],
	"cpf"         => $cpf,	
	"userPassword"=> "{MD5}" . base64_encode(pack('H*',md5($uuid))),
	"mail"        => $mail,
	"posto"       => $Posto,
	"objectclass" => ["userCta","top","inetOrgPerson"],
	"perfil"      => $Perfil,
	"aceite"      => 0,
	"uuid"		  => $uuid

);

if($Identidade != ""){
	$data['identidade'] = $Identidade; 
}
$base_dn = "DC=eb,DC=mil,DC=br";
$path = $rdnAtr . "=" . $_POST[$rdnAtr] . "," . $basedn;
unset($data[$rdnAtr]);
$filter = "(&(|(mail=".$mail.")(cpf=".$cpf."))(objectClass=userCta))";

try{
	$con = new Ldap();
	$con->bind($ldaprdn, $passwordAdm);
	$con->search($base_dn, $filter , array("mail","cpf"));
	$info = $con->getEntries()->entries();
	if($info["count"]==0){
		$con->add($path, $data);
		greetEmail($mail,$uuid,$nomeGuerra,$Posto );
		$con->read($path , array('perfil' , 'cn', 'cpf' , 'mail', 'createTimestamp'));
		$data = $con->getEntries()->entries();
		writeMailLog($mail , $uuid);
		writeCreationLog($data , $path);
		$my = array('Status'=>'Cadastro ok' , 'ERROR'=> 0 ,'dn'=>$path );
		$myJSON = json_encode($my);
		echo($myJSON);
	}else{
		if(($info[0]["mail"][0]==$mail)&&($info[0]["cpf"][0]==$cpf)){
			$my = array('Status'=>'Erro de colisão' , 'ERROR'=> "Email e CPF já cadastrados!" );
			$myJSON = json_encode($my);
			echo($myJSON);
		}
		if(($info[0]["mail"][0]==$mail)&&($info[0]["cpf"][0]!=$cpf)){
			$my = array('Status'=>'Erro de colisão' , 'ERROR'=> "Email já cadastrado!" );
			$myJSON = json_encode($my);
			echo($myJSON);
		}
		if(($info[0]["mail"][0]!=$mail)&&($info[0]["cpf"][0]==$cpf)){
			$my = array('Status'=>'Erro de colisão' , 'ERROR'=> "CPF já cadastrado!" );
			$myJSON = json_encode($my);
			echo($myJSON);
		}
		
	}
	
	
}catch (LdapException $e){
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage(), 'path'=> $path , "data"=>$data, "reaper"=>$reaper);
	$myJSON = json_encode($my);
	echo($myJSON);
	
}catch (phpmailerException $e){
	$con->delete($path);
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$my = array('Status'=>'Erro no cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);
	
}finally {
	$con->close();
}
 */
?>
