<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";
require_once DOKU_PLUGIN."proza/mdl/categories.php";

class Proza_Events extends Proza_Table {
	public $insert_skip = array('id');
	public $update_skip = array('id', 'group_n', 'plan_date');
	public $fields = array(
			'id'	=> array('INTEGER', 'NOT NULL', 'PRIMARY KEY'),
			'name'  => array('TEXT', 'NOT NULL'),
			'group_n' => array('TEXT', 'NOT NULL'),
			'plan_date' => array('TEXT', 'date', 'NOT NULL'),
			'assumptions' => array('TEXT', 'NOT NULL'),
			'assumptions_cache' => array('TEXT', 'NULL'),
			'coordinator' => array('TEXT', 'NOT NULL'),
			'state' => array('INTEGER', 'NOT NULL', 'DEFAULT 0'),
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

	function wiki_prepare(&$post) {
		$wiki_f = array('assumptions' => $post['assumptions'], 'summary' => $post['summary']);
		foreach ($wiki_f as $k => $v) {
			$info = array();
			$post[$k.'_cache'] = p_render('xhtml',p_get_instructions($v), $info);
		}
	}

	function insert($post) {
		$this->wiki_prepare($post);
		parent::insert($post);
	}

	function update($post, $id) {
		$this->wiki_prepare($post);
		parent::update($post, $id);
	}

	function validate($post, $skip_empty=false, $skip=array()) {
		/*summary i finish date są od siebie zależne*/
		if (isset($post['summary']) && trim($post['summary']) != '')
			$this->fields['finish_date'][] = 'NOT NULL';
		if (isset($post['finish_date']) && trim($post['finish_date']) != '')
			$this->fields['summary'][] = 'NOT NULL';
		parent::validate($post, $skip_empty, $skip);
	}

	function years() {
		$res = $this->db->query("SELECT MIN(plan_date) FROM $this->name");
		//istnieje jakikolwiek rekord
		$row = $res->fetchArray();
		if (count($row) > 0) {
			$r1 = strtotime($row[0]);

			$res = $this->db->query("SELECT MIN(finish_date) FROM $this->name");
			$r2 = strtotime($res->fetchArray()[0]);
			$min_year = date('Y', min($r1, $r2));


			$res = $this->db->query("SELECT MAX(plan_date) FROM $this->name");
			$r1 = strtotime($res->fetchArray()[0]);

			$res = $this->db->query("SELECT MAX(finish_date) FROM $this->name");
			$r2 = strtotime($res->fetchArray()[0]);
			$max_year = date('Y', max($r1, $r2));

			return range($min_year, $max_year);
		} 
		return array(date('Y'));
	}
}
