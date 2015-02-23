<?php

require_once DOKU_PLUGIN."proza/mdl/events.php";

$db = new DB();
$events = $db->spawn('events');
$categories = $db->spawn('categories');

try {
	$categories = $categories->select('name', array('group_n' => $this->params['group']));

	$this->t['categories'] = array();
	while ($row = $categories->fetchArray())
		$this->t['categories'][] = $row['name'];

	$helper = $this->loadHelper('proza');
	$this->t['coordinators'] = $helper->users();

} catch (Proza_ValException $e) {
	$this->display_validation_errors($e->getErrors());
	$this->preventDefault();
}

if ($this->params['action'] == 'add')
	try {
		$data = $_POST;
		$data['group_n'] = $this->params['group'];
		$events->insert($data);
		header('Location: ?id='.$this->id('events', 'group', $this->params['group']));
	} catch (Proza_ValException $e) {
		$this->t['errors']['events'] = $e->getErrors();
		$this->t['values'] = $_POST;
	}
