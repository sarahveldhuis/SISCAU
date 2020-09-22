<?php /* version 1.0 alterada pelo ST Mathias (Mar/19) */

include "funcoes.php";
include "LdapClass.php";
include "LdapException.php";
include "SessionClass.php";

$sessao = new Session();
$sessao->destroy();
$sessao->start();
extract($_POST);

//****Logar como adm usando email e senha do usuário siscau (inclusão ST Mathias) *****************
        $con = new Ldap();
        $adminData = $con->getAdminData();
	$con->bind($adminData["user"], $adminData["pass"]); 
       
        
       // $base_dn1 = "DC=". $om . ",DC=eb,DC=mil,DC=br";
        //$filter = "(mail=".$user.")";
        
        // validacao da entrada de dados e criacao do filtro
        $filter = NULL;
        $user=='admin'  ? $filter = $filter . "(cn=". $user . ")" : $filter = $filter . "(mail=". $user . ")";
        //isset($_POST["telephonenumber"]) ? $filter = $filter . "(telephonenumber=". $_POST["telephonenumber"] . ")" : $filter = $filter;
        //isset($filter)        ? $filter = "(|" . $filter . ")" : $filter = $filter;
        
    
       
try{
	 if($user == 'admin'){
            $filter = "(cn=$user)";
            $base_dn = "DC=". $om . ",DC=eb,DC=mil,DC=br";
            $ldaprdn="CN=" . $user . "," . $base_dn;
            //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
            
            $con = new Ldap();
            $con->bind($ldaprdn, $password);       
            $con->read($ldaprdn);
            $info =  $con->getEntries()->entries();	
            //$aceite = $info['count'];
            $aceite = $info[0]['aceite'][0];
            $_SESSION["admin"] = array(
			'user'       => $user,
			'om'         => $om,
			'pass'       => $password,
			'login'      => true&$aceite,
			'user_ip'    => $_SERVER['REMOTE_ADDR'],
			'user_agent' => $_SERVER['HTTP_USER_AGENT'],
			'my_sess'    => true,
			
            );  
            
            if($aceite){
		$redirect = "/SISCAU/HTML/portal.html";
            }else{
		$redirect = "/SISCAU/HTML/aceitecmd.html";
            }
            
            $my = array('Status'=>'Login ok' , 'ERROR'=> 0 , 'redirect'=>$redirect , $info);
            $myJSON = json_encode($my);
            echo($myJSON);
            //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
            
            
            
        }else{
           //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& 
        $base_dn1 = "DC=". $om . ",DC=eb,DC=mil,DC=br";
        $filter = "(mail=".$user.")";
	$con->search($base_dn1, $filter , array("cpf", "userPassword"));
	$info = $con->getEntries()->entries(); 
        
        //verificando a senha do usuário do siscau
        $senhanabase = $info[0]['userpassword'][0];
        $senhainformada = "{MD5}" . base64_encode(pack('H*',md5($password)));
        $cpfuser = $info[0]['cpf'][0];
        
        if($senhanabase == $senhainformada){ 
            
        //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
        //$cpfadm = $info[0]['cpf'][0];
        $ldaprdn="cn=admin,dc=".$om.",dc=eb,dc=mil,dc=br";
        $filter = "(cpf=$cpfuser)";
        $con->search($ldaprdn, $filter , array("cn" , "cpf", "userPassword"));
	$info = $con->getEntries()->entries();
        //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
        $cpfuseradmexiste = $info['count'];
            
            
            
            if($cpfuseradmexiste){
            $ldaprdn = $adminData["user"];
            $password = $adminData["pass"];
            $con = new Ldap();
            $con->bind($ldaprdn, $password);       
            $con->read($ldaprdn);
            $info =  $con->getEntries()->entries();	
            $aceite = $info['count'];
            
            $_SESSION["admin"] = array(
			'user'       => $adminData["user"],
			'om'         => $om,
			'pass'       => $adminData["pass"],
			'login'      => true&$aceite,
			'user_ip'    => $_SERVER['REMOTE_ADDR'],
			'user_agent' => $_SERVER['HTTP_USER_AGENT'],
			'my_sess'    => true,
			
	); 
            if($aceite){
		$redirect = "/SISCAU/HTML/portal.html";
            } 
            
            
            
            }
        //$aceite = 1;   
        }else{
             $ldaprdn="cn=admin,dc=".$om.",dc=eb,dc=mil,dc=br";
             $con = new Ldap();
             $con->bind($ldaprdn, $password);  
            //codificar o tratamento do error
           // $sessao->destroy();	
            //LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
            //$my = array('Status'=>'Credenciais Inválidas' , 'ERROR'=> $e->getMessage(), );
           // $redirect = "/SISCAU/HTML/portal.html";
           // $my = array('Status'=>'Credenciais Inválidas' , 'ERROR'=> 49 , 'redirect'=>$redirect , $info);
           // $myJSON = json_encode($my);
           // echo($myJSON);
            //exit();
            
        }
        

       /* $ldaprdn = $info[0]['dn']; 

        $cpfadm = $info[0]['cpf'][0];
        $ldaprdn="cn=admin,dc=".$om.",dc=eb,dc=mil,dc=br";
        $filter = "(cpf=$cpfadm)";
        $con->search($ldaprdn, $filter , array("cn" , "cpf", "userPassword"));
	$info = $con->getEntries()->entries();

        $user = $info[0]['cn'][0];
        $password = $info[0]['userpassword'][0];*/
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&   
        
        $my = array('Status'=>'Login ok' , 'ERROR'=> 0 , 'redirect'=>$redirect , $info);
	$myJSON = json_encode($my);
	echo($myJSON);
        
        }
        
    
    
    
	/*$my = array('Status'=>'Login ok' , 'ERROR'=> 0 , 'redirect'=>$redirect , $info);
	$myJSON = json_encode($my);
	echo($myJSON);*/
	
	
}catch (SessionException $e){
	$sessao->destroy();
	writeErrLog($e->getLine() , $e->getMessage() , $e->getFile());
	$my = array('Status'=>'Erro de sessão' , 'ERROR'=> $e->getMessage() , 'ERRCODE' => $e->getCode());
	$sessao->destroy();
	$myJSON = json_encode($my);
	echo($myJSON);
}catch (LdapException $e){
	$sessao->destroy();
	
	LdapErrLog($ldaprdn , $e->getMessage()  , json_encode($e->getTrace()));
	$my = array('Status'=>'Erro no Cadastro' , 'ERROR'=> $e->getMessage(), );
	$myJSON = json_encode($my);
	echo($myJSON);

}finally {
	$con->close();
}


?>

