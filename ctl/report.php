<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$filters = array('name');

if (count($_POST) > 0) {

	$query = array('start');
	foreach ($filters as $f) {
		if ($_POST[$f] != '-all')
			array_push($query, $f, $_POST[$f]);
	}

	header('Location: ?id='.$this->id($query));
}

$db = new DB();
$events = $db->spawn('events');

try {

	$where = array();
	foreach ($filters as $f) {
		if (isset($this->params[$f]))
			$where[$f] = $this->params[$f];
	}

	/*year jest traktowany specjalnie*/
	if (isset($this->params['year'])) {
		$year = $this->params['year'];
		$where['plan_date'] = array('BETWEEN', $year.'-01-01', $year.'-12-31');
	}

	$this->t['events'] = $events->select(
		array('id', 'group_n', 'name', 'state', 'plan_date', 'assumptions_cache', 'coordinator', 'summary_cache', 'finish_date'),
		$where, 'plan_date');

	$this->t['helper'] = plugin_load('helper', 'proza');
	$this->t['coordinators'] = $this->t['helper']->users();
	$this->t['groups'] = $this->t['helper']->groups();

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

