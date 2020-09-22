<?php /* version 1.0 */

include "PHPMailer-master/PHPMailerAutoload.php";

$docRoot = getenv("DOCUMENT_ROOT");

function LdapErrLog($user , $error  , $stack){
	$log  = "Stack: ".$stack .",".
			"User:" . $user .",".
			"Error: ".$error.",";
	openlog("SISCAU", LOG_NDELAY, LOG_LOCAL0);
	syslog(LOG_ERR, $log);
	closelog();
}

function writeErrLog($errorLine , $error , $file) {
	$log  = "IP: ".$_SERVER['REMOTE_ADDR'].",".
	"Attempt:" . $file .",".
	"Error: ".$error.",".
	"Linha: ".$errorLine.",";
	openlog("SISCAU", LOG_NDELAY, LOG_LOCAL0);
	syslog(LOG_ERR, $log);
	closelog();
}
function writeModifyLog($userDataOld , $userDataNew , $dn , $newdn) {
	$reaper = preg_split("/@/",$userDataOld[0]["mail"][0]);
	$loginPAold = $reaper[1]. "\\" . $reaper[0];
	$reaper = preg_split("/@/",$userDataNew[0]["mail"][0]);
	$loginPAnew = $reaper[1]. "\\" . $reaper[0];
	$log  = "Ação: Modificação,Dados Antigos: {".
						"Timestamp: " . $userDataOld[0]["modifytimestamp"][0] .",".
						"DN: ".$dn .",".
                        "Nome Completo:" . $userDataOld[0]["cn"][0] .",".
                        "CPF: ". $userDataOld[0]["cpf"][0].",".
                        "Perfil: ". $userDataOld[0]["perfil"][0] .",".
                        "Login: ".$loginPAold . "}" . 
						"Dados Novos: {".
						"Timestamp: " . $userDataNew[0]["modifytimestamp"][0] .",".
						"DN: ".$newdn .",".
                        "Nome Completo:" . $userDataNew[0]["cn"][0] .",".
                        "CPF: ". $userDataNew[0]["cpf"][0].",".
                        "Perfil: ". $userDataNew[0]["perfil"][0] .",".
                        "Login: ".$loginPAnew . "}" ;
	openlog("SISCAU", LOG_NDELAY, LOG_LOCAL0);
	syslog(LOG_NOTICE, $log);
	closelog();
}
function writeCreationLog($userData , $dn, $create=true) {
	if($create){
		$mail = $userData[0]["mail"][0];
	}else{
		$mail = $userData["mail"];
	}
	$reaper = preg_split("/@/",$mail);
	$loginPA = $reaper[1]. "\\" . $reaper[0];
	if($create){
		$log  = 		"Ação: Criação,".
						"Timestamp: " . $userData[0]["createtimestamp"][0] .",".
						"DN: ".$dn .",".
                        "Nome Completo:" . $userData[0]["cn"][0] .",".
                        "CPF: ". $userData[0]["cpf"][0].",".
                        "Perfil: ". $userData[0]["perfil"][0] .",".
                        "Login: ".$loginPA ;
	}else{
		$log  = "Ação: Exclusão,DN: ".$dn .",".
                        "Nome Completo:" . $userData["cn"] .",".
                        "CPF: ". $userData["cpf"].",".
                        "Perfil: ". $userData["perfil"][0] .",".
	                 "Login: ".$loginPA .",";
	}


	openlog("SISCAU", LOG_NDELAY, LOG_LOCAL0);
	syslog(LOG_NOTICE, $log);
	closelog();
}

function writeMailLog($mail , $uuid) {
	$log  = "IP: ".$_SERVER['REMOTE_ADDR'].",".
	"Email enviado para:" . $mail .",".
	"uuid: ".$uuid.",";
	openlog("SISCAU", LOG_NDELAY, LOG_LOCAL0);
	syslog(LOG_INFO, $log);
	closelog();
}

