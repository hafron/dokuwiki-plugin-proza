<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$db = new DB();
$events = $db->spawn('events');

try {
	$this->t['repglob'] = $events->repglob($this->params['year'], $this->lang_code);
	$helper = plugin_load('helper', 'proza');
} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}
