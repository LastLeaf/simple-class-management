<?php if(!defined('__DIRECT_REQUEST__')) exit(-1);

class DbQuery {
	private $conn;
	public $prevSql;
	public $errno;
	public $error;
	public $autoIncreasementId;

	private function dieError() {
		header('HTTP/1.1 500 Internal Server Error');
		echo $this->conn->error;
		exit(-1);
	}

	// connect to server
	function __construct($host, $user, $password, $dbname, $port='3306', $dieOnError=FALSE) {
		$this->conn = new Mysqli($host, $user, $password, $dbname, $port);
		if($this->conn->connect_errno) {
			if($dieOnError) $this->dieError();
			$this->errno = $this->conn->connect_errno;
			$this->error = $this->conn->connect_error;
			$this->conn = FALSE;
			return;
		}
		$this->conn->set_charset('utf8');
		$this->errno = 0;
		$this->error = FALSE;
	}

	// run single query, return FALSE on error, TRUE or result array on success
	public function query($str, $dieOnError=FALSE) {
		$this->prevSql = $str;
		if($this->conn === FALSE)
			$r = FALSE;
		else
			$r = $this->conn->query($str, MYSQLI_STORE_RESULT);
		if($r === FALSE) {
			if($dieOnError) $this->dieError();
			$this->errno = $this->conn->errno;
			$this->error = $this->conn->error;
			return FALSE;
		}
		$this->autoIncreasementId = (int)$this->conn->insert_id;
		if($r === TRUE)
			return TRUE;
		$rt = array();
		while($row = $r->fetch_assoc())
			$rt[] = $row;
		$r->free();
		return $rt;
	}

	// run filtered single query
	public function filteredQuery($str, $arg=array(), $addQuotes=FALSE, $dieOnError=FALSE) {
		if($this->conn === FALSE) {
			if($dieOnError) $this->dieError();
			return FALSE;
		}
		if($addQuotes) {
			foreach($arg as $k => $v)
				if(is_string($v))
					$arg[$k] = '"'.$this->conn->real_escape_string($v).'"';
		} else {
			foreach($arg as $k => $v)
				if(is_string($v))
					$arg[$k] = $this->conn->real_escape_string($v);
		}
		
		return $this->query(vsprintf($str, $arg), $dieOnError);
	}

	// generate INSERT [OR REPLACE] query
	public function insert($table, $map, $replace=FALSE, $dieOnError=FALSE) {
		if($this->conn === FALSE) {
			if($dieOnError) $this->dieError();
			return FALSE;
		}
		foreach($map as $k => $v) {
			if(isset($sk)) {
				$sk .= ',';
				$sv .= ',';
			} else {
				$sk = '';
				$sv = '';
			}
			$sk .= '`'.$k.'`';
			if(is_string($v))
				$sv .= '"'.$this->conn->real_escape_string($v).'"';
			else
				$sv .= $v;
		}
		if($replace)
			return $this->query("REPLACE INTO `$table` ($sk) VALUES ($sv);", $dieOnError);
		else
			return $this->query("INSERT INTO `$table` ($sk) VALUES ($sv);", $dieOnError);
	}

	// generate UPDATE query
	public function update($table, $map, $where, $whereArg, $addQuotes=FALSE, $dieOnError=FALSE) {
		if($this->conn === FALSE) {
			if($dieOnError) $this->dieError();
			return FALSE;
		}
		foreach($map as $k => $v) {
			if(isset($s))
				$s .= ',';
			else
				$s = '';
			if(is_string($v))
				$s .= '`'.$k.'`="'.$this->conn->real_escape_string($v).'"';
			else
				$s .= '`'.$k.'`="'.$v.'"';
		}
		$s = str_replace('%', '%%', $s);
		return $this->filteredQuery("UPDATE `$table` SET $s WHERE $where", $whereArg, $addQuotes, $dieOnError);
	}

	// generate UPDATE-CONCAT query
	public function append($table, $map, $where, $whereArg, $addQuotes=FALSE, $dieOnError=FALSE) {
		if($this->conn === FALSE) {
			if($dieOnError) $this->dieError();
			return FALSE;
		}
		foreach($map as $k => $v) {
			if(isset($s))
				$s .= ',';
			else
				$s = '';
			$s .= '`'.$k.'`=IFNULL(CONCAT(`'.$k.'`, '.$v.'), '.$v.')';
		}
		$s = str_replace('%', '%%', $s);
		return $this->filteredQuery("UPDATE `$table` SET $s WHERE $where", $whereArg, $addQuotes, $dieOnError);
	}

	// generate DELETE query
	public function del($table, $where, $whereArg, $addQuotes=FALSE, $dieOnError=FALSE) {
		return $this->filteredQuery("DELETE FROM `$table` WHERE $where", $whereArg, $addQuotes, $dieOnError);
	}
}

/* END */