function emailrecupera($mail , $uuid , $nome , $posto){
	
	$docRoot = getenv("DOCUMENT_ROOT");
	$iniData = parse_ini_file($docRoot . "/siscau.ini",true);
	
	$mailer = new PHPMailer(true);
	$mailer->CharSet = "UTF-8";
	$mailer->IsSMTP();
	$mailer->SMTPOptions = array(
			'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
			)
	);
	$mailer->Port = 587; //Indica a porta de conexão para a saída de e-mails
	$mailer->Host = $iniData["Email"]["url"];//Endereço do Host do SMTP Locaweb
	$mailer->SMTPAuth = true; //define se haverá ou não autenticação no SMTP
	$mailer->Username = $iniData["Email"]["conta"]; //Login de autenticação do SMTP
	$mailer->Password = $iniData["Email"]["senha"]; //Senha de autenticação do SMTP
	$mailer->FromName = $iniData["Email"]["FromName"]; //Nome que será exibido para o destinatário
	$mailer->From = $iniData["Email"]["From"];; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
	$mailer->AddAddress($mail); //Destinatários
	$mailer->Subject = 'Recuperacao de senha';
	$mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
	$mailer->MsgHTML(
					'<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

	<style type="text/css">
		
		/* Client-specific Styles */
		#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
		body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
		body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */
		/* Reset Styles */
		body{margin:0; padding:0;}
		img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
		table td{border-collapse:collapse;}
		#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}
	
		.standardCSS{
        	color:#505050;
        	font-size:15px;
    	}

    	@media only screen and (max-width:480px){
        	.mediaQueryCSS{
            	color:#CCCCCC;
            	font-size:20px;
       		 }
       		 .flexibleImage{
            	max-width:126px !important;
            	height:auto !important;
            	width 100% !important ;
            	
	        }

        	.flexibleContainer{
            	display:block !important;
            	width:100% !important;
            	
        	}
        	.emailButton{
            	max-width:600px !important;
            	width:100% !important;
        	}

	        .emailButton a{
	            display:block !important;
	            font-size:18px !important;
	        }
	        #bodyTable{
	            display:block !important;
	            width:100% !important;
	        }
	        #emailContainer{
	            display:block !important;
	            width:100% !important;
	        }
    	}
	</style>
	
  </head>
  <body  >
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
	    <tr>
	        <td align="center" valign="top">
	            <table border="0" cellpadding="0" cellspacing="0" width="600" style=" border: 1px solid #DDDDDD;" id="emailContainer">
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">
	                            <tr>
	                                <td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#2b934f;" >
										    <tr>
										        <td valign="top" style="padding-top:20px; ">
										            <table  class="flexibleContainer" align="left" border="0" cellpadding="0" cellspacing="0" width="20%" >
										                
										                <tr  rowspan="2">
										                    <td  align="center" valign="top" >
										                        <img src="https://siscau.2cta.eb.mil.br/SISCAU/Images/logo.png" class="flexibleImage" style="max-width:102" />
										                    </td>
										                </tr>
										              
										            </table>
										            <table align="right" border="0" cellpadding="5%" cellspacing="0" width="80%" class="flexibleContainer">
										                <tr>
										                    <td valign="top" style="color:#FFFFFF; font-family:Helvetica; font-size:16px; line-height:125%; text-align:left; padding-bottom:20px;">
										                        <h3>Recuperação de senha</h3>
										                        Para recuperar sua senha clique no botão abaixo, a partir de uma conexão com a EBNET
										                    </td>
										                    
										                </tr>
										                
										            </table>
										            
										        </td>
										    </tr>
										</table>	                                   
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%"  id="emailBody">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#ebebeb; text-align:left">
	                                    <span style="color=#232925; font-size:22px;"> Informações da conta:</span>
	                                	<br /><br />
	                                	Nome de guerra: <a href="#">'.$nome.'</a>
	                                	<br />
	                                	Posto/Graduação: <a href="#">'.$posto.'</a>
	                                	<br />
	                                	Email: <a href="#">'.$mail.'</a>
	                                	<br /><br />
	                                	<span style="color=#232925; font-size:22px;"> Duvidas?</span>
	                                	<br /> <br />
	                                	Entre em contato com o oficial de informática de sua OM
	                                	<br /> <br />
	                                	
	                                	<table align="center" border="0" cellpadding="0" cellspacing="0" style="background-color:#48a969; border:1px solid #74c690; border-radius:5px;" class="emailButton">
													    <tr>
													        <td  valign="bottom" style="color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; letter-spacing:-.5px; line-height:150%; padding-top:15px; padding-right:30px; padding-bottom:15px; padding-left:30px;">
													            <a href="https://siscau.2cta.eb.mil.br/SISCAU/PHP/recoverRedirect.php?uuid='.$uuid.'" target="_blank" style="color:#FFFFFF; text-decoration:none;">Recupere sua senha!</a>
													        </td>
													    </tr>
										</table>				
	                                	Caso não tenha clicado em esqueci minha senha ignore este email.
	                                	<br /> <br />
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#2b934f; color:#FFFFFF; font-size: 0.8em; text-align:right">
	                                   Desenvolvido pelo 2º Centro de Telemática de Área <br />
	                                   
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	</table>

  </body>
