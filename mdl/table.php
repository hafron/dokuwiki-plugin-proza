<?php
require_once DOKU_PLUGIN."proza/mdl/db.php";

class Proza_ValException extends Exception {}
abstract class Proza_Table {
	public $db, $name, $fields;
	public $text_max = 65000;

	function __construct($db) {
		$this->db = $db;
		$this->name = strtolower(get_class($this));

		$columns = array();
		$db_constraints = array('INTEGER', 'TEXT', 'NOT NULL', 'PRIMARY KEY');
		foreach ($this->fields as $f => $c) {
			$columns[] = $f.' '.implode(' ', array_intersect($db_constraints, $c));
		}

		$q = "CREATE TABLE IF NOT EXISTS ".$this->name." (";
		$q .= implode(',', $columns);
		$q .= ")";

		$this->db->query($q);
	}

	function find_array($a) {
		foreach ($a as $v)
			if (is_array($v))
				return $v;
		return false;
	}

	function validate($post) {
		$errors = array();
		foreach ($this->fields as $f => $c) {
			$v = $post[$f];
			if (in_array('NOT NULL', $c) && $v == '')
				$errors[] = array($f, 'not_null');
			else if (in_array('INTEGER', $c) && !is_numeric($v))
				$errors[] = array($f, 'integer');
			else if (in_array('TEXT', $c) && strlen($v) > $this->text_max)
				$errors[] = array($f, 'text');
			else if (($grps = $this->find_array($c)) && !in_array($v, $grps))
				$errors[] = array($f, 'grp', $grps);
		}
		if (count($errors) > 0)
			throw new Proza_ValException($errors);
	}

	function select($fields='*', $filters=array()) {
		$this->validate($filters);
		return $this->db->query("SELECT $fields FROM ".$this->name."");
	}

	function insert($post) {
		$this->validate($post);
		$toins = array_intersect_key($post, $this->fields);
		$fs = array();
		$vs = array();
		foreach ($toins as $f => $v) {
			$fs[] = $f;
			if (in_array('INTEGER', $this->fields[$f])) 
				$vs[] = $v;
			else
				$vs[] = "'".$this->db->escape($v)."'";
		}
		$this->db->query("INSERT INTO $table (".implode(',', $fs).") VALUES (".implode(',', $vs).")");
	}
}
