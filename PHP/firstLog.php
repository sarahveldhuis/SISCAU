<?php /* version 1.0 */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();

extract($_POST);

$path = $_SESSION["user"]["userpath"];

isset($_POST["userpassword"]) ? $data["userPassword"] = "{MD5}" . base64_encode(pack('H*',md5($_POST["userpassword"]))) : false;
$data["uuid"] = array();
$modify = array();
foreach ($aceite as $key){
	$modifs = array(
			"attrib"  => "aceite",
			"modtype" => LDAP_MODIFY_BATCH_ADD,
			"values"  => [$key],
	);
	$modify[] = $modifs;
	
}


try{
	$con = new Ldap();
	$adminData = $con->getAdminData();
	$con->bind($adminData["user"], $adminData["pass"]);
	$con->modify($path, $data);
	$con->read($path , array( 'aceite' ));
	$info = $con->getEntries()->entries();
	unset($info[0]["aceite"]["count"]);
	if(in_array("0" , $info[0]["aceite"])){
		$modifs = array(
				"attrib"  => "aceite",
				"modtype" => LDAP_MODIFY_BATCH_REMOVE,
				"values"  => ["0"],
		);
		$modify[] = $modifs;
	}
	$con->modify_batch($path, $modify);
	$my = array('Status'=>'Cadastro ok' , 'ERROR'=> 0 );
	$myJSON = json_encode($my);
	echo($myJSON);
	$sessao->destroy();	
	

}catch (LdapException $e){
	LdapErrLog($path , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no Cadastro' , 'ERROR'=> $e->getMessage());
	$myJSON = json_encode($my);
	echo($myJSON);

}finally {
	$con->close();
}

?>