</html>'		
				);
	$mailer->SMTPSecure = 'tls';
	$mailer->Send();
			
	return true;

}

function emailvalidamail($mail , $uuid){
	
	$docRoot = getenv("DOCUMENT_ROOT");
	$iniData = parse_ini_file($docRoot . "/siscau.ini",true);
	
	$mailer = new PHPMailer(true);
	$mailer->CharSet = "UTF-8";
	$mailer->IsSMTP();
	$mailer->SMTPOptions = array(
			'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
			)
	);
	$mailer->Port = 587; //Indica a porta de conexão para a saída de e-mails
	$mailer->Host = $iniData["Email"]["url"];//Endereço do Host do SMTP Locaweb
	$mailer->SMTPAuth = true; //define se haverá ou não autenticação no SMTP
	$mailer->Username = $iniData["Email"]["conta"]; //Login de autenticação do SMTP
	$mailer->Password = $iniData["Email"]["senha"]; //Senha de autenticação do SMTP
	$mailer->FromName = $iniData["Email"]["FromName"]; //Nome que será exibido para o destinatário
	$mailer->From = $iniData["Email"]["From"];; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
	$mailer->AddAddress($mail); //Destinatários
	$mailer->Subject = 'Confirmação de email';
	$mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
	$mailer->MsgHTML(
			'<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

	<style type="text/css">
		
		/* Client-specific Styles */
		#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
		body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
		body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */
		/* Reset Styles */
		body{margin:0; padding:0;}
		img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
		table td{border-collapse:collapse;}
		#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}
	
		.standardCSS{
        	color:#505050;
        	font-size:15px;
    	}

    	@media only screen and (max-width:480px){
        	.mediaQueryCSS{
            	color:#CCCCCC;
            	font-size:20px;
       		 }
       		 .flexibleImage{
            	max-width:126px !important;
            	height:auto !important;
            	width 100% !important ;
            	
	        }

        	.flexibleContainer{
            	display:block !important;
            	width:100% !important;
            	
        	}
        	.emailButton{
            	max-width:600px !important;
            	width:100% !important;
        	}

	        .emailButton a{
	            display:block !important;
	            font-size:18px !important;
	        }
	        #bodyTable{
	            display:block !important;
	            width:100% !important;
	        }
	        #emailContainer{
	            display:block !important;
	            width:100% !important;
	        }
    	}
	</style>
	
  </head>
  <body  >
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
	    <tr>
	        <td align="center" valign="top">
	            <table border="0" cellpadding="0" cellspacing="0" width="600" style=" border: 1px solid #DDDDDD;" id="emailContainer">
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">
	                            <tr>
	                                <td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#2b934f;" >
										    <tr>
										        <td valign="top" style="padding-top:20px; ">
										            <table  class="flexibleContainer" align="left" border="0" cellpadding="0" cellspacing="0" width="20%" >
										                
										                <tr  rowspan="2">
										                    <td  align="center" valign="top" >
										                        <img src="https://siscau.2cta.eb.mil.br/SISCAU/Images/logo.png" class="flexibleImage" style="max-width:102" />
										                    </td>
										                </tr>
										              
										            </table>
										            <table align="right" border="0" cellpadding="5%" cellspacing="0" width="80%" class="flexibleContainer">
										                <tr>
										                    <td valign="top" style="color:#FFFFFF; font-family:Helvetica; font-size:16px; line-height:125%; text-align:left; padding-bottom:20px;">
										                        <h3>Email alterado!</h3>
										                        Por favor valide o seu email clicando no botão, a partir de uma conexão com a EBNET
										                    </td>
										                    
										                </tr>
										                
										            </table>
										            
										        </td>
										    </tr>
										</table>	                                   
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%"  id="emailBody">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#ebebeb; text-align:left">
	                                    
	                                	<span style="color=#232925; font-size:22px;"> Duvidas?</span>
	                                	<br /> <br />
	                                	Entre em contato com o gestor de Internet de sua OM
	                                	<br /> <br />
	                                	
	                                	<table align="center" border="0" cellpadding="0" cellspacing="0" style="background-color:#48a969; border:1px solid #74c690; border-radius:5px;" class="emailButton">
													    <tr>
													        <td  valign="bottom" style="color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; letter-spacing:-.5px; line-height:150%; padding-top:15px; padding-right:30px; padding-bottom:15px; padding-left:30px;">
													            <a href="https://siscau.2cta.eb.mil.br/SISCAU/PHP/validamail.php?uuid='.$uuid. '&mail='.$mail .'" target="_blank" style="color:#FFFFFF; text-decoration:none;">Valide seu email!</a>
													        </td>
													    </tr>
										</table>				
	                                	
	                                
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#2b934f; color:#FFFFFF; font-size: 0.8em; text-align:right">
	                                  Desenvolvido pelo 2º Centro de Telemática de Área <br />
	                                   
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	</table>

  </body>
