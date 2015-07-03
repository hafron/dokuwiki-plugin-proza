<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$db = new DB();
$events = $db->spawn('events');

try {
	$this->t['helper'] = plugin_load('helper', 'proza');
	$this->t['groups'] = $this->t['helper']->groups($this->lang_code);

	$this->t['repglob'] = $events->repglob($this->params['year']);
	$helper = plugin_load('helper', 'proza');
} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}
