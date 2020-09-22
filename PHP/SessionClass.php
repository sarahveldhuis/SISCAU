<?php /* version 1.0 */
class Session {
	
	protected $error;
	
	protected $errorno;
	
	protected $adminData;
	
	
	public function __construct(){
		session_start();
		

	}
	
	public function  start(){
		session_start();
		session_regenerate_id(true);

		
	}
	public function getAdminData(){
		if(isset($_SESSION['admin'])){
			$this->adminData = $_SESSION['admin'];
			return $this->adminData;
		}else{
			$this->errorno = 0;
			$this->error = 'Sessão não inicializada';
			throw new SessionException($this);
		}
	}
	
	public function hijackPrevent(){
		if(($this->adminData['user_ip'] != $_SERVER['REMOTE_ADDR'])&& ($this->adminData['user_agent'] !=$_SERVER['HTTP_USER_AGENT']) && ($this->adminData['my_sess'])){
			$this->errorno = 1;
			$this->error = 'A sua sessão está comprometida por favor entre novamente';
			throw new SessionException($this);
		}
	}
	
	public function destroy(){
		
		setcookie(session_name(),'',0,'/');
		session_unset();
		session_destroy();
		session_write_close();
		
		
		
	}
	public function error(){
		return $this->error;
	}
	
	public function errorno(){
		return $this->errorno;
	}
	
	
	

}

class SessionException extends Exception{

	public function __construct(Session $res)
	{
		parent::__construct($res->error() , $res->errorno() );
	}

}