<?php /* version 1.0 desenvolvida pelo ST Mathias (Mar/19) */
class Ldap{

	const RDNATTR = "cpf";
	/**
	 * PHP's native ldap resource object
	 * @var resource (ldap link)
	 */
	protected $resource;
	/**
	 * PHP's native ldap resource object
	 * @var resource (ldap search)
	 */
	protected $search;
	/**
	 * @var array ldap get_entries output
	 */
	protected $info;
	/**
	 * @var integer The status code of the last executed ldap operation
	 */
	protected $code;
	/**
	 * @var string usuario administrador da base
	 */
	protected $adminuser;
	/**
	 * @var string senha do usuario administrador da base
	 */
	protected $adminpass;
	/**
	 * @var string The status message of the last executed ldap operation
	 */
	protected $message;
	/**
	 * @var bool The status of the last executed ldap operation
	 */
	protected $success;
	/**
	 * @var array mapeamento dos erros exibidos aos usuários
	 */
	protected $errormap = array(-1 => "Não foi possivel conectar com o servidor",
								49 => "Credenciais inválidas",
			   					50 => "Permissões insuficientes",
								68 => "Usuário ja cadastrado"
	);
	/**
	 * Create a new instance
	 *
	 * If $ldapUrl is provided, it will also open connection to the ldap server by calling
	 * self::connect().
	 *
	 * @param string $ldapUrl Optional ldap URI string of the ldap server
	 */
	public function __construct($ldapUrl = null){
		$docRoot = getenv("DOCUMENT_ROOT");                
		$data = parse_ini_file($docRoot . "/siscau.ini",true);
		$ldapUrl = $data["LDAP"]["url"];                
		$this->adminuser = $data["LDAP"]["usuario"];
		$this->adminpass = $data["LDAP"]["senha"];
               
		if($ldapUrl){
			$this->connect($ldapUrl);
		}
	}	
	public function connect($ldapUrl)	{
		// Make sure the connection has been established successfully
		if (! $this->resource = @ldap_connect($ldapUrl)) {                    
			throw new Exception(sprintf("Unable to connect to ldap server %s", $ldapUrl));
		}
		// Set sane defaults for ldap v3 protocol
		ldap_set_option($this->resource , LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->resource , LDAP_OPT_REFERRALS, 0);
		return $this;
	}
	public function bind($bindDn = null, $bindPassword = null)
	{
		if(($bindDn==null)||($bindPassword==null)){
			throw new LdapException($this , true , "Bind Anonimo" , 666 );
		}else{                       
			if(!($this->success = @ldap_bind($this->resource, $bindDn, $bindPassword))){
                            
				throw new LdapException($this);
			}
		}                
		return $this;                
	}
	public function search($basedn , $filter , array $attributes = []){          
		$this->search =ldap_search($this->resource, $basedn, $filter , $attributes);                
		return $this;
	}
	public function lista($basedn , $filter , array $attributes = []){
		$this->search =ldap_list($this->resource, $basedn, $filter , $attributes);
		return $this;
	}
	public function read($basedn , array $attributes = [] , $filter = "(objectclass=*)"){
		$this->search =ldap_read($this->resource, $basedn, $filter , $attributes);
		return $this;
	}
	public function getEntries(){
		$info = $this->info = ldap_get_entries($this->resource, $this->search);
		if(!$this->info){
			throw new LdapException($this);
		}             
		return $this;
	}
	public function modify_batch($dn, $modifs){
		if(!($modify = @ldap_modify_batch($this->resource, $dn, $modifs))){
			throw new LdapException($this);
		}
	}
	public function parseOms(){
		$info = $this->info;
		$data = array();
		$oms = array();
		$om = array();
		for($i=0;$i<$info["count"];$i++){
			$om["om"] = $info[$i]["dc"][0];	
			array_push($oms , $om);
			$om = array();
		}
		$data["oms"] = $oms;
		return $data;			
	}
        //*****Organaniza o array que preenche a caixa de seleção com os administradores (inclusão ST Mathias)*****  
        public function parseCpf(){
		$info = $this->info;
		$data = array();
		$cpfs = array();
		$cpf = array();
		for($i=0;$i<$info["count"];$i++){
			$cpf["cpf"] = $info[$i]["cpf"][0];
                        $cpf["desc"] = $info[$i]["posto"][0] ." ". $info[$i]["nomeguerra"][0];
                        switch ($info[$i]["posto"][0]) {
                            case 'Gen Ex':
                            $cpf["antigo"] = 2;
                            break;
                          case 'Gen Div':
                           $cpf["antigo"] = 3;
                            break;
                          case 'Gen Bda':
                            $cpf["antigo"] = 8;
                            break;
                            case 'Cel':
                            $cpf["antigo"] = 11;
                            break;
                          case 'Ten Cel':
                           $cpf["antigo"] = 12;
                            break;
                          case 'Maj':
                            $cpf["antigo"] = 13;
                            break;
                        case 'Cap':
                            $cpf["antigo"] = 14;
                            break;
                        case '1 Ten':
                            $cpf["antigo"] = 15;
                            break;
                        case '2 Ten':
                            $cpf["antigo"] = 16;
                            break;
                        case 'Asp':
                            $cpf["antigo"] = 17;
                            break;
                        case 'S Ten':
                            $cpf["antigo"] = 18;
                            break;
                        case '1 Sgt':
                            $cpf["antigo"] = 19;
                            break;
                        case '2 Sgt':
                            $cpf["antigo"] = 20;
                            break;
                        case '3 Sgt':
                            $cpf["antigo"] = 21;
                            break;
                        case 'Aluno':
                            $cpf["antigo"] = 22;
                            break;
                        case 'Cb':
                            $cpf["antigo"] = 23;
                            break;
                        case 'Sd':
                            $cpf["antigo"] = 24;
                            break;
                        case 'TM':
                            $cpf["antigo"] = 25;
                            break;
                        case 'T1':
                            $cpf["antigo"] = 26;
                            break;
                        case 'T2':
                            $cpf["antigo"] = 27;
                            break;
                        case 'FC':
                            $cpf["antigo"] = 28;
                            break;
                         case 'Colaborador':
                            $cpf["antigo"] = 29;
                            break;
}
			array_push($cpfs , $cpf);
			$om = array();
		}                
		$data["cpfs"] = $cpfs;
		return $data;			
	}
        //***** Fim Organaniza o array que preenche a caixa de seleção com os administradores*****	
	public function parseUsers(){
		$info = $this->info;                
		$data = array();
		$users = array();
		$usersat=array();
		$userinat=array();
		$user = array();
		$servicos = array();
		$aceite = array();
		for($i=0;$i<$info["count"];$i++){
                    
			for($j=0; $j<$info[$i]["count"];$j++){
				if(($info[$i][$j] != "perfil")&&($info[$i][$j] != "aceite")){
					$user[$info[$i][$j]] = $info[$i][$info[$i][$j]][0];
				}				
			}
			for($j = 0; $j<@$info[$i]["aceite"]["count"];$j++){
				$aceite[] = $info[$i]["aceite"][$j];                               
			}
			for($j=0;$j<$info[$i]["perfil"]["count"];$j++){
				if(@preg_match("/_/",$info[$i]["perfil"][$j])){
					$reaper = @preg_split("/_/", $info[$i]["perfil"][$j]) ;
					
                                        if($reaper[0] == "VPN"){
                                            $servicos[$reaper[0]]=$reaper[0];
                                        }else{
                                            $servicos[$reaper[0]]=$reaper[1]; 
                                        }
				}else{
					$servicos[$info[$i]["perfil"][$j]]=1;
                                        
				}
			}
			$user["servicos"] = $servicos;
			$user["dn"] = $info[$i]["dn"];
			$user["aceite"] = $aceite;
			$aceite = array();
			$servicos = array();
			if((!@in_array("USER" , $info[$i]["aceite"]))&&(@in_array(0 , $info[$i]["aceite"]))){
				array_push($userinat , $user);
			}else{
				array_push($usersat , $user);
			}
			array_push($users , $user);
			$user = array();
		}
		$data["users"] = $users;
		$data["usersAtivos"] = $usersat;
		$data["usersInativos"]=$userinat;
		$data["count"] = $info["count"];
		return $data;
	}
        
