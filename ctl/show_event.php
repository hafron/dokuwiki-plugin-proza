<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$helper = $this->loadHelper('proza');
if (!$helper->user_viewer()) 
	throw new Proza_DBException($this->getLang('e_access_denied'));

$db = new DB();
$events = $db->spawn('events');

try {
	/*znamy id, ale nie znamy grupy*/
	if (isset($this->params['id']) && !isset($this->params['group'])) {
		$ev = $events->select(array('group_n'), array('id' => $this->params['id']));
		$this_ev = $ev->fetchArray();
		header('Location: ?id=proza:show_event:group:'.$this_ev['group_n'].':id:'.$this->params['id']);
	}

	$ev = $events->select(
		array('id', 'group_n', 'state', 'name', 'plan_date', 'assumptions_cache', 'coordinator', 'summary_cache', 'finish_date'),
		array('group_n' => $this->params['group'], 'id' => $this->params['id']));
	$this_ev = $ev->fetchArray();

	if ($this_ev == false) {
		$e = new Proza_ValException('events');
		$e->setErrors(array(array('id', 'not_exists')));
		throw $e;
	}

	$this->t['helper'] = plugin_load('helper', 'proza');

	$this->t['event'] = $this_ev;

	$g_headers = $this->t['helper']->groups($this->lang_code);
	$this->t['group_header'] = $g_headers[$this_ev['group_n']];

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

