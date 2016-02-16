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

	$this->t['helper'] = $helper;

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

if ($this->params['action'] == 'close' || $this->params['action'] == 'reject')
	try {
		
		$id = $this->params['id']; 
		$event = $events->select(
			array('coordinator', 'summary'),
			array('events.id' => $id));

		$this->t['values'] = $event->fetchArray();

		if (!$helper->user_eventeditor($this->t['values'])) 
			throw new Proza_DBException($this->getLang('e_access_denied'));

	/*błędne id - błąd na górę*/
	} catch (Proza_ValException $e) {
		$this->errors = $e->getErrors();
		$this->preventDefault();
	}
elseif ($this->params['action'] == 'close_save' || $this->params['action'] == 'reject_save')
	try {
		$id = $this->params['id']; 
		$event = $events->select(
			array('coordinator', 'state'),
			array('events.id' => $id));
		$row = $event->fetchArray();
		if (!$helper->user_eventeditor($row)) 
			throw new Proza_DBException($this->getLang('e_access_denied'));

		$data['summary'] = $_POST['summary'];
		$data['finish_date'] = $this->t['helper']->norm_date();
		if ($this->params['action'] == 'close_save')
			$data['state'] = 1;
		else
			$data['state'] = 2;

		$events->update_summary($data, $this->params['id']);

		/*wyślij powiadomienie*/
		$g_headers = $this->t['helper']->groups($this->lang_code);
		$to = $data['coordinator'];
		$subject = "[PROZA][$conf[title]] $".$id." ".$this->t[groups][$data[group_n]];
		$body = "Zmieniono program: ".
			DOKU_URL . "doku.php?id=" .
			$this->id('show_event', 'group_n', $this->params['group_n'], 'id', $id);
		$this->t['helper']->mail($to, $subject, $body, $_SERVER[HTTP_HOST]);

		header('Location: ?id='.$this->id('show_event', 'group_n', $this->params['group_n'], 'id', $id));
	} catch (Proza_ValException $e) {
		$this->t['errors']['events'] = $e->getErrors();
		$this->t['values'] = $_POST;
		$this->params['action'] = str_replace('_save', '', $this->params['action']);
	}
