<?php
    
	require_once("rest.inc.php");
	
	class FLEET extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "siva";
		const DB = "users";
		
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();				
			$this->dbConnect();	
		}
		
		private function dbConnect(){
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
				mysql_select_db(self::DB,$this->db);
		}
		
		public function initFleet(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				
		}
			
		private function fleets(){	
			// validate if the request method is GET
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$sql = mysql_query("SELECT device_id, device_label, last_reported, if(TIMESTAMPDIFF(HOUR, last_reported, now()) < 24,'OK','OFFLINE') AS status FROM fleets", $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$result[] = $rlt;
				}
				// Return list in JSON format
				$this->response($this->json($result), 200);
			}
			$this->response('',204);	
		}
		
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	
		
	$fleet = new FLEET;
	$fleet->initFleet();
?>