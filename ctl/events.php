<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$filters = array('name', 'coordinator', 'state', 'year');

if (count($_POST) > 0) {

	$query = array('events', 'group', $this->params['group']);
	foreach ($filters as $f) {
		if ($_POST[$f] != '-all')
			array_push($query, $f, $_POST[$f]);
	}

	header('Location: ?id='.$this->id($query));
}

$db = new DB();
$events = $db->spawn('events');
$categories = $db->spawn('categories');

try {

	$where = array('group_n' => $this->params['group']);
	foreach ($filters as $f) {
		if (isset($this->params[$f]))
			$where[$f] = $this->params[$f];
	}

	/*year jest traktowany specjalnie*/
	if (isset($where['year'])) {
		$year = $where['year'];
		unset($where['year']);

		if (isset($where['state']) && $where['state'] != '0')
			$field = 'finish_date';
		else
			$field = 'plan_date';

		$where[$field] = array('BETWEEN', $year.'-01-01', $year.'-12-31');
	}

	$this->t['events'] = $events->select(
		array('id', 'name', 'state', 'plan_date', 'assumptions_cache', 'coordinator', 'summary_cache', 'finish_date'),
		$where, 'id', 'DESC');

	$this->t['categories'] = $categories->select('name', array('group_n' => $this->params['group']));

	$helper = $this->loadHelper('proza');
	$this->t['coordinators'] = $helper->users();

	$this->t['years'] = $events->years();

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

$this->t['helper'] = plugin_load('helper', 'proza');