        //*********************Inclusao do parse userAdm (inclusão ST Mathias) **********************************************        
	public function parseUsersAdm(){
		$info = $this->info;                
		$data = array();
		$users = array();		
		$user = array();
                
		for($i=0;$i<$info["count"];$i++){                    
			for($j=0; $j<$info[$i]["count"];$j++){                            
                            $user[$info[$i][$j]] = $info[$i][$info[$i][$j]][0];                            
			}                    					
			
                    $user["dn"] = $info[$i]["dn"];			
                    array_push($users , $user);
                    $user = array();
		}
		$data["users"] = $users;		
		$data["count"] = $info["count"];
		return $data;               
	}        
        //*********************Fim da Inclusao do parse userAdm **********************************************
        
	public function add($path , $data){
		if(!($add = @ldap_add($this->resource , $path, $data))){
			throw new LdapException($this);
		}
	}
	public function  delete($path){
		if(!($delete = @ldap_delete($this->resource, $path))){
			throw new LdapException($this);
		}
	}
	public function modify($dn , $data){
		if(!($modify = @ldap_modify($this->resource, $dn, $data))){
			throw new LdapException($this);
		}
	}
	public function rename($dn, $newrdn, $newparent, $deleteoldrdn){
		if(!($rename = @ldap_rename($this->resource, $dn, $newrdn, $newparent, $deleteoldrdn))){
			throw new LdapException($this);			
		}
	}
	public function close(){
		ldap_close($this->resource);
	}
	
