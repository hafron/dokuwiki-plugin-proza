<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";
require_once DOKU_PLUGIN."proza/mdl/categories.php";

class Proza_Entities extends Proza_Table {
	public $fields = array(
			'code'	=> array('TEXT', 'NOT NULL', 'PRIMARY KEY'),
			'name'	=> array('TEXT', 'NOT NULL')
		);
	function __construct($db) {

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
