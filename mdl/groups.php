<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";

class Proza_Groups extends Proza_Table {
	public $insert_skip = array('id');
	public $fields = array(
			'id'	=> array('INTEGER', 'NOT NULL', 'PRIMARY KEY'),
			'pl'	=> array('TEXT', 'NOT NULL'),
			'en'	=> array('TEXT', 'NOT NULL')
		);
	function __construct($db) {
		$helper = plugin_load('helper', 'proza');
		parent::__construct($db);
	}
	function insert($post) {
		$post['pl'] = trim($post['pl']);
		$post['en'] = trim($post['en']);
		parent::insert($post);
	}

	function select_refs($id=-1) {
		$where = '';
		if ($id > -1) 
			$where = "WHERE id=$id";

		return $this->db->query("SELECT id, pl, en,
								(SELECT COUNT(*) FROM events WHERE group_n=groups.id) AS refs
								FROM groups $where");	
	}

	function delete($pk) {
		$r = $this->select_refs($pk)->fetchArray();
		$refs = (int)$r['refs'];
		if ($refs > 0) {
			$e = new Proza_ValException($this->name);
			$e->setErrors(array(array('refs', 'refs')));
			throw $e;
		}
		parent::delete($pk);
	}

	function groups($lang_code='en') {
		if (!in_array($lang_code, array('en', 'pl')))
			throw new Proza_DBException("unknown column: $lang_code in groups");
		$r = $this->db->query("SELECT id, $lang_code FROM groups");
		$groups = array();
		while ($row = $r->fetchArray())
			$groups[$row['id']] = $row[$lang_code];

		return $groups;
	}
}
