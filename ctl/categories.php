<?php

require_once DOKU_PLUGIN."proza/mdl/categories.php";

$db = new DB();
$categories = $db->spawn('categories');

try {
	$this->t['categories'] = $categories->select('name', array('group_n' => $this->params['group']));
} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

if (isset($this->params['confirm_delete'])) {
	$this->t['confirm_delete'] = $this->params['confirm_delete'];
} else if (isset($this->params['delete']))
	try {
		$pk = $this->params['delete'];
		$categories->delete($pk);
		header('Location: ?id='.$this->id('categories', 'group', $this->params['group']));
	} catch (Proza_ValException $e) {
		$this->t['errors'][$e->getMessage()] = $e->getErrors();
	}
else if ($this->params['action'] == 'add')
	try {
		$data = $_POST;
		$data['group_n'] = $this->params['group'];
		$categories->insert($data);
		header('Location: ?id='.$this->id('categories', 'group', $this->params['group']));
	} catch (Proza_ValException $e) {
		$this->t['errors']['categories'] = $e->getErrors();
	}