	public function entries(){
		return $this->info;
	}
	
	public function error(){
		$errno = (string) ldap_errno($this->resource);
		if(isset($this->errormap[$errno])){
			$errstr = $this->errormap[$errno];
		}else{
			$errstr = ldap_error($this->resource);
		}
		return $errstr;
	}
	public function errno(){
		return ldap_errno($this->resource);
	}
	
	public static function parseData($data , $oldData){
		$saida = array();
		$saida["modify"] = array();
		$modify = array();
		
		foreach ($data as $key => $value){
			if($key == "nomecompleto"){
				$reaper = preg_split('/ /',$value);
				$saida["modify"]['cn']          =  $value;
				$saida["modify"]["sn"]          = $reaper[count($reaper)-1];
				$saida["modify"]["givenName"]   = $reaper[0];
			}else if($key == "nomeguerra"){
				$saida["modify"][$key] = $value;
				if(isset($data["posto"])){
					$posto = $data["posto"];
				}else{
					$posto = $oldData[0]["posto"][0];
				}
				$saida["modify"]["displayname"] = $posto . " " . $value;
				
			}else if($key == "posto"){
				$saida["modify"][$key] = $value;
				if(isset($data["nomeguerra"])){
					$nomeguerra = $data["nomeguerra"];
				}else{
					$nomeguerra = $oldData[0]["nomeguerra"][0];
				}
				$saida["modify"]["displayname"] = $value . " " . $nomeguerra;
				
			}else if($key == "servicos"){
				$servicos = $value;
				if($oldData[0]['perfil']['count']>count($servicos)){
					if(in_array("VPN" , $oldData[0]["aceite"] )){
						$modifs = array(
								"attrib"  => "aceite",
								"modtype" => LDAP_MODIFY_BATCH_REMOVE,
								"values"  => ["VPN"],
						);
						$saida["modify_batch"] = array($modifs);
					}
					
				}
				$perfil= array();
				foreach ($servicos as $key => $value){
					$found = false;
					for($j=0;$j<$oldData[0]['perfil']['count'];$j++){
						if(preg_match("/".$key."/" , $oldData[0]['perfil'][$j])){
							$found = true;
							break;							
						}						
					}
					if($found) {
						continue;
					}else{
						$saida["newService"] = true;;
						break;						
					}
				}
				foreach ($servicos as $key => $value){
					if(! (($key=="VPN")&&($value==0))){
						$perfil[] = $key . "_" . $value;
					}
						
				}
				$saida["modify"]["perfil"] = $perfil;
			}else if(($key != self::RDNATTR)&&($key != "dn")&&($key!="mail") ){
				$saida["modify"][$key] = $value;
			}			
		}
		if(empty($saida["modify"])){
			unset($saida["modify"]);
		}
		return $saida;
	}
        
        //*****************Inclusao do parse parseDataAdm (inclusão ST Mathias)***************
        public static function parseDataAdm($data , $oldData){
		$saida = array();
		$saida["modify"] = array();
		$modify = array();
		
		foreach ($data as $key => $value){
		
                            if($key == "cpf"){
				$saida["modify"][$key] = $value;
                            }
                            if($key == "telephonenumber"){
				$saida["modify"][$key] = $value;
                            }                            
			}		
		if(empty($saida["modify"])){
			unset($saida["modify"]);
		}
		return $saida;
	}        
        //*****************Fim da Inclusao do parse parseDataAdm**************************       
        
	public function getAdminData(){
		return array(
				"user" => $this->adminuser,
				"pass" => $this->adminpass
		);
	}
	function __destruct() {
		if (get_resource_type($this->resource) == 'ldap link'){
			$this->close();
		}
		
	}
	
}