</html>'
	);
	$mailer->SMTPSecure = 'tls';
	$mailer->Send();
		
	return true;

}


function greetEmail($mail , $uuid , $nome , $posto ){
		
		$docRoot = getenv("DOCUMENT_ROOT");
		$iniData = parse_ini_file($docRoot . "/siscau.ini",true);
		
		$mailer = new PHPMailer(true);
		$mailer->CharSet = "UTF-8";
		$mailer->IsSMTP();
		$mailer->SMTPOptions = array(
				'ssl' => array(
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
				)
		);
// 		$mailer->SMTPDebug = 2;
		$mailer->Port = 587; //Indica a porta de conexão para a saída de e-mails
		$mailer->Host = $iniData["Email"]["url"];//Endereço do Host do SMTP Locaweb
		$mailer->SMTPAuth = true; //define se haverá ou não autenticação no SMTP
		$mailer->Username = $iniData["Email"]["conta"]; //Login de autenticação do SMTP
		$mailer->Password = $iniData["Email"]["senha"]; //Senha de autenticação do SMTP
		$mailer->FromName = $iniData["Email"]["FromName"]; //Nome que será exibido para o destinatário
		$mailer->From = $iniData["Email"]["From"];; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
		$mailer->AddAddress($mail); //Destinatários
		$mailer->Subject = 'Ativação de conta';
		$mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
		$mailer->MsgHTML(
					'<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

	<style type="text/css">
		
		/* Client-specific Styles */
		#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
		body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
		body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */
		/* Reset Styles */
		body{margin:0; padding:0;}
		img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
		table td{border-collapse:collapse;}
		#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}
	
		.standardCSS{
        	color:#505050;
        	font-size:15px;
    	}

    	@media only screen and (max-width:480px){
        	.mediaQueryCSS{
            	color:#CCCCCC;
            	font-size:20px;
       		 }
       		 .flexibleImage{
            	max-width:126px !important;
            	height:auto !important;
            	width 100% !important ;
            	
	        }

        	.flexibleContainer{
            	display:block !important;
            	width:100% !important;
            	
        	}
        	.emailButton{
            	max-width:600px !important;
            	width:100% !important;
        	}

	        .emailButton a{
	            display:block !important;
	            font-size:18px !important;
	        }
	        #bodyTable{
	            display:block !important;
	            width:100% !important;
	        }
	        #emailContainer{
	            display:block !important;
	            width:100% !important;
	        }
    	}
	</style>
	
  </head>
  <body  >
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
	    <tr>
	        <td align="center" valign="top">
	            <table border="0" cellpadding="0" cellspacing="0" width="600" style=" border: 1px solid #DDDDDD;" id="emailContainer">
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">
	                            <tr>
	                                <td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#2b934f;" >
										    <tr>
										        <td valign="top" style="padding-top:20px; ">
										            <table  class="flexibleContainer" align="left" border="0" cellpadding="0" cellspacing="0" width="20%" >
										                
										                <tr  rowspan="2">
										                    <td  align="center" valign="top" >
										                        <img src="https://siscau.2cta.eb.mil.br/SISCAU/Images/logo.png" class="flexibleImage" style="max-width:102" />
										                    </td>
										                </tr>
										              
										            </table>
										            <table align="right" border="0" cellpadding="5%" cellspacing="0" width="80%" class="flexibleContainer">
										                <tr>
										                    <td valign="top" style="color:#FFFFFF; font-family:Helvetica; font-size:16px; line-height:125%; text-align:left; padding-bottom:20px;">
										                        <h3>Seja bem vindo!</h3>
										                        Você acaba de ser cadastrado no sistema provedor de identidades do 2º CTA
										                    </td>
										                    
										                </tr>
										                
										            </table>
										            
										        </td>
										    </tr>
										</table>	                                   
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%"  id="emailBody">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#ebebeb; text-align:left">
	                                    <span style="color=#232925; font-size:22px;"> Informações da conta:</span>
	                                	<br /><br />
	                                	Nome de guerra: <a href="#">'.$nome.'</a>
	                                	<br />
	                                	Posto: <a href="#">'.$posto.'</a>
	                                	<br />
	                                	Login: <a href="#">'.$mail.'</a>
	                                	<br /><br />
	                                	Ative sua conta pelo botão abaixo e desfrute dos serviços providos por este centro <br />
	                                	<b>Para ativar sua conta é necessário estar na EBNet</b> <br />
	                                	<br /><br />
	                                	<span style="color=#232925; font-size:22px;"> Duvidas?</span>
	                                	<br /> <br />
	                                	Entre em contato com o oficial de informática de sua OM
	                                	<br /> <br />
	                                	
	                                	<table align="center" border="0" cellpadding="0" cellspacing="0" style="background-color:#48a969; border:1px solid #74c690; border-radius:5px;" class="emailButton">
													    <tr>
													        <td  valign="bottom" style="color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; letter-spacing:-.5px; line-height:150%; padding-top:15px; padding-right:30px; padding-bottom:15px; padding-left:30px;">
													            <a href="https://siscau.2cta.eb.mil.br/SISCAU/PHP/first.php?uuid='.$uuid.'&mail='.$mail.'" target="_blank" style="color:#FFFFFF; text-decoration:none;">Ative sua conta!</a>
													        </td>
													    </tr>
													</table>
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#2b934f; color:#FFFFFF; font-size: 0.8em; text-align:right">
	                                   2º Centro de Telemática de Área <br />
	                                    Telefone: (21) 2519-5706 - Fax: (21) 2519-2509 - RITEx: 810-5706 <br />
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	</table>

  </body>
