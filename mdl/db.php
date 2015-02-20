<?php
/**
 * Fabryka dla obiektów bazodanowych.
 */
class Proza_DBException extends Exception {}
class DB {
	/*obiekt bazy danych*/
	public $db;
	function __construct() {
		$file = DOKU_INC . 'data/proza.sqlite';
		$this->db = new SQLite3($file);
		if (!$this->db) 
			throw new DBException("Failed to open SQLite DB file($file): ". $this->db->lastErrorMsg());
	}

	function spawn($name) {
		$name = 'Proza_'.ucfirst($name);
		return new $name($this);
	}

	function escape($s) {
		return "'".$this->db->escapeString(trim($s))."'";
	}

	function query($query)
	{
		$r = @$this->db->query($query);
		if (!$r)  {
			throw new Proza_DBException("SQLite error(".$this->db->lastErrorCode()."): ".
			$this->db->lastErrorMsg()."\nQuery: $query");
		}
		return $r;
	}
}
