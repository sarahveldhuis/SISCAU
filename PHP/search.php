<?php /* version 1.0 */ 

include "funcoes.php";
include "LdapClass.php";
include "SessionClass.php";
include "LdapException.php";

$sessao = new Session();
$vpnNumber = "maxnumvpn";

extract($_POST);

$filter = $filtro;
$attr = array("perfil" , "posto", "mail", "cpf" , "nomeGuerra" , "cn" , "identidade" , "aceite");

try{
	$adminData = $sessao->getAdminData();        
	$sessao->hijackPrevent();
	$base_dn = "dc=".$adminData['om'].",dc=eb,dc=mil,dc=br";
	$password = $adminData['pass'];       
	//$ldaprdn= "cn=admin," .$base_dn;
        //******************************************
        if($adminData["user"] == 'admin'){
            $ldaprdn= "cn=admin," .$base_dn;
        }else{
	//$ldaprdn= "cn=admin," .$base_dn;
	$ldaprdn= $adminData["user"];//************************
        }
        //***************************************************
	
	$con = new Ldap();
	$con->bind($ldaprdn, $password);        
	$con->search($base_dn, $filter,$attr);        
	$data =  $con->getEntries()->parseUsers();
        
        //**********Quadro resumo da VPN ********************************************        
	$filter = "(&(|(perfil=VPN_1)(perfil=VPN_2))(objectClass=userCta))";
	$con->search($base_dn, $filter , array("mail","cpf"));
	$info = $con->getEntries()->entries();	
        $VpnCadastrada = $info["count"];
	$con->read($base_dn , array( $vpnNumber ));
	$info = $con->getEntries()->entries();
      
        $VpnCadastradas["vpncadastradas"]= $VpnCadastrada;
        $VpnCadastradas["vpndisponiveis"]= $info[0][$vpnNumber][0];
        $VpnCadastradas["vpndiferenca"]= $info[0][$vpnNumber][0] - $VpnCadastrada;        
        $data["resumo"][] = $VpnCadastradas;       
        //******************************************************
       
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