</html>'		
				);
		$mailer->SMTPSecure = 'tls';
		$mailer->Send();
		return true;
}

function newServiceEmail($mail , $uuid , $nome , $posto ){

	$docRoot = getenv("DOCUMENT_ROOT");
	$iniData = parse_ini_file($docRoot . "/siscau.ini",true);

	$mailer = new PHPMailer(true);
	$mailer->CharSet = "UTF-8";
	$mailer->IsSMTP();
	$mailer->SMTPOptions = array(
			'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
			)
	);
	// 		$mailer->SMTPDebug = 2;
	$mailer->Port = 587; //Indica a porta de conexão para a saída de e-mails
	$mailer->Host = $iniData["Email"]["url"];//Endereço do Host do SMTP Locaweb
	$mailer->SMTPAuth = true; //define se haverá ou não autenticação no SMTP
	$mailer->Username = $iniData["Email"]["conta"]; //Login de autenticação do SMTP
	$mailer->Password = $iniData["Email"]["senha"]; //Senha de autenticação do SMTP
	$mailer->FromName = $iniData["Email"]["FromName"]; //Nome que será exibido para o destinatário
	$mailer->From = $iniData["Email"]["From"];; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
	$mailer->AddAddress($mail); //Destinatários
	$mailer->Subject = 'Ativação de conta';
	$mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
	$mailer->MsgHTML(
			'<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

	<style type="text/css">
		
		/* Client-specific Styles */
		#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
		body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
		body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */
		/* Reset Styles */
		body{margin:0; padding:0;}
		img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
		table td{border-collapse:collapse;}
		#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}
	
		.standardCSS{
        	color:#505050;
        	font-size:15px;
    	}

    	@media only screen and (max-width:480px){
        	.mediaQueryCSS{
            	color:#CCCCCC;
            	font-size:20px;
       		 }
       		 .flexibleImage{
            	max-width:126px !important;
            	height:auto !important;
            	width 100% !important ;
            	
	        }

        	.flexibleContainer{
            	display:block !important;
            	width:100% !important;
            	
        	}
        	.emailButton{
            	max-width:600px !important;
            	width:100% !important;
        	}

	        .emailButton a{
	            display:block !important;
	            font-size:18px !important;
	        }
	        #bodyTable{
	            display:block !important;
	            width:100% !important;
	        }
	        #emailContainer{
	            display:block !important;
	            width:100% !important;
	        }
    	}
	</style>
	
  </head>
  <body  >
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
	    <tr>
	        <td align="center" valign="top">
	            <table border="0" cellpadding="0" cellspacing="0" width="600" style=" border: 1px solid #DDDDDD;" id="emailContainer">
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">
	                            <tr>
	                                <td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#2b934f;" >
										    <tr>
										        <td valign="top" style="padding-top:20px; ">
										            <table  class="flexibleContainer" align="left" border="0" cellpadding="0" cellspacing="0" width="20%" >
										                
										                <tr  rowspan="2">
										                    <td  align="center" valign="top" >
										                        <img src="https://siscau.2cta.eb.mil.br/SISCAU/Images/logo.png" class="flexibleImage" style="max-width:102" />
										                    </td>
										                </tr>
										              
										            </table>
										            <table align="right" border="0" cellpadding="5%" cellspacing="0" width="80%" class="flexibleContainer">
										                <tr>
										                    <td valign="top" style="color:#FFFFFF; font-family:Helvetica; font-size:16px; line-height:125%; text-align:left; padding-bottom:20px;">
										                        <h3>Parabens!</h3>
										                        Acaba de ser disponibilizado um novo serviço para o seu usuário
										                    </td>
										                    
										                </tr>
										                
										            </table>
										            
										        </td>
										    </tr>
										</table>	                                   
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%"  id="emailBody">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#ebebeb; text-align:left">
	                                    <span style="color=#232925; font-size:22px;"> Informações da conta:</span>
	                                	<br /><br />
	                                	Nome de guerra: <a href="#">'.$nome.'</a>
	                                	<br />
	                                	Posto: <a href="#">'.$posto.'</a>
	                                	<br />
	                                	Login: <a href="#">'.$mail.'</a>
	                                	<br /><br />
	                                	Ative o servico pelo botão abaixo <br />
	                                	
	                                	<br /><br />
	                                	<span style="color=#232925; font-size:22px;"> Duvidas?</span>
	                                	<br /> <br />
	                                	Entre em contato com o oficial de informática de sua OM
	                                	<br /> <br />
	                                	
	                                	<table align="center" border="0" cellpadding="0" cellspacing="0" style="background-color:#48a969; border:1px solid #74c690; border-radius:5px;" class="emailButton">
													    <tr>
													        <td  valign="bottom" style="color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; letter-spacing:-.5px; line-height:150%; padding-top:15px; padding-right:30px; padding-bottom:15px; padding-left:30px;">
													            <a href="https://siscau.2cta.eb.mil.br/SISCAU/PHP/first.php?uuid='.$uuid.'&mail='.$mail.'" target="_blank" style="color:#FFFFFF; text-decoration:none;">Ative sua conta!</a>
													        </td>
													    </tr>
													</table>
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#2b934f; color:#FFFFFF; font-size: 0.8em; text-align:right">
	                                   2º Centro de Telemática de Área <br />
	                                    Telefone: (21) 2519-5706 - Fax: (21) 2519-2509 - RITEx: 810-5706 <br />
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	</table>

  </body>
