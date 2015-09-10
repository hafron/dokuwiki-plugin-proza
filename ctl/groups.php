<?php

require_once DOKU_PLUGIN."proza/mdl/groups.php";

$helper = $this->loadHelper('proza');
if (!$helper->user_admin()) 
	throw new Proza_DBException($this->getLang('e_access_denied'));

$db = new DB();
$groups = $db->spawn('groups');

try {
	$this->t['groups'] = $groups->select_refs();
} catch (Proza_ValException $e) {
	$this->errors = $e->getErrors();
	$this->preventDefault();
}

if (isset($this->params['confirm_delete'])) {
	$this->t['confirm_delete'] = $this->params['confirm_delete'];
} else if (isset($this->params['delete']))
	try {
		$pk = $this->params['delete'];
		$groups->delete($pk);
		header('Location: ?id='.$this->id('groups'));
	} catch (Proza_ValException $e) {
		$this->t['errors']['groups'] = $e->getErrors();
	}
else if ($this->params['action'] == 'add')
	try {
		$data = $_POST;
		$groups->insert($data);
		header('Location: ?id='.$this->id('groups'));
	} catch (Proza_ValException $e) {
		$this->t['action'] = 'add';
		$this->t['errors']['groups'] = $e->getErrors();
		$this->t['values']['pl'] = trim($_POST['pl']);
		$this->t['values']['en'] = trim($_POST['en']);
	}
else if (isset($this->params['edit']))
	try {
		$id = $this->params['edit']; 
		$this->t['action'] = 'update:id:'.$id;
		$group = $groups->select('*', array('id' => $id));
		$this->t['values'] = $group->fetchArray();
	} catch (Proza_ValException $e) {
		$this->t['errors']['groups'] = $e->getErrors();
	}
elseif ($this->params['action'] == 'update') {
	try {
		$data = $_POST;
		$id = $this->params['id']; 
		$data['id'] = $id;
		$groups->update($data, $id);
		header('Location: ?id='.$this->id('groups'));
	} catch (Proza_ValException $e) {
		$this->t['errors']['groups'] = $e->getErrors();
		$this->t['action'] = 'update:id:'.$id;
		$this->t['values']['pl'] = trim($_POST['pl']);
		$this->t['values']['en'] = trim($_POST['en']);
	}
}
else {
	/*default action*/
	$this->t['action'] = 'add';
}

