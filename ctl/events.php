<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$db = new DB();
$events = $db->spawn('events');

try {
	$this->t['events'] = $events->select(
		array('id', 'code', 'name', 'plan_date', 'assumptions', 'coordinator', 'summary', 'finish_date'),
		array('group_n' => $this->params['group']));
} catch (Proza_ValException $e) {
	$this->display_validation_errors($e->getErrors());
	$this->preventDefault();
}
