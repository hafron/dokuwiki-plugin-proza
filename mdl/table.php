<?php
require_once DOKU_PLUGIN."proza/mdl/db.php";

class Table {
	/*obiekt bazy danych*/
	$db;

	/*pola w tabeli*/
	$fields = array();

	/*nazwa tabeli*/
	$name;

	$text_max = 65000;

	function __construct($db) {
		$this->db = $db;
		$this->name = strtolower(get_class($this));

		$q = "CREATE TABLE IF NOT EXISTS $name (";
		$q .= ")";
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
			throw new Exception($errors);
	}

	function select($fields='*', $filters=array()) {
		$this->validate($filters);
		return $this->db->query("SELECT $fields FROM ".$this->name."");
	}

}
