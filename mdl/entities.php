<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";

class Proza_Entities extends Proza_Table {
	public $fields = array(
			'id'	=> array('INTEGER', 'PRIMARY KEY'),
			'name'	=> array('TEXT', 'NOT NULL'),
			'code'	=> array('TEXT', 'NOT NULL'),
			'group_n' => array('TEXT', 'NOT NULL'),
			'coordinator' => array('TEXT', 'NOT NULL')
		);
	function __construct($db) {
		global $auth, $conf;

		$helper = plugin_load('helper', 'proza');
		$fields['group'][] = array_keys($helper->groups());
		$fields['coordinator'] = array_keys($auth->retrieveUsers());
		parent::__construct($db);
	}
}
