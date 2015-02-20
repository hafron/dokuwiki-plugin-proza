<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";
require_once DOKU_PLUGIN."proza/mdl/entities.php";

class Proza_Events extends Proza_Table {
	public $fields = array(
			'id'	=> array('INTEGER', 'NOT NULL', 'PRIMARY KEY'),
			'entity'=> array('TEXT', 'NOT NULL'),
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

		$entities = $db->spawn('entities');
		$ent_keys = array();
		$r = $entities->select('code');
		while ($row = $r->fetchArray()) {
			$ent_keys[] = $row['code'];
		}
		$fields['entity']['list'] = $ent_keys;

		parent::__construct($db);
	}
}
