<?php
require_once DOKU_PLUGIN."proza/mdl/db.php";

class Proza_ValException extends Exception {
	private $errors;
	function setErrors($errors) {
		$this->errors = $errors;
	}
	function getErrors() {
		return $this->errors;
	}
}
abstract class Proza_Table {
	public $db, $name, $fields;
	public $text_max = 65000;
	public $insert_skip = array();

	function __construct($db) {
		$this->db = $db;
		$name = strtolower(get_class($this));
		$name = str_replace('proza_', '', $name);
		$this->name = $name;


		$columns = array();
		$db_constraints = array('INTEGER', 'TEXT', 'NOT NULL', 'PRIMARY KEY', 'UNIQUE');
		foreach ($this->fields as $f => $c) {
			$columns[] = $f.' '.implode(' ', array_intersect($db_constraints, $c));
		}

		$q = "CREATE TABLE IF NOT EXISTS ".$this->name." (";
		$q .= implode(',', $columns);
		$q .= ")";

		$this->db->query($q);
	}

	function validate($post, $skip_null=false) {
		$errors = array();
		foreach ($this->fields as $f => $c) {
			if (!array_key_exists($f, $post)) {
				if ($skip_null) continue;
				if (in_array($f, $this->insert_skip)) continue;
				if (in_array('NOT NULL', $c)) $errors[] = array($f, 'not_null');
				continue;
			} 
			$v = $post[$f];

			if (in_array('NOT NULL', $c) && trim($v) == '')
				$errors[] = array($f, 'not_null');
			else if (in_array('INTEGER', $c) && !is_numeric($v))
				$errors[] = array($f, 'integer');
			else if (in_array('TEXT', $c) && strlen($v) > $this->text_max)
				$errors[] = array($f, 'text', $this->text_max);
			else if (in_array('date', $c) && strtotime($v) === false)
				$errors[] = array($f, 'date'); 
			else if (in_array('PRIMARY KEY', $c) || in_array('UNIQUE', $c)) {
				$r = $this->select($f);
				while ($row = $r->fetchArray()) {
					if ($v == $row[$f]) {
						$errors[] = array($f, 'unique');
						break;
					}
				}
			} else if (isset($c['list']) && !in_array($v, $c['list']))
				$errors[] = array($f, 'list', $grps);
		}
		if (count($errors) > 0) {
			$e = new Proza_ValException($this->name);
			$e->setErrors($errors);
			throw $e;
		}
	}

	function primary_key() {
		foreach ($this->fields as $f => $c) 
			if (in_array('PRIMARY KEY', $c))
				return $f;
	}

	function select($fields='*', $filters=array(), $order='', $desc='ASC') {
		if ( ! is_array($fields)) 
			$fields = array($fields);
		$this->validate($filters, true);

		if ($order == '')
			$order = $this->primary_key();


		$conds = array();
		foreach ($this->fields as $f => $c)
			if (isset ($filters[$f]))
				$conds[] = $f.'='.$this->db->escape($filters[$f]);

		return $this->db->query("SELECT ".implode(',', $fields)." FROM ".$this->name
								.(count($conds) > 0 ? ' WHERE ' : ' ').implode('AND', $conds)."
								ORDER BY $order $desc");
	}

	function insert($post) {
		$this->validate($post);
		$toins = array_intersect_key($post, $this->fields);
		$fs = array();
		$vs = array();
		foreach ($this->fields as $f => $c) {
			if (in_array($f, $this->insert_skip)) continue;
			$fs[] = $f;

			if ( ! isset($post[$f]))
				$vs[] = 'NULL';
			else if (in_array('date', $c))
				$vs[] = $this->db->escape(date('Y-m-d', strtotime($post[$f])));
			else
				$vs[] = $this->db->escape($post[$f]);
		}
		$this->db->query("INSERT INTO ".$this->name." (".implode(',', $fs).") VALUES (".implode(',', $vs).")");
	}

	function delete($pk) {
		$pk_f = $this->primary_key();
		$this->db->query("DELETE FROM ".$this->name." WHERE $pk_f=".$this->db->escape($pk));
	}
}
