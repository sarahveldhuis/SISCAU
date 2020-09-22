<?php /* version 1.0 */ 

session_start();

$my = array('mail' => $_SESSION["user"]["mail"]);
$myJSON = json_encode($my);
echo($myJSON);


?>