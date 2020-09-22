<?php /* version 1.0 */

class LdapException extends Exception{

	public function __construct(Ldap $res , $custom = false , $error = null , $errno = null)
	{
		if($custom) {
			parent::__construct($error, $errno);
		}else {
			parent::__construct($res->error(), $res->errno());
		}
		
	}
	
}

?>