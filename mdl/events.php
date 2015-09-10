<?php

require_once DOKU_PLUGIN."proza/mdl/table.php";
require_once DOKU_PLUGIN."proza/mdl/groups.php";

class Proza_Events extends Proza_Table {
	public $insert_skip = array('id');
	public $update_skip;
	public $fields = array(
			'id'	=> array('INTEGER', 'NOT NULL', 'PRIMARY KEY'),
			'group_n' => array('INTEGER', 'NOT NULL'),
			'plan_date' => array('TEXT', 'date', 'NOT NULL'),
			'assumptions' => array('TEXT', 'NOT NULL'),
			'assumptions_cache' => array('TEXT', 'NULL'),
			'coordinator' => array('TEXT', 'NOT NULL'),
			'state' => array('INTEGER', 'NOT NULL', 'DEFAULT 0', 'state' => array(0, 1, 2)),
			'summary' => array('TEXT', 'NULL'),
			'summary_cache' => array('TEXT', 'NULL'),
			'finish_date' => array('TEXT', 'date', 'NULL')
		);
	public $relations = array(
								array('groups', 'group_n', 'groups.id')
							);
	function __construct($db) {

		$helper = plugin_load('helper', 'proza');
		$fields['coordinator']['list'] = array_keys($helper->users());

		$this->update_skip = array('id');
		/*admin może aktualizować "plan_date"*/
		if (!$helper->user_admin())
			$this->update_skip[] = 'plan_date';

		$groups = $db->spawn('groups');
		$fields['group_n']['list'] = $groups->groups();

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

	function years($group='') {
		$where = '';
		if ($group != '') 
			$where = "WHERE group_n=".$this->db->escape($group);

		$res = $this->db->query("SELECT MIN(plan_date) FROM $this->name $where");
		//istnieje jakikolwiek rekord
		$row = $res->fetchArray();
		if ($row[0] != NULL) {
			$r1 = strtotime($row[0]);

			$res = $this->db->query("SELECT MIN(finish_date) FROM $this->name $where");
			$r2 = strtotime($res->fetchArray()[0]);
			if ($r2 != false)
				$min_year = date('Y', min($r1, $r2));
			else
				$min_year = date('Y', $r1);

			if ($min_year > (int)date('Y'))
				$min_year = date('Y');

			$res = $this->db->query("SELECT MAX(plan_date) FROM $this->name $where");
			$r1 = strtotime($res->fetchArray()[0]);

			$res = $this->db->query("SELECT MAX(finish_date) FROM $this->name $where");
			$r2 = strtotime($res->fetchArray()[0]);
			if ($r2 != false)
				$max_year = date('Y', max($r1, $r2));
			else
				$max_year = date('Y', $r1);

			if ($max_year < (int)date('Y'))
				$max_year = (int)date('Y');

			return range($min_year, $max_year);
		} 
		return array(date('Y'));
	}

	function repglob($year=-1, $langcode='en') {
		if (isset($year) && $year > 0)
			$where[] = "plan_date BETWEEN '".$year."-01-01' AND '".$year."-12-31'";
		$where[] = 'groups.id = a.group_n';

		
		$res = $this->db->query("SELECT $langcode as group_n,

		(SELECT COUNT(*) FROM $this->name AS a
		WHERE ".implode(' AND ',$where).") AS nall,

		(SELECT COUNT(*) FROM $this->name AS a
		WHERE ".implode(' AND ', array_merge($where, array('state = 0'))).") AS nopen,

		(SELECT COUNT(*) FROM $this->name AS a
		WHERE ".implode(' AND ', array_merge($where, array('state = 1', 'plan_date >= finish_date'))).") AS nclosed_ontime,

		(SELECT COUNT(*) FROM $this->name AS a
		WHERE ".implode(' AND ', array_merge($where, array('state = 1', 'plan_date < finish_date'))).") AS nclosed_outdated,

		(SELECT COUNT(*) FROM $this->name AS a
		WHERE ".implode(' AND ', array_merge($where, array('state = 2'))).") AS nrejected
									FROM groups 
									ORDER BY id");

		return $res;
	}
}
