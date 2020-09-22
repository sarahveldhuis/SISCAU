<?php

/*********************************************
Função de validação no AD via protocolo LDAP
como usar:
valida_ldap("servidor", "domíniousuário", "senha");

*********************************************/

function valida_ldap($srv, $usr, $pwd){
$ldap_server = $srv;
$auth_user = $usr;
$auth_pass = $pwd;

$connect = @ldap_connect($ldap_server);
//die($connect);

// Tenta se conectar com o servidor
if (!($connect = @ldap_connect($ldap_server))) {
    
return FALSE;
}else{
    // Set sane defaults for ldap v3 protocol
		ldap_set_option($connect , LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($connect , LDAP_OPT_REFERRALS, 0);
}


// Tenta autenticar no servidor
if (!($bind = @ldap_bind($connect, $auth_user, $auth_pass))) {
   // die('não autenticouuuu');
// se não validar retorna false
return FALSE;
} else {
// se validar retorna true
  // die('logou'); 
return TRUE;
}

}


// EXEMPLO do uso dessa função
$server = "10.3.4.51"; //IP ou nome do servidor
//$dominio = "2cta.eb.mil.br"; //Dominio Ex: @gmail.com
$user = "CPF=01472136730,DC=2CTA,DC=eb,DC=mil,DC=br";//$ldaprdn;
$pass = "Mka04097#";

if (valida_ldap($server, $user, $pass)) {
echo "usuário autenticado<br>";
} else {
echo "usuário ou senha inválida";
}

?>