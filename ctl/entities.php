<?php

require_once DOKU_PLUGIN."proza/mdl/entities.php";
require_once DOKU_PLUGIN."proza/mdl/categories.php";

$db = new DB();
$categories = $db->spawn('categories');
$entities = $db->spawn('entities');

try {
	$this->t['categories'] = $categories->select('name', array('group_n' => $this->params['group']));
} catch (Proza_ValException $e) {
	$this->display_validation_errors($e->getErrors());
	$this->preventDefault();
}

if ($this->params['table'] == 'categories') {
	if (isset($this->params['confirm_delete'])) {
		$this->t['confirm_delete'] = $this->params['confirm_delete'];
	} else if (isset($this->params['delete']))
		try {
			$pk = $this->params['delete'];
			$categories->delete($pk);
		} catch (Proza_ValException $e) {
			$this->t['errors'][$e->getMessage()] = $e->getErrors();
		}
	else 
		try {
			$data = $_POST;
			$data['group_n'] = $this->params['group'];
			$categories->insert($data);
			header('Location: ?id='.$this->id('entities', 'group', $this->params['group']));
		} catch (Proza_ValException $e) {
			$this->t['errors'][$e->getMessage()] = $e->getErrors();
		}
}
