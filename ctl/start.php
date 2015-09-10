<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$helper = $this->loadHelper('proza');
if (!$helper->user_viewer()) 
	throw new Proza_DBException($this->getLang('e_access_denied'));

$filters = array('coordinator');

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

	$where = array('state' => 0);
	foreach ($filters as $f) {
		if (isset($this->params[$f]))
			$where[$f] = $this->params[$f];
	}

	$this->t['events'] = $events->select(
		array('events.id', "groups.$this->lang_code as group_n",
		'state', 'plan_date', 'assumptions_cache', 'coordinator', 'summary_cache', 'finish_date'),
		$where, 'plan_date');

	$this->t['helper'] = plugin_load('helper', 'proza');
	$this->t['coordinators'] = $this->t['helper']->users();

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

