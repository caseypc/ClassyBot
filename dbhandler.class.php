<?php
class classybot_db_handler {
	public function __construct($database_path = 'classybot.db') {
		global $database;
		if(file_exists($database_path)) {
			try {
				//Connect to database
				$database = new PDO('sqlite:'.$database_path);
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		} else {
			try {
				//Create new database if it does not already exist.
				$database = new PDO('sqlite:'.$database_path);
				$database->exec("CREATE TABLE 'configuration' ('key' TEXT PRIMARY KEY NOT NULL, 'value' TEXT)");
				$database->exec("CREATE TABLE 'quotes' ('qid' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'time_added' DATETIME DEFAULT CURRENT_TIMESTAMP, 'who_added' INTEGER NOT NULL DEFAULT 'Unknown', 'quote_text' INTEGER NOT NULL)");
				$database->exec("CREATE TABLE 'auth' ('uid' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'nick' TEXT, 'ident' TEXT, 'host' TEXT, 'login' TEXT, 'password' TEXT)");
				require_once("config.php");
				while(list($config_key, $config_value) = each($CONFIG)) {
					$this->configAdd($config_key, $config_value);
				}
			} catch(PDOException $e) { echo '[DATABASE ERROR]'.$e->getMessage(); }
			
		}
	}
	public function construct_config() {
		global $database;
		try{
			$sql=$database->query("SELECT * FROM 'configuration'");
			echo var_dump($sql);
			$CONFIG=array();
			foreach($sql as $conf_item) {
				$key=$conf_item['key'];
				$value=$conf_item['value'];
				$CONFIG[$key]=$value;
			}
			return $CONFIG;
		} catch(PDOException $e) { die('[DATABASE ERROR] Could not read config from database'.$e->getMessage()); }
	}
	
	public function configAdd($key, $value) {
		global $database;
		$key=SQLite3::escapeString($key);
		$value=SQLite3::escapeString($value);
		try {
			$database->exec("DELETE FROM configuration WHERE key = '".$key."'"); //Remove old value if it exists
			$database->exec("INSERT INTO 'configuration' ('key','value') VALUES ('".$key."','".$value."')"); //Add new value for config variable
		} catch(PDOException $e) { echo '[DATABASE ERROR]'.$e->getMessage(); }
	}
	public function configRead($key) {
		global $database;
		$key=SQLite3::escapeString($key);
		try {
			$sql=$database->query("SELECT * FROM configuration WHERE key = '".$key."'");
			foreach($sql as $reply) {
				if(is_array($reply) && count($reply) >= 1) { return $reply['value']; }
				else { return NULL; }
			}
		} catch(PDOException $e) { echo '[DATABASE ERROR]'.$e->getMessage(); }
	}
	public function configDelete($key) {
		global $database;
		$key=SQLite3::escapeString($key);
		try {
			$database->exec("DELETE FROM 'configuration' WHERE key = '".$key."')");
		} catch(PDOException $e) { echo '[DATABASE ERROR]'.$e->getMessage(); }
	}
	public function channel_add($channel) {
			if($this->configRead('channels') == NULL) {
				$this->configAdd('channels', $channel);
			} else {
				$channel_list=explode(',', $this->configRead('channels'));
				array_push($channel_list, $channel);
				$channel_list=array_unique($channel_list);
				$this->configAdd('channels', implode(',', $channel_list));
			}
	}
	public function channel_del($channel) {
		if($this->configRead('channels') == NULL) {
			return false;
		} else {
			$channel_list = explode(',', $this->configRead('channels'));
			$channel_list = array_diff($channel_list, array($channel));
			$this->configAdd('channels', $channel_list);
		}
	}
}
?>