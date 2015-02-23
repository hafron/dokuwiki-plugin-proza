<?php if (count($this->t['errors']['events']) > 0): ?>
	<?php $this->display_validation_errors($this->t['errors']['events']) ?>
<?php endif ?>
<form action="?id=
<?php echo $this->id('event', 'group', $this->params['group'], 'action', 'add') ?>"
method="post">

<label for="name"><?php echo $this->getLang("h_name") ?></label>
<select id="name" name="name">
	<?php foreach ($this->t['categories'] as $name): ?>
		<option <?php if ($this->t['value'] == $name) echo 'selected' ?>
		 value="<?php echo $name ?>"><?php echo $name ?></option>
	<?php endforeach ?>
</select>

<label for="assumptions"><?php echo $this->getLang("h_assumptions") ?></label>
<textarea id="assupmtions" name="assumptions"><?php echo $this->t['values']['assumptions'] ?></textarea>

<label for="plan_date"><?php echo $this->getLang("h_plan_date") ?></label>
<input id="plan_date" name="plan_date" value="<?php echo $this->t['values']['plan_date'] ?>" type="text" />

<label for="coordinator"><?php echo $this->getLang("h_coordinator") ?></label>
<select id="coordinator" name="coordinator">
	<?php foreach ($this->t['coordinators'] as $nick => $name): ?>
		<option <?php if ($this->t['value'] == $nick) echo 'selected' ?>
		 value="<?php echo $nick ?>"><?php echo $name ?></option>
	<?php endforeach ?>
</select>


<input type="submit" value="<?php echo $this->getLang('save') ?>" />

</form>