</html>'
			);
	$mailer->SMTPSecure = 'tls';
	$mailer->Send();
	return true;
}

function changePassEmail($mail , $uuid , $nome , $posto){
	
	$docRoot = getenv("DOCUMENT_ROOT");
	$iniData = parse_ini_file($docRoot . "/siscau.ini",true);
	
	$mailer = new PHPMailer(true);
	$mailer->CharSet = "UTF-8";
	$mailer->IsSMTP();
	$mailer->SMTPOptions = array(
			'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
			)
	);
	//		$mailer->SMTPDebug = 2;
	$mailer->Port = 587; //Indica a porta de conexão para a saída de e-mails
	$mailer->Host = $iniData["Email"]["url"];//Endereço do Host do SMTP Locaweb
	$mailer->SMTPAuth = true; //define se haverá ou não autenticação no SMTP
	$mailer->Username = $iniData["Email"]["conta"]; //Login de autenticação do SMTP
	$mailer->Password = $iniData["Email"]["senha"]; //Senha de autenticação do SMTP
	$mailer->FromName = $iniData["Email"]["FromName"]; //Nome que será exibido para o destinatário
	$mailer->From = $iniData["Email"]["From"];; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
	$mailer->AddAddress($mail); //Destinatários
	$mailer->Subject = 'Mudança de Senha';
	$mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
	$mailer->MsgHTML(
			'<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

	<style type="text/css">

		/* Client-specific Styles */
		#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
		body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
		body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */
		/* Reset Styles */
		body{margin:0; padding:0;}
		img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
		table td{border-collapse:collapse;}
		#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}

		.standardCSS{
        	color:#505050;
        	font-size:15px;
    	}

    	@media only screen and (max-width:480px){
        	.mediaQueryCSS{
            	color:#CCCCCC;
            	font-size:20px;
       		 }
       		 .flexibleImage{
            	max-width:126px !important;
            	height:auto !important;
            	width 100% !important ;
       
	        }

        	.flexibleContainer{
            	display:block !important;
            	width:100% !important;
       
        	}
        	.emailButton{
            	max-width:600px !important;
            	width:100% !important;
        	}

	        .emailButton a{
	            display:block !important;
	            font-size:18px !important;
	        }
	        #bodyTable{
	            display:block !important;
	            width:100% !important;
	        }
	        #emailContainer{
	            display:block !important;
	            width:100% !important;
	        }
    	}
	</style>

  </head>
  <body  >
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
	    <tr>
	        <td align="center" valign="top">
	            <table border="0" cellpadding="0" cellspacing="0" width="600" style=" border: 1px solid #DDDDDD;" id="emailContainer">
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="emailHeader">
	                            <tr>
	                                <td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#2b934f;" >
										    <tr>
										        <td valign="top" style="padding-top:20px; ">
										            <table  class="flexibleContainer" align="left" border="0" cellpadding="0" cellspacing="0" width="20%" >

										                <tr  rowspan="2">
										                    <td  align="center" valign="top" >
										                        <img src="https://siscau.2cta.eb.mil.br/SISCAU/Images/logo.png" class="flexibleImage" style="max-width:102" />
										                    </td>
										                </tr>

										            </table>
										            <table align="right" border="0" cellpadding="5%" cellspacing="0" width="80%" class="flexibleContainer">
										                <tr>
										                    <td valign="top" style="color:#FFFFFF; font-family:Helvetica; font-size:16px; line-height:125%; text-align:left; padding-bottom:20px;">
										                        Para modificar sua senha clique no botão abaixo em uma rede com acesso a EBNET
										                    </td>

										                </tr>

										            </table>

										        </td>
										    </tr>
										</table>
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%"  id="emailBody">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#ebebeb; text-align:left">
	                                    <span style="color=#232925; font-size:22px;"> Informações da conta:</span>
	                                	<br /><br />
	                                	Nome de guerra: <a href="#">'.$nome.'</a>
	                                	<br />
	                                	Posto/Graduação: <a href="#">'.$posto.'</a>
	                                	<br />
	                                	Email: <a href="#">'.$mail.'</a>
	                                	<br /><br />
	                                	<span style="color=#232925; font-size:22px;"> Duvidas?</span>
	                                	<br /> <br />
	                                	Entre em contato com o gestor de Internet de sua OM
	                                	<br /> <br />

	                                	<table align="center" border="0" cellpadding="0" cellspacing="0" style="background-color:#48a969; border:1px solid #74c690; border-radius:5px;" class="emailButton">
													    <tr>
													        <td  valign="bottom" style="color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; letter-spacing:-.5px; line-height:150%; padding-top:15px; padding-right:30px; padding-bottom:15px; padding-left:30px;">
													            <a href="https://siscau.2cta.eb.mil.br/SISCAU/PHP/novaSenhaRedirect.php?uuid='.$uuid.'" target="_blank" style="color:#FFFFFF; text-decoration:none;">Modifique sua senha!</a>
													        </td>
													    </tr>
													</table>
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	                <tr>
	                    <td align="center" valign="top">
	                        <table border="0" cellpadding="20" cellspacing="0" width="100%" id="emailFooter">
	                            <tr>
	                                <td align="center" valign="top" style="background-color:#2b934f; color:#FFFFFF; font-size: 0.8em; text-align:right">
	                                  Desenvolvido pelo 2º Centro de Telemática de Área <br />
	                                  
	                                </td>
	                            </tr>
	                        </table>
	                    </td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	</table>

  </body>
</html>'
	);
	$mailer->SMTPSecure = 'tls';
	$mailer->Send();
	return true;
}
?>
