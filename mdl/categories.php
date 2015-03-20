<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";

class Proza_Categories extends Proza_Table {
	public $fields = array(
			'name'	=> array('TEXT', 'NOT NULL', 'PRIMARY KEY', 'wiki id'),
			'group_n' => array('TEXT', 'NOT NULL')
		);
	function __construct($db) {
		$helper = plugin_load('helper', 'proza');
		$fields['group']['list'] = array_keys($helper->groups());
		parent::__construct($db);
	}
	function insert($post) {
		$post['name'] = trim($post['name']);
		parent::insert($post);
	}
}
