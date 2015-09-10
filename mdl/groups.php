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

		/**
		 * Przejście na nowy schemat bazy.
		 */
		$r = $this->db->query("SELECT * FROM groups");
		$a = $r->fetchArray();
		if ($a == false) {
							/*ind	pl							en*/
			$data = array(
			'grp_ia' => array(1, 'audyt wewnętrzny',			'internal audit'),
			'grp_mr' =>	array(2, 'przeglądy zarządzania',		'managment review'),
			'grp_ts' => array(3, 'przeglądy techniczne',		'technical review'),
			'grp_pt' => array(4, 'szkolenia personelu',		'personel traning'),
			'grp_pm' => array(5, 'ocena procesów',				'process control'),
			'grp_re' => array(6, 'ocena ryzyka',				'risk treatment'),
			'grp_ce' => array(7, 'ocena zgodności',			'evaluation of compliance'),
			'grp_es' => array(8, 'ocena dostawców', 			'evaluate suppliers'),
			'grp_ec' => array(9, 'ocena klientów', 			'evaluate clients'),
			'grp_ri' => array(10, 'zalecenia doskonalenia', 	'recommendations for improvement')
			);
			$id=1;
			foreach ($data as $key => $row) {
				$this->db->query("INSERT INTO groups(id, pl,en) VALUES ('$row[0]', '$row[1]', '$row[2]')");
				$id++;
			}
			$this->db->query("ALTER TABLE events RENAME TO eventsbackup");
			/*recreate events*/
			$this->db->spawn('events');
			$r = $this->db->query("SELECT * FROM eventsbackup");
			while ($row = $r->fetchArray()) {
			$this->db->query("INSERT INTO
					events(id, group_n, plan_date, assumptions, assumptions_cache,
					coordinator, state, summary, summary_cache, finish_date)
					VALUES
					(
					".$this->db->escape($row[id]).",
					".$this->db->escape($data[$row[group_n]][0]).",
					".$this->db->escape($row[plan_date]).",
					".$this->db->escape($row[assumptions]).",
					".$this->db->escape($row[assumptions_cache]).",
					".$this->db->escape($row[coordinator]).",
					".$this->db->escape($row[state]).",
					".$this->db->escape($row[summary]).",
					".$this->db->escape($row[summary_cache]).",
					".$this->db->escape($row[finish_date])."
					)");
			}
			
		}
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
