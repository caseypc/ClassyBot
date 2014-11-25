<?php
class IRCQuotes {
	public function __construct($quotes_database_path = 'quotes.db') {
		$this->version						= '0.1';
		$this->branch						= 'dev';
		$this->quotes_database_path			= $quotes_database_path;
		$this->debugging = true;
	}
	public function debug($message) {
		if($this->debugging == true) {
			echo "[DEBUG]".$message."\n";
		}
	}
	public function open_database() {
		try {
			$this->db = new PDO('sqlite:'.$this->quotes_database_path);
		} catch(PDOException $ex) {
			$this->error_msg = $ex->getMessage();
			return false;
		}
		return true;
	}
	public function close_database() {
		$this->db = NULL;
		return true;
	}

	
	public function add_quote($channel, $author, $text) {
		$this->debug("Opening quotes database");
		if($this->open_database() == false) {
			return false;
		}
		$this->debug("Database Open");
		try {
			$statement=$this->db->prepare("INSERT INTO quotes (quote_channel,quote_author,quote_text) VALUES (:channel,:author,:text)");
			$statement->execute(array('channel' => $channel, 'text' => $text, 'author' => $author));
		} catch(PDOException $ex) {
			$this->error_msg = $ex->getMessage();
			echo $this->error_msg."\n";
			return false;
		}
		$this->debug("Closing Database");
		$this->last_insert_id=$this->db->lastInsertId('quote_id');
		$this->close_database();
		$this->debug("Database Closed");
		return true;
	}

	public function del_quote($quote_id = NULL) {
		if($quote_id == NULL) {
			return false;
		}
		echo "Opening database\n";
		if($this->open_database() == false) {
			return false;
		}
		try {
			echo "Preparing statement\n";
			$statement=$this->db->prepare("DELETE FROM quotes WHERE quote_id = :id");
			echo "Executing statement\n";
			$statement->execute(array('id' => $quote_id));
		} catch(PDOException $ex) {
			$this->error_msg = $ex->getMessage();
			return false;
		}
		$this->close_database();
		return true;
	}

	public function edit_quote($quote_id, $field, $value) {
		//Placeholder
	}

	public function view_quote($quote_id = NULL) {
		if($quote_id == NULL) {
			$this->debug("Quote ID is NULL");
			return false;
		}
		if(!is_numeric($quote_id)) {
			$this->debug("QuoteID ".$quote_id." is not numeric");
			return false;
		}
		$this->debug("Opening Quotes database");
		if($this->open_database() == false) { return false; }
		$this->debug("Quotes database opened.");
		try {
			$this->debug("Preparing STatement");
			$statement = $this->db->prepare("SELECT * FROM quotes WHERE quote_id=:id LIMIT 1;");
			$this->debug("Statement prepared.");
			$this->debug("Executing");
			$statement->execute(array('id' => $quote_id));
			$this->debug("done.");
		} catch(PDOException $ex) {
			$this->error_msg = $ex->getMessage();
			return false;
		}
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$this->close_database();
		return $rows;
	}

	public function random_quote() {
		$this->debug("Opening quotes database");
		if($this->open_database() == false) { return false; }
		$this->debug("done.");
		try {
			$this->debug("Getting random quote from table quotes");
			$statement=$this->db->prepare("SELECT * FROM quotes ORDER BY RANDOM() LIMIT 1;");
			$this->debug("executing statement");
			$statement->execute();
			$this->debug("Done");
		} catch(PDOException $ex) {
			$this->error_msg = $ex->getMessage();
			$this->debug($ex->getMessage());
			return false;
		}
		$this->close_database();
		$rows=$statement->fetchAll(PDO::FETCH_ASSOC);
		if($this->debugging == true) { var_dump($rows); }
		return $rows;
	}
	public function send_to_log($level, $message) {
		//Placeholder
	}
}
?>