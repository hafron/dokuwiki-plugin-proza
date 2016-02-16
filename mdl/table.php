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
	public $update_skip = array();
	public $relations = array();

	function __construct($db) {
		$this->db = $db;
		$name = strtolower(get_class($this));
		$name = str_replace('proza_', '', $name);
		$this->name = $name;


		$columns = array();
		$db_constraints = array('INTEGER', 'TEXT', 'NOT NULL', 'PRIMARY KEY', 'UNIQUE');
		foreach ($this->fields as $f => $c) {
			$inter = array_intersect($db_constraints, $c);
			$columns[] = $f.' '.implode(' ', $inter);
		}

		$q = "CREATE TABLE IF NOT EXISTS ".$this->name." (";
		$q .= implode(',', $columns);
		$q .= ")";

		$this->db->query($q);
	}


	function pk_unique($post, $skip=array()) {
		$pkf = $this->primary_key();
		if (in_array($f, $skip)) return;

		$v = $post[$pkf];
		if (!isset($v))
			return;

		$r = $this->select($pkf);
		while ($row = $r->fetchArray()) {
			if ($v == $row[$pkf]) {
				$errors = array();
				$errors[] = array($pkf, 'unique');

				$e = new Proza_ValException($this->name);
				$e->setErrors($errors);
				throw $e;
			}
		}
	}

	function pk_exists($post) {
		$pkf = $this->primary_key();
		if (!isset($post[$pkf]))
			return;

		$v = $post[$pkf];

		//filtry - sprawdzamy czy id istnieje
		$r = $this->select($pkf);
		$exists = false;
		while ($row = $r->fetchArray()) {
			if ($v == $row[$pkf]) {
				$exists = true;
				break;
			}
		}
		if (!$exists) {
			$errors = array();
			$errors[] = array($pkf, 'not_exists');

			$e = new Proza_ValException($this->name);
			$e->setErrors($errors);
			throw $e;
		}
	}

	function validate($post, $skip_empty=false, $skip=array()) {

		$errors = array();
		foreach ($this->fields as $f => $c) {

			if (in_array($f, $skip)) continue;

			/*nie ma pola w zapytaniu i nie przeskakujemy go w insercie*/
			if (!array_key_exists($f, $post) && $skip_empty) continue;

			$v = $post[$f];

			if (in_array('NOT NULL', $c) && trim($v) == '')
				$errors[] = array($f, 'not_null');
			else if (in_array('NULL', $c) && trim($v) == '')
				continue;
			else if (in_array('INTEGER', $c) && !is_numeric($v))
				$errors[] = array($f, 'integer');
			else if (in_array('TEXT', $c) && strlen($v) > $this->text_max)
				$errors[] = array($f, 'text', $this->text_max);
			else if (in_array('date', $c) && $v != '' && strtotime($v) === false)
				$errors[] = array($f, 'date'); 
			else if (in_array('wiki id', $c) && $v != '' && cleanID($v) != $v)
				$errors[] = array($f, 'wiki_id'); 
			else if (isset($c['list']) && !in_array($v, $c['list']))
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
				return $this->name.'.'.$f;
	}

	function select($fields='*', $filters=array(), $order='', $desc='ASC') {
		if ( ! is_array($fields)) 
			$fields = array($fields);
		if ( ! is_array($filters)) 
			$filters=array();
		
		/*uwaga na: array(BETWEEN, date1, date2) */
		$vf = array();
		foreach ($filters as $f) {
			if (is_array($f)) {
				$vf[] = $f[1];
				$vf[] = $f[2];
			} else
				$vf[] = $f;
		}
		$this->pk_exists($vf);
		$this->validate($vf, true);

		if ($order == '')
			$order = $this->primary_key();

		$conds = array();
		$from = array($this->name);
		foreach ($this->relations as $rel) {
			$from[] = $rel[0];
			$conds[] = "$rel[1]=$rel[2]";
		}

		foreach ($filters as $f => $v) {
			if (is_array($v)) {
				if ($v[0] == 'BETWEEN')
					$conds[] = $f.' BETWEEN '.$this->db->escape($v[1]).' AND '.$this->db->escape($v[2]);
				else if ($v[0] == 'LIKE')
					$conds[] = $f.' LIKE '.$this->db->escape($v[1]);
			} else {
				$conds[] = $f.'='.$this->db->escape($v);
			}
		}

		$q = "SELECT ".implode(',', $fields)." FROM ".implode(',', $from)
			.(count($conds) > 0 ? ' WHERE ' : ' ').implode(' AND ', $conds)."
			ORDER BY $order $desc";
		return $this->db->query($q);
	}

	function defaults(&$post) {
		/*wstawiamy domyślne wartość dla pól z "DEFAULT"*/
		foreach ($this->fields as $f => $c)
			if (!isset($post[$f]) || $post[$f] == '')
				foreach ($c as $con) 
					if (is_string($con) && strpos($con, 'DEFAULT') === 0) 
						$post[$f] = trim(substr($con, strlen('DEFAULT')));
	}
	function dbfield_prepare($c, $v) {
		if ( ! isset($v) || trim($v) == '') {
			return 'NULL';
		} else if (in_array('date', $c)) {
			$helper = plugin_load('helper', 'proza');
			return $this->db->escape($helper->norm_date($v));
		} else
			return $this->db->escape($v);
	}

	function insert($post) {
		$this->defaults($post);
		$this->pk_unique($post, $this->insert_skip);
		$this->validate($post, false, $this->insert_skip);
		$fs = array();
		$vs = array();
		foreach ($this->fields as $f => $c) {
			if (in_array($f, $this->insert_skip)) continue;
			$fs[] = $f;
			$vs[] = $this->dbfield_prepare($c, $post[$f]);
		}
		$this->db->query("INSERT INTO ".$this->name." (".implode(',', $fs).") VALUES (".implode(',', $vs).")");
	}

	function update($post, $id) {
		//$this->defaults($post);
		$this->pk_unique($post, $this->update_skip);
		$this->validate($post, false, $this->update_skip);
		$v = array();
		foreach ($post as $f => $c) {
			if (!array_key_exists($f, $this->fields)) continue;
			if (in_array($f, $this->update_skip)) continue;
			$v[] = $f.'='.$this->dbfield_prepare($this->fields[$f], $c);
		}
		$pk_f = $this->primary_key();
		$this->db->query("UPDATE ".$this->name." SET ".implode(',', $v)." WHERE $pk_f=".$this->db->escape($id));
	}

	function delete($pk) {
		$pk_f = $this->primary_key();
		$this->db->query("DELETE FROM ".$this->name." WHERE $pk_f=".$this->db->escape($pk));
	}

}
