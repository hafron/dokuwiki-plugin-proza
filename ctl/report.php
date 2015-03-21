<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$db = new DB();
$events = $db->spawn('events');

try {
	$this->t['report'] = $events->report($this->params['group'], $this->params['year']);
	$helper = plugin_load('helper', 'proza');
	$g_headers = $helper->groups();
	$this->t['group_header'] = $g_headers[$this->params['group']];

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

