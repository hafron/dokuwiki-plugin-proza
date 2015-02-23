<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";
require_once DOKU_PLUGIN."proza/mdl/categories.php";

class Proza_Events extends Proza_Table {
	public $fields = array(
			'id'	=> array('INTEGER', 'NOT NULL', 'PRIMARY KEY'),
			'name'  => array('TEXT', 'NOT NULL'),
			'group_n' => array('TEXT', 'NOT NULL'),
			'plan_date' => array('TEXT', 'date', 'NOT NULL'),
			'assumptions' => array('TEXT', 'NOT NULL'),
			'assumptions_cache' => array('TEXT', 'NULL'),
			'coordinator' => array('TEXT', 'NOT NULL'),
			'summary' => array('TEXT', 'NULL'),
			'summary_cache' => array('TEXT', 'NULL'),
			'finish_date' => array('TEXT', 'date', 'NULL')
		);
	function __construct($db) {

		$helper = plugin_load('helper', 'proza');
		$fields['coordinator']['list'] = array_keys($helper->users());

		$categories = $db->spawn('categories');
		$cat_keys = array();
		$r = $categories->select('name');
		while ($row = $r->fetchArray()) {
			$cat_keys[] = $row['name'];
		}
		$fields['name']['list'] = $cat_keys;

		parent::__construct($db);
	}

	function insert($post) {
		$wiki_f = array('assumptions' => $post['assumptions'], 'summary' => $post['summary']);
		foreach ($wiki_f as $k => $v) {
			$info = array();
			$post[$k.'_cache'] = p_render('xhtml',p_get_instructions($v), $info);
		}
		parent::insert($post);
	}
}
