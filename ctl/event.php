<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$helper = $this->loadHelper('proza');
if (!$helper->user_viewer()) 
	throw new Proza_DBException($this->getLang('e_access_denied'));

$db = new DB();
$events = $db->spawn('events');
$groups = $db->spawn('groups');

try {
	$this->t['groups'] = $groups->groups($this->lang_code);
	$this->t['helper'] = $this->loadHelper('proza');
	$this->t['coordinators'] = $this->t['helper']->users();

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

if (count($_POST) == 0) {
	$this->t['values']['group_n'] = $this->params['group_n'];
}
if ($this->params['action'] == 'add')
	try {
		$data = $_POST;
		$events->insert($data);

		$lastid = $events->db->lastid();

		/*wyślij powiadomienie*/
		$g_headers = $this->t['helper']->groups($this->lang_code);
		$to = $data['coordinator'];
		$subject = "[PROZA][$conf[title]] $".$lastid." ".$this->t[groups][$data[group_n]];
		$body = "Dodano do programu: ".
			DOKU_URL . "doku.php?id=" .
			$this->id('show_event', 'group_n', $this->params['group_n'], 'id', $lastid);
		$this->t['helper']->mail($to, $subject, $body, $_SERVER[HTTP_HOST]);

		header('Location: ?id='.$this->id('show_event', 'group_n', $this->params['group_n'], 'id', $lastid));
	} catch (Proza_ValException $e) {
		$this->t['errors']['events'] = $e->getErrors();
		$this->t['values'] = $_POST;
	}
if ($this->params['action'] == 'edit')
	try {
		
		$id = $this->params['id']; 
		$event = $events->select(
			array('group_n', 'state', 'assumptions', 'plan_date', 'coordinator', 'summary'),
			array('events.id' => $id));

		$this->t['values'] = $event->fetchArray();

		if (!$this->t['helper']->user_eventeditor($this->t['values'])) 
			throw new Proza_DBException($this->getLang('e_access_denied'));

	/*błędne id - błąd na górę*/
	} catch (Proza_ValException $e) {
		$this->errors = $e->getErrors();
		$this->preventDefault();
	}
elseif ($this->params['action'] == 'update')
	try {
		$id = $this->params['id']; 
		$event = $events->select(
			array('coordinator'),
			array('events.id' => $id));
		$row = $event->fetchArray();
		if (!$this->t['helper']->user_eventeditor($row)) 
			throw new Proza_DBException($this->getLang('e_access_denied'));

		$data = $_POST;
		if ($data['state'] != 0)
			$data['finish_date'] = $this->t['helper']->norm_date();
		else {
			$data['summary'] = '';
			$data['summary_cache'] = '';
			$data['finish_date'] = '';
		}

		$events->update($data, $this->params['id']);

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
		$this->params['action'] = 'edit';
	}
elseif ($this->params['action'] == 'duplicate')
	try {
		$id = $this->params['id']; 
		$event = $events->select(
			array('name', 'assumptions', 'coordinator'),
			array('id' => $id, 'group_n' => $this->params['group']));

		$this->t['values'] = $event->fetchArray();

	/*błędne id - błąd na górę*/
	} catch (Proza_ValException $e) {
		$this->errors = $e->getErrors();
		$this->preventDefault();
	}
