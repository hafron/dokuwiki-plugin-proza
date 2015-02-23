<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";
require_once DOKU_PLUGIN."proza/mdl/categories.php";

class Proza_Events extends Proza_Table {
	public $fields = array(
			'id'	=> array('INTEGER', 'NOT NULL', 'PRIMARY KEY'),
			'code'  => array('TEXT', 'NOT NULL', 'UNIQUE'),
			'name'  => array('TEXT', 'NOT NULL'),
			'group_n' => array('TEXT', 'NOT NULL'),
			'plan_date' => array('TEXT', 'NOT NULL'),
			'assumptions' => array('TEXT', 'NOT NULL'),
			'coordinator' => array('TEXT', 'NOT NULL'),
			'summary' => array('TEXT', 'NULL'),
			'finish_date' => array('TEXT', 'NULL')
		);
	function __construct($db) {
		global $auth;
		$fields['coordinator']['list'] = array_keys($auth->retrieveUsers());

		$categories = $db->spawn('categories');
		$cat_keys = array();
		$r = $categories->select('name');
		while ($row = $r->fetchArray()) {
			$cat_keys[] = $row['name'];
		}
		$fields['name']['list'] = $cat_keys;

		parent::__construct($db);
	}
}
