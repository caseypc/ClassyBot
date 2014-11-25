<?php
class ircConfigurationManager {
	public function __construct($configuration_path = 'config.json') {
		$this->config_filepath	= $configuration_path;
		$this->read_config();
	}
	
	public function read_config() {
		$this->config	= json_decode(file_get_contents($this->config_filepath));
		$this->config_array		= json_decode(file_get_contents($this->config_filepath), true);
		return true;
	}
	
	public function write_config() {
		file_put_contents($this->config_filepath, json_encode($this->config_array, JSON_PRETTY_PRINT));
		$this->read_config();
		return true;
	}
	
	public function write_mod_settings($module = NULL, $field=Null, $value=NULL) {
		if($value == NULL) { return false; }
		var_dump($this->config->modules);
		$this->config_array['modules'][$module][$field]=$value;
		$this->write_config();
		return true;
	}
	
	public function write_core_settings($field = NULL, $value = NULL) {
		if($value == NULL) { return false; }
		$this->config_array[$field]=$value;
		$this->write_config();
		return true;
	}
	
	public function write_user_settings($user = NULL, $setting = NULL, $value = NULL) {
		if($value == NULL) { return false; }
		$this->config_array['users'][$user][$setting]=$value;
		$this->write_config();
		return true;
	}
	
	public function write_channel_settings($channel = NULL, $setting = NULL, $value = NULL) {
		if($value == NULL) { return false; }
		$this->config_array['channel_settings'][$channel][$setting]=$value;
		$this->write_config();
		return true;
	}
	
	public function add_channel($channel = NULL) {
		if($channel == NULL) { return false; }
		array_push($this->config_array['autojoin_channels'], $channel);
		$this->write_config();
		return true;
	}
	
	public function del_channel($channel = NULL) {
		if($channel == NULL) { return false; }
		$this->config_array['autojoin_channels'] = array_diff($this->config_array['autojoin_channels'], array($channel));
		$this->write_config();
		return true;
	}
	public function check_user_perms($flag = NULL, $user = NULL, $hostname = NULL) {
		echo "Checking args\n";
		if($hostname == NULL) {
			$this->error_msg = "Host not found or invalid";
			return false;
		}
		echo "Checking User\n";
		if(!isset($this->config->users->$user)) {
			$this->error_msg = "User not found";
			return false;
		}
		echo "Checking hostname matches\n";
		if(!isset($this->config->users->$user->hostname) || $this->config->users->$user->hostname != $hostname) {
			$this->error_msg = "Could not verify permissions";
			return false;
		}
		echo "Checking Permissions 1/3\n";
		if(!isset($this->config->users->$user->flags)) {
			$this->error_msg = "User has no permissions";
			return false;
		}
		echo "Checking permissions 2/3\n";
		if(strstr($this->config->users->$user->flags, $flag) != false) {
			return true;
		}
		echo "Checking permissions 3/3\n";
		if(strstr($this->config->users->$user->flags, '*') != false) {
			return true;
		}
		$this->error_msg=NULL;
		echo "No match!\n";
		return false;
	}
}