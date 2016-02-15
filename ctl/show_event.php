<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$helper = $this->loadHelper('proza');
if (!$helper->user_viewer()) 
	throw new Proza_DBException($this->getLang('e_access_denied'));

$db = new DB();
$events = $db->spawn('events');

try {
	$ev = $events->select(
		array('events.id', "groups.$this->lang_code as group_n", 'state',
				'plan_date', 'assumptions_cache', 'coordinator', 'summary_cache', 'cost', 'finish_date'),
		array('events.id' => $this->params['id']));
	$this_ev = $ev->fetchArray();

	if ($this_ev == false) {
		$e = new Proza_ValException('events');
		$e->setErrors(array(array('id', 'not_exists')));
		throw $e;
	}
	$this->t['event'] = $this_ev;

	$this->t['helper'] = plugin_load('helper', 'proza');

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

