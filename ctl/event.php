<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$helper = $this->loadHelper('proza');
if (!$helper->user_viewer()) 
	throw new Proza_DBException($this->getLang('e_access_denied'));

$db = new DB();
$events = $db->spawn('events');
$categories = $db->spawn('categories');

try {
	$categories = $categories->select('name', array('group_n' => $this->params['group']));

	$this->t['categories'] = array();
	while ($row = $categories->fetchArray())
		$this->t['categories'][] = $row['name'];

	$this->t['helper'] = $this->loadHelper('proza');
	$this->t['coordinators'] = $this->t['helper']->users();

} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

if ($this->params['action'] == 'add')
	try {
		$data = $_POST;
		$data['group_n'] = $this->params['group'];
		$events->insert($data);

		$lastid = $events->db->lastid();

		/*wyślij powiadomienie*/
		$g_headers = $this->t['helper']->groups();
		$to = $data['coordinator'];
		$subject = "[PROZA][$conf[title]] ".$g_headers[$data['group_n']]." $".$lastid." $data[name]";
		$body = "Dodano do programu: ".
			DOKU_URL . "doku.php?id=" .
			$this->id('event', 'group', $this->params['group'], 'id', $lastid);
		$this->t['helper']->mail($to, $subject, $body, $_SERVER[HTTP_HOST]);

		header('Location: ?id='.$this->id('show_event', 'group', $this->params['group'], 'id', $lastid));
	} catch (Proza_ValException $e) {
		$this->t['errors']['events'] = $e->getErrors();
		$this->t['values'] = $_POST;
	}
if ($this->params['action'] == 'edit')
	try {
		
		$id = $this->params['id']; 
		$event = $events->select(
			array('name', 'state', 'assumptions', 'plan_date', 'coordinator', 'summary'),
			array('id' => $id, 'group_n' => $this->params['group']));

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
			array('id' => $id, 'group_n' => $this->params['group']));
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
		$g_headers = $this->t['helper']->groups();
		$to = $data['coordinator'];
		$subject = "[PROZA][$conf[title]] ".$g_headers[$data['group_n']]." $".$id." $data[name]";
		$body = "Zmieniono program: ".
			DOKU_URL . "doku.php?id=" .
			$this->id('event', 'group', $this->params['group'], 'id', $id);
		$this->t['helper']->mail($to, $subject, $body, $_SERVER[HTTP_HOST]);

		header('Location: ?id='.$this->id('show_event', 'group', $this->params['group'], 'id', $id));
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
