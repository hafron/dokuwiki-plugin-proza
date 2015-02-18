<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";

class Entities extends Table {
	$fields = array(
			'id'	=> array('INTEGER', 'PRIMARY KEY'),
			'name'	=> array('TEXT', 'NOT NULL'),
			'code'	=> array('TEXT', 'NOT NULL'),
			'group' => array('TEXT', 'NOT NULL'),
			'coordinator' => array('TEXT', 'NOT NULL')
			);
	function __construct($db) {
		global $auth;

		$fields['group'][] = array('grp_mr', 'grp_audit', 'grp_infrastructure', 'grp_training');
		$fields['coordinator'] = array_keys($auth->retriveUsers());
		parent::__construct($db);
	}
}
