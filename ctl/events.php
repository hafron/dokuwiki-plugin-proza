<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$db = new DB();
$events = $db->spawn('events');

try {
	$this->t['events'] = $events->select(
		array('id', 'name', 'plan_date', 'assumptions_cache', 'coordinator', 'summary_cache', 'finish_date'),
		array('group_n' => $this->params['group']), 'id', 'DESC');
} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

$this->t['helper'] = plugin_load('helper', 'proza');
